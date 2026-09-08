@extends('layouts.app')

@php
    $title      = 'Chemical Categories';
    $breadcrumb = [['label' => 'Categories', 'url' => route('categories.index')]];
@endphp

@section('title', 'Chemical Categories')

@section('content')

<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-xl font-bold text-gray-900">Chemical Categories</h1>
        <p class="text-sm text-gray-500 mt-0.5">{{ $categories->total() }} categories defined</p>
    </div>
    @can('create', \App\Models\Chemical::class)
    <x-button href="{{ route('categories.create') }}" icon="plus">Add Category</x-button>
    @endcan
</div>

<!-- Filters -->
<div class="bg-white border border-gray-200 rounded-lg p-4 mb-5">
    <form method="GET" class="flex flex-wrap gap-3">
        <div class="relative flex-1 min-w-48">
            <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search category name or description..."
                   class="pl-9 w-full rounded border border-gray-300 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
        </div>
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded text-sm font-medium hover:bg-blue-700 transition-colors">Filter</button>
        @if(request()->has('search'))
        <a href="{{ route('categories.index') }}" class="px-4 py-2 border border-gray-300 text-gray-600 rounded text-sm hover:bg-gray-50">Clear</a>
        @endif
    </form>
</div>

<!-- Table -->
<div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Category Name</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Color Tag</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Description</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Chemicals</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($categories as $cat)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-5 py-3.5 font-semibold text-gray-900">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full flex-shrink-0" style="background-color: {{ $cat->color ?: '#3b82f6' }}"></span>
                            <span>{{ $cat->name }}</span>
                        </div>
                    </td>
                    <td class="px-4 py-3.5">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-mono font-medium border"
                              style="background-color: {{ $cat->color ? $cat->color . '15' : '#eff6ff' }}; color: {{ $cat->color ?: '#1d4ed8' }}; border-color: {{ $cat->color ? $cat->color . '40' : '#bfdbfe' }}">
                            {{ $cat->color ?: '#3b82f6' }}
                        </span>
                    </td>
                    <td class="px-4 py-3.5 text-xs text-gray-500 max-w-xs truncate">{{ $cat->description ?: '—' }}</td>
                    <td class="px-4 py-3.5 text-center">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                            {{ $cat->chemicals_count }}
                        </span>
                    </td>
                    <td class="px-4 py-3.5">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $cat->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ ucfirst($cat->status) }}
                        </span>
                    </td>
                    <td class="px-4 py-3.5">
                        <div class="flex items-center gap-2">
                            @can('create', \App\Models\Chemical::class)
                            <a href="{{ route('categories.edit', $cat) }}" class="p-1 text-gray-400 hover:text-blue-600 rounded transition-colors" title="Edit">
                                <i data-lucide="pencil" class="w-4 h-4"></i>
                            </a>
                            @if($cat->chemicals_count === 0)
                            <form action="{{ route('categories.destroy', $cat) }}" method="POST" onsubmit="return confirm('Delete category {{ $cat->name }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1 text-gray-400 hover:text-red-600 rounded transition-colors" title="Delete">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </form>
                            @endif
                            @endcan
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-5 py-12 text-center text-sm text-gray-400">No categories found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($categories->hasPages())
    <div class="p-4 border-t border-gray-100">
        {{ $categories->links() }}
    </div>
    @endif
</div>

@endsection
