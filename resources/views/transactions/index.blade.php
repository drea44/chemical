@extends('layouts.app')

@php
    $title      = 'Log Chemical';
    $breadcrumb = [['label' => 'Log Chemical', 'url' => route('transactions.index')]];
@endphp

@section('title', 'Log Chemical')

@push('styles')
<style>
    /* Freeze Pane styles for Log Chemical matrix (Kolom 1-6 Terkunci) */
    .table-freeze {
        border-collapse: separate !important;
        border-spacing: 0 !important;
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
        width: 260px !important;
        min-width: 260px !important;
        max-width: 260px !important;
    }

    .sticky-col-saldo {
        position: sticky !important;
        left: 308px !important;
        width: 110px !important;
        min-width: 110px !important;
        max-width: 110px !important;
    }

    .sticky-col-unit {
        position: sticky !important;
        left: 418px !important;
        width: 68px !important;
        min-width: 68px !important;
        max-width: 68px !important;
    }

    .sticky-col-jumlah-header {
        position: sticky !important;
        left: 486px !important;
        width: 180px !important;
        min-width: 180px !important;
        max-width: 180px !important;
        border-right: 2.5px solid #000000 !important;
    }

    .sticky-col-penerimaan {
        position: sticky !important;
        left: 486px !important;
        width: 90px !important;
        min-width: 90px !important;
        max-width: 90px !important;
    }

    .sticky-col-pengeluaran {
        position: sticky !important;
        left: 576px !important;
        width: 90px !important;
        min-width: 90px !important;
        max-width: 90px !important;
        border-right: 2.5px solid #000000 !important;
        box-shadow: 4px 0 8px -2px rgba(0, 0, 0, 0.22);
    }

    /* Header z-index higher than body */
    thead th.sticky-col {
        z-index: 25 !important;
        background-color: #fedac2 !important;
    }

    tbody td.sticky-col {
        z-index: 10 !important;
    }

    /* Row hover effect maintains background on sticky cells */
    tr.matrix-row:hover td.sticky-col {
        background-color: #fef3c7 !important;
    }

    /* Custom horizontal scrollbar */
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

    {{-- Top Tab Navigation --}}
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

    {{-- Top Header Section --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Log Chemical</h1>
            <p class="text-sm text-gray-500 mt-0.5">Chemical inventory & daily usage record - {{ $monthTitle }}</p>
        </div>
        {{-- Month Navigation --}}
        <div class="flex items-center gap-2 self-start sm:self-auto" x-data="monthNav()">
            {{-- Prev Month --}}
            <a href="{{ route('transactions.index', ['month' => $prevMonth]) }}"
               @click.prevent="goToMonth('{{ route('transactions.index', ['month' => $prevMonth]) }}')"
               class="inline-flex items-center gap-1 px-3 py-1.5 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors shadow-xs"
               title="Bulan sebelumnya: {{ \Carbon\Carbon::createFromFormat('Y-m', $prevMonth)->format('F Y') }}">
                <i data-lucide="chevron-left" class="w-4 h-4"></i>
                <span class="hidden sm:inline">{{ \Carbon\Carbon::createFromFormat('Y-m', $prevMonth)->format('M Y') }}</span>
            </a>

            {{-- Month Select + Apply Button --}}
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
                {{-- Apply button — hanya muncul jika bulan berubah --}}
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

            {{-- Next Month --}}
            <a href="{{ route('transactions.index', ['month' => $nextMonth]) }}"
               @click.prevent="goToMonth('{{ route('transactions.index', ['month' => $nextMonth]) }}')"
               class="inline-flex items-center gap-1 px-3 py-1.5 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors shadow-xs"
               title="Bulan berikutnya: {{ \Carbon\Carbon::createFromFormat('Y-m', $nextMonth)->format('F Y') }}">
                <span class="hidden sm:inline">{{ \Carbon\Carbon::createFromFormat('Y-m', $nextMonth)->format('M Y') }}</span>
                <i data-lucide="chevron-right" class="w-4 h-4"></i>
            </a>
            <span class="text-xs text-gray-400 ml-1">{{ now()->format('d M Y, H:i') }}</span>

            {{-- Loading Overlay --}}
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

    {{-- Flash Messages --}}
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

    {{-- Controls Bar --}}
    <div class="flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-3 pt-1">
        {{-- Left: Search & Filter --}}
        <div class="flex flex-wrap items-center gap-2.5">
            {{-- Search --}}
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

            {{-- Filter Button --}}
            <button type="button" @click="showFilterModal = true"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors shadow-xs cursor-pointer">
                <i data-lucide="filter" class="w-3.5 h-3.5 text-gray-500"></i>
                <span>Filter</span>
                <span class="text-xs text-gray-400 font-normal pl-0.5">({{ $chemicals->total() }})</span>
            </button>
        </div>

        {{-- Right: Add Chemical & Add Date --}}
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

    {{-- TABEL CHEMICAL LOG PERSIS GAMBAR REFERENSI (FREEZE PANE) --}}
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
                        onclick="document.getElementById('matrix-scroll-wrapper').scrollTo({ left: document.getElementById('matrix-scroll-wrapper').scrollWidth, behavior: 'smooth' })"
                        class="px-2.5 py-1 bg-white border border-gray-300 hover:bg-gray-50 rounded text-xs font-semibold text-gray-700 shadow-2xs cursor-pointer inline-flex items-center gap-1 transition-colors">
                    Ke Saldo Akhir <i data-lucide="chevrons-right" class="w-3.5 h-3.5"></i>
                </button>
            </div>
        </div>

        <div class="border border-black overflow-hidden shadow-sm bg-white">
            <div id="matrix-scroll-wrapper" class="overflow-x-auto freeze-scroll-container">
                <table class="w-full text-left table-freeze" style="font-family: Arial, Helvetica, sans-serif; border-collapse: separate; border-spacing: 0;">
                    <thead>
                        {{-- ROW 1 --}}
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
                            <th rowspan="5" class="sticky-col sticky-col-unit py-2.5 px-2 text-center text-sm font-bold text-gray-900 border border-black align-middle" style="background-color: #fedac2;">
                                Unit
                            </th>
                            {{-- JUMLAH Header (Spans Rows 1-3, Colspan 2) --}}
                            <th colspan="2" rowspan="3" class="sticky-col sticky-col-jumlah-header py-2 px-3 text-center text-sm font-bold text-gray-900 border border-black align-middle" style="background-color: #fedac2;">
                                Jumlah
                            </th>

                            {{-- DATE TAKEN HEADERS: Row 1 --}}
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

                            {{-- SALDO AKHIR --}}
                            <th rowspan="5" class="py-2.5 px-3 text-center text-sm font-bold text-gray-900 border border-black min-w-[110px] w-[110px] align-middle" title="SALDO AKHIR" style="background-color: #fedac2;">
                                <div>Saldo Akhir</div>
                                <div class="text-[10px] font-normal text-gray-600">Awal + Masuk - Keluar</div>
                                <span class="sr-only">SALDO AKHIR</span>
                            </th>
                        </tr>

                        {{-- ROW 2: DATE VALUE (e.g. 01/03/2026) --}}
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

                        {{-- ROW 3: ANALYST LABEL --}}
                        <tr style="background-color: #fedac2;">
                            @foreach($logDates as $ld)
                                <th colspan="3" class="py-1 px-2 text-center text-xs font-bold text-gray-900 border border-black tracking-wider">
                                    Analyst
                                </th>
                            @endforeach
                        </tr>
                        @endif

                        {{-- ROW 4: SUB-COLUMNS UNDER JUMLAH (PENERIMAAN & PENGELUARAN) + 3 ANALYST DROPDOWN PILLS --}}
                        <tr style="background-color: #fedac2;">
                            {{-- Penerimaan & Pengeluaran (under Jumlah, spanning rows 4 & 5) --}}
                            <th rowspan="2" class="sticky-col sticky-col-penerimaan py-2 px-2 text-center text-xs font-bold text-gray-900 border border-black align-middle" title="PENERIMAAN" style="background-color: #fedac2;">
                                <span>Penerimaan</span>
                                <span class="sr-only">PENERIMAAN</span>
                            </th>
                            <th rowspan="2" class="sticky-col sticky-col-pengeluaran py-2 px-2 text-center text-xs font-bold text-gray-900 border border-black align-middle" title="PENGELUARAN" style="background-color: #fedac2;">
                                <span>Pengeluaran</span>
                                <span class="sr-only">PENGELUARAN</span>
                            </th>

                            {{-- 3 Analyst Pill Selectors for Date --}}
                            @foreach($logDates as $ld)
                                {{-- Take 1 Analyst Pill --}}
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

                                {{-- Take 2 Analyst Pill --}}
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

                                {{-- Take 3 Analyst Pill --}}
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

                        {{-- ROW 5: SUB-COLUMNS TAKE 1, TAKE 2, TAKE 3 --}}
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
                                {{-- NO --}}
                                <td class="sticky-col sticky-col-no py-1.5 px-2 text-center text-sm text-gray-900 border border-black font-normal" style="background-color: {{ $cellBg }};">
                                    {{ ($chemicals->currentPage() - 1) * $chemicals->perPage() + $idx + 1 }}
                                </td>

                                {{-- CHEMICAL NAME --}}
                                <td class="sticky-col sticky-col-name py-1.5 px-3 text-left text-sm text-gray-900 border border-black font-normal" style="background-color: {{ $cellBg }};"
                                    x-data="chemicalNameCell({{ $chem->id }}, '{{ addslashes($chem->chemical_name) }}')"
                                    @dblclick="startEdit()">
                                    <template x-if="!editing">
                                        <div class="cursor-pointer truncate hover:text-blue-700" title="Klik 2x untuk edit nama">
                                            <span x-text="name">{{ $chem->chemical_name }}</span>
                                        </div>
                                    </template>
                                    <template x-if="editing">
                                        <input type="text" x-model="name" x-ref="chemNameInput"
                                               @blur="saveEdit()" @keydown.enter="saveEdit()" @keydown.escape="editing = false"
                                               class="w-full text-sm px-1 py-0.5 border border-blue-600 bg-white rounded focus:outline-none">
                                    </template>
                                </td>

                                {{-- SALDO AWAL SEMENTARA (ML/G) --}}
                                <td class="sticky-col sticky-col-saldo py-1.5 px-2 text-right text-sm text-gray-900 border border-black font-normal" style="background-color: {{ $cellBg }};"
                                    x-data="balanceCell({{ $chem->id }}, '{{ $periodMonth }}', 'saldo_awal', {{ $valSaldo }})"
                                    @dblclick="startEdit()">
                                    <template x-if="!editing">
                                        <div class="cursor-pointer hover:bg-amber-100/70 px-1 py-0.5 rounded text-right min-h-[22px]" title="Klik 2x untuk edit saldo awal">
                                            <span x-text="val > 0 ? (val % 1 === 0 ? val.toLocaleString('id-ID') : val.toString().replace('.', ',')) : ''"></span>
                                        </div>
                                    </template>
                                    <template x-if="editing">
                                        <input type="number" step="any" x-model="val" x-ref="inputEl"
                                               @blur="saveEdit()" @keydown.enter="saveEdit()" @keydown.escape="editing = false"
                                               class="w-24 text-right text-sm px-1 py-0.5 border border-blue-600 bg-white rounded focus:outline-none">
                                    </template>
                                </td>

                                {{-- UNIT (Pill badge dropdown) --}}
                                <td class="sticky-col sticky-col-unit py-1 px-2 text-center border border-black" style="background-color: {{ $cellBg }};"
                                    x-data="unitPillSelector({{ $chem->id }}, '{{ strtolower(trim($chem->unit ?? '')) }}')">
                                    <div class="relative inline-block text-left">
                                        <button type="button" @click="openDropdown = !openDropdown"
                                                :class="unitClass(unit)"
                                                class="inline-flex items-center justify-between gap-1.5 px-2.5 py-0.5 text-xs font-semibold rounded-md border shadow-2xs transition-all cursor-pointer w-16">
                                            <span x-text="unit ? unit : '-'"></span>
                                            <svg class="w-3 h-3 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        </button>

                                        {{-- Dropdown options --}}
                                        <div x-show="openDropdown" @click.away="openDropdown = false"
                                             class="absolute left-0 mt-1 w-20 bg-white border border-gray-300 rounded shadow-lg z-40 py-1 text-left"
                                             x-cloak>
                                            <button type="button" @click="selectUnit('g')" class="w-full text-left px-3 py-1 text-xs hover:bg-yellow-100 text-yellow-900 font-semibold">g</button>
                                            <button type="button" @click="selectUnit('ml')" class="w-full text-left px-3 py-1 text-xs hover:bg-orange-100 text-orange-900 font-semibold">ml</button>
                                            <button type="button" @click="selectUnit('L')" class="w-full text-left px-3 py-1 text-xs hover:bg-blue-100 text-blue-900 font-semibold">L</button>
                                            <button type="button" @click="selectUnit('kg')" class="w-full text-left px-3 py-1 text-xs hover:bg-green-100 text-green-900 font-semibold">kg</button>
                                            <button type="button" @click="selectUnit('')" class="w-full text-left px-3 py-1 text-xs hover:bg-gray-100 text-gray-500 italic">(kosong)</button>
                                        </div>
                                    </div>
                                </td>

                                {{-- JUMLAH: PENERIMAAN (Editable inline) --}}
                                <td class="sticky-col sticky-col-penerimaan py-1.5 px-2 text-center text-sm text-gray-900 border border-black font-normal" style="background-color: {{ $cellBg }};"
                                    x-data="balanceCell({{ $chem->id }}, '{{ $periodMonth }}', 'penerimaan', {{ $valPenerimaan }})"
                                    @dblclick="startEdit()">
                                    <template x-if="!editing">
                                        <div class="cursor-pointer hover:bg-amber-100/70 px-1 py-0.5 rounded text-center min-h-[22px]" title="Klik 2x untuk input penerimaan">
                                            <span x-text="val > 0 ? (val % 1 === 0 ? val.toLocaleString('id-ID') : val.toString().replace('.', ',')) : ''"></span>
                                        </div>
                                    </template>
                                    <template x-if="editing">
                                        <input type="number" step="any" x-model="val" x-ref="inputEl"
                                               @blur="saveEdit()" @keydown.enter="saveEdit()" @keydown.escape="editing = false"
                                               class="w-20 text-center text-sm px-1 py-0.5 border border-blue-600 bg-white rounded focus:outline-none">
                                    </template>
                                </td>

                                {{-- JUMLAH: PENGELUARAN (Shows 0 or usage amount, editable inline) --}}
                                <td class="sticky-col sticky-col-pengeluaran py-1.5 px-2 text-center text-sm text-gray-900 border border-black font-normal" style="background-color: {{ $cellBg }};">
                                    <span id="pengeluaran-m-{{ $chem->id }}">
                                        {{ $valPengeluaran > 0 ? (floor($valPengeluaran) == $valPengeluaran ? number_format($valPengeluaran, 0, ',', '.') : str_replace('.', ',', (string)$valPengeluaran)) : '0' }}
                                    </span>
                                </td>

                                {{-- DATE TAKEN: TAKE 1, TAKE 2, TAKE 3 --}}
                                @foreach($logDates as $ld)
                                    @php
                                        $usage = $row['usages'][$ld->id] ?? ['take_1' => null, 'take_2' => null, 'take_3' => null];
                                        $t1 = $usage['take_1'];
                                        $t2 = $usage['take_2'];
                                        $t3 = $usage['take_3'];
                                    @endphp

                                    {{-- Take 1 --}}
                                    <td class="py-1.5 px-1 text-center text-sm text-gray-900 border border-black font-normal min-w-[55px] w-[55px]"
                                        x-data="usageCell({{ $chem->id }}, {{ $ld->id }}, 'take_1', {{ $t1 !== null ? $t1 : 'null' }})"
                                        @dblclick="startEdit()">
                                        <template x-if="!editing">
                                            <div class="cursor-pointer hover:bg-amber-100/70 px-1 py-0.5 rounded text-center min-h-[22px]" title="Klik 2x untuk input Take 1">
                                                <span x-text="val !== null && val > 0 ? (val % 1 === 0 ? val.toLocaleString('id-ID') : val.toString().replace('.', ',')) : ''"></span>
                                            </div>
                                        </template>
                                        <template x-if="editing">
                                            <input type="number" step="any" x-model="val" x-ref="inputEl"
                                                   @blur="saveEdit()" @keydown.enter="saveEdit()" @keydown.escape="editing = false"
                                                   class="w-16 text-center text-sm px-1 py-0.5 border border-blue-600 bg-white rounded focus:outline-none">
                                        </template>
                                    </td>

                                    {{-- Take 2 --}}
                                    <td class="py-1.5 px-1 text-center text-sm text-gray-900 border border-black font-normal min-w-[55px] w-[55px]"
                                        x-data="usageCell({{ $chem->id }}, {{ $ld->id }}, 'take_2', {{ $t2 !== null ? $t2 : 'null' }})"
                                        @dblclick="startEdit()">
                                        <template x-if="!editing">
                                            <div class="cursor-pointer hover:bg-amber-100/70 px-1 py-0.5 rounded text-center min-h-[22px]" title="Klik 2x untuk input Take 2">
                                                <span x-text="val !== null && val > 0 ? (val % 1 === 0 ? val.toLocaleString('id-ID') : val.toString().replace('.', ',')) : ''"></span>
                                            </div>
                                        </template>
                                        <template x-if="editing">
                                            <input type="number" step="any" x-model="val" x-ref="inputEl"
                                                   @blur="saveEdit()" @keydown.enter="saveEdit()" @keydown.escape="editing = false"
                                                   class="w-16 text-center text-sm px-1 py-0.5 border border-blue-600 bg-white rounded focus:outline-none">
                                        </template>
                                    </td>

                                    {{-- Take 3 --}}
                                    <td class="py-1.5 px-1 text-center text-sm text-gray-900 border border-black font-normal min-w-[55px] w-[55px]"
                                        x-data="usageCell({{ $chem->id }}, {{ $ld->id }}, 'take_3', {{ $t3 !== null ? $t3 : 'null' }})"
                                        @dblclick="startEdit()">
                                        <template x-if="!editing">
                                            <div class="cursor-pointer hover:bg-amber-100/70 px-1 py-0.5 rounded text-center min-h-[22px]" title="Klik 2x untuk input Take 3">
                                                <span x-text="val !== null && val > 0 ? (val % 1 === 0 ? val.toLocaleString('id-ID') : val.toString().replace('.', ',')) : ''"></span>
                                            </div>
                                        </template>
                                        <template x-if="editing">
                                            <input type="number" step="any" x-model="val" x-ref="inputEl"
                                                   @blur="saveEdit()" @keydown.enter="saveEdit()" @keydown.escape="editing = false"
                                                   class="w-16 text-center text-sm px-1 py-0.5 border border-blue-600 bg-white rounded focus:outline-none">
                                        </template>
                                    </td>
                                @endforeach

                                {{-- SALDO AKHIR --}}
                                <td class="py-1.5 px-3 text-right text-sm font-bold text-gray-900 border border-black min-w-[110px] w-[110px]">
                                    <span id="saldo-akhir-m-{{ $chem->id }}">
                                        {{ number_format($valSaldoAkhir, 0, ',', '.') }}
                                    </span>
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

        {{-- Table Footer Info --}}
        <div class="flex flex-col sm:flex-row items-center justify-between gap-2 px-1 pt-1 text-xs text-gray-500">
            <div class="flex items-center gap-1.5">
                <i data-lucide="edit-3" class="w-3.5 h-3.5 text-gray-400"></i>
                <span>Klik dua kali pada cell (nama, saldo awal, penerimaan) untuk edit langsung. Unit dapat diganti via dropdown pill.</span>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                {{-- Per-page control --}}
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

    {{-- MODAL 1: ADD DATE --}}
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

    {{-- MODAL 2: ADD CHEMICAL QUICKLY TO LOG --}}
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
                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wide mb-1">Satuan / Unit <span class="text-red-500">*</span></label>
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

    {{-- MODAL 3: FILTER --}}
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

</div>

@endsection

@push('scripts')
<script>
function logChemicalMatrix() {
    return {
        showAddDateModal: false,
        showAddChemicalModal: false,
        showFilterModal: false,
    }
}

function monthNav() {
    return {
        currentMonth: '{{ $periodMonth }}',
        selectedMonth: '{{ $periodMonth }}',
        loading: false,
        goToMonth(url) {
            this.loading = true;
            // Beri sedikit delay agar overlay tampil sebelum navigate
            setTimeout(() => { window.location.href = url; }, 80);
        }
    }
}

// Unit Pill component with dropdown
function unitPillSelector(chemicalId, initialUnit) {
    return {
        chemicalId: chemicalId,
        unit: initialUnit,
        openDropdown: false,
        unitClass(u) {
            const low = (u || '').toLowerCase();
            if (low === 'g') {
                return 'bg-[#fef08a] text-[#854d0e] border-[#eab308] hover:bg-[#fde047]';
            } else if (low === 'ml') {
                return 'bg-[#fed7aa] text-[#9a3412] border-[#f97316] hover:bg-[#fdba74]';
            } else if (low === 'l') {
                return 'bg-blue-100 text-blue-800 border-blue-300 hover:bg-blue-200';
            } else if (low === 'kg') {
                return 'bg-emerald-100 text-emerald-800 border-emerald-300 hover:bg-emerald-200';
            }
            return 'bg-gray-100 text-gray-400 border-gray-300 hover:bg-gray-200';
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

// Cell component for Chemical Name
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

// Cell component for Analyst Take Pill Dropdown
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

// Cell component for Daily Takes (Take 1, Take 2, Take 3)
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
                    value: this.val
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

// Cell component for Saldo Awal and Penerimaan
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
                    value: this.val
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

@if(request('highlight'))
<script>
document.addEventListener('DOMContentLoaded', function () {
    const highlightId = {{ (int)request('highlight') }};
    const row = document.getElementById('row-' + highlightId);
    if (row) {
        // Smooth scroll to the row
        row.scrollIntoView({ behavior: 'smooth', block: 'center' });

        // Pulse animation: fade out the yellow after 2.5s
        setTimeout(function () {
            row.style.transition = 'background-color 1s ease';
            row.style.backgroundColor = '';
            row.classList.remove('ring-2', 'ring-yellow-400', 'ring-inset');
        }, 2500);
    }
});
</script>
@endif
@endpush
