<?php

namespace App\Http\Controllers;

use App\Models\Chemical;
use App\Models\Supplier;
use App\Services\AuditLogService;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Supplier::class);

        $query = Supplier::withCount('chemicals');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('contact_person', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $suppliers = $query->orderBy('name')->paginate(15)->withQueryString();

        return view('suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        $this->authorize('create', Supplier::class);
        return view('suppliers.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Supplier::class);

        $validated = $request->validate([
            'name'           => 'required|string|max:255|unique:chemical_suppliers,name',
            'contact_person' => 'nullable|string|max:255',
            'email'          => 'nullable|email|max:255',
            'phone'          => 'nullable|string|max:50',
            'website'        => 'nullable|url|max:255',
            'address'        => 'nullable|string|max:1000',
            'status'         => 'required|in:active,inactive',
            'notes'          => 'nullable|string|max:1000',
        ]);

        $supplier = Supplier::create($validated);

        AuditLogService::logCreated('Supplier', $supplier->id, ['name' => $supplier->name]);

        return redirect()->route('suppliers.index')
            ->with('success', "Supplier '{$supplier->name}' created successfully.");
    }

    public function show(Supplier $supplier)
    {
        $this->authorize('view', $supplier);
        return redirect()->route('suppliers.edit', $supplier);
    }

    public function edit(Supplier $supplier)
    {
        $this->authorize('update', $supplier);
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $this->authorize('update', $supplier);

        $validated = $request->validate([
            'name'           => 'required|string|max:255|unique:chemical_suppliers,name,' . $supplier->id,
            'contact_person' => 'nullable|string|max:255',
            'email'          => 'nullable|email|max:255',
            'phone'          => 'nullable|string|max:50',
            'website'        => 'nullable|url|max:255',
            'address'        => 'nullable|string|max:1000',
            'status'         => 'required|in:active,inactive',
            'notes'          => 'nullable|string|max:1000',
        ]);

        $old = $supplier->only(['name', 'status', 'email', 'phone']);
        $supplier->update($validated);

        AuditLogService::logUpdated('Supplier', $supplier->id, $old, $validated);

        return redirect()->route('suppliers.index')
            ->with('success', "Supplier '{$supplier->name}' updated successfully.");
    }

    public function destroy(Supplier $supplier)
    {
        $this->authorize('delete', $supplier);

        $count = Chemical::where('supplier', $supplier->name)->count();
        if ($count > 0) {
            return back()->withErrors([
                'error' => "Cannot delete supplier '{$supplier->name}' because it is linked to {$count} chemical(s)."
            ]);
        }

        $name = $supplier->name;
        $id = $supplier->id;
        $supplier->delete();

        AuditLogService::logDeleted('Supplier', $id, ['name' => $name]);

        return redirect()->route('suppliers.index')
            ->with('success', "Supplier '{$name}' deleted successfully.");
    }
}
