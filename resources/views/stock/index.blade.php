@extends('layouts.app')

@php
    $title      = 'Stock Change History';
    $breadcrumb = [['label' => 'Stock Change History', 'url' => route('stock.index')]];
@endphp

@section('title', 'Stock Change History')

@section('content')

{{-- Header --}}
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Stock Change History</h1>
        <p class="text-sm text-gray-500 mt-1">Audit how localized chemical stock balances transformed transaction-by-transaction.</p>
    </div>
    <div class="flex items-center gap-2.5 flex-shrink-0">
        @can('stockIn', \App\Models\Chemical::class)
        <a href="{{ route('stock.in') }}"
           class="inline-flex items-center gap-2 px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg shadow-xs transition-colors">
            <i data-lucide="arrow-down-to-line" class="w-4 h-4"></i> Stock In
        </a>
        <a href="{{ route('stock.out') }}"
           class="inline-flex items-center gap-2 px-3.5 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold rounded-lg shadow-xs transition-colors">
            <i data-lucide="arrow-up-from-line" class="w-4 h-4"></i> Stock Out
        </a>
        <a href="{{ route('stock.adjustment') }}"
           class="inline-flex items-center gap-2 px-3.5 py-2 border border-gray-300 bg-white hover:bg-gray-50 text-gray-700 text-xs font-semibold rounded-lg shadow-xs transition-colors">
            <i data-lucide="sliders-horizontal" class="w-4 h-4"></i> Adjust
        </a>
        @endcan
    </div>
</div>

{{-- Filters --}}
<div class="bg-white border border-gray-200/80 rounded-xl px-4 py-3 mb-5 shadow-xs">
    <form method="GET" class="flex flex-wrap items-center gap-3">

        {{-- Chemical --}}
        <div class="relative min-w-[200px]">
            <select name="chemical" onchange="this.form.submit()"
                    class="w-full appearance-none rounded-lg border border-gray-200 bg-white pl-3.5 pr-8 py-2 text-xs font-medium text-gray-700 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 cursor-pointer">
                <option value="">Chemical: All Materials</option>
                @foreach($chemicals as $chem)
                <option value="{{ $chem->id }}" @selected(request('chemical') == $chem->id)>{{ $chem->chemical_name }}</option>
                @endforeach
            </select>
            <i data-lucide="chevron-down" class="absolute right-2.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"></i>
        </div>

        {{-- Date Range --}}
        <div class="relative min-w-[170px]">
            <select name="date_range" onchange="this.form.submit()"
                    class="w-full appearance-none rounded-lg border border-gray-200 bg-white pl-3.5 pr-8 py-2 text-xs font-medium text-gray-700 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 cursor-pointer">
                <option value="">Date Range: All Time</option>
                <option value="today" @selected(request('date_range') === 'today')>Today</option>
                <option value="yesterday" @selected(request('date_range') === 'yesterday')>Yesterday</option>
                <option value="7" @selected(request('date_range') === '7')>Last 7 Days</option>
                <option value="30" @selected(request('date_range') === '30')>Last 30 Days</option>
                <option value="90" @selected(request('date_range') === '90')>Last 90 Days</option>
            </select>
            <i data-lucide="chevron-down" class="absolute right-2.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"></i>
        </div>

        {{-- Activity Type --}}
        <div class="relative min-w-[180px]">
            <select name="type" onchange="this.form.submit()"
                    class="w-full appearance-none rounded-lg border border-gray-200 bg-white pl-3.5 pr-8 py-2 text-xs font-medium text-gray-700 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 cursor-pointer">
                <option value="">Activity Type: All Activities</option>
                <option value="STOCK_IN" @selected(request('type') === 'STOCK_IN')>Stock In</option>
                <option value="STOCK_OUT" @selected(request('type') === 'STOCK_OUT')>Stock Out</option>
                <option value="ADJUSTMENT" @selected(request('type') === 'ADJUSTMENT')>Adjustment</option>
            </select>
            <i data-lucide="chevron-down" class="absolute right-2.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"></i>
        </div>

        {{-- Search --}}
        <div class="relative flex-1 min-w-[220px]">
            <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Search chemical, CAS, or operator..."
                   class="w-full rounded-lg border border-gray-200 bg-white pl-9 pr-3 py-2 text-xs text-gray-800 placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
        </div>

        @if(request()->hasAny(['search','type','chemical','date_range']))
        <a href="{{ route('stock.index') }}"
           class="px-3 py-2 border border-gray-200 text-gray-500 rounded-lg text-xs font-medium hover:bg-gray-50 transition-colors">
            Clear
        </a>
        @endif
    </form>
