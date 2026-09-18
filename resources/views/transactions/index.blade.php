@extends('layouts.app')

@php
    $title      = 'Log Chemical';
    $breadcrumb = [['label' => 'Log Chemical', 'url' => route('transactions.index')]];
@endphp

@section('title', 'Log Chemical')

@push('styles')
<style>

    .table-freeze {
        border-collapse: separate !important;
        border-spacing: 0 !important;
        table-layout: fixed !important;
    }

    .sticky-col-no {
        position: sticky !important;
        left: 0px !important;
        width: 48px !important;
        min-width: 48px !important;
        max-width: 48px !important;
    }

    .sticky-col-name {
        position: sticky !important;
        left: 48px !important;
        width: 290px !important;
        min-width: 290px !important;
        max-width: 290px !important;
    }

    .sticky-col-saldo {
        position: sticky !important;
        left: 338px !important;
        width: 110px !important;
        min-width: 110px !important;
        max-width: 110px !important;
    }

    .sticky-col-unit {
        position: sticky !important;
        left: 448px !important;
        width: 74px !important;
        min-width: 74px !important;
        max-width: 74px !important;
    }

    .sticky-col-jumlah-header {
        position: sticky !important;
        left: 522px !important;
        width: 180px !important;
        min-width: 180px !important;
        max-width: 180px !important;
        border-right: 3px solid #000000 !important;
        box-shadow: 4px 0 8px -2px rgba(0, 0, 0, 0.22);
    }

    .sticky-col-penerimaan {
        position: sticky !important;
        left: 522px !important;
        width: 90px !important;
        min-width: 90px !important;
        max-width: 90px !important;
    }

    .sticky-col-pengeluaran {
        position: sticky !important;
        left: 612px !important;
        width: 90px !important;
        min-width: 90px !important;
        max-width: 90px !important;
        border-right: 3px solid #000000 !important;
        box-shadow: 4px 0 8px -2px rgba(0, 0, 0, 0.22);
    }

    thead th.sticky-col {
        z-index: 25 !important;
        background-color: #fedac2 !important;
    }

    tbody td.sticky-col {
        z-index: 10 !important;
        background-clip: padding-box;
        overflow: hidden;
    }

    tbody tr:nth-child(odd) td.sticky-col {
        background-color: #ffffff;
    }
    tbody tr:nth-child(even) td.sticky-col {
        background-color: #fde8d0;
    }

    tr.matrix-row:hover td.sticky-col {
        background-color: #fef3c7 !important;
    }

    .ring-2.ring-yellow-400 td.sticky-col,
    .ring-2.ring-yellow-400.matrix-row td.sticky-col {
        background-color: #fef9c3 !important;
    }

    .freeze-scroll-container::-webkit-scrollbar {
        height: 11px;
    }
    .freeze-scroll-container::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 4px;
    }
    .freeze-scroll-container::-webkit-scrollbar-thumb {
        background: #94a3b8;
        border-radius: 4px;
    }
    .freeze-scroll-container::-webkit-scrollbar-thumb:hover {
        background: #64748b;
    }
</style>
@endpush

