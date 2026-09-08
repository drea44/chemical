@extends('layouts.app')

@php
    $title      = 'Reports';
    $breadcrumb = [['label' => 'Reports', 'url' => route('reports.index')]];
    $tab        = request('tab', 'inventory');
@endphp

@section('title', 'Reports')

@section('content')

<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-xl font-bold text-gray-900">Reports</h1>
        <p class="text-sm text-gray-500 mt-0.5">Inventory analysis and compliance reporting</p>
    </div>
    <div class="flex gap-2">
        <x-button href="{{ route('reports.export-csv', ['type' => $tab]) }}" variant="secondary" icon="download" size="sm">Export CSV</x-button>
    </div>
</div>

<!-- Summary Stats -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <x-stat-card label="Total Chemicals"  value="{{ $stats['total_chemicals'] }}" icon="flask-conical" color="blue" />
    <x-stat-card label="Safe"             value="{{ $stats['active'] }}"          icon="check-circle"  color="green" />
    <x-stat-card label="Low Stock"        value="{{ $stats['low_stock'] }}"        icon="trending-down" color="yellow" />
    <x-stat-card label="Needs Attention"  value="{{ $stats['critical'] }}"         icon="alert-triangle" color="red" />
</div>

<!-- Tab Nav -->
<div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
    <div class="border-b border-gray-200 bg-gray-50">
        <nav class="flex gap-0 px-5 pt-3">
            @foreach([
                ['tab' => 'inventory',    'label' => 'Inventory Summary',   'icon' => 'package'],
                ['tab' => 'movement',     'label' => 'Stock Movement',       'icon' => 'arrow-left-right'],
                ['tab' => 'expiry',       'label' => 'Expiry Report',        'icon' => 'calendar-x'],
                ['tab' => 'adjustments',  'label' => 'Adjustments',          'icon' => 'sliders-horizontal'],
            ] as $t)
            <a href="{{ route('reports.index', ['tab' => $t['tab']]) }}"
               class="flex items-center gap-2 px-4 py-2.5 text-sm font-medium rounded-t border-b-2 transition-colors mr-1
                      {{ $tab === $t['tab'] ? 'border-blue-600 text-blue-600 bg-white' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-white' }}">
                <i data-lucide="{{ $t['icon'] }}" class="w-4 h-4"></i>
                {{ $t['label'] }}
            </a>
            @endforeach
        </nav>
    </div>

    <div class="p-0">

        {{-- TAB: Inventory Summary --}}
        @if($tab === 'inventory')
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Chemical</th>
                        <th class="text-left px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Category</th>
                        <th class="text-left px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Location</th>
                        <th class="text-right px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Stock</th>
                        <th class="text-right px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Min</th>
                        <th class="text-left px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Expiry</th>
                        <th class="text-left px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($inventorySummary as $c)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-5 py-3">
                            <p class="font-medium text-gray-800 text-xs">{{ $c->chemical_name }}</p>
                            <p class="text-xs text-gray-400 font-mono">{{ $c->chemical_code }}</p>
                        </td>
                        <td class="px-3 py-3 text-xs text-gray-500">{{ $c->category?->name ?? '—' }}</td>
                        <td class="px-3 py-3 text-xs text-gray-500">{{ $c->location?->name ?? '—' }}</td>
                        <td class="px-3 py-3 text-right font-mono text-sm font-semibold text-gray-800">
                            {{ number_format($c->current_stock, 2) }} {{ $c->unit }}
                        </td>
                        <td class="px-3 py-3 text-right font-mono text-xs text-gray-400">{{ number_format($c->minimum_stock, 2) }}</td>
                        <td class="px-3 py-3 text-xs {{ $c->isExpired() ? 'text-red-600 font-medium' : 'text-gray-500' }}">
                            {{ $c->expiry_date?->format('d M Y') ?? '—' }}
                        </td>
                        <td class="px-3 py-3"><x-status-badge :status="$c->status" /></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- TAB: Stock Movement --}}
        @elseif($tab === 'movement')
        <div class="p-4 border-b border-gray-100 bg-gray-50">
            <form method="GET" class="flex gap-3">
                <input type="hidden" name="tab" value="movement">
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="rounded border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="rounded border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded text-sm font-medium hover:bg-blue-700">Filter</button>
            </form>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Code</th>
                        <th class="text-left px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Chemical</th>
                        <th class="text-left px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Type</th>
                        <th class="text-right px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Qty</th>
                        <th class="text-right px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Before</th>
                        <th class="text-right px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">After</th>
                        <th class="text-left px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">By</th>
                        <th class="text-left px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($stockMovement as $tx)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3 font-mono text-xs text-gray-600">{{ $tx->transaction_code }}</td>
                        <td class="px-3 py-3 text-xs font-medium text-gray-800">{{ $tx->chemical?->chemical_name ?? '—' }}</td>
                        <td class="px-3 py-3"><x-status-badge :status="$tx->transaction_type" /></td>
                        <td class="px-3 py-3 text-right font-mono text-xs font-bold {{ $tx->transaction_type === 'STOCK_IN' ? 'text-green-600' : 'text-red-500' }}">
                            {{ $tx->transaction_type === 'STOCK_IN' ? '+' : '-' }}{{ number_format($tx->quantity, 2) }}
                        </td>
                        <td class="px-3 py-3 text-right font-mono text-xs text-gray-400">{{ number_format($tx->stock_before, 2) }}</td>
                        <td class="px-3 py-3 text-right font-mono text-xs font-semibold text-gray-800">{{ number_format($tx->stock_after, 2) }}</td>
                        <td class="px-3 py-3 text-xs text-gray-500">{{ $tx->performer?->name ?? '—' }}</td>
                        <td class="px-3 py-3 text-xs text-gray-400 whitespace-nowrap">{{ $tx->transaction_date?->format('d M Y H:i') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="px-5 py-12 text-center text-sm text-gray-400">No movement data for selected period</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- TAB: Expiry Report --}}
        @elseif($tab === 'expiry')
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Chemical</th>
                        <th class="text-left px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Location</th>
                        <th class="text-right px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Stock</th>
                        <th class="text-left px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Expiry Date</th>
                        <th class="text-left px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Days Left</th>
                        <th class="text-left px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($expiryReport as $c)
                    <tr class="hover:bg-gray-50 {{ $c->isExpired() ? 'bg-red-50' : '' }}">
                        <td class="px-5 py-3">
                            <a href="{{ route('chemicals.show', $c) }}" class="font-medium text-gray-800 hover:text-blue-600 text-xs">{{ $c->chemical_name }}</a>
                            <p class="text-xs text-gray-400 font-mono">{{ $c->chemical_code }}</p>
                        </td>
                        <td class="px-3 py-3 text-xs text-gray-500">{{ $c->location?->name ?? '—' }}</td>
                        <td class="px-3 py-3 text-right font-mono text-xs text-gray-700">{{ number_format($c->current_stock, 2) }} {{ $c->unit }}</td>
                        <td class="px-3 py-3 text-xs {{ $c->isExpired() ? 'text-red-600 font-semibold' : 'text-gray-700' }}">
                            {{ $c->expiry_date->format('d M Y') }}
                        </td>
                        <td class="px-3 py-3 text-xs">
                            @php $days = now()->diffInDays($c->expiry_date, false); @endphp
                            <span class="{{ $days < 0 ? 'text-red-600 font-semibold' : ($days <= 30 ? 'text-orange-600 font-medium' : 'text-gray-500') }}">
                                {{ $days < 0 ? abs($days) . ' days ago' : $days . ' days' }}
                            </span>
                        </td>
                        <td class="px-3 py-3"><x-status-badge :status="$c->status" /></td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-5 py-12 text-center text-sm text-gray-400">No chemicals with expiry dates</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- TAB: Adjustments --}}
        @elseif($tab === 'adjustments')
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Code</th>
                        <th class="text-left px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Chemical</th>
                        <th class="text-right px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Previous</th>
                        <th class="text-right px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Adjusted</th>
                        <th class="text-right px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Difference</th>
                        <th class="text-left px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Reason</th>
                        <th class="text-left px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">By</th>
                        <th class="text-left px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($adjustmentReport as $adj)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3 font-mono text-xs text-gray-600">{{ $adj->adjustment_code }}</td>
                        <td class="px-3 py-3 text-xs font-medium text-gray-800">{{ $adj->chemical?->chemical_name ?? '—' }}</td>
                        <td class="px-3 py-3 text-right font-mono text-xs text-gray-500">{{ number_format($adj->previous_stock, 2) }}</td>
                        <td class="px-3 py-3 text-right font-mono text-xs font-semibold text-gray-800">{{ number_format($adj->adjusted_stock, 2) }}</td>
                        <td class="px-3 py-3 text-right font-mono text-xs font-bold {{ $adj->difference > 0 ? 'text-green-600' : 'text-red-500' }}">
                            {{ $adj->difference > 0 ? '+' : '' }}{{ number_format($adj->difference, 2) }}
                        </td>
                        <td class="px-3 py-3 text-xs text-gray-500 max-w-xs truncate">{{ $adj->reason }}</td>
                        <td class="px-3 py-3 text-xs text-gray-500">{{ $adj->adjuster?->name ?? '—' }}</td>
                        <td class="px-3 py-3 text-xs text-gray-400 whitespace-nowrap">{{ $adj->created_at?->format('d M Y H:i') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="px-5 py-12 text-center text-sm text-gray-400">No adjustments recorded</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @endif

    </div>
</div>

@endsection
