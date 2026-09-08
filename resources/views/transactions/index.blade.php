@extends('layouts.app')

@php
    $title      = 'Transaction History';
    $breadcrumb = [['label' => 'Transactions', 'url' => route('transactions.index')]];
@endphp

@section('title', 'Transaction History')

@section('content')

<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-xl font-bold text-gray-900">Transaction History</h1>
        <p class="text-sm text-gray-500 mt-0.5">{{ $transactions->total() }} transactions recorded</p>
    </div>
    <x-button href="{{ route('reports.export-csv', ['type' => 'transactions']) }}" variant="secondary" icon="download" size="sm">Export CSV</x-button>
</div>

<!-- Filters -->
<div class="bg-white border border-gray-200 rounded-lg p-4 mb-5">
    <form method="GET" class="flex flex-wrap gap-3">
        <div class="relative flex-1 min-w-48">
            <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search code, reference, chemical..."
                   class="pl-9 w-full rounded border border-gray-300 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
        </div>
        <select name="type" class="rounded border border-gray-300 px-3 py-2 text-sm text-gray-700 focus:border-blue-500 focus:outline-none bg-white">
            <option value="">All Types</option>
            @foreach(['STOCK_IN','STOCK_OUT','ADJUSTMENT','TRANSFER'] as $t)
            <option value="{{ $t }}" @selected(request('type') === $t)>{{ str_replace('_',' ',$t) }}</option>
            @endforeach
        </select>
        <select name="user" class="rounded border border-gray-300 px-3 py-2 text-sm text-gray-700 focus:border-blue-500 focus:outline-none bg-white">
            <option value="">All Users</option>
            @foreach($users as $u)
            <option value="{{ $u->id }}" @selected(request('user') == $u->id)>{{ $u->name }}</option>
            @endforeach
        </select>
        <input type="date" name="date_from" value="{{ request('date_from') }}"
               class="rounded border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
        <input type="date" name="date_to" value="{{ request('date_to') }}"
               class="rounded border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded text-sm font-medium hover:bg-blue-700 transition-colors">Filter</button>
        @if(request()->hasAny(['search','type','user','date_from','date_to']))
        <a href="{{ route('transactions.index') }}" class="px-4 py-2 border border-gray-300 text-gray-600 rounded text-sm hover:bg-gray-50">Clear</a>
        @endif
    </form>
</div>

<!-- Table -->
<div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Transaction</th>
                    <th class="text-left px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Chemical</th>
                    <th class="text-left px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Type</th>
                    <th class="text-right px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Before</th>
                    <th class="text-right px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Change</th>
                    <th class="text-right px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">After</th>
                    <th class="text-left px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">User</th>
                    <th class="text-left px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Date</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($transactions as $tx)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-5 py-3">
                        <p class="font-mono text-xs font-semibold text-gray-700">{{ $tx->transaction_code }}</p>
                        @if($tx->reference_number)
                        <p class="text-xs text-gray-400">Ref: {{ $tx->reference_number }}</p>
                        @endif
                    </td>
                    <td class="px-3 py-3">
                        <a href="{{ route('chemicals.show', $tx->chemical_id) }}" class="text-xs font-medium text-gray-800 hover:text-blue-600">
                            {{ $tx->chemical?->chemical_name ?? '—' }}
                        </a>
                        <p class="text-xs text-gray-400 font-mono">{{ $tx->chemical?->chemical_code }}</p>
                    </td>
                    <td class="px-3 py-3"><x-status-badge :status="$tx->transaction_type" /></td>
                    <td class="px-3 py-3 text-right font-mono text-xs text-gray-500">{{ number_format($tx->stock_before, 2) }}</td>
                    <td class="px-3 py-3 text-right font-mono text-xs font-bold {{ $tx->transaction_type === 'STOCK_IN' ? 'text-green-600' : 'text-red-500' }}">
                        {{ $tx->transaction_type === 'STOCK_IN' ? '+' : '-' }}{{ number_format($tx->quantity, 2) }} {{ $tx->unit }}
                    </td>
                    <td class="px-3 py-3 text-right font-mono text-xs font-bold text-gray-800">{{ number_format($tx->stock_after, 2) }}</td>
                    <td class="px-3 py-3 text-xs text-gray-500">{{ $tx->performer?->name ?? '—' }}</td>
                    <td class="px-3 py-3 text-xs text-gray-400 whitespace-nowrap">{{ $tx->transaction_date?->format('d M Y H:i') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-5 py-16 text-center">
                        <i data-lucide="arrow-left-right" class="w-10 h-10 text-gray-200 mx-auto mb-3"></i>
                        <p class="text-sm text-gray-400">No transactions found</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($transactions->hasPages())
    <div class="px-5 py-3 border-t border-gray-100 flex items-center justify-between">
        <p class="text-xs text-gray-500">Showing {{ $transactions->firstItem() }}–{{ $transactions->lastItem() }} of {{ $transactions->total() }}</p>
        {{ $transactions->links('pagination::simple-tailwind') }}
    </div>
    @endif
</div>

@endsection