@section('content')
<div x-data="logChemicalMatrix()" class="space-y-4 font-sans">

    <div class="flex items-center gap-2 border-b border-gray-200 pb-3">
        <a href="{{ route('transactions.master-report') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-semibold text-gray-600 bg-white border border-gray-200 hover:bg-gray-50 transition-colors">
            <i data-lucide="file-spreadsheet" class="w-4 h-4 text-amber-500"></i>
            Master Report
        </a>
        <a href="{{ route('transactions.warning-stock') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-semibold text-gray-600 bg-white border border-gray-200 hover:bg-gray-50 transition-colors">
            <i data-lucide="alert-triangle" class="w-4 h-4 text-red-500"></i>
            Warning Stock
        </a>
        <a href="{{ route('transactions.matrix') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-semibold text-white bg-blue-600 shadow-sm transition-colors">
            <i data-lucide="calendar-days" class="w-4 h-4"></i>
            Daily Usage Sheet
        </a>
    </div>

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Log Chemical</h1>
            <p class="text-sm text-gray-500 mt-0.5">Chemical inventory & daily usage record - {{ $monthTitle }}</p>
        </div>

        <div class="flex items-center gap-2 self-start sm:self-auto" x-data="monthNav()">

            <a href="{{ route('transactions.index', ['month' => $prevMonth]) }}"
               @click.prevent="goToMonth('{{ route('transactions.index', ['month' => $prevMonth]) }}')"
               class="inline-flex items-center gap-1 px-3 py-1.5 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors shadow-xs"
               title="Bulan sebelumnya: {{ \Carbon\Carbon::createFromFormat('Y-m', $prevMonth)->format('F Y') }}">
                <i data-lucide="chevron-left" class="w-4 h-4"></i>
                <span class="hidden sm:inline">{{ \Carbon\Carbon::createFromFormat('Y-m', $prevMonth)->format('M Y') }}</span>
            </a>

            <div class="relative inline-flex items-center gap-1">
                <div class="relative">
                    <select id="month-select"
                            x-model="selectedMonth"
                            class="appearance-none pl-3 pr-8 py-1.5 bg-blue-50 border border-blue-200 rounded-lg text-sm font-semibold text-blue-700 hover:bg-blue-100/80 transition-colors cursor-pointer shadow-xs focus:ring-2 focus:ring-blue-500 focus:outline-none"
                            title="Pilih Bulan — klik Terapkan untuk pindah">
                        @foreach($availableMonths ?? [] as $m)
                            <option value="{{ $m }}" {{ $periodMonth === $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::createFromFormat('Y-m', $m)->format('F Y') }}
                            </option>
                        @endforeach
                        @if(!in_array($periodMonth, $availableMonths ?? []))
                            <option value="{{ $periodMonth }}" selected>{{ $monthTitle }}</option>
                        @endif
                    </select>
                    <i data-lucide="chevron-down" class="w-4 h-4 text-blue-600 absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                </div>

                <button type="button"
                        x-show="selectedMonth !== currentMonth"
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 scale-90"
                        x-transition:enter-end="opacity-100 scale-100"
                        @click="goToMonth('{{ route('transactions.index') }}?month=' + selectedMonth)"
                        class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg shadow-xs transition-colors cursor-pointer"
                        title="Terapkan bulan yang dipilih">
                    <i data-lucide="check" class="w-3.5 h-3.5"></i>
                    <span>Terapkan</span>
                </button>
            </div>

            <a href="{{ route('transactions.index', ['month' => $nextMonth]) }}"
               @click.prevent="goToMonth('{{ route('transactions.index', ['month' => $nextMonth]) }}')"
               class="inline-flex items-center gap-1 px-3 py-1.5 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors shadow-xs"
               title="Bulan berikutnya: {{ \Carbon\Carbon::createFromFormat('Y-m', $nextMonth)->format('F Y') }}">
                <span class="hidden sm:inline">{{ \Carbon\Carbon::createFromFormat('Y-m', $nextMonth)->format('M Y') }}</span>
                <i data-lucide="chevron-right" class="w-4 h-4"></i>
            </a>
            <span class="text-xs text-gray-400 ml-1">{{ now()->format('d M Y, H:i') }}</span>

            <div x-show="loading" x-cloak
                 class="fixed inset-0 z-[9999] flex flex-col items-center justify-center bg-white/80 backdrop-blur-sm">
                <div class="flex flex-col items-center gap-3 p-6 bg-white rounded-2xl shadow-xl border border-blue-100">
                    <svg class="animate-spin w-9 h-9 text-blue-500" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    <p class="text-sm font-semibold text-blue-700">Memuat data bulan...</p>
                    <p class="text-xs text-gray-400">Mohon tunggu, data sedang disiapkan</p>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div id="flash-success"
         class="flex items-center gap-2.5 px-4 py-2.5 bg-green-50 border border-green-200 rounded-lg text-sm text-green-800 font-medium shadow-xs"
         x-data="{show:true}" x-show="show" x-transition>
        <i data-lucide="check-circle-2" class="w-4 h-4 text-green-500 flex-shrink-0"></i>
        <span>{{ session('success') }}</span>
        <button @click="show=false" class="ml-auto text-green-400 hover:text-green-600">
            <i data-lucide="x" class="w-3.5 h-3.5"></i>
        </button>
    </div>
    @endif
    @if(session('error'))
    <div class="flex items-center gap-2.5 px-4 py-2.5 bg-red-50 border border-red-200 rounded-lg text-sm text-red-800 font-medium shadow-xs"
         x-data="{show:true}" x-show="show" x-transition>
        <i data-lucide="alert-circle" class="w-4 h-4 text-red-500 flex-shrink-0"></i>
        <span>{{ session('error') }}</span>
        <button @click="show=false" class="ml-auto text-red-400 hover:text-red-600">
            <i data-lucide="x" class="w-3.5 h-3.5"></i>
        </button>
    </div>
    @endif

    <div class="flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-3 pt-1">

        <div class="flex flex-wrap items-center gap-2.5">

            <form method="GET" action="{{ route('transactions.index') }}" class="relative min-w-[260px]">
                <input type="hidden" name="month" value="{{ $periodMonth }}">
                @if(request('category'))
                    <input type="hidden" name="category" value="{{ request('category') }}">
                @endif
                <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search chemicals, batch or CAS..."
                       class="w-full pl-9 pr-3 py-1.5 text-sm bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent placeholder-gray-400 shadow-xs">
            </form>

            <button type="button" @click="showFilterModal = true"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors shadow-xs cursor-pointer">
                <i data-lucide="filter" class="w-3.5 h-3.5 text-gray-500"></i>
                <span>Filter</span>
                <span class="text-xs text-gray-400 font-normal pl-0.5">({{ $chemicals->total() }})</span>
            </button>
        </div>

        <div class="flex items-center gap-2 self-end lg:self-auto">
            <button type="button" @click="showAddChemicalModal = true"
                    class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-white border border-gray-300 rounded-lg text-sm font-semibold text-gray-800 hover:bg-gray-50 transition-colors shadow-xs cursor-pointer">
                <i data-lucide="flask-conical" class="w-4 h-4 text-gray-700"></i>
                Add Chemical
            </button>

            <button type="button" @click="showAddDateModal = true"
                    class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors cursor-pointer">
                <i data-lucide="plus" class="w-4 h-4"></i>
                Add Date
            </button>
        </div>
    </div>

    <div class="space-y-2">
        <div class="flex flex-wrap items-center justify-between text-xs text-gray-600 px-1 gap-2">
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-amber-100 border border-amber-300 text-amber-900 rounded-md font-semibold shadow-2xs">
                    <i data-lucide="lock" class="w-3.5 h-3.5 text-amber-700"></i> Kolom 1-6 Terkunci (Locked)
                </span>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-blue-50 border border-blue-200 text-blue-800 rounded-md font-semibold shadow-2xs">
                    <i data-lucide="arrow-left-right" class="w-3.5 h-3.5 text-blue-600"></i> Geser Horizontal untuk Tanggal 1 - {{ $logDates->count() }}
                </span>
            </div>
            <div class="flex items-center gap-1.5">
                <button type="button"
                        onclick="document.getElementById('matrix-scroll-wrapper').scrollTo({ left: 0, behavior: 'smooth' })"
                        class="px-2.5 py-1 bg-white border border-gray-300 hover:bg-gray-50 rounded text-xs font-semibold text-gray-700 shadow-2xs cursor-pointer inline-flex items-center gap-1 transition-colors">
                    <i data-lucide="chevrons-left" class="w-3.5 h-3.5"></i> Ke Tanggal 1
                </button>
                <button type="button"
                        onclick="const el = document.getElementById('matrix-scroll-wrapper'); el.scrollTo({ left: el.scrollWidth - el.clientWidth, behavior: 'smooth' })"
                        class="px-2.5 py-1 bg-white border border-gray-300 hover:bg-gray-50 rounded text-xs font-semibold text-gray-700 shadow-2xs cursor-pointer inline-flex items-center gap-1 transition-colors">
                    Ke Saldo Akhir <i data-lucide="chevrons-right" class="w-3.5 h-3.5"></i>
                </button>
            </div>
        </div>

        <div class="border border-black overflow-hidden shadow-sm bg-white">
            <div id="matrix-scroll-wrapper" class="overflow-x-auto freeze-scroll-container">
                @php
                    $numDates = max($logDates->count(), 1);
                    $totalTableWidth = 702 + ($numDates * 165) + 110 + 110;
                @endphp
                <table class="text-left table-freeze" style="width: {{ $totalTableWidth }}px; min-width: {{ $totalTableWidth }}px; max-width: {{ $totalTableWidth }}px; font-family: Arial, Helvetica, sans-serif; border-collapse: separate; border-spacing: 0; table-layout: fixed;">
                    <colgroup>

                        <col style="width: 48px; min-width: 48px; max-width: 48px;">

                        <col style="width: 290px; min-width: 290px; max-width: 290px;">

                        <col style="width: 110px; min-width: 110px; max-width: 110px;">

                        <col style="width: 74px; min-width: 74px; max-width: 74px;">

                        <col style="width: 90px; min-width: 90px; max-width: 90px;">

                        <col style="width: 90px; min-width: 90px; max-width: 90px;">

                        @forelse($logDates as $ld)
                            <col style="width: 55px; min-width: 55px; max-width: 55px;">
                            <col style="width: 55px; min-width: 55px; max-width: 55px;">
                            <col style="width: 55px; min-width: 55px; max-width: 55px;">
                        @empty
                            <col style="width: 165px; min-width: 165px; max-width: 165px;">
                        @endforelse

                        <col style="width: 110px; min-width: 110px; max-width: 110px;">

                        <col style="width: 110px; min-width: 110px; max-width: 110px;">
                    </colgroup>
                    <thead>

                        <tr style="background-color: #fedac2;">
                            <th rowspan="5" class="sticky-col sticky-col-no py-2.5 px-2 text-center text-sm font-bold text-gray-900 border border-black align-middle" style="background-color: #fedac2;">
                                No
                            </th>
                            <th rowspan="5" class="sticky-col sticky-col-name py-2.5 px-3 text-center text-sm font-bold text-gray-900 border border-black align-middle" style="background-color: #fedac2;">
                                Chemical Name
                            </th>
                            <th rowspan="5" class="sticky-col sticky-col-saldo py-2 px-2 text-center text-sm font-bold text-gray-900 border border-black leading-tight align-middle" title="SALDO AWAL" style="background-color: #fedac2;">
                                <div>Saldo awal</div>
                                <div>sementara</div>
                                <div class="text-xs font-semibold text-gray-700">(ml/g)</div>
                                <span class="sr-only">SALDO AWAL</span>
                            </th>
                            <th rowspan="5" class="sticky-col sticky-col-unit py-2.5 px-1 text-center text-sm font-bold text-gray-900 border border-black align-middle" style="background-color: #fedac2;">
                                Satuan
                            </th>

                            <th colspan="2" rowspan="3" class="sticky-col sticky-col-jumlah-header py-2 px-3 text-center text-sm font-bold text-gray-900 border border-black align-middle" style="background-color: #fedac2;">
                                Jumlah
                            </th>

                            @forelse($logDates as $ld)
                                <th colspan="3" class="py-1 px-2 text-center text-xs font-bold text-gray-900 border border-black tracking-wide min-w-[165px] w-[165px]">
                                    Date Taken
                                </th>
                            @empty
                                <th colspan="3" rowspan="5" class="py-4 px-4 text-center text-xs text-gray-500 border border-black">
                                    <div>Belum ada tanggal.</div>
                                    <button type="button" @click="showAddDateModal = true" class="mt-1 text-xs text-blue-600 font-semibold underline">
                                        + Tambah Tanggal
                                    </button>
                                </th>
                            @endforelse

                            <th rowspan="5" class="py-2.5 px-3 text-center text-sm font-bold text-gray-900 border border-black min-w-[110px] w-[110px] align-middle" title="SALDO AKHIR" style="background-color: #fedac2;">
                                <div>Saldo Akhir</div>
                                <div class="text-[10px] font-normal text-gray-600">Awal + Masuk - Keluar</div>
                                <span class="sr-only">SALDO AKHIR</span>
                            </th>

                            <th rowspan="5" class="py-2.5 px-2 text-center text-xs font-bold text-gray-900 border border-black min-w-[110px] w-[110px] align-middle" style="background-color: #fedac2;">
                                Actions
                            </th>
                        </tr>

                        @if($logDates->isNotEmpty())
                        <tr style="background-color: #fedac2;">
                            @foreach($logDates as $ld)
                                <th colspan="3" class="py-1 px-2 text-center text-xs font-semibold text-gray-900 border border-black relative group/date">
                                    <span>{{ $ld->log_date->format('d/m/Y') }}</span>
                                    <form method="POST" action="{{ route('transactions.dates.destroy', $ld->id) }}"
                                          onsubmit="return confirm('Hapus kolom tanggal {{ $ld->log_date->format('d/m/Y') }} ini?')"
                                          class="inline-block ml-1 opacity-0 group-hover/date:opacity-100 transition-opacity">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-gray-400 hover:text-red-600 align-middle" title="Hapus kolom tanggal ini">
                                            <i data-lucide="trash-2" class="w-3 h-3 inline"></i>
                                        </button>
                                    </form>
                                </th>
                            @endforeach
                        </tr>

                        <tr style="background-color: #fedac2;">
                            @foreach($logDates as $ld)
                                <th colspan="3" class="py-1 px-2 text-center text-xs font-bold text-gray-900 border border-black tracking-wider">
                                    Analyst
                                </th>
                            @endforeach
                        </tr>
                        @endif

                        <tr style="background-color: #fedac2;">

                            <th rowspan="2" class="sticky-col sticky-col-penerimaan py-2 px-2 text-center text-xs font-bold text-gray-900 border border-black align-middle" title="PENERIMAAN" style="background-color: #fedac2;">
                                <span>Penerimaan</span>
                                <span class="sr-only">PENERIMAAN</span>
                            </th>
                            <th rowspan="2" class="sticky-col sticky-col-pengeluaran py-2 px-2 text-center text-xs font-bold text-gray-900 border border-black align-middle" title="PENGELUARAN" style="background-color: #fedac2;">
                                <span>Pengeluaran</span>
                                <span class="sr-only">PENGELUARAN</span>
                            </th>

                            @foreach($logDates as $ld)

                                <th class="p-1 text-center border border-black w-18"
                                    x-data="analystTakePill({{ $ld->id }}, 'analyst_take_1', '{{ addslashes($ld->analyst_take_1 ?? '') }}')">
                                    <div class="relative">
                                        <button type="button" @click="openDropdown = !openDropdown"
                                                :class="name ? 'bg-[#fef3c7] text-gray-800 border-amber-300' : 'bg-[#f3f4f6] text-gray-400 border-gray-300'"
                                                class="inline-flex items-center justify-between gap-1 w-full px-1.5 py-0.5 text-[11px] font-semibold rounded border shadow-2xs cursor-pointer min-h-[22px]"
                                                :title="name ? 'Analis Take 1: ' + name : 'Pilih Analis Take 1'">
                                            <span class="truncate max-w-[55px]" x-text="name ? name : ''"></span>
                                            <svg class="w-2.5 h-2.5 flex-shrink-0 text-gray-700 ml-auto" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/>
                                            </svg>
                                        </button>

                                        <div x-show="openDropdown" @click.away="openDropdown = false"
                                             class="absolute left-0 mt-1 w-32 bg-white border border-gray-300 rounded shadow-lg z-30 py-1 text-left"
                                             x-cloak>
                                            @foreach($analysts as $an)
                                                <button type="button" @click="selectAnalyst('{{ $an }}')"
                                                        class="w-full text-left px-2.5 py-1 text-xs hover:bg-amber-100 text-gray-800 font-medium">
                                                    {{ $an }}
                                                </button>
                                            @endforeach
                                            <hr class="my-1 border-gray-200">
                                            <template x-if="!showCustomInput">
                                                <button type="button" @click="showCustomInput = true" class="w-full text-left px-2.5 py-1 text-[11px] text-blue-600 hover:bg-blue-50 font-medium">
                                                    + Nama Baru
                                                </button>
                                            </template>
                                            <template x-if="showCustomInput">
                                                <div class="p-1">
                                                    <input type="text" x-model="customName" @keydown.enter="saveCustom()" placeholder="Ketik nama..."
                                                           class="w-full text-xs px-1.5 py-1 border border-blue-500 rounded focus:outline-none">
                                                </div>
                                            </template>
                                            <button type="button" @click="selectAnalyst('')" class="w-full text-left px-2.5 py-1 text-[11px] hover:bg-gray-100 text-gray-400 italic">
                                                (Kosong)
                                            </button>
                                        </div>
                                    </div>
                                </th>

                                <th class="p-1 text-center border border-black w-18"
                                    x-data="analystTakePill({{ $ld->id }}, 'analyst_take_2', '{{ addslashes($ld->analyst_take_2 ?? '') }}')">
                                    <div class="relative">
                                        <button type="button" @click="openDropdown = !openDropdown"
                                                :class="name ? 'bg-[#fef3c7] text-gray-800 border-amber-300' : 'bg-[#f3f4f6] text-gray-400 border-gray-300'"
                                                class="inline-flex items-center justify-between gap-1 w-full px-1.5 py-0.5 text-[11px] font-semibold rounded border shadow-2xs cursor-pointer min-h-[22px]"
                                                :title="name ? 'Analis Take 2: ' + name : 'Pilih Analis Take 2'">
                                            <span class="truncate max-w-[55px]" x-text="name ? name : ''"></span>
                                            <svg class="w-2.5 h-2.5 flex-shrink-0 text-gray-700 ml-auto" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/>
                                            </svg>
                                        </button>

                                        <div x-show="openDropdown" @click.away="openDropdown = false"
                                             class="absolute left-0 mt-1 w-32 bg-white border border-gray-300 rounded shadow-lg z-30 py-1 text-left"
                                             x-cloak>
                                            @foreach($analysts as $an)
                                                <button type="button" @click="selectAnalyst('{{ $an }}')"
                                                        class="w-full text-left px-2.5 py-1 text-xs hover:bg-amber-100 text-gray-800 font-medium">
                                                    {{ $an }}
                                                </button>
                                            @endforeach
                                            <hr class="my-1 border-gray-200">
                                            <template x-if="!showCustomInput">
                                                <button type="button" @click="showCustomInput = true" class="w-full text-left px-2.5 py-1 text-[11px] text-blue-600 hover:bg-blue-50 font-medium">
                                                    + Nama Baru
                                                </button>
                                            </template>
                                            <template x-if="showCustomInput">
                                                <div class="p-1">
                                                    <input type="text" x-model="customName" @keydown.enter="saveCustom()" placeholder="Ketik nama..."
                                                           class="w-full text-xs px-1.5 py-1 border border-blue-500 rounded focus:outline-none">
                                                </div>
                                            </template>
                                            <button type="button" @click="selectAnalyst('')" class="w-full text-left px-2.5 py-1 text-[11px] hover:bg-gray-100 text-gray-400 italic">
                                                (Kosong)
                                            </button>
                                        </div>
                                    </div>
                                </th>

                                <th class="p-1 text-center border border-black w-18"
                                    x-data="analystTakePill({{ $ld->id }}, 'analyst_take_3', '{{ addslashes($ld->analyst_take_3 ?? '') }}')">
                                    <div class="relative">
                                        <button type="button" @click="openDropdown = !openDropdown"
                                                :class="name ? 'bg-[#fef3c7] text-gray-800 border-amber-300' : 'bg-[#f3f4f6] text-gray-400 border-gray-300'"
                                                class="inline-flex items-center justify-between gap-1 w-full px-1.5 py-0.5 text-[11px] font-semibold rounded border shadow-2xs cursor-pointer min-h-[22px]"
                                                :title="name ? 'Analis Take 3: ' + name : 'Pilih Analis Take 3'">
                                            <span class="truncate max-w-[55px]" x-text="name ? name : ''"></span>
                                            <svg class="w-2.5 h-2.5 flex-shrink-0 text-gray-700 ml-auto" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/>
                                            </svg>
                                        </button>

                                        <div x-show="openDropdown" @click.away="openDropdown = false"
                                             class="absolute left-0 mt-1 w-32 bg-white border border-gray-300 rounded shadow-lg z-30 py-1 text-left"
                                             x-cloak>
                                            @foreach($analysts as $an)
                                                <button type="button" @click="selectAnalyst('{{ $an }}')"
                                                        class="w-full text-left px-2.5 py-1 text-xs hover:bg-amber-100 text-gray-800 font-medium">
                                                    {{ $an }}
                                                </button>
                                            @endforeach
                                            <hr class="my-1 border-gray-200">
                                            <template x-if="!showCustomInput">
                                                <button type="button" @click="showCustomInput = true" class="w-full text-left px-2.5 py-1 text-[11px] text-blue-600 hover:bg-blue-50 font-medium">
                                                    + Nama Baru
                                                </button>
                                            </template>
                                            <template x-if="showCustomInput">
                                                <div class="p-1">
                                                    <input type="text" x-model="customName" @keydown.enter="saveCustom()" placeholder="Ketik nama..."
                                                           class="w-full text-xs px-1.5 py-1 border border-blue-500 rounded focus:outline-none">
                                                </div>
                                            </template>
                                            <button type="button" @click="selectAnalyst('')" class="w-full text-left px-2.5 py-1 text-[11px] hover:bg-gray-100 text-gray-400 italic">
                                                (Kosong)
                                            </button>
                                        </div>
                                    </div>
                                </th>
                            @endforeach
                        </tr>

                        @if($logDates->isNotEmpty())
                        <tr style="background-color: #fedac2;">
                            @foreach($logDates as $ld)
                                <th class="py-1 px-0.5 text-center text-xs font-bold text-gray-900 border border-black min-w-[55px] w-[55px]">Take 1</th>
                                <th class="py-1 px-0.5 text-center text-xs font-bold text-gray-900 border border-black min-w-[55px] w-[55px]">Take 2</th>
                                <th class="py-1 px-0.5 text-center text-xs font-bold text-gray-900 border border-black min-w-[55px] w-[55px]">Take 3</th>
                            @endforeach
                        </tr>
                        @endif
                    </thead>

                    <tbody>
                        @forelse($chemicals as $idx => $chem)
                            @php
                                $row = $rows[$chem->id] ?? [
                                    'saldo_awal'  => (float)$chem->current_stock,
                                    'penerimaan'  => 0,
                                    'pengeluaran' => 0,
                                    'saldo_akhir' => (float)$chem->current_stock,
                                    'usages'      => [],
                                ];
                                $isEven = ($idx % 2 === 1);
                                $rowBg = $isEven ? '#fde8d0' : '#ffffff';
                                $cellBg = request('highlight') == $chem->id ? '#fef9c3' : $rowBg;

                                $valSaldo = $row['saldo_awal'];
                                $valPenerimaan = $row['penerimaan'];
                                $valPengeluaran = $row['pengeluaran'];
                                $valSaldoAkhir = $row['saldo_akhir'];
                            @endphp
                            <tr style="background-color: {{ $cellBg }};"
                                class="matrix-row hover:bg-amber-50/60 transition-colors {{ request('highlight') == $chem->id ? 'ring-2 ring-yellow-400 ring-inset' : '' }}"
                                id="row-{{ $chem->id }}">

                                <td class="sticky-col sticky-col-no py-1.5 px-2 text-center text-sm text-gray-900 border border-black font-normal" style="background-color: {{ $cellBg }};">
                                    {{ ($chemicals->currentPage() - 1) * $chemicals->perPage() + $idx + 1 }}
                                </td>

                                <td class="sticky-col sticky-col-name py-1.5 px-3 text-left text-sm text-gray-900 border border-black font-normal align-middle" style="background-color: {{ $cellBg }} !important;"
                                    x-data="chemicalNameCell({{ $chem->id }}, '{{ addslashes($chem->chemical_name) }}')"
                                    @dblclick="startEdit()">
                                    <template x-if="!editing">
                                        <div class="cursor-pointer hover:text-blue-700 whitespace-normal leading-snug py-0.5"
                                             style="word-break: break-word; overflow-wrap: anywhere;"
                                             title="{{ $chem->chemical_name }} (Klik 2x untuk edit)">
                                            <span x-text="name">{{ $chem->chemical_name }}</span>
                                        </div>
                                    </template>
                                    <template x-if="editing">
                                        <input type="text" x-model="name" x-ref="chemNameInput"
                                               @blur="saveEdit()" @keydown.enter="saveEdit()" @keydown.escape="editing = false"
                                               class="w-full text-sm px-1.5 py-0.5 border border-blue-600 bg-white rounded focus:outline-none">
                                    </template>
                                </td>

                                <td class="sticky-col sticky-col-saldo py-1.5 px-2 text-right text-sm text-gray-900 border border-black font-normal" style="background-color: {{ $cellBg }} !important;"
                                    x-data="balanceCell({{ $chem->id }}, '{{ $periodMonth }}', 'saldo_awal', {{ $valSaldo }})"
                                    @dblclick="startEdit()">
                                    <template x-if="!editing">
                                        <div class="cursor-pointer hover:bg-amber-100/70 px-1 py-0.5 rounded text-right min-h-[22px]" title="Klik 2x untuk edit saldo awal">
                                            <span x-text="val > 0 ? (val % 1 === 0 ? val.toString() : val.toString().replace('.', ',')) : ''"></span>
                                        </div>
                                    </template>
                                    <template x-if="editing">
                                        <input type="text" inputmode="decimal" x-model="val" x-ref="inputEl"
                                               @blur="saveEdit()" @keydown.enter="saveEdit()" @keydown.escape="editing = false"
                                               class="w-24 text-right text-sm px-1 py-0.5 border border-blue-600 bg-white rounded focus:outline-none">
                                    </template>
                                </td>

                                <td class="sticky-col sticky-col-unit py-1 px-1 text-center align-middle border border-black"
                                    :class="openDropdown ? '!z-40' : ''"
                                    style="background-color: {{ $cellBg }} !important;"
                                    x-data="unitPillSelector({{ $chem->id }}, '{{ strtolower(trim($chem->unit ?? '')) }}')">
                                    <div class="relative flex items-center justify-center">
                                        <button type="button" @click="openDropdown = !openDropdown"
                                                :class="unitClass(unit)"
                                                class="inline-flex items-center justify-center gap-1 w-[52px] h-[22px] text-[11px] font-bold rounded-md border shadow-2xs transition-all cursor-pointer select-none"
                                                title="Klik untuk ubah satuan">
                                            <span x-text="unit ? unit : '-'" class="truncate"></span>
                                            <svg class="w-2.5 h-2.5 opacity-70 flex-shrink-0 transition-transform duration-150"
                                                 :class="openDropdown ? 'rotate-180' : ''"
                                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        </button>

                                        <div x-show="openDropdown" @click.away="openDropdown = false"
                                             class="absolute left-1/2 -translate-x-1/2 top-full mt-1 w-28 bg-white border border-gray-200 rounded-lg shadow-xl z-50 py-1 divide-y divide-gray-100 text-left text-xs"
                                             x-cloak>
                                            <div class="py-0.5">
                                                <button type="button" @click="selectUnit('g')" class="w-full flex items-center justify-between px-2.5 py-1 text-xs hover:bg-amber-50 text-gray-700 hover:text-amber-900 transition-colors">
                                                    <span class="font-semibold text-amber-700">g</span>
                                                    <span class="text-[10px] text-gray-400">gram</span>
                                                </button>
                                                <button type="button" @click="selectUnit('ml')" class="w-full flex items-center justify-between px-2.5 py-1 text-xs hover:bg-orange-50 text-gray-700 hover:text-orange-900 transition-colors">
                                                    <span class="font-semibold text-orange-700">ml</span>
                                                    <span class="text-[10px] text-gray-400">mililiter</span>
                                                </button>
                                                <button type="button" @click="selectUnit('L')" class="w-full flex items-center justify-between px-2.5 py-1 text-xs hover:bg-blue-50 text-gray-700 hover:text-blue-900 transition-colors">
                                                    <span class="font-semibold text-blue-700">L</span>
                                                    <span class="text-[10px] text-gray-400">liter</span>
                                                </button>
                                                <button type="button" @click="selectUnit('kg')" class="w-full flex items-center justify-between px-2.5 py-1 text-xs hover:bg-emerald-50 text-gray-700 hover:text-emerald-900 transition-colors">
                                                    <span class="font-semibold text-emerald-700">kg</span>
                                                    <span class="text-[10px] text-gray-400">kilogram</span>
                                                </button>
                                                <button type="button" @click="selectUnit('pcs')" class="w-full flex items-center justify-between px-2.5 py-1 text-xs hover:bg-purple-50 text-gray-700 hover:text-purple-900 transition-colors">
                                                    <span class="font-semibold text-purple-700">pcs</span>
                                                    <span class="text-[10px] text-gray-400">pieces</span>
                                                </button>
                                            </div>
                                            <div class="py-0.5">
                                                <button type="button" @click="selectUnit('')" class="w-full text-left px-2.5 py-1 text-[11px] text-gray-400 hover:bg-gray-100 hover:text-gray-600 italic transition-colors">
                                                    (kosong)
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td class="sticky-col sticky-col-penerimaan py-1.5 px-2 text-center text-sm text-gray-900 border border-black font-normal" style="background-color: {{ $cellBg }} !important;"
                                    x-data="balanceCell({{ $chem->id }}, '{{ $periodMonth }}', 'penerimaan', {{ $valPenerimaan }})"
                                    @dblclick="startEdit()">
                                    <template x-if="!editing">
                                        <div class="cursor-pointer hover:bg-amber-100/70 px-1 py-0.5 rounded text-center min-h-[22px]" title="Klik 2x untuk input penerimaan">
                                            <span x-text="val > 0 ? (val % 1 === 0 ? val.toString() : val.toString().replace('.', ',')) : ''"></span>
                                        </div>
                                    </template>
                                    <template x-if="editing">
                                        <input type="text" inputmode="decimal" x-model="val" x-ref="inputEl"
                                               @blur="saveEdit()" @keydown.enter="saveEdit()" @keydown.escape="editing = false"
                                               class="w-20 text-center text-sm px-1 py-0.5 border border-blue-600 bg-white rounded focus:outline-none">
                                    </template>
                                </td>

                                <td class="sticky-col sticky-col-pengeluaran py-1.5 px-2 text-center text-sm text-gray-900 border border-black font-normal" style="background-color: {{ $cellBg }} !important;">
                                    <span id="pengeluaran-m-{{ $chem->id }}">
                                        {{ $valPengeluaran > 0 ? (floor($valPengeluaran) == $valPengeluaran ? number_format($valPengeluaran, 0, ',', '') : rtrim(rtrim(number_format($valPengeluaran, 4, ',', ''), '0'), ',')) : '0' }}
                                    </span>
                                </td>

                                @foreach($logDates as $ld)
                                    @php
                                        $usage = $row['usages'][$ld->id] ?? ['take_1' => null, 'take_2' => null, 'take_3' => null];
                                        $t1 = $usage['take_1'];
                                        $t2 = $usage['take_2'];
                                        $t3 = $usage['take_3'];
                                    @endphp

                                    <td class="py-1.5 px-1 text-center text-sm text-gray-900 border border-black font-normal min-w-[55px] w-[55px]"
                                        x-data="usageCell({{ $chem->id }}, {{ $ld->id }}, 'take_1', {{ $t1 !== null ? $t1 : 'null' }})"
                                        @dblclick="startEdit()">
                                        <template x-if="!editing">
                                            <div class="cursor-pointer hover:bg-amber-100/70 px-1 py-0.5 rounded text-center min-h-[22px]" title="Klik 2x untuk input Take 1">
                                                <span x-text="val !== null && val > 0 ? (val % 1 === 0 ? val.toString() : val.toString().replace('.', ',')) : ''"></span>
                                            </div>
                                        </template>
                                        <template x-if="editing">
                                            <input type="text" inputmode="decimal" x-model="val" x-ref="inputEl"
                                                   @blur="saveEdit()" @keydown.enter="saveEdit()" @keydown.escape="editing = false"
                                                   class="w-16 text-center text-sm px-1 py-0.5 border border-blue-600 bg-white rounded focus:outline-none">
                                        </template>
                                    </td>

                                    <td class="py-1.5 px-1 text-center text-sm text-gray-900 border border-black font-normal min-w-[55px] w-[55px]"
                                        x-data="usageCell({{ $chem->id }}, {{ $ld->id }}, 'take_2', {{ $t2 !== null ? $t2 : 'null' }})"
                                        @dblclick="startEdit()">
                                        <template x-if="!editing">
                                            <div class="cursor-pointer hover:bg-amber-100/70 px-1 py-0.5 rounded text-center min-h-[22px]" title="Klik 2x untuk input Take 2">
                                                <span x-text="val !== null && val > 0 ? (val % 1 === 0 ? val.toString() : val.toString().replace('.', ',')) : ''"></span>
                                            </div>
                                        </template>
                                        <template x-if="editing">
                                            <input type="text" inputmode="decimal" x-model="val" x-ref="inputEl"
                                                   @blur="saveEdit()" @keydown.enter="saveEdit()" @keydown.escape="editing = false"
                                                   class="w-16 text-center text-sm px-1 py-0.5 border border-blue-600 bg-white rounded focus:outline-none">
                                        </template>
                                    </td>

                                    <td class="py-1.5 px-1 text-center text-sm text-gray-900 border border-black font-normal min-w-[55px] w-[55px]"
                                        x-data="usageCell({{ $chem->id }}, {{ $ld->id }}, 'take_3', {{ $t3 !== null ? $t3 : 'null' }})"
                                        @dblclick="startEdit()">
                                        <template x-if="!editing">
                                            <div class="cursor-pointer hover:bg-amber-100/70 px-1 py-0.5 rounded text-center min-h-[22px]" title="Klik 2x untuk input Take 3">
                                                <span x-text="val !== null && val > 0 ? (val % 1 === 0 ? val.toString() : val.toString().replace('.', ',')) : ''"></span>
                                            </div>
                                        </template>
                                        <template x-if="editing">
                                            <input type="text" inputmode="decimal" x-model="val" x-ref="inputEl"
                                                   @blur="saveEdit()" @keydown.enter="saveEdit()" @keydown.escape="editing = false"
                                                   class="w-16 text-center text-sm px-1 py-0.5 border border-blue-600 bg-white rounded focus:outline-none">
                                        </template>
                                    </td>
                                @endforeach

                                <td class="py-1.5 px-3 text-right text-sm font-bold text-gray-900 border border-black min-w-[110px] w-[110px]">
                                    <span id="saldo-akhir-m-{{ $chem->id }}">
                                        {{ floor($valSaldoAkhir) == $valSaldoAkhir ? number_format($valSaldoAkhir, 0, ',', '') : rtrim(rtrim(number_format($valSaldoAkhir, 4, ',', ''), '0'), ',') }}
                                    </span>
                                </td>

                                <td class="py-1.5 px-2 text-center border border-black min-w-[110px] w-[110px]" style="background-color: {{ $cellBg }};">
                                    <div class="flex flex-col items-center gap-1">
                                        <button type="button"
                                            class="inline-flex items-center gap-1 px-2 py-0.5 text-[11px] font-semibold bg-blue-50 text-blue-700 border border-blue-200 rounded hover:bg-blue-100 transition-colors cursor-pointer w-full justify-center"
                                            @click="openRowEdit({
                                                id: {{ $chem->id }},
                                                name: {{ json_encode($chem->chemical_name) }},
                                                unit: {{ json_encode($chem->unit ?? '') }},
                                                saldo_awal: {{ $valSaldo }},
                                                penerimaan: {{ $valPenerimaan }},
                                                period_month: '{{ $periodMonth }}'
                                            })">
                                            <i data-lucide="pencil" class="w-2.5 h-2.5"></i> Edit
                                        </button>
                                        <button type="button"
                                            class="inline-flex items-center gap-1 px-2 py-0.5 text-[11px] font-semibold bg-red-50 text-red-700 border border-red-200 rounded hover:bg-red-100 transition-colors cursor-pointer w-full justify-center"
                                            @click="confirmRowDelete({{ $chem->id }}, {{ json_encode($chem->chemical_name) }})">
                                            <i data-lucide="trash-2" class="w-2.5 h-2.5"></i> Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ 7 + ($logDates->count() * 3) }}" class="py-8 text-center text-gray-500 text-sm border border-black">
                                    Tidak ada data chemical.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row items-center justify-between gap-2 px-1 pt-1 text-xs text-gray-500">
            <div class="flex items-center gap-1.5">
                <i data-lucide="edit-3" class="w-3.5 h-3.5 text-gray-400"></i>
                <span>Klik dua kali pada cell (nama, saldo awal, penerimaan) untuk edit langsung. Satuan dapat diganti via dropdown pill.</span>
            </div>
            <div class="flex flex-wrap items-center gap-3">

                <div class="flex items-center gap-1.5">
                    <span class="text-gray-400">Tampilkan:</span>
                    @foreach([50, 100, 250] as $pp)
                        @php
                            $ppUrl = request()->fullUrlWithQuery(['per_page' => $pp, 'page' => 1]);
                            $isActive = (int)request('per_page', 100) === $pp;
                        @endphp
                        <a href="{{ $ppUrl }}"
                           class="px-2 py-0.5 rounded border text-xs font-semibold transition-colors
                                  {{ $isActive ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50' }}">
                            {{ $pp }}
                        </a>
                    @endforeach
                </div>
                <span>Menampilkan {{ $chemicals->firstItem() ?? 0 }}&ndash;{{ $chemicals->lastItem() ?? 0 }} dari {{ $chemicals->total() }} chemicals</span>
                @if($chemicals->hasPages())
                    <div class="inline-flex">
                        {{ $chemicals->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div x-show="showAddDateModal"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs"
         x-cloak>
        <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6 border border-gray-100"
             @click.away="showAddDateModal = false">
            <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-4">
                <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                    <i data-lucide="calendar-plus" class="w-5 h-5 text-blue-600"></i>
                    Add Date Column
                </h3>
                <button type="button" @click="showAddDateModal = false" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('transactions.dates.store') }}">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wide mb-1">Date Taken</label>
                        <input type="date" name="log_date" value="{{ date('Y-m-d') }}" required
                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-end gap-2.5">
                    <button type="button" @click="showAddDateModal = false"
                            class="px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-5 py-2 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors shadow-xs">
                        Add Column
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div x-show="showAddChemicalModal"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs"
         x-cloak>
        <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6 border border-gray-100"
             @click.away="showAddChemicalModal = false">
            <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-4">
                <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                    <i data-lucide="flask-conical" class="w-5 h-5 text-blue-600"></i>
                    Add Chemical to Log Sheet
                </h3>
                <button type="button" @click="showAddChemicalModal = false" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('transactions.quick-add-chemical') }}">
                @csrf
                <input type="hidden" name="period_month" value="{{ $periodMonth }}">

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wide mb-1">Chemical Name <span class="text-red-500">*</span></label>
                        <input type="text" name="chemical_name" required placeholder="e.g. Potassium chloride"
                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wide mb-1">Satuan <span class="text-red-500">*</span></label>
                        <select name="unit" required
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none bg-white">
                            <option value="g">g</option>
                            <option value="ml">ml</option>
                            <option value="L">L</option>
                            <option value="kg">kg</option>
                            <option value="pcs">pcs</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wide mb-1">Saldo Awal</label>
                            <input type="number" step="any" name="saldo_awal" value="0"
                                   class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none font-mono">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wide mb-1">Penerimaan</label>
                            <input type="number" step="any" name="penerimaan" value="0"
                                   class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none font-mono">
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-end gap-2.5">
                    <button type="button" @click="showAddChemicalModal = false"
                            class="px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-5 py-2 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors shadow-xs">
                        Save Chemical
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div x-show="showFilterModal"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs"
         x-cloak>
        <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6 border border-gray-100"
             @click.away="showFilterModal = false">
            <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-4">
                <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                    <i data-lucide="filter" class="w-5 h-5 text-blue-600"></i>
                    Filter Log Chemical
                </h3>
                <button type="button" @click="showFilterModal = false" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form method="GET" action="{{ route('transactions.index') }}">
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wide mb-1">Period (Month / Year)</label>
                        <input type="month" name="month" value="{{ $periodMonth }}" required
                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wide mb-1">Category</label>
                        <select name="category" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            <option value="">All Categories</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" @selected(request('category') == $cat->id)>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    @if(request('search'))
                        <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif
                </div>

                <div class="mt-6 flex items-center justify-between">
                    <a href="{{ route('transactions.index', ['month' => '2026-04']) }}"
                       class="text-xs text-blue-600 hover:underline">
                        Reset to April 2026
                    </a>
                    <div class="flex items-center gap-2">
                        <button type="button" @click="showFilterModal = false"
                                class="px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                            Close
                        </button>
                        <button type="submit"
                                class="px-5 py-2 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors shadow-xs">
                            Apply Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
         x-show="showRowEditModal" x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         @click.self="showRowEditModal = false">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full p-6 border border-gray-100"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
             @click.stop>
            <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-4">
                <h3 class="text-base font-bold text-gray-900 flex items-center gap-2">
                    <i data-lucide="pencil" class="w-4 h-4 text-blue-600"></i>
                    Edit Chemical — Log Sheet
                </h3>
                <button @click="showRowEditModal=false" class="text-gray-400 hover:text-gray-600 cursor-pointer">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <div class="space-y-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wide mb-1">Nama Chemical</label>
                    <input x-model="rowEdit.name"
                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none"
                           placeholder="Nama chemical...">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wide mb-1">Unit</label>
                        <input x-model="rowEdit.unit"
                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none"
                               placeholder="ml / g / pcs">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wide mb-1">Saldo Awal</label>
                        <input type="text" inputmode="decimal" x-model="rowEdit.saldo_awal"
                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none font-mono"
                               placeholder="0">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wide mb-1">Penerimaan</label>
                    <input type="text" inputmode="decimal" x-model="rowEdit.penerimaan"
                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none font-mono"
                           placeholder="0">
                </div>
            </div>

            <div class="mt-5 flex items-center justify-end gap-2 pt-4 border-t border-gray-100">
                <button type="button" @click="showRowEditModal=false"
                        class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                    Batal
                </button>
                <button type="button" @click="saveRowEdit()" :disabled="rowSaving"
                        class="px-5 py-2 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors shadow-sm disabled:opacity-60">
                    <span x-text="rowSaving ? 'Menyimpan...' : 'Simpan Perubahan'"></span>
                </button>
            </div>
        </div>
    </div>

    <div class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
         x-show="showRowDeleteModal" x-cloak
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         @click.self="showRowDeleteModal = false">
        <div class="bg-white rounded-xl shadow-2xl max-w-sm w-full p-6 border border-gray-100" @click.stop
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
            <div class="flex flex-col items-center gap-3 text-center">
                <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center">
                    <i data-lucide="trash-2" class="w-6 h-6 text-red-500"></i>
                </div>
                <h3 class="text-base font-bold text-gray-900">Hapus Chemical?</h3>
                <p class="text-sm text-gray-500">
                    Chemical <strong x-text="rowDeleteTarget.name" class="text-gray-800"></strong>
                    beserta semua data log harian dan bulanannya akan dihapus permanen dan tidak dapat dikembalikan.
                </p>
            </div>
            <div class="flex items-center justify-center gap-2 mt-5">
                <button type="button" @click="showRowDeleteModal=false"
                        class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                    Batal
                </button>
                <button type="button" @click="doRowDelete()" :disabled="rowDeleting"
                        class="px-5 py-2 text-sm font-semibold text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors shadow-sm disabled:opacity-60">
                    <span x-text="rowDeleting ? 'Menghapus...' : 'Ya, Hapus'"></span>
                </button>
            </div>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
