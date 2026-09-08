@extends('layouts.app')

@php
    $title      = 'Dashboard';
    $breadcrumb = [];
@endphp

@section('title', 'Dashboard')

@section('content')

<!-- Page Header -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="text-xl font-bold text-gray-900">Overview</h1>
        <p class="text-sm text-gray-500 mt-0.5">Chemical inventory status — {{ now()->format('d M Y, H:i') }}</p>
    </div>
    <div class="flex gap-2">
        @can('create', \App\Models\Chemical::class)
        <x-button href="{{ route('chemicals.create') }}" icon="plus" size="sm">Add Chemical</x-button>
        @endcan
    </div>
</div>

<!-- Stat Cards -->
<div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-6 gap-3 sm:gap-4 mb-6">
    <x-stat-card label="Total Chemicals"  value="{{ $stats['total_chemicals'] }}"  icon="flask-conical"   color="blue"   href="{{ route('chemicals.index') }}" />
    <x-stat-card label="Total Stock"      value="{{ number_format($stats['total_stock'], 0) }}" icon="package" color="blue" />
    <x-stat-card label="Low Stock"        value="{{ $stats['low_stock'] }}"         icon="trending-down"  color="yellow" href="{{ route('chemicals.index', ['status' => 'LOW']) }}" />
    <x-stat-card label="Expiring Soon"    value="{{ $stats['expiring_soon'] }}"     icon="calendar-x"    color="orange" href="{{ route('chemicals.index', ['status' => 'EXPIRING_SOON']) }}" />
    <x-stat-card label="Critical"         value="{{ $stats['critical_stock'] }}"    icon="alert-triangle" color="red"   href="{{ route('chemicals.index', ['status' => 'CRITICAL']) }}" />
    <x-stat-card label="Expired"          value="{{ $stats['expired'] }}"           icon="x-circle"      color="red"   href="{{ route('chemicals.index', ['status' => 'EXPIRED']) }}" />
</div>

<!-- Charts Row -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-6">

    <!-- Stock Movement Bar Chart -->
    <div class="lg:col-span-2 bg-white border border-gray-200 rounded-lg p-5">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-sm font-semibold text-gray-900">Stock Movement</h2>
                <p class="text-xs text-gray-400">Last 7 days · Inbound vs Outbound</p>
            </div>
            <div class="flex gap-4 text-xs">
                <span class="flex items-center gap-1.5"><span class="w-3 h-2 rounded-sm bg-blue-500 inline-block"></span>Inbound</span>
                <span class="flex items-center gap-1.5"><span class="w-3 h-2 rounded-sm bg-red-400 inline-block"></span>Outbound</span>
            </div>
        </div>
        <canvas id="stockMovementChart" height="200"></canvas>
    </div>

    <!-- Status Doughnut Chart -->
    <div class="bg-white border border-gray-200 rounded-lg p-5">
        <div class="mb-4">
            <h2 class="text-sm font-semibold text-gray-900">Stock Status</h2>
            <p class="text-xs text-gray-400">Current distribution</p>
        </div>
        <canvas id="stockStatusChart" height="180"></canvas>
        <div class="mt-4 space-y-2">
            <div class="flex justify-between text-xs">
                <span class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-green-500 inline-block"></span>Safe</span>
                <span class="font-semibold text-gray-700">{{ $stockStatus['safe'] }}</span>
            </div>
            <div class="flex justify-between text-xs">
                <span class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-yellow-400 inline-block"></span>Low</span>
                <span class="font-semibold text-gray-700">{{ $stockStatus['low'] }}</span>
            </div>
            <div class="flex justify-between text-xs">
                <span class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-red-500 inline-block"></span>Critical</span>
                <span class="font-semibold text-gray-700">{{ $stockStatus['critical'] }}</span>
            </div>
            <div class="flex justify-between text-xs">
                <span class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-orange-400 inline-block"></span>Expiring Soon</span>
                <span class="font-semibold text-gray-700">{{ $stockStatus['expiring_soon'] }}</span>
            </div>
            <div class="flex justify-between text-xs">
                <span class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-red-800 inline-block"></span>Expired</span>
                <span class="font-semibold text-gray-700">{{ $stockStatus['expired'] }}</span>
            </div>
        </div>
    </div>
</div>

