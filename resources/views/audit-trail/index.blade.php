@extends('layouts.app')

@php
    $title      = 'Audit Trail Ledger';
    $breadcrumb = [['label' => 'Audit Trail Ledger', 'url' => route('audit-trail.index')]];
@endphp

@section('title', 'Audit Trail Ledger')

@section('content')

{{-- Header --}}
<div class="mb-5">
    <h1 class="text-xl font-bold text-gray-900">Audit Trail Ledger</h1>
    <p class="text-sm text-gray-500 mt-0.5">Cryptographically logged chemical transactions, permission state changes, and OSHA/EPA compliance actions.</p>
</div>

{{-- Filters + Export --}}
<div class="flex flex-wrap items-center justify-between gap-3 mb-4">
    <form method="GET" class="flex flex-wrap items-center gap-2">

        {{-- Authorized User --}}
        <div class="relative">
            <select name="user" onchange="this.form.submit()"
                    class="appearance-none rounded border border-gray-300 pl-3 pr-7 py-1.5 text-xs text-gray-600 bg-white cursor-pointer focus:outline-none">
                <option value="">AUTHORIZED USER&#10;All Terminals</option>
                @foreach($users as $u)
                <option value="{{ $u->id }}" @selected(request('user') == $u->id)>{{ $u->name }}</option>
                @endforeach
            </select>
            <i data-lucide="chevron-down" class="absolute right-2 top-1/2 -translate-y-1/2 w-3 h-3 text-gray-400 pointer-events-none"></i>
        </div>

        {{-- OS System Module --}}
        <div class="relative">
            <select name="module" onchange="this.form.submit()"
                    class="appearance-none rounded border border-gray-300 pl-3 pr-7 py-1.5 text-xs text-gray-600 bg-white cursor-pointer focus:outline-none">
                <option value="">OS SYSTEM MODULE&#10;All Modules</option>
                @foreach($modules as $m)
                <option value="{{ $m }}" @selected(request('module') === $m)>{{ $m }}</option>
                @endforeach
            </select>
            <i data-lucide="chevron-down" class="absolute right-2 top-1/2 -translate-y-1/2 w-3 h-3 text-gray-400 pointer-events-none"></i>
        </div>

        {{-- Action Type --}}
        <div class="relative">
            <select name="action" onchange="this.form.submit()"
                    class="appearance-none rounded border border-gray-300 pl-3 pr-7 py-1.5 text-xs text-gray-600 bg-white cursor-pointer focus:outline-none">
                <option value="">ACTION TYPE&#10;All Actions</option>
                @foreach($actions as $a)
                <option value="{{ $a }}" @selected(request('action') === $a)>{{ ucfirst($a) }}</option>
                @endforeach
            </select>
            <i data-lucide="chevron-down" class="absolute right-2 top-1/2 -translate-y-1/2 w-3 h-3 text-gray-400 pointer-events-none"></i>
        </div>

        {{-- Date Range --}}
        <div class="relative">
            <select name="date_range" onchange="this.form.submit()"
                    class="appearance-none rounded border border-gray-300 pl-3 pr-7 py-1.5 text-xs text-gray-600 bg-white cursor-pointer focus:outline-none">
                <option value="">DATE RANGE&#10;Last 7 Days</option>
                <option value="7" @selected(request('date_range') == '7')>Last 7 Days</option>
                <option value="30" @selected(request('date_range') == '30')>Last 30 Days</option>
                <option value="90" @selected(request('date_range') == '90')>Last 90 Days</option>
                <option value="all" @selected(request('date_range') == 'all')>All Time</option>
            </select>
            <i data-lucide="chevron-down" class="absolute right-2 top-1/2 -translate-y-1/2 w-3 h-3 text-gray-400 pointer-events-none"></i>
        </div>
    </form>

    <a href="{{ route('reports.export-csv', ['type' => 'audit']) }}"
       class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 bg-white text-gray-700 text-sm font-medium rounded hover:bg-gray-50 transition-colors">
        <i data-lucide="download" class="w-4 h-4"></i>
        Export Logs
    </a>
</div>

