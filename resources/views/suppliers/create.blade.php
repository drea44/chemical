@extends('layouts.app')

@php
    $title      = 'Add Supplier';
    $breadcrumb = [
        ['label' => 'Suppliers', 'url' => route('suppliers.index')],
        ['label' => 'Add Supplier', 'url' => '#'],
    ];
@endphp

@section('title', 'Add Supplier')

@section('content')

<div class="max-w-2xl">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Add Chemical Supplier</h1>
            <p class="text-sm text-gray-500 mt-0.5">Register a chemical vendor or distributor</p>
        </div>
        <x-button href="{{ route('suppliers.index') }}" variant="secondary" icon="arrow-left" size="sm">Back</x-button>
    </div>

    <form method="POST" action="{{ route('suppliers.store') }}" class="space-y-5">
        @csrf

        <div class="bg-white border border-gray-200 rounded-lg p-6 space-y-4">
            <x-form-input label="Supplier / Company Name" name="name" :value="old('name')" required placeholder="e.g. PT Smart-Lab Indonesia" />

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-form-input label="Contact Person" name="contact_person" :value="old('contact_person')" placeholder="e.g. Budi Santoso" />
                <x-form-input label="Email Address" name="email" type="email" :value="old('email')" placeholder="sales@supplier.com" />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-form-input label="Phone Number" name="phone" :value="old('phone')" placeholder="+62 21 555 1234" />
                <x-form-input label="Website" name="website" type="url" :value="old('website')" placeholder="https://www.supplier.com" />
            </div>

            <div class="space-y-1">
                <label for="status" class="block text-sm font-medium text-gray-700">Status <span class="text-red-500">*</span></label>
                <select id="status" name="status" required
                        class="block w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none bg-white">
                    <option value="active" @selected(old('status', 'active') === 'active')>Active</option>
                    <option value="inactive" @selected(old('status') === 'inactive')>Inactive</option>
                </select>
            </div>

            <div class="space-y-1">
                <label for="address" class="block text-sm font-medium text-gray-700">Office / Warehouse Address</label>
                <textarea id="address" name="address" rows="2"
                          placeholder="Street address, city, postal code..."
                          class="block w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">{{ old('address') }}</textarea>
            </div>

            <div class="space-y-1">
                <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                <textarea id="notes" name="notes" rows="2"
                          placeholder="Payment terms, contract numbers, delivery notes..."
                          class="block w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">{{ old('notes') }}</textarea>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('suppliers.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 rounded text-sm hover:bg-gray-50 transition-colors">Cancel</a>
            <button type="submit" class="px-5 py-2 bg-blue-600 text-white rounded text-sm font-medium hover:bg-blue-700 transition-colors">Save Supplier</button>
        </div>
    </form>
</div>

@endsection
