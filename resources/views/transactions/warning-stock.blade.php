@extends('layouts.app')

@php
    $title      = 'Warning Stock - Chemical Stock Warning';
    $breadcrumb = [
        ['label' => 'Log Chemical', 'url' => route('transactions.master-report')],
        ['label' => 'Warning Stock', 'url' => '#'],
    ];
@endphp

@section('title', 'CHEMICAL STOCK WARNING')

@push('styles')
<style>
    .warning-banner {
        background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
        color: #ffffff;
        font-weight: 800;
        font-size: 1.25rem;
        letter-spacing: 0.05em;
        text-align: center;
        padding: 0.875rem 1.5rem;
        border-radius: 0.5rem 0.5rem 0 0;
        text-transform: uppercase;
        border: 1px solid #b91c1c;
        border-bottom: none;
    }

    .warning-table {
        border-collapse: collapse;
        width: 100%;
        font-size: 0.875rem;
    }

    .warning-table th {
        background-color: #fde8d7;
        color: #7c2d12;
        font-weight: 700;
        border: 1px solid #d1d5db;
        padding: 0.6rem 0.75rem;
        text-align: center;
    }

    .warning-table td {
        border: 1px solid #e5e7eb;
        padding: 0.45rem 0.75rem;
        color: #1f2937;
    }

    .warning-table tbody tr:nth-child(even) {
        background-color: #fafafa;
    }

    .warning-table tbody tr:hover {
        background-color: #fff1f2;
    }

    /* Status Badges strictly matching Reference Image 2 */
    .status-cell-ok {
        background-color: #00c853 !important;
        color: #000000 !important;
        font-weight: 700 !important;
        text-align: center !important;
        font-size: 0.8125rem;
        letter-spacing: 0.025em;
    }

    .status-cell-refill {
        background-color: #dc2626 !important;
        color: #ffffff !important;
        font-weight: 700 !important;
        text-align: center !important;
        font-size: 0.8125rem;
        letter-spacing: 0.025em;
        animation: pulse-subtle 2s infinite ease-in-out;
    }

    @keyframes pulse-subtle {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.88; }
    }

    .tab-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.15s ease;
    }
    .tab-pill.active {
        background-color: #dc2626;
        color: #ffffff;
        box-shadow: 0 1px 3px rgba(220,38,38,0.3);
    }
    .tab-pill.inactive {
        background-color: #ffffff;
        color: #4b5563;
        border: 1px solid #e5e7eb;
    }
    .tab-pill.inactive:hover {
        background-color: #f3f4f6;
        color: #111827;
    }
