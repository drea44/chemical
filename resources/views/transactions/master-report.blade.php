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

    .report-table tbody {
        counter-reset: rownum {{ ($chemicals->firstItem() ?? 1) - 1 }};
    }
    .report-table tbody tr td.td-rownum::before {
        counter-increment: rownum;
        content: counter(rownum);
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

    .modal-overlay {
        position: fixed; inset: 0; background: rgba(0,0,0,0.5);
        display: flex; align-items: center; justify-content: center;
        z-index: 9999; backdrop-filter: blur(3px);
    }
    .modal-box {
        background: #fff; border-radius: 1rem;
        box-shadow: 0 25px 70px rgba(0,0,0,0.22);
        width: 100%; max-width: 560px; padding: 1.75rem;
        animation: modalIn 0.22s cubic-bezier(.16,1,.3,1);
        max-height: 92vh; overflow-y: auto;
    }
    @keyframes modalIn {
        from { opacity:0; transform: scale(0.94) translateY(-12px); }
        to   { opacity:1; transform: scale(1) translateY(0); }
    }
    .form-label  { display:block; font-size:0.75rem; font-weight:600; color:#374151; margin-bottom:0.3rem; letter-spacing:0.01em; }
    .form-input  { width:100%; border:1.5px solid #e5e7eb; border-radius:0.5rem; padding:0.5rem 0.75rem; font-size:0.875rem; outline:none; transition:border-color .15s, box-shadow .15s; background:#fafafa; color:#111827; }
    .form-input:focus { border-color:#f59e0b; box-shadow:0 0 0 3px rgba(245,158,11,0.15); background:#fff; }

    .month-grid {
        width: 100%;
        border-collapse: collapse;
        border-radius: 0.5rem;
        overflow: hidden;
        border: 1.5px solid #e5e7eb;
        font-size: 0.8125rem;
    }
    .month-grid thead th {
        background: #fef3c7;
        color: #78350f;
        font-weight: 700;
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        padding: 0.5rem 0.75rem;
        border-bottom: 1.5px solid #fcd34d;
        text-align: left;
    }
    .month-grid thead th:nth-child(2),
    .month-grid thead th:nth-child(3) { text-align: center; }
    .month-grid tbody tr { border-bottom: 1px solid #f3f4f6; }
    .month-grid tbody tr:last-child { border-bottom: none; }
    .month-grid tbody tr:nth-child(odd)  { background: #fffbeb; }
    .month-grid tbody tr:nth-child(even) { background: #ffffff; }
    .month-grid tbody tr:hover { background: #fef9c3; }
    .month-grid td {
        padding: 0.45rem 0.6rem;
        vertical-align: middle;
    }
    .month-grid td:first-child {
        font-weight: 600;
        color: #374151;
        width: 72px;
        white-space: nowrap;
    }
    .month-grid td:nth-child(2) { width: auto; }
    .month-grid td:nth-child(3) { width: 100px; }
    .month-grid .mg-input {
        width: 100%;
        border: 1.5px solid #e5e7eb;
        border-radius: 0.4rem;
        padding: 0.375rem 0.6rem;
        font-size: 0.8125rem;
        outline: none;
        transition: border-color .15s, box-shadow .15s;
        background: #fff;
        color: #111827;
        font-family: ui-monospace, monospace;
    }
    .month-grid .mg-input:focus {
        border-color: #f59e0b;
        box-shadow: 0 0 0 3px rgba(245,158,11,0.12);
    }
    .month-grid .mg-unit {
        width: 100%;
        border: 1.5px solid #e5e7eb;
        border-radius: 0.4rem;
        padding: 0.375rem 0.5rem;
        font-size: 0.8125rem;
        outline: none;
        transition: border-color .15s, box-shadow .15s;
        background: #fff;
        color: #6b7280;
        text-align: center;
    }
    .month-grid .mg-unit:focus {
        border-color: #f59e0b;
        box-shadow: 0 0 0 3px rgba(245,158,11,0.12);
        color: #111827;
    }

    .btn-save    { display:inline-flex; align-items:center; gap:0.4rem; padding:0.55rem 1.25rem; background:linear-gradient(135deg,#f59e0b,#d97706); color:#fff; border-radius:0.5rem; font-size:0.875rem; font-weight:700; border:none; cursor:pointer; transition:opacity .15s, transform .1s; box-shadow:0 2px 8px rgba(217,119,6,0.35); }
    .btn-save:hover { opacity:.9; transform:translateY(-1px); }
    .btn-save:active { transform:translateY(0); }
    .btn-cancel  { display:inline-flex; align-items:center; gap:0.4rem; padding:0.55rem 1.25rem; background:#f9fafb; color:#374151; border-radius:0.5rem; font-size:0.875rem; font-weight:600; border:1.5px solid #e5e7eb; cursor:pointer; transition:background .15s; }
    .btn-cancel:hover { background:#f3f4f6; color:#111827; }
    .btn-edit    { display:inline-flex; align-items:center; gap:0.3rem; padding:0.3rem 0.65rem; background:#fffbeb; color:#92400e; border:1px solid #fcd34d; border-radius:0.4rem; font-size:0.75rem; font-weight:600; cursor:pointer; transition:all .15s; }
    .btn-edit:hover { background:#fef3c7; border-color:#f59e0b; }
    .btn-del     { display:inline-flex; align-items:center; gap:0.3rem; padding:0.3rem 0.65rem; background:#fff1f2; color:#991b1b; border:1px solid #fca5a5; border-radius:0.4rem; font-size:0.75rem; font-weight:600; cursor:pointer; transition:all .15s; }
    .btn-del:hover { background:#fee2e2; border-color:#f87171; }
</style>
@endpush

@section('content')
<div x-data="masterReportEdit()" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-5">

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

        <div class="flex items-center gap-2 flex-wrap">

            <form action="{{ route('transactions.master-report') }}" method="GET" class="flex items-center gap-2">
                <div class="relative">
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Search chemical name..."
                           class="w-52 sm:w-60 pl-9 pr-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 bg-white">
                    <i data-lucide="search" class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                </div>
                <select name="per_page" onchange="this.form.submit()" class="text-xs border border-gray-300 rounded-lg px-2 py-2 bg-white text-gray-700 font-medium focus:outline-none focus:ring-2 focus:ring-amber-500">
                    <option value="all" {{ request('per_page', 'all') === 'all' ? 'selected' : '' }}>Semua Chemical</option>
                    <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50 per hal</option>
                    <option value="100" {{ request('per_page') == '100' ? 'selected' : '' }}>100 per hal</option>
                </select>
                @if(request('search') || (request('per_page') && request('per_page') !== 'all'))
                    <a href="{{ route('transactions.master-report') }}" class="px-2.5 py-1.5 text-xs text-gray-600 hover:text-gray-900 border border-gray-300 rounded-lg bg-white">
                        Reset
                    </a>
                @endif
                <button type="submit" class="px-3.5 py-1.5 text-sm font-medium text-white bg-amber-600 hover:bg-amber-700 rounded-lg transition-colors cursor-pointer">
                    Filter
                </button>
            </form>

            @can('create', \App\Models\Chemical::class)
            <button type="button" @click="showAddModal = true"
                    class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-amber-600 hover:bg-amber-700 text-white text-sm font-semibold rounded-lg shadow-xs transition-colors cursor-pointer">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Tambah Chemical</span>
            </button>
            @endcan
        </div>
    </div>

    @if(session('success'))
    <div class="flex items-center gap-2 px-4 py-2.5 bg-green-50 border border-green-200 rounded-lg text-sm text-green-800 font-medium"
         x-data="{show:true}" x-show="show" x-transition>
        <i data-lucide="check-circle-2" class="w-4 h-4 text-green-500"></i>
        <span>{{ session('success') }}</span>
        <button @click="show=false" class="ml-auto text-green-400 hover:text-green-600"><i data-lucide="x" class="w-3.5 h-3.5"></i></button>
    </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">

        <div class="report-banner">
            REPORT MONITORING CHEMICAL
        </div>

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
                        <th rowspan="2" class="text-center" style="width: 100px;">Actions</th>
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
                            $cMatrix = $matrix[$chem->id] ?? [];
                        @endphp
                        <tr id="mr-row-{{ $chem->id }}" x-bind:class="deletedIds.includes({{ $chem->id }}) ? 'hidden' : ''">
                            <td class="text-center font-medium text-gray-500 td-rownum"></td>
                            <td class="font-medium text-gray-900"
                                x-ref="name_{{ $chem->id }}"
                                id="mr-name-{{ $chem->id }}">
                                {{ $chem->chemical_name }}
                            </td>
                            @foreach($reportMonths as $mKey => $mLabel)
                                @php
                                    $mData        = $cMatrix[$mKey] ?? null;
                                    $amount       = $mData['amount'] ?? null;
                                    $monthUnit    = $mData['unit'] ?? null;
                                    $displayAmount = '';
                                    if ($amount !== null && $amount !== '') {
                                        $num = (float)$amount;
                                        if (floor($num) == $num) {
                                            $displayAmount = number_format($num, 0, ',', '');
                                        } else {
                                            $displayAmount = rtrim(rtrim(number_format($num, 4, ',', ''), '0'), ',');
                                        }
                                    }
                                @endphp
                                <td class="text-center border-l font-mono text-sm" style="border-left: 1.5px solid #e5e7eb;"
                                    id="mr-amount-{{ $chem->id }}-{{ $mKey }}">
                                    {{ $displayAmount }}
                                </td>
                                <td class="text-center text-xs text-gray-600"
                                    id="mr-unit-{{ $chem->id }}-{{ $mKey }}">
                                    {{ $displayAmount !== '' ? ($monthUnit ?? '') : '' }}
                                </td>
                            @endforeach
                            <td class="text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button class="btn-edit"
                                        @click="openEdit({
                                            id: {{ $chem->id }},
                                            name: {{ json_encode($chem->chemical_name) }},
                                            months: {{ json_encode(collect($reportMonths)->mapWithKeys(fn($l,$k) => [$k => [
                                                'amount' => $cMatrix[$k]['amount'] ?? null,
                                                'unit'   => $cMatrix[$k]['unit'] ?? null,
                                            ]])) }}
                                        })">
                                        <i data-lucide="pencil" class="w-3 h-3"></i> Edit
                                    </button>
                                    <button class="btn-del"
                                        @click="confirmDelete({{ $chem->id }}, {{ json_encode($chem->chemical_name) }})">
                                        <i data-lucide="trash-2" class="w-3 h-3"></i> Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ 3 + count($reportMonths) * 2 }}" class="text-center py-8 text-gray-500 text-sm">
                                Tidak ada data chemical yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-gray-200 bg-gray-50 flex flex-col sm:flex-row items-center justify-between gap-3">
            <p class="text-xs text-gray-500">
                Menampilkan <span class="font-medium text-gray-700">{{ $chemicals->firstItem() ?? 0 }}</span> - <span class="font-medium text-gray-700">{{ $chemicals->lastItem() ?? 0 }}</span> dari <span class="font-medium text-gray-700">{{ $chemicals->total() }}</span> chemical
            </p>
            <div>
                {{ $chemicals->links() }}
            </div>
        </div>

    </div>

    <div class="modal-overlay" x-show="showEditModal" x-cloak @click.self="showEditModal=false"
         @keydown.enter.window="if(showEditModal && !saving) saveEdit()"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="modal-box" @click.stop>
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-bold text-gray-900 flex items-center gap-2">
                    <i data-lucide="pencil" class="w-4 h-4 text-amber-500"></i>
                    Edit Chemical — Master Report
                </h3>
                <button @click="showEditModal=false" class="text-gray-400 hover:text-gray-600 cursor-pointer">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <div class="space-y-3">

                <div>
                    <label class="form-label">Nama Chemical</label>
                    <input x-model="edit.name" class="form-input" placeholder="Nama chemical..."
                           @keydown.enter.prevent="if(!saving) saveEdit()">
                </div>

                <div class="border-t border-gray-100 pt-4 mt-1">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-1 h-4 rounded-full bg-amber-400"></div>
                        <p class="text-xs font-bold text-gray-600 uppercase tracking-wider">Saldo Awal &amp; Satuan per Bulan</p>
                    </div>
                    <table class="month-grid">
                        <thead>
                            <tr>
                                <th>Bulan</th>
                                <th class="text-center">Amount</th>
                                <th class="text-center">Satuan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reportMonths as $mKey => $mLabel)
                            <tr>
                                <td>
                                    <span class="inline-flex items-center gap-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400 flex-shrink-0"></span>
                                        {{ $mLabel }}
                                    </span>
                                </td>
                                <td>
                                    <input type="text" inputmode="decimal"
                                           x-model="edit.months['{{ $mKey }}'].amount"
                                           class="mg-input font-mono text-center" placeholder="0"
                                           @keydown.enter.prevent="if(!saving) saveEdit()">
                                </td>
                                <td>
                                    <input type="text"
                                           x-model="edit.months['{{ $mKey }}'].unit"
                                           class="mg-unit" placeholder="ml/g/pcs"
                                           @keydown.enter.prevent="if(!saving) saveEdit()">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 mt-5 pt-4 border-t border-gray-100">
                <button class="btn-cancel" @click="showEditModal=false">Batal</button>
                <button class="btn-save" @click="saveEdit()" :disabled="saving">
                    <i data-lucide="save" class="w-3.5 h-3.5"></i>
                    <span x-text="saving ? 'Menyimpan...' : 'Simpan'"></span>
                </button>
            </div>
        </div>
    </div>

    <div class="modal-overlay" x-show="showDeleteModal" x-cloak @click.self="showDeleteModal=false"
         @keydown.enter.window="if(showDeleteModal && !deleting) doDelete()"
         x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100">
        <div class="modal-box max-w-sm" @click.stop>
            <div class="flex flex-col items-center gap-3 text-center py-2">
                <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center">
                    <i data-lucide="trash-2" class="w-6 h-6 text-red-500"></i>
                </div>
                <h3 class="text-base font-bold text-gray-900">Hapus Chemical?</h3>
                <p class="text-sm text-gray-500">
                    Chemical <strong x-text="deleteTarget.name" class="text-gray-800"></strong> beserta semua data log-nya akan dihapus permanen.
                </p>
            </div>
            <div class="flex items-center justify-center gap-2 mt-5">
                <button class="btn-cancel" @click="showDeleteModal=false">Batal</button>
                <button class="btn-del" style="padding:0.5rem 1.1rem;" @click="doDelete()" :disabled="deleting">
                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                    <span x-text="deleting ? 'Menghapus...' : 'Ya, Hapus'"></span>
                </button>
            </div>
        </div>
    </div>

    <div class="modal-overlay" x-show="showAddModal" x-cloak @click.self="showAddModal=false"
         x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100">
        <div class="modal-box max-w-lg" @click.stop>
            <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-4">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center">
                        <i data-lucide="flask-conical" class="w-4 h-4 text-amber-700"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Tambah Chemical ke Master Report</h3>
                        <p class="text-xs text-gray-500">Daftarkan chemical baru beserta saldo awal per bulan</p>
                    </div>
                </div>
                <button type="button" @click="showAddModal=false" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <div x-show="addError" x-cloak class="mb-3 flex items-center gap-2 px-3 py-2 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
                <i data-lucide="alert-circle" class="w-4 h-4 flex-shrink-0"></i>
                <span x-text="addError"></span>
            </div>

            <form id="addChemicalForm" @submit.prevent="submitAdd()">
                @csrf
                <input type="hidden" name="redirect_to" value="master-report">

                <div class="space-y-4">
                    <div>
                        <label class="form-label">Nama Chemical <span class="text-red-500">*</span></label>
                        <input type="text" name="chemical_name" id="add_chemical_name" required placeholder="Contoh: Potassium chloride"
                               class="form-input">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="form-label">Satuan <span class="text-red-500">*</span></label>
                            <select name="unit" id="add_unit" required class="form-input bg-white">
                                <option value="g">g (Gram)</option>
                                <option value="ml">ml (Mililiter)</option>
                                <option value="L">L (Liter)</option>
                                <option value="kg">kg (Kilogram)</option>
                                <option value="pcs">pcs (Pieces)</option>
                                <option value="vial">vial</option>
                                <option value="bottle">bottle</option>
                                <option value="pack">pack</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Stok Minimum (Opsional)</label>
                            <input type="text" inputmode="decimal" name="minimum_stock" placeholder="0"
                                   class="form-input font-mono">
                        </div>
                    </div>

                    <div>
                        <label class="form-label font-semibold text-gray-800">Saldo Awal per Bulan (Monitoring):</label>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 mt-1">
                            @foreach($reportMonths as $mKey => $mLabel)
                            <div>
                                <label class="text-xs text-gray-500 block mb-1 font-medium">{{ $mLabel }}</label>
                                <input type="text" inputmode="decimal"
                                       name="months[{{ $mKey }}]"
                                       placeholder="0"
                                       class="form-input text-xs font-mono text-center">
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 mt-6 pt-4 border-t border-gray-100">
                    <button type="button" class="btn-cancel" @click="showAddModal=false">Batal</button>
                    <button type="submit" class="btn-save" :disabled="adding">
                        <i data-lucide="plus" class="w-4 h-4"></i>
                        <span x-text="adding ? 'Menyimpan...' : 'Simpan Chemical'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

@push('scripts')
<script>
function masterReportEdit() {
    return {
        showAddModal: false,
        showEditModal: false,
        showDeleteModal: false,
        saving: false,
        deleting: false,
        adding: false,
        addError: '',
        deletedIds: [],
        currentOriginalName: '',
        edit: { id: null, name: '', months: {} },
        deleteTarget: { id: null, name: '' },

        openEdit(row) {
            const monthsCopy = JSON.parse(JSON.stringify(row.months || {}));
            for (const [k, v] of Object.entries(monthsCopy)) {
                if (v && v.amount !== null && v.amount !== undefined && v.amount !== '') {
                    const clean = String(v.amount).replace(',', '.');
                    const num = parseFloat(clean);
                    if (!isNaN(num)) {
                        if (Number.isInteger(num)) {
                            v.amount = num.toString();
                        } else {
                            v.amount = num.toFixed(4).replace(/\.?0+$/, '').replace('.', ',');
                        }
                    }
                } else if (v) {
                    v.amount = '';
                }
            }
            this.currentOriginalName = row.name;
            this.edit = { id: row.id, name: row.name, months: monthsCopy };
            this.showEditModal = true;
        },

        async saveEdit() {
            if (this.saving) return;
            this.saving = true;
            const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
            const chemId = this.edit.id;

            try {
                if (this.edit.name && this.edit.name.trim() !== '' && this.edit.name !== this.currentOriginalName) {
                    await fetch('{{ route('transactions.update-chemical') }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
                        body: JSON.stringify({ chemical_id: chemId, field: 'chemical_name', value: this.edit.name.trim() })
                    });
                }

                for (const [mKey, mData] of Object.entries(this.edit.months)) {
                    const amt  = mData?.amount;
                    const unit = mData?.unit ?? '';

                    const cleanAmt = (amt !== null && amt !== undefined && String(amt).trim() !== '')
                        ? String(amt).trim().replace(',', '.')
                        : null;

                    await fetch('{{ route('transactions.update-balance') }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
                        body: JSON.stringify({ chemical_id: chemId, period_month: mKey, field: 'saldo_awal', value: cleanAmt })
                    });

                    await fetch('{{ route('transactions.update-balance') }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
                        body: JSON.stringify({ chemical_id: chemId, period_month: mKey, field: 'unit', value: unit ? unit.trim() : null })
                    });
                }

                const nameEl = document.getElementById('mr-name-' + chemId);
                if (nameEl) nameEl.textContent = this.edit.name;

                const monthKeys = @json(array_keys($reportMonths));
                monthKeys.forEach(mKey => {
                    const mData = this.edit.months[mKey];
                    const amt   = mData?.amount;
                    const unit  = mData?.unit ?? '';

                    let display = '';
                    if (amt !== null && amt !== undefined && String(amt).trim() !== '') {
                        const clean = String(amt).replace(',', '.');
                        const n = parseFloat(clean);
                        if (!isNaN(n)) {
                            display = Number.isInteger(n)
                                ? n.toString()
                                : n.toFixed(4).replace(/\.?0+$/, '').replace('.', ',');
                        }
                    }

                    const amtEl = document.getElementById('mr-amount-' + chemId + '-' + mKey);
                    if (amtEl) amtEl.textContent = display;

                    const unitEl = document.getElementById('mr-unit-' + chemId + '-' + mKey);
                    if (unitEl) unitEl.textContent = (display !== '' && unit !== '') ? unit : '';
                });

                this.showEditModal = false;

                sessionStorage.setItem('mr_keep_row', chemId);
                sessionStorage.setItem('mr_scroll_y', window.scrollY);

                window.location.reload();
            } catch(e) {
                alert('Gagal menyimpan. Silakan coba lagi.');
            } finally {
                this.saving = false;
            }
        },

        confirmDelete(id, name) {
            this.deleteTarget = { id, name };
            this.showDeleteModal = true;
        },

        async doDelete() {
            if (this.deleting) return;
            this.deleting = true;
            const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
            try {
                const resp = await fetch('{{ route('transactions.chemicals.destroy', ['chemical' => '__ID__']) }}'.replace('__ID__', this.deleteTarget.id), {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' }
                });
                if (resp.ok) {
                    this.deletedIds.push(this.deleteTarget.id);
                    this.showDeleteModal = false;
                } else {
                    const data = await resp.json().catch(() => ({}));
                    const msg = data.message || (resp.status === 403 ? 'Anda tidak punya izin untuk menghapus chemical ini.' : 'Gagal menghapus. Silakan coba lagi.');
                    alert(msg);
                }
            } catch(e) {
                alert('Error saat menghapus.');
            } finally {
                this.deleting = false;
            }
        },

        async submitAdd() {
            if (this.adding) return;
            this.adding = true;
            this.addError = '';
            const form = document.getElementById('addChemicalForm');
            const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
            const formData = new FormData(form);

            for (const [key, val] of Array.from(formData.entries())) {
                if (typeof val === 'string' && (key.startsWith('months[') || key === 'minimum_stock' || key === 'saldo_awal')) {
                    formData.set(key, val.replace(',', '.').trim());
                }
            }

            try {
                const resp = await fetch('{{ route('transactions.quick-add-chemical') }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest' },
                    body: formData
                });

                const data = await resp.json().catch(() => ({}));

                if (resp.ok && data.success) {
                    const chemId = data.chemical?.id ?? '';
                    window.location.href = '{{ route('transactions.master-report') }}'
                        + (chemId ? '?highlight=' + chemId : '');
                } else {
                    const firstError = data.errors ? Object.values(data.errors)[0]?.[0] : null;
                    this.addError = firstError || data.message || 'Gagal menyimpan. Silakan coba lagi.';
                    this.adding = false;
                }
            } catch(e) {
                this.addError = 'Terjadi kesalahan koneksi.';
                this.adding = false;
            }
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const urlParams = new URLSearchParams(window.location.search);
    const highlightId = urlParams.get('highlight');
    const keepRow = sessionStorage.getItem('mr_keep_row') || highlightId;
    const scrollY = sessionStorage.getItem('mr_scroll_y');

    if (keepRow) {
        sessionStorage.removeItem('mr_keep_row');
        sessionStorage.removeItem('mr_scroll_y');
        const tr = document.getElementById('mr-row-' + keepRow);
        if (tr) {
            tr.scrollIntoView({ behavior: 'smooth', block: 'center' });
            tr.style.transition = 'background-color 0.8s ease';
            tr.style.backgroundColor = '#fef3c7';
            setTimeout(() => {
                tr.style.backgroundColor = '';
            }, 2500);
        } else if (scrollY) {
            window.scrollTo(0, parseInt(scrollY));
        }

        if (highlightId) {
            const newUrl = new URL(window.location);
            newUrl.searchParams.delete('highlight');
            window.history.replaceState({}, document.title, newUrl.toString());
        }
    }
});
</script>
@endpush

@endsection

