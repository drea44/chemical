@extends('layouts.app')

@php
    $title      = 'Edit ' . $chemical->chemical_name;
    $breadcrumb = [
        ['label' => 'Chemical Registry', 'url' => route('chemicals.index')],
        ['label' => $chemical->chemical_name, 'url' => route('chemicals.show', $chemical)],
        ['label' => 'Edit', 'url' => '#'],
    ];
@endphp

@section('title', 'Edit Chemical')

@section('content')

<div class="max-w-5xl">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Edit Chemical</h1>
            <p class="text-sm text-gray-500 mt-0.5">{{ $chemical->chemical_name }} · <span class="font-mono text-blue-600">{{ $chemical->chemical_code }}</span></p>
        </div>
        <x-button href="{{ route('chemicals.show', $chemical) }}" variant="secondary" icon="arrow-left" size="sm">Back</x-button>
    </div>

    <form method="POST" action="{{ route('chemicals.update', $chemical) }}" class="space-y-6">
        @csrf @method('PUT')

        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                <h2 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                    <i data-lucide="info" class="w-4 h-4 text-blue-500"></i>Basic Information
                </h2>
            </div>
            <div class="px-6 py-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-form-input label="Chemical Name" name="chemical_name" :value="$chemical->chemical_name" :required="true" />
                <x-form-input label="CAS Number" name="cas_number" :value="$chemical->cas_number" />
                <x-form-input label="Chemical Code" name="chemical_code" :value="$chemical->chemical_code" />
                <x-form-select label="Category" name="category_id" :required="true" :selected="$chemical->category_id"
                    :options="$categories->pluck('name', 'id')->toArray()" />
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Supplier</label>
                    <input type="text" name="supplier" list="supplier-list" value="{{ old('supplier', $chemical->supplier) }}"
                           placeholder="Select or enter supplier..."
                           class="block w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
                    <datalist id="supplier-list">
                        @foreach($suppliers as $sup)
                            <option value="{{ $sup->name }}">{{ $sup->contact_person ? $sup->name . ' (' . $sup->contact_person . ')' : $sup->name }}</option>
                        @endforeach
                    </datalist>
                </div>
                <x-form-input label="Manufacturer" name="manufacturer" :value="$chemical->manufacturer" />
                <x-form-input label="Catalog Number" name="catalog_number" :value="$chemical->catalog_number" />
                <x-form-input label="Batch Number" name="batch_number" :value="$chemical->batch_number" />
                <x-form-input label="Lot Number" name="lot_number" :value="$chemical->lot_number" />
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                <h2 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                    <i data-lucide="package" class="w-4 h-4 text-blue-500"></i>Stock Thresholds
                </h2>
                <p class="text-xs text-gray-400 mt-1">Note: To change stock levels, use Stock In / Stock Out / Adjustment.</p>
            </div>
            <div class="px-6 py-5 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Current Stock</label>
                    <input type="text" value="{{ $chemical->current_stock }} {{ $chemical->unit }}" disabled
                           class="block w-full rounded border border-gray-200 px-3 py-2 text-sm text-gray-400 bg-gray-50 cursor-not-allowed">
                </div>
                <x-form-input label="Minimum Stock" name="minimum_stock" type="number" :value="$chemical->minimum_stock" :required="true" />
                <x-form-input label="Maximum Stock" name="maximum_stock" type="number" :value="$chemical->maximum_stock" />
                <x-form-input label="Unit" name="unit" :value="$chemical->unit" :required="true" />
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                <h2 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                    <i data-lucide="map-pin" class="w-4 h-4 text-blue-500"></i>Storage & Safety
                </h2>
            </div>
            <div class="px-6 py-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-form-select label="Location" name="location_id" :selected="$chemical->location_id" placeholder="Select location"
                    :options="$locations->mapWithKeys(fn($l) => [$l->id => $l->name . ' (' . ($l->building ?? '') . ')'])->toArray()" />
                <x-form-input label="Hazard Class" name="hazard_class" :value="$chemical->hazard_class" />
                <x-form-select label="Physical State" name="physical_state" :selected="$chemical->physical_state" placeholder="Select state"
                    :options="['solid' => 'Solid', 'liquid' => 'Liquid', 'gas' => 'Gas', 'powder' => 'Powder', 'solution' => 'Solution']" />
                <x-form-input label="Concentration" name="concentration" :value="$chemical->concentration" />
                <div class="space-y-1 md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Storage Condition</label>
                    <textarea name="storage_condition" rows="2" class="block w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">{{ old('storage_condition', $chemical->storage_condition) }}</textarea>
                </div>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                <h2 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                    <i data-lucide="calendar" class="w-4 h-4 text-blue-500"></i>Dates
                </h2>
            </div>
            <div class="px-6 py-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-form-input label="Received Date" name="received_date" type="date" :value="$chemical->received_date?->format('Y-m-d')" />
                <x-form-input label="Expiry Date" name="expiry_date" type="date" :value="$chemical->expiry_date?->format('Y-m-d')" />
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Additional Notes</label>
            <textarea name="notes" rows="3" class="block w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">{{ old('notes', $chemical->notes) }}</textarea>
        </div>

        <div class="flex items-center justify-end gap-3 pb-4">
            <x-button href="{{ route('chemicals.show', $chemical) }}" variant="secondary">Cancel</x-button>
            <x-button type="submit" icon="save">Save Changes</x-button>
        </div>
    </form>
</div>

@endsection
