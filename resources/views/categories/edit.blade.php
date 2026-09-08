@extends('layouts.app')

@php
    $title      = 'Edit Category';
    $breadcrumb = [
        ['label' => 'Categories', 'url' => route('categories.index')],
        ['label' => $category->name, 'url' => '#'],
    ];
@endphp

@section('title', 'Edit Category')

@section('content')

<div class="max-w-2xl">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Edit Category: {{ $category->name }}</h1>
            <p class="text-sm text-gray-500 mt-0.5">{{ $category->chemicals_count ?? 0 }} chemicals currently in this category</p>
        </div>
        <x-button href="{{ route('categories.index') }}" variant="secondary" icon="arrow-left" size="sm">Back</x-button>
    </div>

    <form method="POST" action="{{ route('categories.update', $category) }}" class="space-y-5">
        @csrf
        @method('PUT')

        <div class="bg-white border border-gray-200 rounded-lg p-6 space-y-4">
            <x-form-input label="Category Name" name="name" :value="old('name', $category->name)" required />

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label for="color" class="block text-sm font-medium text-gray-700">Badge Color</label>
                    <div class="flex items-center gap-3">
                        <input type="color" id="colorPicker" value="{{ old('color', $category->color ?: '#3b82f6') }}"
                               oninput="document.getElementById('color').value = this.value"
                               class="w-10 h-10 rounded border border-gray-300 cursor-pointer p-0.5 bg-white">
                        <input type="text" id="color" name="color" value="{{ old('color', $category->color ?: '#3b82f6') }}"
                               oninput="document.getElementById('colorPicker').value = this.value"
                               class="flex-1 rounded border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
                    </div>
                </div>

                <div class="space-y-1">
                    <label for="status" class="block text-sm font-medium text-gray-700">Status <span class="text-red-500">*</span></label>
                    <select id="status" name="status" required
                            class="block w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none bg-white">
                        <option value="active" @selected(old('status', $category->status) === 'active')>Active</option>
                        <option value="inactive" @selected(old('status', $category->status) === 'inactive')>Inactive</option>
                    </select>
                </div>
            </div>

            <div class="space-y-1">
                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                <textarea id="description" name="description" rows="3"
                          class="block w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">{{ old('description', $category->description) }}</textarea>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('categories.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 rounded text-sm hover:bg-gray-50 transition-colors">Cancel</a>
            <button type="submit" class="px-5 py-2 bg-blue-600 text-white rounded text-sm font-medium hover:bg-blue-700 transition-colors">Update Category</button>
        </div>
    </form>
</div>

@endsection
