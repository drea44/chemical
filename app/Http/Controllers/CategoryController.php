<?php

namespace App\Http\Controllers;

use App\Models\ChemicalCategory;
use App\Services\AuditLogService;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = ChemicalCategory::withCount('chemicals');
        if ($search = $request->get('search')) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
        }
        $categories = $query->orderBy('name')->paginate(20)->withQueryString();
        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        $this->authorize('create', \App\Models\Chemical::class); // ADMIN/STOCK_MANAGER
        return view('categories.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', \App\Models\Chemical::class);
        $validated = $request->validate([
            'name'        => 'required|string|max:100|unique:chemical_categories,name',
            'description' => 'nullable|string|max:500',
            'color'       => 'nullable|string|max:7',
            'status'      => 'required|in:active,inactive',
        ]);
        $cat = ChemicalCategory::create($validated);
        AuditLogService::logCreated('Category', $cat->id, ['name' => $cat->name]);
        return redirect()->route('categories.index')
            ->with('success', "Category '{$cat->name}' created successfully.");
    }

    public function show(ChemicalCategory $category)
    {
        return redirect()->route('categories.edit', $category);
    }

    public function edit(ChemicalCategory $category)
    {
        $this->authorize('create', \App\Models\Chemical::class);
        $category->loadCount('chemicals');
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, ChemicalCategory $category)
    {
        $this->authorize('create', \App\Models\Chemical::class);
        $validated = $request->validate([
            'name'        => 'required|string|max:100|unique:chemical_categories,name,' . $category->id,
            'description' => 'nullable|string|max:500',
            'color'       => 'nullable|string|max:7',
            'status'      => 'required|in:active,inactive',
        ]);
        $old = $category->only(['name', 'status']);
        $category->update($validated);
        AuditLogService::logUpdated('Category', $category->id, $old, $validated);
        return redirect()->route('categories.index')
            ->with('success', "Category '{$category->name}' updated.");
    }

    public function destroy(ChemicalCategory $category)
    {
        $this->authorize('delete', \App\Models\Chemical::class);
        $count = $category->chemicals()->count();
        if ($count > 0) {
            return back()->with('error', "Cannot delete category '{$category->name}': {$count} chemical(s) are assigned to this category.");
        }
        AuditLogService::logDeleted('Category', $category->id, ['name' => $category->name]);
        $category->delete();
        return redirect()->route('categories.index')
            ->with('success', "Category deleted.");
    }
}
