@extends('layouts.app')

@php
    $title      = 'Add Chemical';
    $breadcrumb = [
        ['label' => 'Chemical Registry', 'url' => route('chemicals.index')],
        ['label' => 'Add Chemical', 'url' => '#'],
    ];
@endphp

@section('title', 'Add Chemical')

@section('content')

<div class="max-w-5xl">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Register New Chemical</h1>
            <p class="text-sm text-gray-500 mt-0.5">Auto-generated code: <span class="font-mono text-blue-600">{{ $nextCode }}</span></p>
        </div>
        <x-button href="{{ route('chemicals.index') }}" variant="secondary" icon="arrow-left" size="sm">Back</x-button>
    </div>

    <form method="POST" action="{{ route('chemicals.store') }}" class="space-y-6">
        @csrf

        <!-- Section 1: Basic Information -->
        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                <h2 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                    <i data-lucide="info" class="w-4 h-4 text-blue-500"></i>Basic Information
                </h2>
            </div>
            <div class="px-6 py-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-form-input label="Chemical Name" name="chemical_name" placeholder="e.g. Acetone" :required="true" />
                <x-form-input label="CAS Number" name="cas_number" placeholder="e.g. 67-64-1" hint="Chemical Abstracts Service registry number" />
                <x-form-input label="Chemical Code" name="chemical_code" :value="$nextCode" placeholder="{{ $nextCode }}" hint="Leave blank to auto-generate" />
                <x-form-select label="Category" name="category_id" :required="true" placeholder="Select category"
                    :options="$categories->pluck('name', 'id')->toArray()" />
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Supplier</label>
                    <input type="text" name="supplier" list="supplier-list" value="{{ old('supplier') }}"
                           placeholder="Select or enter supplier..."
                           class="block w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
                    <datalist id="supplier-list">
                        @foreach($suppliers as $sup)
                            <option value="{{ $sup->name }}">{{ $sup->contact_person ? $sup->name . ' (' . $sup->contact_person . ')' : $sup->name }}</option>
                        @endforeach
                    </datalist>
                </div>
                <x-form-input label="Manufacturer" name="manufacturer" placeholder="e.g. Merck KGaA" />
                <x-form-input label="Catalog Number" name="catalog_number" placeholder="e.g. ACS-32201" />
            </div>
        </div>

        <!-- Section 2: Inventory -->
        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                <h2 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                    <i data-lucide="package" class="w-4 h-4 text-blue-500"></i>Inventory
                </h2>
            </div>
            <div class="px-6 py-5 grid grid-cols-1 md:grid-cols-3 gap-4">
                <x-form-input label="Initial Quantity" name="current_stock" type="number" placeholder="0" :required="true" hint="Current stock level" />
                <x-form-input label="Minimum Stock" name="minimum_stock" type="number" placeholder="0" :required="true" hint="Alert threshold" />
                <x-form-input label="Maximum Stock" name="maximum_stock" type="number" placeholder="0" hint="Upper capacity limit" />
                <x-form-input label="Unit" name="unit" placeholder="e.g. L, kg, g, mL" :required="true" />
                <x-form-input label="Batch Number" name="batch_number" placeholder="e.g. BT2024-001" />
                <x-form-input label="Lot Number" name="lot_number" placeholder="e.g. LT-20240115" />
            </div>
        </div>

        <!-- Section 3: Storage -->
        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                <h2 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                    <i data-lucide="map-pin" class="w-4 h-4 text-blue-500"></i>Storage & Location
                </h2>
            </div>
            <div class="px-6 py-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-form-select label="Storage Location" name="location_id" placeholder="Select location"
                    :options="$locations->mapWithKeys(fn($l) => [$l->id => $l->name . ' (' . ($l->building ?? '') . ')'])->toArray()" />
                <div class="space-y-1">
                    <label class="block text-sm font-medium text-gray-700">Storage Condition</label>
                    <textarea name="storage_condition" rows="2" placeholder="e.g. Store below 25°C, away from ignition sources"
                              class="block w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">{{ old('storage_condition') }}</textarea>
                </div>
            </div>
        </div>

        <!-- Section 4: Safety -->
        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                <h2 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                    <i data-lucide="shield-alert" class="w-4 h-4 text-blue-500"></i>Safety Information
                </h2>
            </div>
            <div class="px-6 py-5 grid grid-cols-1 md:grid-cols-3 gap-4">
                <x-form-input label="Hazard Class" name="hazard_class" placeholder="e.g. Flammable Liquid" />
                <x-form-select label="Physical State" name="physical_state" placeholder="Select state"
                    :options="['solid' => 'Solid', 'liquid' => 'Liquid', 'gas' => 'Gas', 'powder' => 'Powder', 'solution' => 'Solution']" />
                <x-form-input label="Concentration" name="concentration" placeholder="e.g. 99.8%, 37%" />
            </div>
        </div>

        <!-- Section 5: Dates -->
        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                <h2 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                    <i data-lucide="calendar" class="w-4 h-4 text-blue-500"></i>Dates
                </h2>
            </div>
            <div class="px-6 py-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-form-input label="Received Date" name="received_date" type="date" :value="date('Y-m-d')" />
                <x-form-input label="Expiry Date" name="expiry_date" type="date" />
            </div>
        </div>

        <!-- Section 6: Notes -->
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Additional Notes</label>
            <textarea name="notes" rows="3" placeholder="Any additional information about this chemical..."
                      class="block w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">{{ old('notes') }}</textarea>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-end gap-3 pb-4">
            <x-button href="{{ route('chemicals.index') }}" variant="secondary">Cancel</x-button>
            <x-button type="submit" icon="check">Register Chemical & Generate QR</x-button>
        </div>
    </form>
</div>

@endsection
