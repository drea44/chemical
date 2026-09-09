<?php

namespace App\Http\Controllers;

use App\Http\Requests\StockAdjustmentRequest;
use App\Http\Requests\StockInRequest;
use App\Http\Requests\StockOutRequest;
use App\Models\Chemical;
use App\Services\StockService;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function __construct(private StockService $stockService) {}

    public function index()
    {
        $chemicals = Chemical::with(['category', 'location'])
            ->orderByRaw("CASE status WHEN 'CRITICAL' THEN 1 WHEN 'EXPIRED' THEN 2 WHEN 'EXPIRING_SOON' THEN 3 WHEN 'LOW' THEN 4 ELSE 5 END")
            ->paginate(20);
        return view('stock.index', compact('chemicals'));
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
