<?php

namespace App\Http\Controllers;

use App\Models\Chemical;
use App\Models\StockAdjustment;
use App\Models\StockTransaction;
use App\Services\AuditLogService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'inventory');

        $stats = [
            'total_chemicals' => Chemical::count(),
            'active'          => Chemical::where('status', 'SAFE')->count(),
            'low_stock'       => Chemical::whereIn('status', ['LOW'])->count(),
            'critical'        => Chemical::whereIn('status', ['CRITICAL', 'EXPIRED', 'EXPIRING_SOON'])->count(),
        ];

        $inventorySummary = Chemical::with(['category', 'location'])
            ->orderBy('chemical_name')->get();

        $stockMovement = StockTransaction::with(['chemical', 'performer'])
            ->when($request->date_from, fn($q) => $q->whereDate('transaction_date', '>=', $request->date_from))
            ->when($request->date_to, fn($q) => $q->whereDate('transaction_date', '<=', $request->date_to))
            ->orderBy('transaction_date', 'desc')
            ->get();

        $expiryReport = Chemical::whereNotNull('expiry_date')
            ->orderBy('expiry_date')
            ->with('location')
            ->get();

        $adjustmentReport = StockAdjustment::with(['chemical', 'adjuster', 'approver'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('reports.index', compact(
            'stats', 'tab', 'inventorySummary', 'stockMovement', 'expiryReport', 'adjustmentReport'
        ));
    }

    public function exportCsv(Request $request)
    {
        $request->validate([
            'type' => 'nullable|string|in:inventory,movement,transactions,expiry,adjustments',
        ]);

        $type = $request->get('type', 'inventory');
        $date = now()->format('Y-m-d');
        $filename = "{$type}_report_{$date}.csv";

        $data = $this->getExportData($type);

        $response = response()->streamDownload(function () use ($data) {
            $out = fopen('php://output', 'w');
            if (!empty($data)) {
                fputcsv($out, array_keys($data[0]));
                foreach ($data as $row) {
                    fputcsv($out, $row);
                }
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);

        AuditLogService::log('exported', 'Report', 'Report', null, null, ['type' => $type, 'format' => 'csv']);

        return $response;
    }

    private function getExportData(string $type): array
    {
        return match($type) {
            'inventory' => Chemical::with(['category', 'location'])->get()->map(fn($c) => [
                'Code'          => $c->chemical_code,
                'Name'          => $c->chemical_name,
                'CAS Number'    => $c->cas_number,
                'Category'      => $c->category?->name,
                'Location'      => $c->location?->name,
                'Current Stock' => $c->current_stock,
                'Unit'          => $c->unit,
                'Min Stock'     => $c->minimum_stock,
                'Status'        => $c->status,
                'Expiry Date'   => $c->expiry_date?->format('Y-m-d'),
            ])->toArray(),

            'movement', 'transactions' => StockTransaction::with(['chemical', 'performer'])->orderBy('transaction_date', 'desc')->get()->map(fn($t) => [
                'Transaction Code' => $t->transaction_code,
                'Date'             => $t->transaction_date?->format('Y-m-d H:i'),
                'Chemical'         => $t->chemical?->chemical_name,
                'Type'             => $t->transaction_type,
                'Quantity'         => $t->quantity,
                'Unit'             => $t->unit,
                'Stock Before'     => $t->stock_before,
                'Stock After'      => $t->stock_after,
                'Performed By'     => $t->performer?->name,
                'Status'           => $t->status,
            ])->toArray(),

            'expiry' => Chemical::whereNotNull('expiry_date')->with('location')->orderBy('expiry_date')->get()->map(fn($c) => [
                'Code'          => $c->chemical_code,
                'Name'          => $c->chemical_name,
                'Location'      => $c->location?->name,
                'Current Stock' => $c->current_stock,
                'Unit'          => $c->unit,
                'Expiry Date'   => $c->expiry_date?->format('Y-m-d'),
                'Days Remaining'=> $c->expiry_date ? now()->diffInDays($c->expiry_date, false) : null,
                'Status'        => $c->status,
            ])->toArray(),

            'adjustments' => StockAdjustment::with(['chemical', 'adjuster', 'approver'])->orderBy('created_at', 'desc')->get()->map(fn($a) => [
                'Adjustment Code' => $a->adjustment_code,
                'Date'            => $a->created_at?->format('Y-m-d H:i'),
                'Chemical'        => $a->chemical?->chemical_name,
                'Previous Stock'  => $a->previous_stock,
                'Adjusted Stock'  => $a->adjusted_stock,
                'Difference'      => $a->difference,
                'Reason'          => $a->reason,
                'Adjusted By'     => $a->adjuster?->name,
                'Approved By'     => $a->approver?->name,
                'Status'          => $a->status,
            ])->toArray(),

            default => [],
        };
    }
}
