@extends('layouts.app')

@php
    $title      = 'Reports Console';
    $breadcrumb = [['label' => 'Reports Console', 'url' => route('reports.index')]];
    $tab        = $tab ?? request('tab', 'inventory');
@endphp

@section('title', 'Reports Console')

@section('content')

{{-- Header --}}
<div class="mb-5">
    <h1 class="text-xl font-bold text-gray-900">Reports Console</h1>
    <p class="text-sm text-gray-500 mt-0.5">Aggregate inventory audits, material turnover metrics, safety logs, and stock statistics.</p>
</div>

{{-- Tab Navigation --}}
<div class="flex flex-wrap gap-2 mb-4">
    @foreach([
        ['tab' => 'inventory',   'label' => 'Inventory Report'],
        ['tab' => 'movement',    'label' => 'Stock Movement'],
        ['tab' => 'usage',       'label' => 'Chemical Usage'],
        ['tab' => 'expiry',      'label' => 'Expiry Report'],
        ['tab' => 'low_stock',   'label' => 'Low Stock'],
        ['tab' => 'adjustments', 'label' => 'Transaction Report'],
    ] as $t)
    <a href="{{ route('reports.index', ['tab' => $t['tab']]) }}"
       class="px-4 py-2 rounded text-sm font-medium transition-colors
              {{ $tab === $t['tab']
                  ? 'bg-blue-600 text-white'
                  : 'bg-white border border-gray-300 text-gray-600 hover:bg-gray-50' }}">
        {{ $t['label'] }}
    </a>
    @endforeach
</div>

{{-- Filter Row + Export Buttons --}}
<div class="flex flex-wrap items-center justify-between gap-3 mb-5">
    <div class="flex items-center gap-2">
        <div class="relative">
            <select name="date_filter" onchange="window.location.href='?tab={{ $tab }}&date_filter='+this.value"
                    class="appearance-none rounded border border-gray-300 pl-3 pr-7 py-1.5 text-sm text-gray-700 bg-white cursor-pointer font-medium focus:outline-none">
                <option>Date Range: Today (Live)</option>
                <option>Last 7 Days</option>
                <option>Last 30 Days</option>
                <option>All Time</option>
            </select>
            <i data-lucide="chevron-down" class="absolute right-2 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-400 pointer-events-none"></i>
        </div>
        <div class="relative">
            <select name="category_filter" onchange="this.form && this.form.submit()"
                    class="appearance-none rounded border border-gray-300 pl-3 pr-7 py-1.5 text-sm text-gray-700 bg-white cursor-pointer font-medium focus:outline-none">
                <option>Category Filter: All Categories</option>
                @foreach($categories ?? [] as $cat)
                <option>{{ $cat->name }}</option>
                @endforeach
            </select>
            <i data-lucide="chevron-down" class="absolute right-2 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-400 pointer-events-none"></i>
        </div>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('reports.export-csv', ['type' => $tab]) }}"
           class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 bg-white text-gray-700 text-sm font-medium rounded hover:bg-gray-50 transition-colors">
            Export CSV
        </a>
        <button onclick="window.print()"
                class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded transition-colors">
            Download PDF
        </button>
    </div>
</div>

