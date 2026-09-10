@extends('layouts.app')

@php
    $title      = 'Transaction History';
    $breadcrumb = [['label' => 'Transaction History', 'url' => route('transactions.index')]];
@endphp

@section('title', 'Transaction History')

@section('content')

{{-- Header --}}
<div class="flex items-center justify-between mb-5">
    <div>
        <h1 class="text-xl font-bold text-gray-900">Transaction History</h1>
        <p class="text-sm text-gray-500 mt-0.5">Full historical registry of stock-in, stock-out and manual adjustments.</p>
    </div>
    <a href="{{ route('reports.export-csv', ['type' => 'transactions'] + request()->query()) }}"
       class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded transition-colors">
        <i data-lucide="download" class="w-4 h-4"></i>
        Export CSV
    </a>
</div>

{{-- Filters --}}
<div class="bg-white border border-gray-200 rounded-lg px-4 py-3 mb-4">
    <form method="GET" class="flex flex-wrap items-center gap-2">

        {{-- Type --}}
        <div class="relative">
            <select name="type"
                    onchange="this.form.submit()"
                    class="appearance-none rounded border border-gray-300 pl-3 pr-7 py-1.5 text-sm text-gray-700 focus:border-blue-500 focus:outline-none bg-white cursor-pointer">
                <option value="">Type: All Types</option>
                @foreach(['STOCK_IN' => 'Stock In', 'STOCK_OUT' => 'Stock Out', 'ADJUSTMENT' => 'Adjustment'] as $val => $label)
                <option value="{{ $val }}" @selected(request('type') === $val)>{{ $label }}</option>
                @endforeach
            </select>
            <i data-lucide="chevron-down" class="absolute right-2 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-400 pointer-events-none"></i>
        </div>

        {{-- Chemical --}}
        <div class="relative">
            <select name="chemical"
                    onchange="this.form.submit()"
                    class="appearance-none rounded border border-gray-300 pl-3 pr-7 py-1.5 text-sm text-gray-700 focus:border-blue-500 focus:outline-none bg-white cursor-pointer">
                <option value="">Chemical: All Chemicals</option>
                @foreach($chemicals as $chem)
                <option value="{{ $chem->id }}" @selected(request('chemical') == $chem->id)>
                    {{ $chem->chemical_name }}
                </option>
                @endforeach
            </select>
            <i data-lucide="chevron-down" class="absolute right-2 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-400 pointer-events-none"></i>
        </div>

        {{-- Operator --}}
        <div class="relative">
            <select name="user"
                    onchange="this.form.submit()"
                    class="appearance-none rounded border border-gray-300 pl-3 pr-7 py-1.5 text-sm text-gray-700 focus:border-blue-500 focus:outline-none bg-white cursor-pointer">
                <option value="">Operator: All Users</option>
                @foreach($users as $u)
                <option value="{{ $u->id }}" @selected(request('user') == $u->id)>{{ $u->name }}</option>
                @endforeach
            </select>
            <i data-lucide="chevron-down" class="absolute right-2 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-400 pointer-events-none"></i>
        </div>

        {{-- Date Range --}}
        <div class="relative">
            <select name="date_range"
                    onchange="this.form.submit()"
                    class="appearance-none rounded border border-gray-300 pl-3 pr-7 py-1.5 text-sm text-gray-700 focus:border-blue-500 focus:outline-none bg-white cursor-pointer">
                <option value="">Date Range: Last 30 Days</option>
                <option value="7">Last 7 Days</option>
                <option value="30" @selected(request('date_range') == '30')>Last 30 Days</option>
                <option value="90">Last 90 Days</option>
                <option value="365">Last Year</option>
            </select>
            <i data-lucide="chevron-down" class="absolute right-2 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-400 pointer-events-none"></i>
        </div>

        {{-- Search --}}
        <div class="relative flex-1 min-w-48">
            <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-400"></i>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Search transactions, batches, notes..."
                   class="pl-8 w-full rounded border border-gray-300 py-1.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
        </div>

        @if(request()->hasAny(['search','type','user','date_from','date_to','chemical','date_range']))
        <a href="{{ route('transactions.index') }}"
           class="px-3 py-1.5 border border-gray-300 text-gray-500 rounded text-sm hover:bg-gray-50">Clear</a>
        @endif
    </form>
</div>

