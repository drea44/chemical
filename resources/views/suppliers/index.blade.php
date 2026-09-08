@extends('layouts.app')

@php
    $title      = 'Suppliers';
    $breadcrumb = [['label' => 'Suppliers', 'url' => route('suppliers.index')]];
@endphp

@section('title', 'Suppliers')

@section('content')

<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-xl font-bold text-gray-900">Chemical Suppliers</h1>
        <p class="text-sm text-gray-500 mt-0.5">{{ $suppliers->total() }} suppliers registered</p>
    </div>
    @can('create', \App\Models\Chemical::class)
    <x-button href="{{ route('suppliers.create') }}" icon="plus">Add Supplier</x-button>
    @endcan
</div>

<!-- Filters -->
<div class="bg-white border border-gray-200 rounded-lg p-4 mb-5">
    <form method="GET" class="flex flex-wrap gap-3">
        <div class="relative flex-1 min-w-48">
            <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search supplier name, contact, email, phone..."
                   class="pl-9 w-full rounded border border-gray-300 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
        </div>
        <select name="status" class="rounded border border-gray-300 px-3 py-2 text-sm text-gray-700 focus:border-blue-500 focus:outline-none bg-white">
            <option value="">All Statuses</option>
            <option value="active" @selected(request('status') === 'active')>Active</option>
            <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
        </select>
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded text-sm font-medium hover:bg-blue-700 transition-colors">Filter</button>
        @if(request()->hasAny(['search', 'status']))
        <a href="{{ route('suppliers.index') }}" class="px-4 py-2 border border-gray-300 text-gray-600 rounded text-sm hover:bg-gray-50">Clear</a>
        @endif
    </form>
</div>

<!-- Table -->
<div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Supplier Name</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Contact Person</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Email / Phone</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Chemicals</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($suppliers as $supplier)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-5 py-3.5">
                        <p class="font-semibold text-gray-900">{{ $supplier->name }}</p>
                        @if($supplier->website)
                        <a href="{{ $supplier->website }}" target="_blank" class="text-xs text-blue-600 hover:underline flex items-center gap-1 mt-0.5">
                            {{ parse_url($supplier->website, PHP_URL_HOST) ?? $supplier->website }}
                            <i data-lucide="external-link" class="w-3 h-3"></i>
                        </a>
                        @endif
                    </td>
                    <td class="px-4 py-3.5 text-xs text-gray-700 font-medium">{{ $supplier->contact_person ?: '—' }}</td>
                    <td class="px-4 py-3.5 text-xs text-gray-500">
                        @if($supplier->email)
                        <p class="flex items-center gap-1"><i data-lucide="mail" class="w-3 h-3 text-gray-400"></i>{{ $supplier->email }}</p>
                        @endif
                        @if($supplier->phone)
                        <p class="flex items-center gap-1 mt-0.5"><i data-lucide="phone" class="w-3 h-3 text-gray-400"></i>{{ $supplier->phone }}</p>
                        @endif
                        @if(!$supplier->email && !$supplier->phone)
                        <span>—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3.5 text-center">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700">
                            {{ $supplier->chemicals_count }}
                        </span>
                    </td>
                    <td class="px-4 py-3.5">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $supplier->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ ucfirst($supplier->status) }}
                        </span>
                    </td>
                    <td class="px-4 py-3.5">
                        <div class="flex items-center gap-2">
                            @can('create', \App\Models\Chemical::class)
                            <a href="{{ route('suppliers.edit', $supplier) }}" class="p-1 text-gray-400 hover:text-blue-600 rounded transition-colors" title="Edit">
                                <i data-lucide="pencil" class="w-4 h-4"></i>
                            </a>
                            @if($supplier->chemicals_count === 0)
                            <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST" onsubmit="return confirm('Delete supplier {{ $supplier->name }}?')">
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
                    <td colspan="6" class="px-5 py-12 text-center text-sm text-gray-400">No suppliers found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($suppliers->hasPages())
    <div class="p-4 border-t border-gray-100">
        {{ $suppliers->links() }}
    </div>
    @endif
</div>

@endsection
