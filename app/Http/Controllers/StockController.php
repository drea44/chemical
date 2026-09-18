<?php

namespace App\Http\Controllers;

use App\Http\Requests\StockInOutRequest;
use App\Http\Requests\StockAdjustmentRequest;
use App\Models\Chemical;
use App\Models\StockTransaction;
use App\Services\StockService;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function __construct(private StockService $stockService) {}

    public function index(Request $request)
    {
        $query = StockTransaction::with(['chemical', 'performer']);

        if ($chemicalId = $request->get('chemical')) {
            $query->where('chemical_id', $chemicalId);
        }

        if ($dateRange = $request->get('date_range')) {
            if ($dateRange === 'today') {
                $query->whereDate('transaction_date', today());
            } elseif ($dateRange === 'yesterday') {
                $query->whereDate('transaction_date', today()->subDay());
            } elseif (is_numeric($dateRange)) {
                $query->where('transaction_date', '>=', now()->subDays((int)$dateRange));
            }
        }

        if ($type = $request->get('type')) {
            $query->where('transaction_type', $type);
        }

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('transaction_code', 'like', "%{$search}%")
                  ->orWhere('reference_number', 'like', "%{$search}%")
                  ->orWhereHas('chemical', function ($c) use ($search) {
                      $c->where('chemical_name', 'like', "%{$search}%")
                        ->orWhere('chemical_code', 'like', "%{$search}%")
                        ->orWhere('cas_number', 'like', "%{$search}%")
                        ->orWhere('batch_number', 'like', "%{$search}%");
                  })
                  ->orWhereHas('performer', function ($u) use ($search) {
                      $u->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $transactions = $query->orderBy('transaction_date', 'desc')
            ->paginate(10)
            ->withQueryString();

        $chemicals = Chemical::orderBy('chemical_name')->get(['id', 'chemical_name', 'chemical_code']);

        return view('stock.index', compact('transactions', 'chemicals'));
    }

    public function showStockInOut(Request $request)
    {
        $this->authorize('adjustStock');

        $chemicals = Chemical::where('status', '!=', 'EXPIRED')
            ->with(['category', 'location'])->orderBy('chemical_name')->get();
        $selected = $request->query('chemical')
            ? Chemical::find($request->query('chemical'))
            : null;

        return view('stock.stock-in-out', compact('chemicals', 'selected'));
    }

    public function processStockInOut(StockInOutRequest $request)
    {
        $this->authorize('adjustStock');

        $chemical = Chemical::findOrFail($request->chemical_id);
        $type     = $request->transaction_type;

        try {
            if ($type === 'STOCK_IN') {
                $this->stockService->stockIn($chemical, (float) $request->quantity, $request->validated());
                $label = 'Stock In';
                $sign  = '+';
            } else {
                $this->stockService->stockOut($chemical, (float) $request->quantity, $request->validated());
                $label = 'Stock Out';
                $sign  = '-';
            }

            $chemical->refresh();

            return redirect()->route('stock.stock-in-out')
                ->with('success', "{$label} recorded: {$sign}{$request->quantity} {$chemical->unit} for {$chemical->chemical_name}. Current stock: {$chemical->current_stock} {$chemical->unit}.");
        } catch (\RuntimeException $e) {
            return redirect()->back()->withInput()
                ->withErrors(['quantity' => $e->getMessage()]);
        }
    }

    public function showAdjustment(Request $request)
    {

        return redirect()->route('stock.stock-in-out');
    }

    public function processAdjustment(StockAdjustmentRequest $request)
    {

        return redirect()->route('stock.stock-in-out')
            ->with('warning', 'Direct adjustment has been replaced by Stock In Out. Please use the new form.');
    }

    public function showStockIn(Request $request)
    {
        return redirect()->route('stock.stock-in-out');
    }

    public function processStockIn(Request $request)
    {
        return redirect()->route('stock.stock-in-out');
    }

    public function showStockOut(Request $request)
    {
        return redirect()->route('stock.stock-in-out');
    }

    public function processStockOut(Request $request)
    {
        return redirect()->route('stock.stock-in-out');
    }
}

