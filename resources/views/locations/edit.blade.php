@extends('layouts.app')

@php
    $title      = 'Edit Location';
    $breadcrumb = [
        ['label' => 'Locations', 'url' => route('locations.index')],
        ['label' => $location->name, 'url' => '#'],
    ];
@endphp

@section('title', 'Edit Location')

@section('content')

<div class="max-w-2xl">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Edit Location: {{ $location->name }}</h1>
            <p class="text-sm text-gray-500 mt-0.5">{{ $location->chemicals_count ?? 0 }} chemicals stored here</p>
        </div>
        <x-button href="{{ route('locations.index') }}" variant="secondary" icon="arrow-left" size="sm">Back</x-button>
    </div>

    <form method="POST" action="{{ route('locations.update', $location) }}" class="space-y-5">
        @csrf
        @method('PUT')

        <div class="bg-white border border-gray-200 rounded-lg p-6 space-y-4">
            <x-form-input label="Location Name" name="name" :value="old('name', $location->name)" required />

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <x-form-input label="Building" name="building" :value="old('building', $location->building)" />
                <x-form-input label="Room" name="room" :value="old('room', $location->room)" />
                <x-form-input label="Shelf / Rack" name="shelf" :value="old('shelf', $location->shelf)" />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label for="storage_type" class="block text-sm font-medium text-gray-700">Storage Type</label>
                    <select id="storage_type" name="storage_type"
                            class="block w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none bg-white">
                        <option value="Room Temperature" @selected(old('storage_type', $location->storage_type) === 'Room Temperature')>Room Temperature Cabinet</option>
                        <option value="Cold Room (2-8°C)" @selected(old('storage_type', $location->storage_type) === 'Cold Room (2-8°C)')>Cold Room / Refrigerator (2-8°C)</option>
                        <option value="Freezer (-20°C)" @selected(old('storage_type', $location->storage_type) === 'Freezer (-20°C)')>Freezer (-20°C)</option>
                        <option value="Ultra-Low Freezer (-80°C)" @selected(old('storage_type', $location->storage_type) === 'Ultra-Low Freezer (-80°C)')>Ultra-Low Freezer (-80°C)</option>
                        <option value="Flammables Safety Cabinet" @selected(old('storage_type', $location->storage_type) === 'Flammables Safety Cabinet')>Flammables Safety Cabinet</option>
                        <option value="Corrosives/Acid Cabinet" @selected(old('storage_type', $location->storage_type) === 'Corrosives/Acid Cabinet')>Corrosives / Acid Cabinet</option>
                        <option value="Desiccator" @selected(old('storage_type', $location->storage_type) === 'Desiccator')>Desiccator</option>
                    </select>
                </div>

                <x-form-input label="Temperature Range" name="temperature_range" :value="old('temperature_range', $location->temperature_range)" placeholder="e.g. 15°C - 25°C" />
            </div>

            <div class="space-y-1">
                <label for="status" class="block text-sm font-medium text-gray-700">Status <span class="text-red-500">*</span></label>
                <select id="status" name="status" required
                        class="block w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none bg-white">
                    <option value="active" @selected(old('status', $location->status) === 'active')>Active</option>
                    <option value="inactive" @selected(old('status', $location->status) === 'inactive')>Inactive</option>
                </select>
            </div>

            <div class="space-y-1">
                <label for="description" class="block text-sm font-medium text-gray-700">Description / Access Protocols</label>
                <textarea id="description" name="description" rows="3"
                          class="block w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">{{ old('description', $location->description) }}</textarea>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('locations.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 rounded text-sm hover:bg-gray-50 transition-colors">Cancel</a>
            <button type="submit" class="px-5 py-2 bg-blue-600 text-white rounded text-sm font-medium hover:bg-blue-700 transition-colors">Update Location</button>
        </div>
    </form>
</div>

@endsection
