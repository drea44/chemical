@extends('layouts.app')

@php
    $title      = 'Stock Overview';
    $breadcrumb = [['label' => 'Stock', 'url' => route('stock.index')]];
@endphp

@section('title', 'Stock Overview')

@section('content')

<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-xl font-bold text-gray-900">Stock Overview</h1>
        <p class="text-sm text-gray-500 mt-0.5">Current inventory levels sorted by priority</p>
    </div>
    <div class="flex gap-2">
        @can('stockIn', \App\Models\Chemical::class)
        <x-button href="{{ route('stock.in') }}" variant="success" icon="arrow-down-circle" size="sm">Stock In</x-button>
        <x-button href="{{ route('stock.out') }}" variant="danger" icon="arrow-up-circle" size="sm">Stock Out</x-button>
        <x-button href="{{ route('stock.adjustment') }}" variant="secondary" icon="sliders-horizontal" size="sm">Adjust</x-button>
        @endcan
    </div>
</div>

<div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Chemical</th>
                    <th class="text-left px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Category</th>
                    <th class="text-left px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Location</th>
                    <th class="text-right px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Current</th>
                    <th class="text-right px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Min</th>
                    <th class="text-left px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide w-32">Level</th>
                    <th class="text-left px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                    <th class="text-left px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($chemicals as $chem)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-5 py-3">
                        <a href="{{ route('chemicals.show', $chem) }}" class="font-semibold text-gray-900 hover:text-blue-600 text-xs">
                            {{ $chem->chemical_name }}
                        </a>
                        <p class="text-xs text-gray-400 font-mono">{{ $chem->chemical_code }}</p>
                    </td>
                    <td class="px-3 py-3 text-xs text-gray-500">{{ $chem->category?->name ?? '—' }}</td>
                    <td class="px-3 py-3 text-xs text-gray-500">{{ $chem->location?->name ?? '—' }}</td>
                    <td class="px-3 py-3 text-right font-mono text-sm font-semibold text-gray-800">
                        {{ number_format($chem->current_stock, 2) }}<span class="text-xs text-gray-400 ml-1">{{ $chem->unit }}</span>
                    </td>
                    <td class="px-3 py-3 text-right font-mono text-xs text-gray-400">
                        {{ number_format($chem->minimum_stock, 2) }}
                    </td>
                    <td class="px-3 py-3">
                        @php
                            $max = $chem->maximum_stock ?: ($chem->minimum_stock * 3);
                            $pct = $max > 0 ? min(100, ($chem->current_stock / $max) * 100) : 0;
                            $barColor = match($chem->status) {
                                'SAFE' => 'bg-green-500', 'LOW' => 'bg-yellow-400',
                                'CRITICAL', 'EXPIRED' => 'bg-red-500', 'EXPIRING_SOON' => 'bg-orange-400',
                                default => 'bg-gray-300'
                            };
                        @endphp
                        <div class="h-2 bg-gray-100 rounded-full w-24 overflow-hidden">
                            <div class="h-full rounded-full {{ $barColor }}" style="width: {{ $pct }}%"></div>
                        </div>
                        <p class="text-xs text-gray-400 mt-0.5">{{ number_format($pct, 0) }}%</p>
                    </td>
                    <td class="px-3 py-3"><x-status-badge :status="$chem->status" /></td>
                    <td class="px-3 py-3">
                        <div class="flex items-center gap-1">
                            @can('stockIn', \App\Models\Chemical::class)
                            <a href="{{ route('stock.in', ['chemical' => $chem->id]) }}"
                               class="p-1.5 text-gray-400 hover:text-green-600 hover:bg-green-50 rounded transition-colors" title="Stock In">
                                <i data-lucide="plus" class="w-4 h-4"></i>
                            </a>
                            <a href="{{ route('stock.out', ['chemical' => $chem->id]) }}"
                               class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded transition-colors" title="Stock Out">
                                <i data-lucide="minus" class="w-4 h-4"></i>
                            </a>
                            <a href="{{ route('stock.adjustment', ['chemical' => $chem->id]) }}"
                               class="p-1.5 text-gray-400 hover:text-yellow-600 hover:bg-yellow-50 rounded transition-colors" title="Adjust">
                                <i data-lucide="sliders-horizontal" class="w-4 h-4"></i>
                            </a>
                            @endcan
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-5 py-16 text-center text-sm text-gray-400">No chemicals in inventory</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($chemicals->hasPages())
    <div class="px-5 py-3 border-t border-gray-100">
        {{ $chemicals->links('pagination::simple-tailwind') }}
    </div>
    @endif
</div>

@endsection
