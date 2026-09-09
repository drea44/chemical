@extends('layouts.app')

@php
    $title      = $chemical->chemical_name;
    $breadcrumb = [
        ['label' => 'Chemical Registry', 'url' => route('chemicals.index')],
        ['label' => $chemical->chemical_name, 'url' => '#'],
    ];
@endphp

@section('title', $chemical->chemical_name)

@section('content')

<!-- Header -->
<div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-6">
    <div>
        <div class="flex items-center gap-3 mb-1">
            <h1 class="text-xl font-bold text-gray-900">{{ $chemical->chemical_name }}</h1>
            <x-status-badge :status="$chemical->status" />
        </div>
        <div class="flex items-center gap-3 text-sm text-gray-500">
            <span class="font-mono text-blue-600">{{ $chemical->chemical_code }}</span>
            @if($chemical->cas_number)
            <span>·</span>
            <span>CAS: <span class="font-mono">{{ $chemical->cas_number }}</span></span>
            @endif
        </div>
    </div>
    <div class="flex items-center gap-2 flex-wrap">
        <a href="{{ route('chemicals.label', $chemical) }}" target="_blank"
           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded text-xs font-semibold bg-gray-900 text-white hover:bg-gray-800 transition-colors shadow-sm">
            <i data-lucide="printer" class="w-3.5 h-3.5"></i>Print Label
        </a>
        @can('update', $chemical)
        <x-button href="{{ route('stock.in', ['chemical' => $chemical->id]) }}" variant="success" icon="arrow-down-circle" size="sm">Stock In</x-button>
        <x-button href="{{ route('stock.out', ['chemical' => $chemical->id]) }}" variant="danger" icon="arrow-up-circle" size="sm">Stock Out</x-button>
        <x-button href="{{ route('chemicals.edit', $chemical) }}" variant="secondary" icon="pencil" size="sm">Edit</x-button>
        @endcan
        <x-button href="{{ route('chemicals.index') }}" variant="secondary" icon="arrow-left" size="sm">Back</x-button>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    <!-- Left Column: Details -->
    <div class="lg:col-span-2 space-y-5">

        <!-- Chemical Info -->
        <div class="bg-white border border-gray-200 rounded-lg">
            <div class="px-5 py-4 border-b border-gray-100 bg-gray-50">
                <h2 class="text-sm font-semibold text-gray-700">Chemical Information</h2>
            </div>
            <div class="p-5 grid grid-cols-2 md:grid-cols-3 gap-4">
                @php
                    $fields = [
                        'Category'          => $chemical->category?->name,
                        'Supplier'          => $chemical->supplier,
                        'Manufacturer'      => $chemical->manufacturer,
                        'Catalog Number'    => $chemical->catalog_number,
                        'Batch Number'      => $chemical->batch_number,
                        'Lot Number'        => $chemical->lot_number,
                        'Concentration'     => $chemical->concentration,
                        'Physical State'    => ucfirst($chemical->physical_state ?? '—'),
                        'Hazard Class'      => $chemical->hazard_class,
                        'Storage Condition' => $chemical->storage_condition,
                        'Location'          => $chemical->location?->full_address,
                        'Unit'              => $chemical->unit,
                    ];
                @endphp
                @foreach($fields as $label => $value)
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide font-medium">{{ $label }}</p>
                    <p class="text-sm text-gray-800 mt-0.5 font-medium">{{ $value ?: '—' }}</p>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Stock Visualization -->
        <div class="bg-white border border-gray-200 rounded-lg p-5">
            <h2 class="text-sm font-semibold text-gray-700 mb-4">Stock Level</h2>
            <div class="flex items-end gap-8 mb-4">
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide">Current Stock</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $chemical->formatted_stock }}</p>
                    <p class="text-sm text-gray-400">{{ $chemical->unit }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide">Minimum</p>
                    <p class="text-lg font-semibold text-gray-600">{{ $chemical->formatted_min_stock }} {{ $chemical->unit }}</p>
                </div>
                @if($chemical->maximum_stock)
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide">Maximum</p>
                    <p class="text-lg font-semibold text-gray-600">{{ $chemical->maximum_stock == floor($chemical->maximum_stock) ? number_format($chemical->maximum_stock, 0) : rtrim(rtrim(number_format($chemical->maximum_stock, 4), '0'), '.') }} {{ $chemical->unit }}</p>
                </div>
                @endif
            </div>
            @if($chemical->maximum_stock)
            <div>
                <div class="flex justify-between text-xs text-gray-400 mb-1">
                    <span>0</span>
                    <span>{{ number_format($chemical->maximum_stock, 0) }} {{ $chemical->unit }}</span>
                </div>
                <div class="h-3 bg-gray-100 rounded-full overflow-hidden">
                    @php
                        $pct = min(100, ($chemical->current_stock / $chemical->maximum_stock) * 100);
                        $barColor = $chemical->status === 'SAFE' ? '#22c55e' : ($chemical->status === 'LOW' ? '#eab308' : '#ef4444');
                    @endphp
                    <div class="h-full rounded-full transition-all" style="width: {{ $pct }}%; background: {{ $barColor }};"></div>
                </div>
                <p class="text-xs text-gray-400 mt-1">{{ number_format($pct, 1) }}% of maximum capacity</p>
            </div>
            @endif
        </div>

        <!-- Stock Movement Ledger -->
        <div class="bg-white border border-gray-200 rounded-lg">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="text-sm font-semibold text-gray-700">Stock Movement Ledger</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Date</th>
                            <th class="text-left px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Type</th>
                            <th class="text-right px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Before</th>
                            <th class="text-right px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Change</th>
                            <th class="text-right px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">After</th>
                            <th class="text-left px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">User</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($chemical->stockTransactions as $tx)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-5 py-3 text-xs text-gray-500">{{ $tx->transaction_date?->format('d M Y H:i') }}</td>
                            <td class="px-3 py-3"><x-status-badge :status="$tx->transaction_type" /></td>
                            <td class="px-3 py-3 text-right font-mono text-xs text-gray-600">{{ number_format($tx->stock_before, 2) }}</td>
                            <td class="px-3 py-3 text-right font-mono text-xs font-semibold {{ $tx->transaction_type === 'STOCK_IN' ? 'text-green-600' : 'text-red-500' }}">
                                {{ $tx->transaction_type === 'STOCK_IN' ? '+' : '-' }}{{ number_format($tx->quantity, 2) }}
                            </td>
                            <td class="px-3 py-3 text-right font-mono text-xs font-bold text-gray-800">{{ number_format($tx->stock_after, 2) }}</td>
                            <td class="px-3 py-3 text-xs text-gray-500">{{ $tx->performer?->name ?? '—' }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="px-5 py-8 text-center text-sm text-gray-400">No transactions recorded yet</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Right Column -->
    <div class="space-y-5">

        <!-- QR Code Card -->
        <div class="bg-white border border-gray-200 rounded-lg p-5">
            <h2 class="text-sm font-semibold text-gray-700 mb-4">QR Code</h2>
            <div class="flex justify-center p-4 bg-gray-50 rounded-lg border border-gray-100">
                @if($chemical->qr_code && file_exists(public_path(ltrim($chemical->qr_code, '/'))))
                    <img src="{{ $chemical->qr_code }}" alt="QR Code {{ $chemical->chemical_code }}" class="w-40 h-40">
                @else
                    <div class="w-40 h-40 flex flex-col items-center justify-center text-gray-300">
                        <i data-lucide="qr-code" class="w-16 h-16 mb-2"></i>
                        <p class="text-xs">No QR generated</p>
                    </div>
                @endif
            </div>
            <p class="text-center text-xs font-mono text-gray-500 mt-3">{{ $chemical->chemical_code }}</p>
            <div class="mt-3 flex gap-2">
                <a href="{{ $chemical->qr_code }}" download="qr-{{ $chemical->chemical_code }}.svg"
                   class="flex-1 text-center py-2 border border-gray-300 rounded text-xs font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                    Download QR
                </a>
                <a href="{{ route('chemicals.label', $chemical) }}" target="_blank"
                   class="flex-1 text-center py-2 bg-blue-600 rounded text-xs font-medium text-white hover:bg-blue-700 transition-colors flex items-center justify-center gap-1">
                    <i data-lucide="printer" class="w-3.5 h-3.5"></i>Print Label
                </a>
            </div>
        </div>

        <!-- Dates & Expiry -->
        <div class="bg-white border border-gray-200 rounded-lg p-5">
            <h2 class="text-sm font-semibold text-gray-700 mb-4">Dates & Validity</h2>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-xs text-gray-500">Received</span>
                    <span class="text-sm font-medium text-gray-700">{{ $chemical->received_date?->format('d M Y') ?? '—' }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-xs text-gray-500">Expiry</span>
                    <span class="text-sm font-medium {{ $chemical->isExpired() ? 'text-red-600' : ($chemical->isExpiringSoon() ? 'text-orange-600' : 'text-gray-700') }}">
                        {{ $chemical->expiry_date?->format('d M Y') ?? '—' }}
                    </span>
                </div>
                @if($chemical->expiry_date)
                <div class="pt-2 border-t border-gray-100">
                    <p class="text-xs text-gray-400">
                        @if($chemical->isExpired())
                            <span class="text-red-600 font-medium">⚠ Expired {{ $chemical->expiry_date->diffForHumans() }}</span>
                        @else
                            Expires {{ $chemical->expiry_date->diffForHumans() }}
                        @endif
                    </p>
                </div>
                @endif
            </div>
        </div>

        <!-- Meta -->
        <div class="bg-white border border-gray-200 rounded-lg p-5">
            <h2 class="text-sm font-semibold text-gray-700 mb-3">Record Info</h2>
            <div class="space-y-2 text-xs">
                <div class="flex justify-between">
                    <span class="text-gray-400">Created by</span>
                    <span class="text-gray-700">{{ $chemical->creator?->name ?? '—' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400">Created at</span>
                    <span class="text-gray-700">{{ $chemical->created_at?->format('d M Y') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400">Last updated</span>
                    <span class="text-gray-700">{{ $chemical->updated_at?->format('d M Y') }}</span>
                </div>
            </div>
        </div>

        @if($chemical->notes)
        <div class="bg-white border border-gray-200 rounded-lg p-5">
            <h2 class="text-sm font-semibold text-gray-700 mb-2">Notes</h2>
            <p class="text-sm text-gray-600 leading-relaxed">{{ $chemical->notes }}</p>
        </div>
        @endif
    </div>
</div>

@endsection
