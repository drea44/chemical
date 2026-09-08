<?php

namespace App\Http\Controllers;

use App\Models\ChemicalLocation;
use App\Services\AuditLogService;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function index(Request $request)
    {
        $query = ChemicalLocation::withCount('chemicals');
        if ($search = $request->get('search')) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('building', 'like', "%{$search}%")
                  ->orWhere('room', 'like', "%{$search}%");
        }
        $locations = $query->orderBy('name')->paginate(20)->withQueryString();
        return view('locations.index', compact('locations'));
    }

    public function create()
    {
        $this->authorize('create', \App\Models\Chemical::class);
        return view('locations.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', \App\Models\Chemical::class);
        $validated = $request->validate([
            'name'              => 'required|string|max:100|unique:chemical_locations,name',
            'building'          => 'nullable|string|max:100',
            'room'              => 'nullable|string|max:100',
            'storage_type'      => 'nullable|string|max:100',
            'temperature_range' => 'nullable|string|max:50',
            'description'       => 'nullable|string|max:500',
            'status'            => 'required|in:active,inactive',
        ]);
        $loc = ChemicalLocation::create($validated);
        AuditLogService::logCreated('Location', $loc->id, ['name' => $loc->name]);
        return redirect()->route('locations.index')
            ->with('success', "Location '{$loc->name}' created successfully.");
    }

    public function show(ChemicalLocation $location)
    {
        return redirect()->route('locations.edit', $location);
    }

    public function edit(ChemicalLocation $location)
    {
        $this->authorize('create', \App\Models\Chemical::class);
        $location->loadCount('chemicals');
        return view('locations.edit', compact('location'));
    }

    public function update(Request $request, ChemicalLocation $location)
    {
        $this->authorize('create', \App\Models\Chemical::class);
        $validated = $request->validate([
            'name'              => 'required|string|max:100|unique:chemical_locations,name,' . $location->id,
            'building'          => 'nullable|string|max:100',
            'room'              => 'nullable|string|max:100',
            'storage_type'      => 'nullable|string|max:100',
            'temperature_range' => 'nullable|string|max:50',
            'description'       => 'nullable|string|max:500',
            'status'            => 'required|in:active,inactive',
        ]);
        $old = $location->only(['name', 'status', 'building', 'room']);
        $location->update($validated);
        AuditLogService::logUpdated('Location', $location->id, $old, $validated);
        return redirect()->route('locations.index')
            ->with('success', "Location '{$location->name}' updated.");
    }

    public function destroy(ChemicalLocation $location)
    {
        $this->authorize('delete', \App\Models\Chemical::class);
        $count = $location->chemicals()->count();
        if ($count > 0) {
            return back()->with('error', "Cannot delete location '{$location->name}': {$count} chemical(s) are stored in this location.");
        }
        AuditLogService::logDeleted('Location', $location->id, ['name' => $location->name]);
        $location->delete();
        return redirect()->route('locations.index')->with('success', "Location deleted.");
    }
}