function logChemicalMatrix() {
    return {
        showAddDateModal: false,
        showAddChemicalModal: false,
        showFilterModal: false,

        showRowEditModal: false,
        rowSaving: false,
        rowEdit: { id: null, name: '', unit: '', saldo_awal: 0, penerimaan: 0, period_month: '' },

        showRowDeleteModal: false,
        rowDeleting: false,
        rowDeleteTarget: { id: null, name: '' },
        deletedRowIds: [],

        openRowEdit(row) {
            const formatNum = (v) => {
                if (v === null || v === undefined || v === '') return '';
                const clean = String(v).replace(',', '.');
                const n = parseFloat(clean);
                if (isNaN(n) || n === 0) return '0';
                if (Number.isInteger(n)) return n.toString();
                return n.toFixed(4).replace(/\.?0+$/, '').replace('.', ',');
            };
            this.rowEdit = {
                ...row,
                saldo_awal: formatNum(row.saldo_awal),
                penerimaan: formatNum(row.penerimaan),
            };
            this.showRowEditModal = true;
        },

        async saveRowEdit() {
            this.rowSaving = true;
            const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
            const base = '{{ url('/transactions') }}';
            try {
                const cleanSaldo = String(this.rowEdit.saldo_awal || '').replace(',', '.').trim();
                const cleanPenerimaan = String(this.rowEdit.penerimaan || '').replace(',', '.').trim();

                await fetch(base + '/update-chemical', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
                    body: JSON.stringify({ chemical_id: this.rowEdit.id, field: 'chemical_name', value: this.rowEdit.name })
                });
                await fetch(base + '/update-chemical', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
                    body: JSON.stringify({ chemical_id: this.rowEdit.id, field: 'unit', value: this.rowEdit.unit })
                });
                await fetch(base + '/update-balance', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
                    body: JSON.stringify({ chemical_id: this.rowEdit.id, period_month: this.rowEdit.period_month, field: 'saldo_awal', value: cleanSaldo })
                });
                const res = await fetch(base + '/update-balance', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
                    body: JSON.stringify({ chemical_id: this.rowEdit.id, period_month: this.rowEdit.period_month, field: 'penerimaan', value: cleanPenerimaan })
                });
                const data = await res.json();

                const saldoEl = document.getElementById('saldo-akhir-m-' + this.rowEdit.id);
                if (saldoEl && data.saldo_akhir !== undefined) saldoEl.textContent = data.saldo_akhir;

                this.showRowEditModal = false;
                sessionStorage.setItem('idx_keep_row', this.rowEdit.id);
                sessionStorage.setItem('idx_scroll_y', window.scrollY);
                window.location.reload();
            } catch(e) {
                alert('Gagal menyimpan. Silakan coba lagi.');
            } finally {
                this.rowSaving = false;
            }
        },

        confirmRowDelete(id, name) {
            this.rowDeleteTarget = { id, name };
            this.showRowDeleteModal = true;
        },

        async doRowDelete() {
            this.rowDeleting = true;
            const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
            try {
                const resp = await fetch(`/transactions/chemicals/${this.rowDeleteTarget.id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' }
                });
                if (resp.ok) {
                    const row = document.getElementById('row-' + this.rowDeleteTarget.id);
                    if (row) row.remove();
                    this.showRowDeleteModal = false;
                } else {
                    alert('Gagal menghapus. Silakan coba lagi.');
                }
            } catch(e) {
                alert('Error saat menghapus.');
            } finally {
                this.rowDeleting = false;
            }
        }
    }
}

function monthNav() {
    return {
        currentMonth: '{{ $periodMonth }}',
        selectedMonth: '{{ $periodMonth }}',
        loading: false,
        goToMonth(url) {
            this.loading = true;
            setTimeout(() => { window.location.href = url; }, 80);
        }
    }
}

function unitPillSelector(chemicalId, initialUnit) {
    return {
        chemicalId: chemicalId,
        unit: initialUnit,
        openDropdown: false,
        unitClass(u) {
            const low = (u || '').toLowerCase();
            if (low === 'g') {
                return 'bg-amber-100/90 text-amber-900 border-amber-400/80 hover:bg-amber-200';
            } else if (low === 'ml') {
                return 'bg-orange-100/90 text-orange-900 border-orange-400/80 hover:bg-orange-200';
            } else if (low === 'l') {
                return 'bg-blue-100/90 text-blue-900 border-blue-400/80 hover:bg-blue-200';
            } else if (low === 'kg') {
                return 'bg-emerald-100/90 text-emerald-900 border-emerald-400/80 hover:bg-emerald-200';
            } else if (low === 'pcs') {
                return 'bg-purple-100/90 text-purple-900 border-purple-400/80 hover:bg-purple-200';
            }
            return 'bg-gray-100 text-gray-500 border-gray-300 hover:bg-gray-200';
        },
        selectUnit(newUnit) {
            this.unit = newUnit;
            this.openDropdown = false;
            fetch("{{ route('transactions.update-chemical') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Accept": "application/json"
                },
                body: JSON.stringify({
                    chemical_id: this.chemicalId,
                    field: 'unit',
                    value: this.unit
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    this.unit = data.value;
                }
            })
            .catch(err => console.error("Failed to update unit:", err));
        }
    }
}

function chemicalNameCell(chemicalId, initialName) {
    return {
        chemicalId: chemicalId,
        name: initialName,
        editing: false,
        startEdit() {
            this.editing = true;
            this.$nextTick(() => {
                if (this.$refs.chemNameInput) {
                    this.$refs.chemNameInput.focus();
                    this.$refs.chemNameInput.select();
                }
            });
        },
        saveEdit() {
            this.editing = false;
            fetch("{{ route('transactions.update-chemical') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Accept": "application/json"
                },
                body: JSON.stringify({
                    chemical_id: this.chemicalId,
                    field: 'chemical_name',
                    value: this.name
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    this.name = data.value;
                }
            })
            .catch(err => console.error("Failed to update chemical name:", err));
        }
    }
}

function analystTakePill(logDateId, takeField, initialName) {
    return {
        logDateId: logDateId,
        field: takeField,
        name: initialName || '',
        openDropdown: false,
        showCustomInput: false,
        customName: '',
        selectAnalyst(selected) {
            this.name = selected;
            this.openDropdown = false;
            this.showCustomInput = false;
            this.save();
        },
        saveCustom() {
            if (this.customName.trim()) {
                this.name = this.customName.trim();
                this.openDropdown = false;
                this.showCustomInput = false;
                this.customName = '';
                this.save();
            }
        },
        save() {
            fetch("{{ route('transactions.update-analyst') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Accept": "application/json"
                },
                body: JSON.stringify({
                    log_date_id: this.logDateId,
                    field: this.field,
                    analyst_name: this.name
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    this.name = data.analyst_name || '';
                }
            })
            .catch(err => console.error("Failed to update analyst:", err));
        }
    }
}

function usageCell(chemicalId, logDateId, field, initialVal) {
    return {
        chemicalId: chemicalId,
        logDateId: logDateId,
        field: field,
        val: initialVal,
        editing: false,
        startEdit() {
            this.editing = true;
            this.$nextTick(() => {
                if (this.$refs.inputEl) {
                    this.$refs.inputEl.focus();
                    this.$refs.inputEl.select();
                }
            });
        },
        saveEdit() {
            this.editing = false;
            const cleanVal = this.val !== null && this.val !== undefined && this.val !== ''
                ? String(this.val).replace(',', '.').trim()
                : '';
            fetch("{{ route('transactions.update-cell') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Accept": "application/json"
                },
                body: JSON.stringify({
                    chemical_id: this.chemicalId,
                    log_date_id: this.logDateId,
                    field: this.field,
                    value: cleanVal
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    this.val = data.value;
                    const pEl = document.getElementById('pengeluaran-m-' + this.chemicalId);
                    const sEl = document.getElementById('saldo-akhir-m-' + this.chemicalId);
                    if (pEl) pEl.innerText = data.pengeluaran;
                    if (sEl) sEl.innerText = data.saldo_akhir;

                    const pElD = document.getElementById('pengeluaran-d-' + this.chemicalId);
                    const sElD = document.getElementById('saldo-akhir-d-' + this.chemicalId);
                    if (pElD) pElD.innerText = data.pengeluaran;
                    if (sElD) sElD.innerText = data.saldo_akhir;
                }
            })
            .catch(err => {
                console.error("Failed to update cell:", err);
            });
        }
    }
}

function balanceCell(chemicalId, periodMonth, field, initialVal) {
    return {
        chemicalId: chemicalId,
        periodMonth: periodMonth,
        field: field,
        val: initialVal,
        editing: false,
        startEdit() {
            this.editing = true;
            this.$nextTick(() => {
                if (this.$refs.inputEl) {
                    this.$refs.inputEl.focus();
                    this.$refs.inputEl.select();
                }
            });
        },
        saveEdit() {
            this.editing = false;
            const cleanVal = this.val !== null && this.val !== undefined && this.val !== ''
                ? String(this.val).replace(',', '.').trim()
                : '';
            fetch("{{ route('transactions.update-balance') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Accept": "application/json"
                },
                body: JSON.stringify({
                    chemical_id: this.chemicalId,
                    period_month: this.periodMonth,
                    field: this.field,
                    value: cleanVal
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    this.val = data.value;
                    const pEl = document.getElementById('pengeluaran-m-' + this.chemicalId);
                    const sEl = document.getElementById('saldo-akhir-m-' + this.chemicalId);
                    if (pEl) pEl.innerText = data.pengeluaran;
                    if (sEl) sEl.innerText = data.saldo_akhir;

                    const pElD = document.getElementById('pengeluaran-d-' + this.chemicalId);
                    const sElD = document.getElementById('saldo-akhir-d-' + this.chemicalId);
                    if (pElD) pElD.innerText = data.pengeluaran;
                    if (sElD) sElD.innerText = data.saldo_akhir;
                }
            })
            .catch(err => {
                console.error("Failed to update balance:", err);
            });
        }
    }
}
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const keepId = sessionStorage.getItem('idx_keep_row');
    const scrollY = sessionStorage.getItem('idx_scroll_y');
    if (keepId) {
        sessionStorage.removeItem('idx_keep_row');
        sessionStorage.removeItem('idx_scroll_y');
        const row = document.getElementById('row-' + keepId);
        if (row) {
            row.scrollIntoView({ behavior: 'smooth', block: 'center' });
            row.classList.add('ring-2', 'ring-yellow-400', 'ring-inset');
            setTimeout(function () {
                row.classList.remove('ring-2', 'ring-yellow-400', 'ring-inset');
            }, 2500);
        } else if (scrollY) {
            window.scrollTo(0, parseInt(scrollY));
        }
    }

    @if(request('highlight'))
    const highlightId = {{ (int)request('highlight') }};
    const highlightRow = document.getElementById('row-' + highlightId);
    if (highlightRow) {
        highlightRow.scrollIntoView({ behavior: 'smooth', block: 'center' });
        setTimeout(function () {
            highlightRow.style.transition = 'background-color 1s ease';
            highlightRow.style.backgroundColor = '';
            highlightRow.classList.remove('ring-2', 'ring-yellow-400', 'ring-inset');
        }, 2500);
    }
    @endif
});
</script>
@endpush