</style>
@endpush

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-5">

    {{-- Top Tab Navigation --}}
    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-200 pb-4">
        <div class="flex items-center gap-2">
            <a href="{{ route('transactions.master-report') }}" class="tab-pill inactive">
                <i data-lucide="file-spreadsheet" class="w-4 h-4 text-amber-500"></i>
                Master Report
            </a>
            <a href="{{ route('transactions.warning-stock') }}" class="tab-pill active">
                <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                Warning Stock
            </a>
            <a href="{{ route('transactions.matrix') }}" class="tab-pill inactive" title="Daily Usage Matrix">
                <i data-lucide="calendar-days" class="w-4 h-4 text-gray-400"></i>
                Daily Usage Sheet
            </a>
        </div>

        {{-- Search & Filter Controls --}}
        <form action="{{ route('transactions.warning-stock') }}" method="GET" class="flex flex-wrap items-center gap-2">
            {{-- Status Filter Dropdown --}}
            <select name="status"
                    onchange="this.form.submit()"
                    class="py-1.5 px-3 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 bg-white">
                <option value="all" @selected(request('status') === 'all' || !request('status'))>
                    All Status ({{ $totalCount }})
                </option>
                <option value="ok" @selected(request('status') === 'ok')>
                    Status: OK ({{ $okCount }})
                </option>
                <option value="refill" @selected(request('status') === 'refill')>
                    Status: Time to Refill ({{ $refillCount }})
                </option>
            </select>

            {{-- Search input --}}
            <div class="relative">
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Search chemical name..."
                       class="w-56 sm:w-64 pl-9 pr-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 bg-white">
                <i data-lucide="search" class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none"></i>
            </div>

            @if(request('search') || (request('status') && request('status') !== 'all'))
                <a href="{{ route('transactions.warning-stock') }}" class="px-2.5 py-1.5 text-xs text-gray-600 hover:text-gray-900 border border-gray-300 rounded-lg bg-white">
                    Reset
                </a>
            @endif

            <button type="submit" class="px-3.5 py-1.5 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors">
                Filter
            </button>
        </form>
    </div>

    {{-- Alert summary banner if refill needed --}}
    @if($refillCount > 0)
    <div class="flex items-center justify-between bg-red-50 border border-red-200 rounded-lg p-3 text-sm text-red-700">
        <div class="flex items-center gap-2">
            <i data-lucide="alert-circle" class="w-5 h-5 text-red-600 flex-shrink-0"></i>
            <span>
                Terdapat <strong class="font-bold text-red-800">{{ $refillCount }}</strong> chemical dengan status <span class="px-1.5 py-0.5 rounded text-xs font-bold bg-red-600 text-white">Time to Refill</span> yang membutuhkan pengisian stok.
            </span>
        </div>
        <a href="{{ route('transactions.warning-stock', ['status' => 'refill']) }}" class="text-xs font-semibold text-red-700 hover:text-red-900 underline whitespace-nowrap">
            Lihat Yang Perlu Refill &rarr;
        </a>
    </div>
    @endif

    {{-- Main Card --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">

        {{-- Header Banner matching Reference Image 2 --}}
        <div class="warning-banner">
            CHEMICAL STOCK WARNING
        </div>

        {{-- Responsive Table Container --}}
        <div class="overflow-x-auto">
            <table class="warning-table">
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th class="text-left" style="min-width: 280px;">Chemical Name</th>
                        <th style="width: 110px;">Stok Awal</th>
                        <th style="width: 70px;">Unit</th>
                        <th style="width: 120px;">Minimal Stok</th>
                        <th style="width: 130px;">Remaining Stock</th>
                        <th style="width: 130px;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($chemicals as $idx => $chem)
                        @php
                            $rowNum    = $chemicals->firstItem() + $idx;
                            $rData     = $rows[$chem->id] ?? [];
                            $stokAwal  = $rData['stok_awal'] ?? (float)$chem->current_stock;
                            $minStock  = $rData['min_stock'] ?? (float)$chem->minimum_stock;
                            $remaining = $rData['remaining_stock'] ?? (float)$chem->current_stock;
                            $status    = $rData['status'] ?? 'OK';

                            $fmtStokAwal = ($stokAwal !== null && $stokAwal !== '')
                                ? (floor($stokAwal) == $stokAwal ? number_format($stokAwal, 0, ',', '.') : rtrim(rtrim(number_format($stokAwal, 2, ',', '.'), '0'), ','))
                                : '';

                            $fmtMinStock = ($minStock > 0)
                                ? (floor($minStock) == $minStock ? number_format($minStock, 0, ',', '.') : rtrim(rtrim(number_format($minStock, 2, ',', '.'), '0'), ','))
                                : '';

                            $fmtRemaining = ($remaining !== null && $remaining !== '')
                                ? (floor($remaining) == $remaining ? number_format($remaining, 0, ',', '.') : rtrim(rtrim(number_format($remaining, 2, ',', '.'), '0'), ','))
                                : '';
                        @endphp
                        <tr>
                            <td class="text-center font-medium text-gray-500">{{ $rowNum }}</td>
                            <td class="font-medium text-gray-900">
                                {{ $chem->chemical_name }}
                            </td>
                            <td class="text-center font-mono text-sm">
                                {{ $fmtStokAwal }}
                            </td>
                            <td class="text-center text-xs text-gray-600">
                                {{ $chem->unit ?? '-' }}
                            </td>
                            <td class="text-center font-mono text-sm">
                                {{ $fmtMinStock }}
                            </td>
                            <td class="text-center font-mono text-sm font-semibold {{ $remaining < 0 ? 'text-red-600' : 'text-gray-900' }}">
                                {{ $fmtRemaining }}
                            </td>
                            <td class="{{ $status === 'Time to Refill' ? 'status-cell-refill' : 'status-cell-ok' }}">
                                {{ $status }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-8 text-gray-500 text-sm">
                                Tidak ada data chemical yang sesuai filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Footer & Pagination --}}
        <div class="p-4 border-t border-gray-200 bg-gray-50 flex flex-col sm:flex-row items-center justify-between gap-3">
            <p class="text-xs text-gray-500">
                Menampilkan <span class="font-medium text-gray-700">{{ $chemicals->firstItem() ?? 0 }}</span> - <span class="font-medium text-gray-700">{{ $chemicals->lastItem() ?? 0 }}</span> dari <span class="font-medium text-gray-700">{{ $chemicals->total() }}</span> chemical
            </p>
            <div>
                {{ $chemicals->links() }}
            </div>
        </div>

    </div>

</div>
@endsection
