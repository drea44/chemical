@extends('layouts.app')

@php
    $title      = 'Chemical Registry';
    $breadcrumb = [['label' => 'Chemical Registry', 'url' => route('chemicals.index')]];
@endphp

@section('title', 'Chemical Registry')

@section('content')

<!-- Header -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-xl font-bold text-gray-900">Chemical Registry</h1>
        <p class="text-sm text-gray-500 mt-0.5">{{ $chemicals->total() }} chemicals registered</p>
    </div>
    <div class="flex items-center gap-2 flex-wrap">
        <x-button href="{{ route('reports.export-csv', ['type' => 'inventory']) }}" variant="secondary" icon="download" size="sm">Export CSV</x-button>
        @can('create', \App\Models\Chemical::class)
        <x-button href="{{ route('chemicals.create') }}" icon="plus">Add Chemical</x-button>
        @endcan
    </div>
</div>

<!-- Filters -->
<div class="bg-white border border-gray-200 rounded-lg p-4 mb-5">
    <form method="GET" action="{{ route('chemicals.index') }}" class="flex flex-wrap gap-3">
        <!-- Search -->
        <div class="relative flex-1 min-w-48">
            <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, code, CAS, supplier..."
                   class="pl-9 w-full rounded border border-gray-300 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
        </div>
        <!-- Category -->
        <select name="category" class="rounded border border-gray-300 px-3 py-2 text-sm text-gray-700 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 bg-white">
            <option value="">All Categories</option>
            @foreach($categories as $cat)
            <option value="{{ $cat->id }}" @selected(request('category') == $cat->id)>{{ $cat->name }}</option>
            @endforeach
        </select>
        <!-- Location -->
        <select name="location" class="rounded border border-gray-300 px-3 py-2 text-sm text-gray-700 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 bg-white">
            <option value="">All Locations</option>
            @foreach($locations as $loc)
            <option value="{{ $loc->id }}" @selected(request('location') == $loc->id)>{{ $loc->name }}</option>
            @endforeach
        </select>
        <!-- Status -->
        <select name="status" class="rounded border border-gray-300 px-3 py-2 text-sm text-gray-700 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 bg-white">
            <option value="">All Statuses</option>
            @foreach(['SAFE','LOW','CRITICAL','EXPIRED','EXPIRING_SOON'] as $s)
            <option value="{{ $s }}" @selected(request('status') == $s)>{{ ucfirst(strtolower(str_replace('_', ' ', $s))) }}</option>
            @endforeach
        </select>
        <!-- Per Page -->
        <select name="per_page" class="rounded border border-gray-300 px-3 py-2 text-sm text-gray-700 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 bg-white">
            <option value="20" @selected(request('per_page') == 20)>20 per page</option>
            <option value="50" @selected(request('per_page') == 50)>50 per page</option>
            <option value="100" @selected(request('per_page') == 100)>100 per page</option>
            <option value="200" @selected(request('per_page') == 200)>All (200) per page</option>
        </select>
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded text-sm font-medium hover:bg-blue-700 transition-colors">Filter</button>
        @if(request()->hasAny(['search','category','location','status','per_page']))
        <a href="{{ route('chemicals.index') }}" class="px-4 py-2 border border-gray-300 text-gray-600 rounded text-sm hover:bg-gray-50 transition-colors">Clear</a>
        @endif
    </form>
</div>