{{-- Stats Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-5">
    <div class="bg-white border border-gray-200 rounded-lg p-5">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-2">Total Audited Items</p>
        <p class="text-3xl font-bold text-gray-900">{{ $stats['total_chemicals'] }}</p>
    </div>
    <div class="bg-white border border-gray-200 rounded-lg p-5">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-2">Active Safe Stock</p>
        <p class="text-3xl font-bold text-green-500">{{ $stats['active'] }}</p>
    </div>
    <div class="bg-white border border-gray-200 rounded-lg p-5">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-2">Low Level Warnings</p>
        <p class="text-3xl font-bold text-orange-500">{{ $stats['low_stock'] }}</p>
    </div>
    <div class="bg-white border border-gray-200 rounded-lg p-5">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-2">Critical Risk Items</p>
        <p class="text-3xl font-bold text-red-500">{{ $stats['critical'] }}</p>
    </div>
</div>

{{-- Main Table --}}
<div class="bg-white border border-gray-200 rounded-lg overflow-hidden mb-5">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-200">
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Chemical & CAS</th>
                    <th class="text-left px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Class</th>
                    <th class="text-left px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Storage Site</th>
                    <th class="text-right px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Current Vol</th>
                    <th class="text-right px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Min Threshold</th>
                    <th class="text-left px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Safety Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($inventorySummary as $c)
                @php
                    $statusColor = match($c->status) {
                        'SAFE'         => 'text-green-600 bg-green-50',
                        'LOW'          => 'text-orange-600 bg-orange-50',
                        'CRITICAL'     => 'text-red-600 bg-red-50',
                        'EXPIRED'      => 'text-red-600 bg-red-50',
                        'EXPIRING_SOON'=> 'text-yellow-700 bg-yellow-50',
                        default        => 'text-gray-600 bg-gray-100',
                    };
                    $statusLabel = match($c->status) {
                        'SAFE'         => 'SAFE',
                        'LOW'          => 'ALERT',
                        'CRITICAL'     => 'ALERT',
                        'EXPIRED'      => 'EXPIRED',
                        'EXPIRING_SOON'=> 'NEAR EXPIRY',
                        default        => $c->status,
                    };
                @endphp
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-5 py-3">
                        <a href="{{ route('chemicals.show', $c) }}" class="text-sm font-semibold text-gray-900 hover:text-blue-600">
                            {{ $c->chemical_name }}
                        </a>
                        <p class="text-xs text-gray-400 font-mono">CAS: {{ $c->cas_number ?? '—' }}</p>
                    </td>
                    <td class="px-3 py-3 text-sm text-gray-600">{{ $c->category?->name ?? '—' }}</td>
                    <td class="px-3 py-3 text-sm text-gray-600">{{ $c->location?->name ?? '—' }}</td>
                    <td class="px-3 py-3 text-right">
                        <span class="font-mono text-sm font-bold text-gray-900">{{ number_format($c->current_stock, 2) }} {{ $c->unit }}</span>
                    </td>
                    <td class="px-3 py-3 text-right">
                        <span class="font-mono text-sm text-gray-400">{{ number_format($c->minimum_stock, 0) }} {{ $c->unit }}</span>
                    </td>
                    <td class="px-3 py-3">
                        <span class="inline-block px-2.5 py-0.5 rounded text-xs font-bold {{ $statusColor }}">
                            {{ $statusLabel }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-5 py-12 text-center text-sm text-gray-400">No inventory data available</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Stock Allocation Chart --}}
<div class="bg-white border border-gray-200 rounded-lg p-6">
    <h3 class="text-sm font-semibold text-gray-800 mb-6">Stock Allocation by Chemical Category</h3>
    <div class="flex items-end justify-around gap-4">
        @php
            $chartData = [
                ['label' => 'Bases',     'value' => 80,  'color' => '#3b82f6'],
                ['label' => 'Acids',     'value' => 140, 'color' => '#f59e0b'],
                ['label' => 'Solvents',  'value' => 210, 'color' => '#10b981'],
                ['label' => 'Aldehydes', 'value' => 45,  'color' => '#ef4444'],
                ['label' => 'Reagents',  'value' => 120, 'color' => '#6b7280'],
                ['label' => 'Other',     'value' => 65,  'color' => '#9ca3af'],
            ];
            $maxVal = max(array_column($chartData, 'value'));
        @endphp
        @foreach($chartData as $bar)
        <div class="flex flex-col items-center gap-2">
            <span class="text-xs font-semibold text-gray-700">{{ $bar['value'] }} units</span>
            <div class="w-12 rounded-t" style="height: {{ max(20, ($bar['value'] / $maxVal) * 140) }}px; background: {{ $bar['color'] }};"></div>
            <span class="text-xs text-gray-500">{{ $bar['label'] }}</span>
        </div>
        @endforeach
    </div>
</div>

@endsection
