@extends('layouts.app')

@php
    $title      = 'Edit Supplier';
    $breadcrumb = [
        ['label' => 'Suppliers', 'url' => route('suppliers.index')],
        ['label' => $supplier->name, 'url' => '#'],
    ];
@endphp

@section('title', 'Edit Supplier')

@section('content')

<div class="max-w-2xl">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Edit Supplier: {{ $supplier->name }}</h1>
            <p class="text-sm text-gray-500 mt-0.5">{{ $supplier->chemicals_count ?? 0 }} chemicals linked to this supplier</p>
        </div>
        <x-button href="{{ route('suppliers.index') }}" variant="secondary" icon="arrow-left" size="sm">Back</x-button>
    </div>

    <form method="POST" action="{{ route('suppliers.update', $supplier) }}" class="space-y-5">
        @csrf
        @method('PUT')

        <div class="bg-white border border-gray-200 rounded-lg p-6 space-y-4">
            <x-form-input label="Supplier / Company Name" name="name" :value="old('name', $supplier->name)" required />

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-form-input label="Contact Person" name="contact_person" :value="old('contact_person', $supplier->contact_person)" />
                <x-form-input label="Email Address" name="email" type="email" :value="old('email', $supplier->email)" />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-form-input label="Phone Number" name="phone" :value="old('phone', $supplier->phone)" />
                <x-form-input label="Website" name="website" type="url" :value="old('website', $supplier->website)" />
            </div>

            <div class="space-y-1">
                <label for="status" class="block text-sm font-medium text-gray-700">Status <span class="text-red-500">*</span></label>
                <select id="status" name="status" required
                        class="block w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none bg-white">
                    <option value="active" @selected(old('status', $supplier->status) === 'active')>Active</option>
                    <option value="inactive" @selected(old('status', $supplier->status) === 'inactive')>Inactive</option>
                </select>
            </div>

            <div class="space-y-1">
                <label for="address" class="block text-sm font-medium text-gray-700">Office / Warehouse Address</label>
                <textarea id="address" name="address" rows="2"
                          class="block w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">{{ old('address', $supplier->address) }}</textarea>
            </div>

            <div class="space-y-1">
                <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                <textarea id="notes" name="notes" rows="2"
                          class="block w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">{{ old('notes', $supplier->notes) }}</textarea>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('suppliers.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 rounded text-sm hover:bg-gray-50 transition-colors">Cancel</a>
            <button type="submit" class="px-5 py-2 bg-blue-600 text-white rounded text-sm font-medium hover:bg-blue-700 transition-colors">Update Supplier</button>
        </div>
    </form>
</div>

@endsection