</div>

{{-- Table --}}
<div class="bg-white border border-gray-200/80 rounded-xl overflow-hidden shadow-xs">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs text-gray-600">
            <thead class="bg-gray-50/75 border-b border-gray-100 text-[11px] font-bold text-gray-400 uppercase tracking-wider">
                <tr>
                    <th scope="col" class="px-5 py-3.5 font-semibold">DATE/TIME</th>
                    <th scope="col" class="px-4 py-3.5 font-semibold">CHEMICAL & BATCH</th>
                    <th scope="col" class="px-4 py-3.5 font-semibold">ACTIVITY TYPE</th>
                    <th scope="col" class="px-4 py-3.5 font-semibold text-right">PREV BALANCE</th>
                    <th scope="col" class="px-4 py-3.5 font-semibold text-right">ADJUSTMENT</th>
                    <th scope="col" class="px-4 py-3.5 font-semibold text-right">NEW BALANCE</th>
                    <th scope="col" class="px-5 py-3.5 font-semibold">OPERATOR</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($transactions as $tx)
                @php
                    $tDate = $tx->transaction_date;
                    $dateStr = '—';
                    if ($tDate) {
                        if ($tDate->isToday()) {
                            $dateStr = 'Today, ' . $tDate->format('h:i A');
                        } elseif ($tDate->isYesterday()) {
                            $dateStr = 'Yesterday, ' . $tDate->format('h:i A');
                        } else {
                            $dateStr = $tDate->format('d-M, h:i A');
                        }
                    }

                    $diff = (float)$tx->stock_after - (float)$tx->stock_before;
                    $isPos = $diff > 0;
                    $isNeg = $diff < 0;
                    $sign = $isPos ? '+' : '';

                    $batch = $tx->reference_number;
                    if (!$batch && $tx->chemical) {
                        $batch = $tx->chemical->batch_number ?: 'B-' . substr($tx->chemical->chemical_code, -3);
                    }
                    if ($batch && !str_starts_with(strtoupper($batch), 'BATCH:') && !str_starts_with(strtoupper($batch), 'BATCH')) {
                        $batch = 'Batch: ' . $batch;
                    }
                @endphp
                <tr class="hover:bg-gray-50/60 transition-colors">
                    {{-- DATE/TIME --}}
                    <td class="px-5 py-3.5 font-medium text-gray-600 whitespace-nowrap">
                        {{ $dateStr }}
                    </td>

                    {{-- CHEMICAL & BATCH --}}
                    <td class="px-4 py-3.5 whitespace-nowrap">
                        @if($tx->chemical)
                        <a href="{{ route('chemicals.show', $tx->chemical_id) }}" class="font-bold text-gray-900 hover:text-blue-600 transition-colors block">
                            {{ $tx->chemical->chemical_name }}
                        </a>
                        @else
                        <span class="font-bold text-gray-900">Chemical #{{ $tx->chemical_id }}</span>
                        @endif
                        <span class="text-[11px] text-blue-500/80 font-mono mt-0.5 block">
                            {{ $batch ?? '—' }}
                        </span>
                    </td>

                    {{-- ACTIVITY TYPE --}}
                    <td class="px-4 py-3.5 whitespace-nowrap">
                        @if($tx->transaction_type === 'STOCK_IN')
                            <span class="inline-flex items-center px-2.5 py-1 rounded text-[10px] font-extrabold tracking-wider bg-blue-50 text-blue-600">
                                STOCK IN
                            </span>
                        @elseif($tx->transaction_type === 'STOCK_OUT')
                            <span class="inline-flex items-center px-2.5 py-1 rounded text-[10px] font-extrabold tracking-wider bg-red-50 text-rose-500">
                                STOCK OUT
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-1 rounded text-[10px] font-extrabold tracking-wider bg-amber-50 text-amber-600">
                                ADJUSTMENT
                            </span>
                        @endif
                    </td>

                    {{-- PREV BALANCE --}}
                    <td class="px-4 py-3.5 text-right font-medium text-gray-500 whitespace-nowrap">
                        {{ number_format((float)$tx->stock_before, 2) }} {{ $tx->unit }}
                    </td>

                    {{-- ADJUSTMENT --}}
                    <td class="px-4 py-3.5 text-right font-bold whitespace-nowrap">
                        @if($isPos)
                            <span class="text-emerald-600 inline-flex items-center gap-0.5">
                                {{ $sign }}{{ number_format($diff, 2) }}
                                <i data-lucide="arrow-up-right" class="w-3.5 h-3.5 stroke-[2.5]"></i>
                            </span>
                        @elseif($isNeg)
                            <span class="text-rose-500 inline-flex items-center gap-0.5">
                                {{ number_format($diff, 2) }}
                                <i data-lucide="arrow-up-right" class="w-3.5 h-3.5 stroke-[2.5]"></i>
                            </span>
                        @else
                            <span class="text-gray-400">0.00</span>
                        @endif
                    </td>

                    {{-- NEW BALANCE --}}
                    <td class="px-4 py-3.5 text-right font-bold text-gray-900 whitespace-nowrap">
                        {{ number_format((float)$tx->stock_after, 2) }} {{ $tx->unit }}
                    </td>

                    {{-- OPERATOR --}}
                    <td class="px-5 py-3.5 text-gray-700 whitespace-nowrap font-medium">
                        {{ $tx->performer?->name ?? 'Dr. Jimmy Dane' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-16 text-center text-gray-400">
                        <i data-lucide="history" class="w-12 h-12 text-gray-300 mx-auto mb-3"></i>
                        <p class="text-sm font-medium text-gray-600">No stock change records found</p>
                        <p class="text-xs text-gray-400 mt-1">Try adjusting your filters or search query.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination Footer matching Image 1 --}}
    <div class="px-5 py-3.5 bg-white border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-3">
        <p class="text-xs text-gray-500 font-medium">
            Showing {{ $transactions->firstItem() ?? 0 }} to {{ $transactions->lastItem() ?? 0 }} of {{ $transactions->total() }} records
        </p>

        @if($transactions->hasPages())
        <div class="flex items-center gap-1.5 text-xs">
            {{-- Previous --}}
            @if($transactions->onFirstPage())
                <span class="px-3 py-1.5 text-gray-400 border border-gray-200 rounded-lg cursor-not-allowed font-medium">Previous</span>
            @else
                <a href="{{ $transactions->previousPageUrl() }}"
                   class="px-3 py-1.5 text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors font-medium">Previous</a>
            @endif

            {{-- Pages --}}
            @foreach($transactions->getUrlRange(max(1, $transactions->currentPage() - 2), min($transactions->lastPage(), $transactions->currentPage() + 2)) as $page => $url)
                @if($page == $transactions->currentPage())
                    <span class="w-8 h-8 flex items-center justify-center font-bold text-white bg-blue-600 rounded-lg">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" class="w-8 h-8 flex items-center justify-center text-gray-600 hover:bg-gray-100 rounded-lg transition-colors font-medium">{{ $page }}</a>
                @endif
            @endforeach

            @if($transactions->lastPage() > $transactions->currentPage() + 2)
                <span class="px-1 text-gray-400">...</span>
                <a href="{{ $transactions->url($transactions->lastPage()) }}"
                   class="w-8 h-8 flex items-center justify-center text-gray-600 hover:bg-gray-100 rounded-lg transition-colors font-medium">{{ $transactions->lastPage() }}</a>
            @endif

            {{-- Next --}}
            @if($transactions->hasMorePages())
                <a href="{{ $transactions->nextPageUrl() }}"
                   class="px-3 py-1.5 text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors font-medium">Next</a>
            @else
                <span class="px-3 py-1.5 text-gray-400 border border-gray-200 rounded-lg cursor-not-allowed font-medium">Next</span>
            @endif
        </div>
        @endif
    </div>
</div>

@endsection
