@extends('layouts.app')

@php
    $title      = 'Master Report - Monitoring Chemical';
    $breadcrumb = [
        ['label' => 'Log Chemical', 'url' => route('transactions.master-report')],
        ['label' => 'Master Report', 'url' => '#'],
    ];
@endphp

@section('title', 'REPORT MONITORING CHEMICAL')

@push('styles')
<style>
    .report-banner {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: #000000;
        font-weight: 800;
        font-size: 1.25rem;
        letter-spacing: 0.05em;
        text-align: center;
        padding: 0.875rem 1.5rem;
        border-radius: 0.5rem 0.5rem 0 0;
        text-transform: uppercase;
        border: 1px solid #d97706;
        border-bottom: none;
    }

    .report-table {
        border-collapse: collapse;
        width: 100%;
        font-size: 0.875rem;
    }

    .report-table th {
        background-color: #fef3c7;
        color: #78350f;
        font-weight: 700;
        border: 1px solid #d1d5db;
        padding: 0.5rem 0.75rem;
    }

    .report-table td {
        border: 1px solid #e5e7eb;
        padding: 0.45rem 0.75rem;
        color: #1f2937;
    }

    .report-table tbody tr:nth-child(even) {
        background-color: #fafafa;
    }

    .report-table tbody tr:hover {
        background-color: #fef9c3;
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
        background-color: #2563eb;
        color: #ffffff;
        box-shadow: 0 1px 3px rgba(37,99,235,0.3);
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
            <a href="{{ route('transactions.master-report') }}" class="tab-pill active">
                <i data-lucide="file-spreadsheet" class="w-4 h-4"></i>
                Master Report
            </a>
            <a href="{{ route('transactions.warning-stock') }}" class="tab-pill inactive">
                <i data-lucide="alert-triangle" class="w-4 h-4 text-amber-500"></i>
                Warning Stock
            </a>
            <a href="{{ route('transactions.matrix') }}" class="tab-pill inactive" title="Daily Usage Matrix">
                <i data-lucide="calendar-days" class="w-4 h-4 text-gray-400"></i>
                Daily Usage Sheet
            </a>
        </div>

        {{-- Simple Search Form --}}
        <form action="{{ route('transactions.master-report') }}" method="GET" class="flex items-center gap-2">
            <div class="relative">
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Search chemical name..."
                       class="w-64 sm:w-72 pl-9 pr-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 bg-white">
                <i data-lucide="search" class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none"></i>
            </div>
            @if(request('search'))
                <a href="{{ route('transactions.master-report') }}" class="px-2.5 py-1.5 text-xs text-gray-600 hover:text-gray-900 border border-gray-300 rounded-lg bg-white">
                    Reset
                </a>
            @endif
            <button type="submit" class="px-3.5 py-1.5 text-sm font-medium text-white bg-amber-600 hover:bg-amber-700 rounded-lg transition-colors">
                Filter
            </button>
        </form>
    </div>

    {{-- Main Card --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">

        {{-- Header Banner matching Reference Image 1 --}}
        <div class="report-banner">
            REPORT MONITORING CHEMICAL
        </div>

        {{-- Responsive Table Container --}}
        <div class="overflow-x-auto">
            <table class="report-table">
                <thead>
                    <tr>
                        <th rowspan="2" class="text-center w-12" style="width: 50px;">No</th>
                        <th rowspan="2" class="text-left" style="min-width: 280px;">Chemical Name</th>
                        @foreach($reportMonths as $mKey => $mLabel)
                            <th colspan="2" class="text-center border-l" style="border-left: 1.5px solid #d1d5db;">
                                {{ $mLabel }}
                            </th>
                        @endforeach
                    </tr>
                    <tr>
                        @foreach($reportMonths as $mKey => $mLabel)
                            <th class="text-center" style="width: 95px; border-left: 1.5px solid #d1d5db;">Amount</th>
                            <th class="text-center" style="width: 85px;">Unit (ml/g)</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse($chemicals as $idx => $chem)
                        @php
                            $rowNum = $chemicals->firstItem() + $idx;
                            $cMatrix = $matrix[$chem->id] ?? [];
                        @endphp
                        <tr>
                            <td class="text-center font-medium text-gray-500">{{ $rowNum }}</td>
                            <td class="font-medium text-gray-900">
                                {{ $chem->chemical_name }}
                            </td>
                            @foreach($reportMonths as $mKey => $mLabel)
                                @php
                                    $amount = $cMatrix[$mKey] ?? null;
                                    $displayAmount = '';
                                    if ($amount !== null && $amount !== '') {
                                        $num = (float)$amount;
                                        if (floor($num) == $num) {
                                            $displayAmount = number_format($num, 0, ',', '.');
                                        } else {
                                            $displayAmount = rtrim(rtrim(number_format($num, 2, ',', '.'), '0'), ',');
                                        }
                                    }
                                @endphp
                                <td class="text-center border-l font-mono text-sm" style="border-left: 1.5px solid #e5e7eb;">
                                    {{ $displayAmount }}
                                </td>
                                <td class="text-center text-xs text-gray-600">
                                    {{ $displayAmount !== '' ? ($chem->unit ?? '-') : '' }}
                                </td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ 2 + count($reportMonths) * 2 }}" class="text-center py-8 text-gray-500 text-sm">
                                Tidak ada data chemical yang ditemukan.
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