<!-- Table -->
<div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    @php
                        $sort    = request('sort', 'chemical_name');
                        $dir     = request('dir', 'asc');
                        $nextDir = $dir === 'asc' ? 'desc' : 'asc';
                    @endphp
                    @foreach([
                        ['field' => 'chemical_name', 'label' => 'Chemical'],
                        ['field' => 'cas_number',    'label' => 'CAS Number'],
                        ['field' => null,            'label' => 'Category'],
                        ['field' => null,            'label' => 'Location'],
                        ['field' => 'current_stock', 'label' => 'Stock'],
                        ['field' => 'expiry_date',   'label' => 'Expiry'],
                        ['field' => 'status',        'label' => 'Status'],
                        ['field' => null,            'label' => 'Actions'],
                    ] as $col)
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide whitespace-nowrap">
                        @if($col['field'])
                        <a href="{{ route('chemicals.index', array_merge(request()->query(), ['sort' => $col['field'], 'dir' => $sort === $col['field'] ? $nextDir : 'asc'])) }}"
                           class="flex items-center gap-1 hover:text-gray-700 transition-colors">
                            {{ $col['label'] }}
                            @if($sort === $col['field'])
                            <i data-lucide="{{ $dir === 'asc' ? 'chevron-up' : 'chevron-down' }}" class="w-3 h-3 text-blue-500"></i>
                            @endif
                        </a>
                        @else
                            {{ $col['label'] }}
                        @endif
                    </th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($chemicals as $chem)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-3">
                        <div>
                            <a href="{{ route('chemicals.show', $chem) }}" class="font-semibold text-gray-900 hover:text-blue-600 transition-colors">
                                {{ $chem->chemical_name }}
                            </a>
                            <p class="text-xs text-gray-400 font-mono">{{ $chem->chemical_code }}</p>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-xs font-mono text-gray-600">{{ $chem->cas_number ?? '—' }}</td>
                    <td class="px-4 py-3 text-xs text-gray-600">{{ $chem->category?->name ?? '—' }}</td>
                    <td class="px-4 py-3 text-xs text-gray-600">
                        {{ $chem->location?->name ?? '—' }}
                        @if($chem->location?->room)
                        <span class="text-gray-400">/ {{ $chem->location->room }}</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <div class="text-sm font-semibold text-gray-900">{{ $chem->formatted_stock }}</div>
                        <div class="text-xs text-gray-400">{{ $chem->unit }} · Min {{ $chem->formatted_min_stock }}</div>
                        @if($chem->maximum_stock)
                        <div class="mt-1 h-1 bg-gray-100 rounded-full w-20 overflow-hidden">
                            <div class="h-full rounded-full {{ $chem->status === 'SAFE' ? 'bg-green-500' : ($chem->status === 'LOW' ? 'bg-yellow-400' : 'bg-red-500') }}"
                                 style="width: {{ min(100, ($chem->current_stock / $chem->maximum_stock) * 100) }}%"></div>
                        </div>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-600">
                        @if($chem->expiry_date)
                        <span class="{{ $chem->isExpired() ? 'text-red-600 font-medium' : ($chem->isExpiringSoon() ? 'text-orange-600 font-medium' : '') }}">
                            {{ $chem->expiry_date->format('d M Y') }}
                        </span>
                        @else
                        <span class="text-gray-400">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3"><x-status-badge :status="$chem->status" /></td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-1">
                            <a href="{{ route('chemicals.show', $chem) }}" title="View"
                               class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded transition-colors">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                            </a>
                            @can('update', $chem)
                            <a href="{{ route('chemicals.edit', $chem) }}" title="Edit"
                               class="p-1.5 text-gray-400 hover:text-green-600 hover:bg-green-50 rounded transition-colors">
                                <i data-lucide="pencil" class="w-4 h-4"></i>
                            </a>
                            @endcan
                            @can('delete', $chem)
                            <form method="POST" action="{{ route('chemicals.destroy', $chem) }}"
                                  onsubmit="return confirm('Are you sure you want to delete {{ addslashes($chem->chemical_name) }}? This action cannot be undone.')">
                                @csrf @method('DELETE')
                                <button type="submit" title="Delete"
                                        class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded transition-colors">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </form>
                            @endcan
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-4 py-16 text-center">
                        <i data-lucide="flask-conical" class="w-12 h-12 text-gray-200 mx-auto mb-3"></i>
                        <p class="text-sm font-medium text-gray-400">No chemicals found</p>
                        <p class="text-xs text-gray-300 mt-1">Try adjusting your search or filters</p>
                        @can('create', \App\Models\Chemical::class)
                        <a href="{{ route('chemicals.create') }}" class="mt-4 inline-flex items-center gap-2 text-sm text-blue-600 hover:text-blue-800 font-medium">
                            <i data-lucide="plus" class="w-4 h-4"></i>Add first chemical
                        </a>
                        @endcan
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($chemicals->hasPages())
    <div class="px-4 py-3 border-t border-gray-100 flex items-center justify-between">
        <p class="text-xs text-gray-500">
            Showing {{ $chemicals->firstItem() }}–{{ $chemicals->lastItem() }} of {{ $chemicals->total() }} results
        </p>
        <div class="flex gap-1">
            {{ $chemicals->links('pagination::simple-tailwind') }}
        </div>
    </div>
    @endif
</div>

@endsection