{{-- Table --}}
<div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-200">
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">ID</th>
                    <th class="text-left px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Date/Time</th>
                    <th class="text-left px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Chemical Name</th>
                    <th class="text-left px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Batch</th>
                    <th class="text-left px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Type</th>
                    <th class="text-right px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Quantity</th>
                    <th class="text-left px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Operator</th>
                    <th class="text-right px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Balance</th>
                    <th class="text-left px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Notes</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($transactions as $tx)
                @php
                    $isIn  = $tx->transaction_type === 'STOCK_IN';
                    $isAdj = $tx->transaction_type === 'ADJUSTMENT';
                    $sign  = $isIn ? '+' : ($isAdj && ($tx->stock_after - $tx->stock_before) > 0 ? '+' : '-');
                    $qtyColor = $isIn ? 'text-green-600' : ($isAdj ? 'text-orange-600' : 'text-red-500');
                @endphp
                <tr class="hover:bg-gray-50 transition-colors">
                    {{-- ID --}}
                    <td class="px-5 py-3">
                        <span class="font-mono text-xs font-bold text-gray-800">{{ $tx->transaction_code }}</span>
                    </td>
                    {{-- Date/Time --}}
                    <td class="px-3 py-3 text-xs text-gray-500 whitespace-nowrap">
                        {{ $tx->transaction_date?->diffForHumans() ?? '—' }}
                    </td>
                    {{-- Chemical --}}
                    <td class="px-3 py-3">
                        <a href="{{ route('chemicals.show', $tx->chemical_id) }}"
                           class="text-sm font-semibold text-gray-900 hover:text-blue-600 leading-tight block">
                            {{ $tx->chemical?->chemical_name ?? '—' }}
                        </a>
                    </td>
                    {{-- Batch --}}
                    <td class="px-3 py-3">
                        <span class="text-xs text-gray-500 font-mono">{{ $tx->reference_number ?? ('B-' . substr($tx->transaction_code, -3)) }}</span>
                    </td>
                    {{-- Type badge --}}
                    <td class="px-3 py-3">
                        @if($tx->transaction_type === 'STOCK_IN')
                            <span class="inline-block px-2 py-0.5 rounded text-xs font-bold bg-blue-100 text-blue-700">STOCK IN</span>
                        @elseif($tx->transaction_type === 'STOCK_OUT')
                            <span class="inline-block px-2 py-0.5 rounded text-xs font-bold bg-red-100 text-red-600">STOCK OUT</span>
                        @else
                            <span class="inline-block px-2 py-0.5 rounded text-xs font-bold bg-orange-100 text-orange-600">ADJUSTMENT</span>
                        @endif
                    </td>
                    {{-- Quantity --}}
                    <td class="px-3 py-3 text-right">
                        <span class="font-mono text-sm font-bold {{ $qtyColor }}">
                            {{ $sign }}{{ number_format($tx->quantity, 2) }} {{ $tx->unit }}
                        </span>
                    </td>
                    {{-- Operator --}}
                    <td class="px-3 py-3 text-sm text-gray-600">{{ $tx->performer?->name ?? '—' }}</td>
                    {{-- Balance --}}
                    <td class="px-3 py-3 text-right">
                        <span class="font-mono text-sm font-semibold text-gray-700">
                            {{ number_format($tx->stock_after, 2) }} {{ $tx->unit }}
                        </span>
                    </td>
                    {{-- Notes --}}
                    <td class="px-3 py-3 text-xs text-gray-400 max-w-[140px] truncate">
                        {{ $tx->notes ?? '—' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="px-5 py-16 text-center">
                        <i data-lucide="list-x" class="w-10 h-10 text-gray-200 mx-auto mb-3"></i>
                        <p class="text-sm text-gray-400">No transactions found</p>
                        @if(request()->hasAny(['search','type','user','date_from','date_to','chemical']))
                        <a href="{{ route('transactions.index') }}" class="text-xs text-blue-600 hover:underline mt-1 block">Clear filters</a>
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($transactions->hasPages())
    <div class="px-5 py-3 border-t border-gray-100 flex items-center justify-between">
        <p class="text-xs text-gray-500">
            Showing {{ $transactions->firstItem() }} to {{ $transactions->lastItem() }} of {{ $transactions->total() }} records
        </p>
        <div class="flex items-center gap-1">
            {{-- Previous --}}
            @if($transactions->onFirstPage())
                <span class="px-3 py-1.5 text-sm text-gray-300 border border-gray-200 rounded">Previous</span>
            @else
                <a href="{{ $transactions->previousPageUrl() }}"
                   class="px-3 py-1.5 text-sm text-gray-600 border border-gray-300 rounded hover:bg-gray-50 transition-colors">Previous</a>
            @endif

            {{-- Page numbers --}}
            @foreach($transactions->getUrlRange(max(1, $transactions->currentPage() - 2), min($transactions->lastPage(), $transactions->currentPage() + 2)) as $page => $url)
                @if($page == $transactions->currentPage())
                    <span class="px-3 py-1.5 text-sm font-bold text-white bg-blue-600 border border-blue-600 rounded">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" class="px-3 py-1.5 text-sm text-gray-600 border border-gray-300 rounded hover:bg-gray-50 transition-colors">{{ $page }}</a>
                @endif
            @endforeach

            @if($transactions->lastPage() > 5)
                <span class="px-2 text-gray-400 text-sm">...</span>
                <a href="{{ $transactions->url($transactions->lastPage()) }}"
                   class="px-3 py-1.5 text-sm text-gray-600 border border-gray-300 rounded hover:bg-gray-50 transition-colors">
                    {{ $transactions->lastPage() }}
                </a>
            @endif

            {{-- Next --}}
            @if($transactions->hasMorePages())
                <a href="{{ $transactions->nextPageUrl() }}"
                   class="px-3 py-1.5 text-sm text-gray-600 border border-gray-300 rounded hover:bg-gray-50 transition-colors">Next</a>
            @else
                <span class="px-3 py-1.5 text-sm text-gray-300 border border-gray-200 rounded">Next</span>
            @endif
        </div>
    </div>
    @endif
</div>

@endsection
