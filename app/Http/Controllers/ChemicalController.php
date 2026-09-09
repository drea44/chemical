<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreChemicalRequest;
use App\Http\Requests\UpdateChemicalRequest;
use App\Models\Chemical;
use App\Models\ChemicalCategory;
use App\Models\ChemicalLocation;
use App\Models\Supplier;
use App\Services\AuditLogService;
use App\Services\ChemicalService;
use App\Services\QRCodeService;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChemicalController extends Controller
{
    public function __construct(
        private ChemicalService $chemicalService,
        private QRCodeService   $qrCodeService,
        private StockService    $stockService,
    ) {}

    public function index(Request $request)
    {
        $this->authorize('viewAny', Chemical::class);

        $query = Chemical::with(['category', 'location']);

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('chemical_name', 'like', "%{$search}%")
                  ->orWhere('chemical_code', 'like', "%{$search}%")
                  ->orWhere('cas_number', 'like', "%{$search}%")
                  ->orWhere('batch_number', 'like', "%{$search}%")
                  ->orWhere('supplier', 'like', "%{$search}%");
            });
        }

        if ($category = $request->get('category')) {
            $query->where('category_id', $category);
        }

        if ($location = $request->get('location')) {
            $query->where('location_id', $location);
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($hazard = $request->get('hazard')) {
            $query->where('hazard_class', 'like', "%{$hazard}%");
        }

        $sortField = $request->get('sort', 'chemical_name');
        $sortDir   = $request->get('dir', 'asc');
        $allowedSorts = ['chemical_name', 'chemical_code', 'current_stock', 'expiry_date', 'status', 'created_at'];
        if (in_array($sortField, $allowedSorts)) {
            $query->orderBy($sortField, $sortDir === 'desc' ? 'desc' : 'asc');
        }

        $perPage   = (int) $request->get('per_page', 20);
        $perPage   = in_array($perPage, [20, 50, 100, 200]) ? $perPage : 20;
        $chemicals = $query->paginate($perPage)->withQueryString();
        $categories = ChemicalCategory::where('status', 'active')->orderBy('name')->get();
        $locations  = ChemicalLocation::where('status', 'active')->orderBy('name')->get();

        return view('chemicals.index', compact('chemicals', 'categories', 'locations'));
    }

    public function create()
    {
        $this->authorize('create', Chemical::class);

        $categories = ChemicalCategory::where('status', 'active')->orderBy('name')->get();
        $locations  = ChemicalLocation::where('status', 'active')->orderBy('name')->get();
        $suppliers  = Supplier::where('status', 'active')->orderBy('name')->get();
        $nextCode   = $this->chemicalService->generateChemicalCode();

        return view('chemicals.create', compact('categories', 'locations', 'suppliers', 'nextCode'));
    }

    public function store(StoreChemicalRequest $request)
    {
        $data = $request->validated();

        if (empty($data['chemical_code'])) {
            $data['chemical_code'] = $this->chemicalService->generateChemicalCode();
        }

        $data['created_by'] = Auth::id();
        $data['updated_by'] = Auth::id();

        $chemical = new Chemical($data);
        $chemical->updateStatus();
        $chemical->save();

        // Generate QR code
        $qrContent       = $this->qrCodeService->buildContent($chemical->chemical_code);
        $chemical->qr_code = $this->qrCodeService->generate($chemical->chemical_code, $qrContent);
        $chemical->save();

        // Record initial stock transaction if initial stock > 0 (BUG-04: delegate to StockService)
        if ($chemical->current_stock > 0) {
            $this->stockService->createInitialStockTransaction($chemical);
        }

        AuditLogService::logCreated('Chemical', $chemical->id, [
            'chemical_name' => $chemical->chemical_name,
            'chemical_code' => $chemical->chemical_code,
            'initial_stock' => $chemical->current_stock,
        ]);

        return redirect()->route('chemicals.show', $chemical)
            ->with('success', "Chemical '{$chemical->chemical_name}' registered successfully. QR code generated.");
    }

    public function show(Chemical $chemical)
    {
        $this->authorize('view', $chemical);
        $chemical->load(['category', 'location', 'creator', 'stockTransactions.performer']);
        return view('chemicals.show', compact('chemical'));
    }

    public function printLabel(Chemical $chemical)
    {
        $this->authorize('view', $chemical);
        $chemical->load(['category', 'location']);
        return view('chemicals.label', compact('chemical'));
    }

    public function edit(Chemical $chemical)
    {
        $this->authorize('update', $chemical);
        $categories = ChemicalCategory::where('status', 'active')->orderBy('name')->get();
        $locations  = ChemicalLocation::where('status', 'active')->orderBy('name')->get();
        $suppliers  = Supplier::where('status', 'active')->orderBy('name')->get();
        return view('chemicals.edit', compact('chemical', 'categories', 'locations', 'suppliers'));
    }

    public function update(UpdateChemicalRequest $request, Chemical $chemical)
    {
        $old = $chemical->only(['chemical_name', 'minimum_stock', 'maximum_stock', 'location_id', 'expiry_date']);

        $data = $request->validated();
        $data['updated_by'] = Auth::id();
        $chemical->fill($data);
        $chemical->updateStatus();
        $chemical->save();

        AuditLogService::logUpdated('Chemical', $chemical->id, $old, $request->validated());

        return redirect()->route('chemicals.show', $chemical)
            ->with('success', "Chemical '{$chemical->chemical_name}' updated successfully.");
    }

    public function destroy(Chemical $chemical)
    {
        $this->authorize('delete', $chemical);

        // Foreign key constraint protection: prevent unhandled QueryException
        $txCount = $chemical->stockTransactions()->count();
        $adjCount = $chemical->stockAdjustments()->count();

        if ($txCount > 0 || $adjCount > 0) {
            $details = [];
            if ($txCount > 0) $details[] = "{$txCount} stock transaction(s)";
            if ($adjCount > 0) $details[] = "{$adjCount} stock adjustment(s)";
            $reason = implode(' and ', $details);

            return back()->with('error', "Cannot delete chemical '{$chemical->chemical_name}' because it has {$reason} recorded. For regulatory audit and traceability, chemicals with transaction history cannot be deleted.");
        }

        $name = $chemical->chemical_name;
        $this->qrCodeService->delete($chemical->chemical_code);
        AuditLogService::logDeleted('Chemical', $chemical->id, ['chemical_name' => $name]);
        $chemical->delete();

        return redirect()->route('chemicals.index')
            ->with('success', "Chemical '{$name}' has been removed from the registry.");
    }
}