{{-- Table --}}
<div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-200">
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide whitespace-nowrap">Date/Time</th>
                    <th class="text-left px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide whitespace-nowrap">Authorized User</th>
                    <th class="text-left px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide whitespace-nowrap">Activity<br>Description</th>
                    <th class="text-left px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide whitespace-nowrap">System Module</th>
                    <th class="text-left px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide whitespace-nowrap">Affected Item</th>
                    <th class="text-left px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide whitespace-nowrap">Prev State</th>
                    <th class="text-left px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide whitespace-nowrap">New State</th>
                    <th class="text-left px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide whitespace-nowrap">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($logs as $log)
                @php
                    $actionMap = [
                        'created'  => ['label' => 'CREATE',     'class' => 'text-green-600'],
                        'updated'  => ['label' => 'UPDATE',     'class' => 'text-blue-600'],
                        'deleted'  => ['label' => 'DELETE',     'class' => 'text-red-600'],
                        'stock_in' => ['label' => 'STOCK_IN',   'class' => 'text-green-600'],
                        'stock_out'=> ['label' => 'STOCK_OUT',  'class' => 'text-red-600'],
                        'adjusted' => ['label' => 'ADJUST',     'class' => 'text-orange-600'],
                        'login'    => ['label' => 'LOGIN',      'class' => 'text-blue-600'],
                        'logout'   => ['label' => 'LOGOUT',     'class' => 'text-gray-600'],
                        'exported' => ['label' => 'EXPORT',     'class' => 'text-purple-600'],
                        'permission' => ['label' => 'PERMISSION', 'class' => 'text-orange-600'],
                        'deactivated' => ['label' => 'DEACTIVATE', 'class' => 'text-red-600'],
                        'alert'    => ['label' => 'ALERT',      'class' => 'text-orange-600'],
                    ];
                    $am = $actionMap[$log->action] ?? ['label' => strtoupper($log->action), 'class' => 'text-gray-600'];

                    // Avatar initials + color
                    $initials = strtoupper(substr($log->user?->name ?? 'S', 0, 1) . substr(explode(' ', $log->user?->name ?? 'SYS')[1] ?? 'Y', 0, 1));
                    $avatarColors = ['JD' => 'bg-blue-500', 'SC' => 'bg-green-500', 'AT' => 'bg-purple-500'];
                    $avatarClass  = $avatarColors[$initials] ?? 'bg-gray-400';

                    // Old & new values
                    $oldVal = $log->old_values ? (is_array($log->old_values) ? collect($log->old_values)->first() : $log->old_values) : null;
                    $newVal = $log->new_values ? (is_array($log->new_values) ? collect($log->new_values)->first() : $log->new_values) : null;
                @endphp
                <tr class="hover:bg-gray-50 transition-colors">
                    {{-- Date/Time --}}
                    <td class="px-5 py-4 whitespace-nowrap">
                        <p class="text-sm text-gray-700">{{ $log->created_at->format('d-M-Y') }}, {{ $log->created_at->format('H:i') }}</p>
                    </td>
                    {{-- Authorized User --}}
                    <td class="px-3 py-4">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0 {{ $avatarClass }}">
                                {{ $log->user ? strtoupper(substr($log->user->name, 0, 2)) : 'SY' }}
                            </div>
                            <div>
                                @if($log->user)
                                <p class="text-sm font-semibold text-blue-600">{{ $log->user->name }}</p>
                                @else
                                <p class="text-sm font-semibold text-gray-500">Admin System</p>
                                @endif
                            </div>
                        </div>
                    </td>
                    {{-- Activity Description --}}
                    <td class="px-3 py-4 max-w-[160px]">
                        <p class="text-xs text-gray-600 leading-relaxed">
                            {{ $log->description ?? ucfirst(str_replace('_', ' ', $log->action)) . ' on ' . $log->module }}
                        </p>
                    </td>
                    {{-- System Module --}}
                    <td class="px-3 py-4 text-sm text-gray-600">{{ $log->module }}</td>
                    {{-- Affected Item --}}
                    <td class="px-3 py-4">
                        @if($log->record_id)
                        <p class="text-sm font-semibold text-gray-900">
                            {{ $log->record_type ? class_basename($log->record_type) . ' #' . $log->record_id : '#' . $log->record_id }}
                        </p>
                        @else
                        <span class="text-gray-400">—</span>
                        @endif
                    </td>
                    {{-- Prev State --}}
                    <td class="px-3 py-4 text-xs text-gray-400 font-mono">
                        {{ $oldVal ? (strlen((string)$oldVal) > 12 ? substr($oldVal, 0, 12).'...' : $oldVal) : '—' }}
                    </td>
                    {{-- New State --}}
                    <td class="px-3 py-4 text-sm font-semibold text-gray-700">
                        {{ $newVal ? (strlen((string)$newVal) > 14 ? substr($newVal, 0, 14).'...' : $newVal) : '—' }}
                    </td>
                    {{-- Action badge --}}
                    <td class="px-3 py-4">
                        <span class="text-xs font-bold {{ $am['class'] }}">{{ $am['label'] }}</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-5 py-16 text-center">
                        <i data-lucide="shield-check" class="w-10 h-10 text-gray-200 mx-auto mb-3"></i>
                        <p class="text-sm text-gray-400">No audit logs found</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($logs->hasPages())
    <div class="px-5 py-3 border-t border-gray-100 flex items-center justify-between">
        <p class="text-xs text-gray-500">
            Showing {{ $logs->firstItem() }} to {{ $logs->lastItem() }} of {{ $logs->total() }} audit trails
        </p>
        <div class="flex items-center gap-1">
            @if($logs->onFirstPage())
                <span class="px-3 py-1.5 text-sm text-gray-300 border border-gray-200 rounded">Previous</span>
            @else
                <a href="{{ $logs->previousPageUrl() }}"
                   class="px-3 py-1.5 text-sm text-gray-600 border border-gray-300 rounded hover:bg-gray-50">Previous</a>
            @endif

            @foreach($logs->getUrlRange(max(1,$logs->currentPage()-2), min($logs->lastPage(),$logs->currentPage()+2)) as $page => $url)
                @if($page == $logs->currentPage())
                    <span class="px-3 py-1.5 text-sm font-bold text-white bg-blue-600 border border-blue-600 rounded">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" class="px-3 py-1.5 text-sm text-gray-600 border border-gray-300 rounded hover:bg-gray-50">{{ $page }}</a>
                @endif
            @endforeach

            @if($logs->lastPage() > 5)
            <span class="px-1 text-gray-400">...</span>
            <a href="{{ $logs->url($logs->lastPage()) }}"
               class="px-3 py-1.5 text-sm text-gray-600 border border-gray-300 rounded hover:bg-gray-50">{{ $logs->lastPage() }}</a>
            @endif

            @if($logs->hasMorePages())
                <a href="{{ $logs->nextPageUrl() }}"
                   class="px-3 py-1.5 text-sm text-gray-600 border border-gray-300 rounded hover:bg-gray-50">Next</a>
            @else
                <span class="px-3 py-1.5 text-sm text-gray-300 border border-gray-200 rounded">Next</span>
            @endif
        </div>
    </div>
    @endif
</div>

@endsection
