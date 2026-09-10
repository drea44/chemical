<?php

namespace App\Http\Controllers;

use App\Http\Requests\StockAdjustmentRequest;
use App\Http\Requests\StockInRequest;
use App\Http\Requests\StockOutRequest;
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

    // STOCK IN
    public function showStockIn(Request $request)
    {
        // BUG-05: Use registered Gate instead of manual role check
        $this->authorize('stockIn');

        $chemicals = Chemical::where('status', '!=', 'EXPIRED')
            ->with(['category', 'location'])->orderBy('chemical_name')->get();
        $selected = $request->query('chemical')
            ? Chemical::find($request->query('chemical'))
            : null;
        return view('stock.in', compact('chemicals', 'selected'));
    }

    public function processStockIn(StockInRequest $request)
    {
        // BUG-05: Authorization already handled by StockInRequest::authorize() which calls canManageStock()
        // Gate check here for belt-and-suspenders consistency
        $this->authorize('stockIn');

        $chemical = Chemical::findOrFail($request->chemical_id);
        $tx = $this->stockService->stockIn($chemical, (float) $request->quantity, $request->validated());

        return redirect()->route('stock.in')
            ->with('success', "Stock In recorded: +{$request->quantity} {$chemical->unit} for {$chemical->chemical_name}. New stock: {$chemical->current_stock} {$chemical->unit}.");
    }

    // STOCK OUT
    public function showStockOut(Request $request)
    {
        $this->authorize('stockOut');

        $chemicals = Chemical::whereNotIn('status', ['EXPIRED'])
            ->with(['category', 'location'])->orderBy('chemical_name')->get();
        $selected = $request->query('chemical')
            ? Chemical::find($request->query('chemical'))
            : null;
        return view('stock.out', compact('chemicals', 'selected'));
    }

    public function processStockOut(StockOutRequest $request)
    {
        $this->authorize('stockOut');

        $chemical = Chemical::findOrFail($request->chemical_id);

        try {
            $tx = $this->stockService->stockOut($chemical, (float) $request->quantity, $request->validated());
            return redirect()->route('stock.out')
                ->with('success', "Stock Out recorded: -{$request->quantity} {$chemical->unit} for {$chemical->chemical_name}. New stock: {$chemical->current_stock} {$chemical->unit}.");
        } catch (\RuntimeException $e) {
            return redirect()->back()->withInput()
                ->withErrors(['quantity' => $e->getMessage()]);
        }
    }

    // STOCK ADJUSTMENT
    public function showAdjustment(Request $request)
    {
        $this->authorize('adjustStock');

        $chemicals = Chemical::with(['category', 'location'])->orderBy('chemical_name')->get();
        $selected = $request->query('chemical')
            ? Chemical::find($request->query('chemical'))
            : null;
        return view('stock.adjustment', compact('chemicals', 'selected'));
    }

    public function processAdjustment(StockAdjustmentRequest $request)
    {
        $this->authorize('adjustStock');

        $chemical  = Chemical::findOrFail($request->chemical_id);
        $newStock  = (float) $request->adjusted_stock;
        $oldStock  = (float) $chemical->current_stock;
        $diff      = $newStock - $oldStock;
        $pctChange = $oldStock > 0 ? abs($diff / $oldStock) * 100 : 100;

        $adjustment = $this->stockService->adjust($chemical, $newStock, $request->validated());

        $warning = '';
        if ($pctChange >= 20) {
            $warning = " Warning: This adjustment represents a " . round($pctChange, 1) . "% change from previous stock.";
        }

        return redirect()->route('stock.adjustment')
            ->with('success', "Stock adjusted for {$chemical->chemical_name}. Previous: {$oldStock}, New: {$newStock} {$chemical->unit}.{$warning}");
    }
}
