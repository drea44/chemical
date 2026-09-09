<?php

namespace App\Http\Controllers;

use App\Models\Chemical;
use App\Models\StockAdjustment;
use App\Models\StockTransaction;
use App\Services\AuditLogService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'monitoring_current');

        // ── Stats (single aggregated query) ─────────────────────────────────
        $statusCounts = Chemical::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $stats = [
            'total_chemicals' => $statusCounts->sum(),
            'active'          => (int) ($statusCounts['SAFE'] ?? 0),
            'low_stock'       => (int) ($statusCounts['LOW'] ?? 0),
            'critical'        => (int) ($statusCounts['CRITICAL'] ?? 0)
                               + (int) ($statusCounts['EXPIRED'] ?? 0)
                               + (int) ($statusCounts['EXPIRING_SOON'] ?? 0),
        ];

        $inventorySummary = Chemical::with(['category', 'location'])
            ->orderBy('id')->get();

        // ── Monitoring periods (June, May, April, March 2026) ────────────────
        $junePeriod  = ['start' => Carbon::parse('2026-06-01 00:00:00'), 'end' => Carbon::parse('2026-06-30 23:59:59')];
        $mayPeriod   = ['start' => Carbon::parse('2026-05-01 00:00:00'), 'end' => Carbon::parse('2026-05-31 23:59:59')];
        $aprilPeriod = ['start' => Carbon::parse('2026-04-01 00:00:00'), 'end' => Carbon::parse('2026-04-30 23:59:59')];
        $marchPeriod = ['start' => Carbon::parse('2026-03-01 00:00:00'), 'end' => Carbon::parse('2026-03-31 23:59:59')];

        // ── Monitoring reports (BUG-10 fix: single aggregate per month) ──────
        $monitoringReportJune   = $this->buildMonitoringReport($junePeriod['start'],  $junePeriod['end'],  [], true);
        $monitoringReportMay    = $this->buildMonitoringReport($mayPeriod['start'],   $mayPeriod['end'],   [$junePeriod]);
        $monitoringReportApril  = $this->buildMonitoringReport($aprilPeriod['start'], $aprilPeriod['end'], [$junePeriod, $mayPeriod]);
        $monitoringReportMarch  = $this->buildMonitoringReport($marchPeriod['start'], $marchPeriod['end'], [$junePeriod, $mayPeriod, $aprilPeriod]);

        // Aliases for forward-compatibility
        $monitoringReportCurrent = $monitoringReportJune;
        $monitoringReportLast    = $monitoringReportMay;
        $monitoringReportTwo     = $monitoringReportApril;
        $monitoringReportThree   = $monitoringReportMarch;

        // ── Stock Movement (filtered) ─────────────────────────────────────────
        $stockMovement = StockTransaction::with(['chemical', 'performer'])
            ->when($request->date_from, fn($q) => $q->whereDate('transaction_date', '>=', $request->date_from))
            ->when($request->date_to,   fn($q) => $q->whereDate('transaction_date', '<=', $request->date_to))
            ->orderBy('transaction_date', 'desc')
            ->paginate(50, ['*'], 'movement_page')
            ->withQueryString();

        // ── Expiry Report ─────────────────────────────────────────────────────
        $expiryReport = Chemical::whereNotNull('expiry_date')
            ->orderBy('expiry_date')
            ->with('location')
            ->get();

        // ── Adjustment Report ─────────────────────────────────────────────────
        $adjustmentReport = StockAdjustment::with(['chemical', 'adjuster', 'approver'])
            ->orderBy('created_at', 'desc')
            ->paginate(50, ['*'], 'adj_page')
            ->withQueryString();

        return view('reports.index', compact(
            'stats', 'tab', 'inventorySummary',
            'monitoringReportJune', 'monitoringReportMay',
            'monitoringReportApril', 'monitoringReportMarch',
            'monitoringReportCurrent', 'monitoringReportLast',
            'monitoringReportTwo', 'monitoringReportThree',
            'stockMovement', 'expiryReport', 'adjustmentReport'
        ));
    }

    /**
     * Build a monitoring report for a given period.
     *
     * @param  Carbon       $periodStart      Start of the period being reported
     * @param  Carbon       $periodEnd        End of the period being reported
     * @param  array        $laterMonths      Array of ['start'=>Carbon, 'end'=>Carbon] for months
     *                                        AFTER the period (needed to reconstruct period-end stock)
     * @param  bool         $isCurrentMonth   If true, saldo_akhir = current_stock (no reconstruction needed)
     * @return \Illuminate\Support\Collection
     */
    private function buildMonitoringReport(
        Carbon $periodStart,
        Carbon $periodEnd,
        array $laterMonths = [],
        bool $isCurrentMonth = false
    ): \Illuminate\Support\Collection {
        // Load all chemicals with their category and location
        $chemicals = Chemical::with(['category', 'location'])->orderBy('id')->get();

        // ── Load period transactions in ONE aggregated query (BUG-10) ─────────
        $periodAgg = StockTransaction::selectRaw('
                chemical_id,
                transaction_type,
                SUM(quantity) as total
            ')
            ->whereBetween('transaction_date', [
                $periodStart->toDateTimeString(),
                $periodEnd->toDateTimeString(),
            ])
            ->whereIn('transaction_type', ['STOCK_IN', 'STOCK_OUT'])
            ->groupBy('chemical_id', 'transaction_type')
            ->get()
            ->groupBy('chemical_id');

        // ── Load each later-month aggregates in ONE query each (BUG-10) ──────
        $laterAggregates = [];
        foreach ($laterMonths as $lm) {
            $agg = StockTransaction::selectRaw('
                    chemical_id,
                    transaction_type,
                    SUM(quantity) as total
                ')
                ->whereBetween('transaction_date', [
                    $lm['start']->toDateTimeString(),
                    $lm['end']->toDateTimeString(),
                ])
                ->whereIn('transaction_type', ['STOCK_IN', 'STOCK_OUT'])
                ->groupBy('chemical_id', 'transaction_type')
                ->get()
                ->groupBy('chemical_id');

            $laterAggregates[] = $agg;
        }

        // ── Load period detail transactions for "takes" display ──────────────
        // (only STOCK_OUT details needed for display in the report table)
        $periodDetails = StockTransaction::with('performer')
            ->whereBetween('transaction_date', [
                $periodStart->toDateTimeString(),
                $periodEnd->toDateTimeString(),
            ])
            ->where('transaction_type', 'STOCK_OUT')
            ->orderBy('transaction_date')
            ->get()
            ->groupBy('chemical_id');

        return $chemicals->map(function ($chemical, $idx) use (
            $periodAgg, $laterAggregates, $periodDetails, $isCurrentMonth
        ) {
            $cid = $chemical->id;

            // Period IN/OUT
            $periodGroup    = $periodAgg->get($cid, collect());
            $periodIn       = (float) optional($periodGroup->firstWhere('transaction_type', 'STOCK_IN'))->total;
            $periodOut      = (float) optional($periodGroup->firstWhere('transaction_type', 'STOCK_OUT'))->total;

            // Reconstruct saldo_akhir (end-of-period stock)
            // Logic: currentStock = saldoAkhir + laterOut - laterIn (for all later months)
            if ($isCurrentMonth) {
                $saldoAkhir = (float) $chemical->current_stock;
            } else {
                $saldoAkhir = (float) $chemical->current_stock;
                foreach ($laterAggregates as $laterAgg) {
                    $laterGroup = $laterAgg->get($cid, collect());
                    $laterOut   = (float) optional($laterGroup->firstWhere('transaction_type', 'STOCK_OUT'))->total;
                    $laterIn    = (float) optional($laterGroup->firstWhere('transaction_type', 'STOCK_IN'))->total;
                    $saldoAkhir += $laterOut - $laterIn;
                }
            }

            $saldoAwal = $saldoAkhir + $periodOut - $periodIn;

            $takes = $periodDetails->get($cid, collect());

            return (object) [
                'no'            => $idx + 1,
                'chemical'      => $chemical,
                'saldo_awal'    => $saldoAwal,
                'initial_stock' => $saldoAwal,
                'penerimaan'    => $periodIn,
                'pengeluaran'   => $periodOut,
                'used_stock'    => $periodOut,
                'saldo_akhir'   => $saldoAkhir,
                'current_stock' => $saldoAkhir,
                'unit'          => $chemical->unit,
                'takes'         => $takes,
                'status'        => $chemical->status,
            ];
        });
    }

    public function exportCsv(Request $request)
    {
        $request->validate([
            'type' => 'nullable|string|in:inventory,movement,transactions,expiry,adjustments,monitoring,monitoring_current,monitoring_last,monitoring_two,monitoring_three,monitoring_june,monitoring_may,monitoring_april,monitoring_march',
        ]);

        $type     = $request->get('type', 'monitoring_june');
        $date     = now()->format('Y-m-d');
        $filename = "{$type}_report_{$date}.csv";

        $data = $this->getExportData($type);

        $response = response()->streamDownload(function () use ($data) {
            $out = fopen('php://output', 'w');
            if (!empty($data)) {
                fputcsv($out, array_keys((array) $data[0]));
                foreach ($data as $row) {
                    fputcsv($out, (array) $row);
                }
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);

        AuditLogService::log('exported', 'Report', 'Report', null, null, ['type' => $type, 'format' => 'csv']);

        return $response;
    }

    private function getExportData(string $type): array
    {
        $junePeriod  = ['start' => Carbon::parse('2026-06-01 00:00:00'), 'end' => Carbon::parse('2026-06-30 23:59:59')];
        $mayPeriod   = ['start' => Carbon::parse('2026-05-01 00:00:00'), 'end' => Carbon::parse('2026-05-31 23:59:59')];
        $aprilPeriod = ['start' => Carbon::parse('2026-04-01 00:00:00'), 'end' => Carbon::parse('2026-04-30 23:59:59')];
        $marchPeriod = ['start' => Carbon::parse('2026-03-01 00:00:00'), 'end' => Carbon::parse('2026-03-31 23:59:59')];

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

            'movement', 'transactions' => StockTransaction::with(['chemical', 'performer'])
                ->orderBy('transaction_date', 'desc')->get()->map(fn($t) => [
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

            'expiry' => Chemical::whereNotNull('expiry_date')->with('location')
                ->orderBy('expiry_date')->get()->map(fn($c) => [
                'Code'           => $c->chemical_code,
                'Name'           => $c->chemical_name,
                'Location'       => $c->location?->name,
                'Current Stock'  => $c->current_stock,
                'Unit'           => $c->unit,
                'Expiry Date'    => $c->expiry_date?->format('Y-m-d'),
                'Days Remaining' => $c->expiry_date ? now()->diffInDays($c->expiry_date, false) : null,
                'Status'         => $c->status,
            ])->toArray(),

            'adjustments' => StockAdjustment::with(['chemical', 'adjuster', 'approver'])
                ->orderBy('created_at', 'desc')->get()->map(fn($a) => [
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

            'monitoring', 'monitoring_current', 'monitoring_june' => $this->buildMonitoringReport(
                $junePeriod['start'], $junePeriod['end'], [], true
            )->map(fn($r) => [
                'No'          => $r->no,
                'Code'        => $r->chemical->chemical_code,
                'Name'        => $r->chemical->chemical_name,
                'Satuan'      => $r->unit,
                'Saldo Awal'  => $r->saldo_awal,
                'Penerimaan'  => $r->penerimaan,
                'Pengeluaran' => $r->pengeluaran,
                'Saldo Akhir' => $r->saldo_akhir,
                'Status'      => $r->status,
            ])->toArray(),

            'monitoring_last', 'monitoring_may' => $this->buildMonitoringReport(
                $mayPeriod['start'], $mayPeriod['end'], [$junePeriod]
            )->map(fn($r) => [
                'No'          => $r->no,
                'Code'        => $r->chemical->chemical_code,
                'Name'        => $r->chemical->chemical_name,
                'Satuan'      => $r->unit,
                'Saldo Awal'  => $r->saldo_awal,
                'Penerimaan'  => $r->penerimaan,
                'Pengeluaran' => $r->pengeluaran,
                'Saldo Akhir' => $r->saldo_akhir,
                'Status'      => $r->status,
            ])->toArray(),

            'monitoring_two', 'monitoring_april' => $this->buildMonitoringReport(
                $aprilPeriod['start'], $aprilPeriod['end'], [$junePeriod, $mayPeriod]
            )->map(fn($r) => [
                'No'          => $r->no,
                'Code'        => $r->chemical->chemical_code,
                'Name'        => $r->chemical->chemical_name,
                'Satuan'      => $r->unit,
                'Saldo Awal'  => $r->saldo_awal,
                'Penerimaan'  => $r->penerimaan,
                'Pengeluaran' => $r->pengeluaran,
                'Saldo Akhir' => $r->saldo_akhir,
                'Status'      => $r->status,
            ])->toArray(),

            'monitoring_three', 'monitoring_march' => $this->buildMonitoringReport(
                $marchPeriod['start'], $marchPeriod['end'], [$junePeriod, $mayPeriod, $aprilPeriod]
            )->map(fn($r) => [
                'No'          => $r->no,
                'Code'        => $r->chemical->chemical_code,
                'Name'        => $r->chemical->chemical_name,
                'Satuan'      => $r->unit,
                'Saldo Awal'  => $r->saldo_awal,
                'Penerimaan'  => $r->penerimaan,
                'Pengeluaran' => $r->pengeluaran,
                'Saldo Akhir' => $r->saldo_akhir,
                'Status'      => $r->status,
            ])->toArray(),

            default => [],
        };
    }
}