<!-- Bottom Row: Recent Activity + Critical Alerts -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    <!-- Recent Activity -->
    <div class="lg:col-span-2 bg-white border border-gray-200 rounded-lg">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
            <h2 class="text-sm font-semibold text-gray-900">Recent Inventory Activity</h2>
            <a href="{{ route('transactions.index') }}" class="text-xs text-blue-600 hover:text-blue-800 font-medium">View all →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Chemical</th>
                        <th class="text-left px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Type</th>
                        <th class="text-right px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Qty</th>
                        <th class="text-left px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">User</th>
                        <th class="text-left px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($recentActivity as $tx)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-5 py-3">
                            <a href="{{ route('chemicals.show', $tx->chemical_id) }}" class="font-medium text-gray-800 hover:text-blue-600 text-xs">
                                {{ $tx->chemical?->chemical_name ?? 'N/A' }}
                            </a>
                        </td>
                        <td class="px-3 py-3"><x-status-badge :status="$tx->transaction_type" /></td>
                        <td class="px-3 py-3 text-right font-mono text-xs text-gray-600">
                            {{ $tx->transaction_type === 'STOCK_IN' ? '+' : '-' }}{{ number_format($tx->quantity, 1) }}
                        </td>
                        <td class="px-3 py-3 text-xs text-gray-500">{{ $tx->performer?->name ?? '—' }}</td>
                        <td class="px-3 py-3 text-xs text-gray-400">{{ $tx->transaction_date?->format('d M H:i') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-5 py-8 text-center text-sm text-gray-400">No recent activity</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Critical Alerts -->
    <div class="bg-white border border-gray-200 rounded-lg">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
            <h2 class="text-sm font-semibold text-gray-900">Critical Alerts</h2>
            @if($criticalAlerts->count() > 0)
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-50 text-red-600">
                {{ $criticalAlerts->count() }}
            </span>
            @endif
        </div>
        <div class="divide-y divide-gray-50">
            @forelse($criticalAlerts as $chem)
            <div class="px-5 py-3 hover:bg-gray-50 transition-colors">
                <div class="flex items-start justify-between gap-2">
                    <div class="min-w-0">
                        <a href="{{ route('chemicals.show', $chem) }}" class="text-xs font-semibold text-gray-800 hover:text-blue-600 truncate block">
                            {{ $chem->chemical_name }}
                        </a>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $chem->location?->name ?? 'No location' }}</p>
                    </div>
                    <x-status-badge :status="$chem->status" size="sm" />
                </div>
                <div class="mt-1.5 flex items-center gap-2 text-xs text-gray-500">
                    @if($chem->status === 'EXPIRED')
                        <i data-lucide="calendar-x" class="w-3 h-3 text-red-500"></i>
                        Expired {{ $chem->expiry_date?->format('d M Y') }}
                    @elseif($chem->status === 'EXPIRING_SOON')
                        <i data-lucide="clock" class="w-3 h-3 text-orange-500"></i>
                        Expires {{ $chem->expiry_date?->diffForHumans() }}
                    @else
                        <i data-lucide="trending-down" class="w-3 h-3 text-red-500"></i>
                        Stock: {{ number_format($chem->current_stock, 1) }} {{ $chem->unit }}
                    @endif
                </div>
            </div>
            @empty
            <div class="px-5 py-8 text-center">
                <i data-lucide="check-circle" class="w-8 h-8 text-green-400 mx-auto mb-2"></i>
                <p class="text-sm text-gray-400">All chemicals are within safe limits</p>
            </div>
            @endforelse
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
const movementData = @json($stockMovement);
const statusData   = @json($stockStatus);

// Bar Chart — Stock Movement
new Chart(document.getElementById('stockMovementChart'), {
    type: 'bar',
    data: {
        labels: movementData.days,
        datasets: [
            {
                label: 'Inbound',
                data: movementData.inbound,
                backgroundColor: 'rgba(37,99,235,0.75)',
                borderRadius: 4,
                borderSkipped: false,
            },
            {
                label: 'Outbound',
                data: movementData.outbound,
                backgroundColor: 'rgba(239,68,68,0.65)',
                borderRadius: 4,
                borderSkipped: false,
            }
        ]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            x: { grid: { display: false }, ticks: { font: { size: 11 } } },
            y: { grid: { color: '#f3f4f6' }, ticks: { font: { size: 11 }, precision: 0 }, beginAtZero: true }
        }
    }
});

// Doughnut Chart — Stock Status
new Chart(document.getElementById('stockStatusChart'), {
    type: 'doughnut',
    data: {
        labels: ['Safe', 'Low', 'Critical', 'Expiring Soon', 'Expired'],
        datasets: [{
            data: [
                statusData.safe,
                statusData.low,
                statusData.critical,
                statusData.expiring_soon,
                statusData.expired,
            ],
            backgroundColor: ['#22c55e','#eab308','#ef4444','#f97316','#991b1b'],
            borderWidth: 2,
            borderColor: '#ffffff',
        }]
    },
    options: {
        responsive: true,
        cutout: '65%',
        plugins: { legend: { display: false } },
    }
});
</script>
@endpush
