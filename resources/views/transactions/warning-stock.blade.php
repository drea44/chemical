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

    .modal-overlay {
        position: fixed; inset: 0; background: rgba(0,0,0,0.45);
        display: flex; align-items: center; justify-content: center;
        z-index: 9999; backdrop-filter: blur(2px);
    }
    .modal-box {
        background: #fff; border-radius: 0.75rem;
        box-shadow: 0 20px 60px rgba(0,0,0,0.2);
        width: 100%; max-width: 480px; padding: 1.5rem;
        animation: modalIn 0.2s ease;
    }
    @keyframes modalIn {
        from { opacity:0; transform: scale(0.95) translateY(-8px); }
        to   { opacity:1; transform: scale(1) translateY(0); }
    }
    .form-label  { display:block; font-size:0.75rem; font-weight:600; color:#374151; margin-bottom:0.25rem; }
    .form-input  { width:100%; border:1px solid #d1d5db; border-radius:0.5rem; padding:0.45rem 0.65rem; font-size:0.875rem; outline:none; transition:border-color .15s; }
    .form-input:focus { border-color:#dc2626; box-shadow:0 0 0 3px rgba(220,38,38,0.12); }
    .btn-save    { display:inline-flex; align-items:center; gap:0.4rem; padding:0.5rem 1.1rem; background:#dc2626; color:#fff; border-radius:0.5rem; font-size:0.875rem; font-weight:600; border:none; cursor:pointer; transition:background .15s; }
    .btn-save:hover { background:#b91c1c; }
    .btn-cancel  { display:inline-flex; align-items:center; gap:0.4rem; padding:0.5rem 1.1rem; background:#f3f4f6; color:#374151; border-radius:0.5rem; font-size:0.875rem; font-weight:600; border:1px solid #e5e7eb; cursor:pointer; transition:background .15s; }
    .btn-cancel:hover { background:#e5e7eb; }
    .btn-edit    { display:inline-flex; align-items:center; gap:0.3rem; padding:0.3rem 0.65rem; background:#fff1f2; color:#991b1b; border:1px solid #fca5a5; border-radius:0.4rem; font-size:0.75rem; font-weight:600; cursor:pointer; transition:all .15s; }
    .btn-edit:hover { background:#fee2e2; border-color:#f87171; }
    .btn-del     { display:inline-flex; align-items:center; gap:0.3rem; padding:0.3rem 0.65rem; background:#fef2f2; color:#7f1d1d; border:1px solid #fca5a5; border-radius:0.4rem; font-size:0.75rem; font-weight:600; cursor:pointer; transition:all .15s; }
    .btn-del:hover { background:#fee2e2; }
</style>
@endpush

@section('content')
<div x-data="warningStockEdit()" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-5">

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

        <div class="flex flex-wrap items-center gap-2">

            <form action="{{ route('transactions.warning-stock') }}" method="GET" class="flex flex-wrap items-center gap-2">

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

                <button type="submit" class="px-3.5 py-1.5 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors cursor-pointer">
                    Filter
                </button>
            </form>

            @can('create', \App\Models\Chemical::class)
            <button type="button" @click="showAddModal = true"
                    class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-lg shadow-xs transition-colors cursor-pointer">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Tambah Chemical</span>
            </button>
            @endcan
        </div>
    </div>

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

    @if(session('success'))
    <div class="flex items-center gap-2 px-4 py-2.5 bg-green-50 border border-green-200 rounded-lg text-sm text-green-800 font-medium"
         x-data="{show:true}" x-show="show" x-transition>
        <i data-lucide="check-circle-2" class="w-4 h-4 text-green-500"></i>
        <span>{{ session('success') }}</span>
        <button @click="show=false" class="ml-auto text-green-400 hover:text-green-600"><i data-lucide="x" class="w-3.5 h-3.5"></i></button>
    </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">

        <div class="warning-banner">
            CHEMICAL STOCK WARNING
        </div>

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
                        <th style="width: 110px;">Actions</th>
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
                                ? (floor($stokAwal) == $stokAwal ? number_format($stokAwal, 0, ',', '') : rtrim(rtrim(number_format($stokAwal, 4, ',', ''), '0'), ','))
                                : '';

                            $fmtMinStock = ($minStock > 0)
                                ? (floor($minStock) == $minStock ? number_format($minStock, 0, ',', '') : rtrim(rtrim(number_format($minStock, 4, ',', ''), '0'), ','))
                                : '';

                            $fmtRemaining = ($remaining !== null && $remaining !== '')
                                ? (floor($remaining) == $remaining ? number_format($remaining, 0, ',', '') : rtrim(rtrim(number_format($remaining, 4, ',', ''), '0'), ','))
                                : '';
                        @endphp
                        <tr x-bind:class="deletedIds.includes({{ $chem->id }}) ? 'hidden' : ''" class="{{ request('highlight') == $chem->id ? 'bg-red-50 ring-2 ring-red-400' : '' }}">
                            <td class="text-center font-medium text-gray-500">{{ $rowNum }}</td>
                            <td class="font-medium text-gray-900" id="ws-name-{{ $chem->id }}">
                                {{ $chem->chemical_name }}
                            </td>
                            <td class="text-center font-mono text-sm">
                                {{ $fmtStokAwal }}
                            </td>
                            <td class="text-center text-xs text-gray-600" id="ws-unit-{{ $chem->id }}">
                                {{ $chem->unit ?? '-' }}
                            </td>
                            <td class="text-center font-mono text-sm" id="ws-minstock-{{ $chem->id }}">
                                {{ $fmtMinStock }}
                            </td>
                            <td class="text-center font-mono text-sm font-semibold {{ $remaining < 0 ? 'text-red-600' : 'text-gray-900' }}">
                                {{ $fmtRemaining }}
                            </td>
                            <td class="{{ $status === 'Time to Refill' ? 'status-cell-refill' : 'status-cell-ok' }}"
                                id="ws-status-{{ $chem->id }}">
                                {{ $status }}
                            </td>
                            <td class="text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button class="btn-edit"
                                        @click="openEdit({
                                            id: {{ $chem->id }},
                                            name: {{ json_encode($chem->chemical_name) }},
                                            unit: {{ json_encode($chem->unit ?? '') }},
                                            min_stock: {{ (float)($chem->minimum_stock ?? 0) }}
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
                            <td colspan="8" class="text-center py-8 text-gray-500 text-sm">
                                Tidak ada data chemical yang sesuai filter.
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
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="modal-box" @click.stop>
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-bold text-gray-900 flex items-center gap-2">
                    <i data-lucide="pencil" class="w-4 h-4 text-red-500"></i>
                    Edit Chemical — Warning Stock
                </h3>
                <button @click="showEditModal=false" class="text-gray-400 hover:text-gray-600 cursor-pointer">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <div class="space-y-3">
                <div>
                    <label class="form-label">Nama Chemical</label>
                    <input x-model="edit.name" class="form-input" placeholder="Nama chemical...">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="form-label">Unit (ml/g/pcs...)</label>
                        <input x-model="edit.unit" class="form-input" placeholder="ml / g / pcs">
                    </div>
                    <div>
                        <label class="form-label">Minimal Stok</label>
                        <input type="text" inputmode="decimal" x-model="edit.min_stock" class="form-input font-mono" placeholder="0">
                    </div>
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
        <div class="modal-box max-w-md" @click.stop>
            <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-4">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-red-100 flex items-center justify-center">
                        <i data-lucide="flask-conical" class="w-4 h-4 text-red-700"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Tambah Chemical ke Warning Stock</h3>
                        <p class="text-xs text-gray-500">Daftarkan chemical baru dengan batas stok minimum</p>
                    </div>
                </div>
                <button type="button" @click="showAddModal=false" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('transactions.quick-add-chemical') }}">
                @csrf
                <input type="hidden" name="redirect_to" value="warning-stock">

                <div class="space-y-4">
                    <div>
                        <label class="form-label">Nama Chemical <span class="text-red-500">*</span></label>
                        <input type="text" name="chemical_name" required placeholder="Contoh: Sodium Hydroxide"
                               class="form-input">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="form-label">Satuan <span class="text-red-500">*</span></label>
                            <select name="unit" required class="form-input bg-white">
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
                            <label class="form-label">Stok Saat Ini</label>
                            <input type="number" step="any" min="0" name="current_stock" value="0"
                                   class="form-input font-mono">
                        </div>
                    </div>

                    <div>
                        <label class="form-label">Stok Minimum (Batas Alert Refill) <span class="text-red-500">*</span></label>
                        <input type="number" step="any" min="0" name="minimum_stock" required value="10" placeholder="10"
                               class="form-input font-mono">
                        <p class="text-xs text-gray-500 mt-1">Jika stok saat ini &le; batas ini, status akan otomatis menjadi <span class="text-red-600 font-semibold">Time to Refill</span>.</p>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 mt-6 pt-4 border-t border-gray-100">
                    <button type="button" class="btn-cancel" @click="showAddModal=false">Batal</button>
                    <button type="submit" class="btn-save">
                        <i data-lucide="plus" class="w-4 h-4"></i>
                        <span>Simpan Chemical</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

@push('scripts')
<script>
function warningStockEdit() {
    return {
        showAddModal: false,
        showEditModal: false,
        showDeleteModal: false,
        saving: false,
        deleting: false,
        deletedIds: [],
        edit: { id: null, name: '', unit: '', min_stock: 0 },
        deleteTarget: { id: null, name: '' },

        openEdit(row) {
            const formatNum = (v) => {
                if (v === null || v === undefined || v === '') return '';
                const clean = String(v).replace(',', '.');
                const n = parseFloat(clean);
                if (isNaN(n) || n === 0) return '0';
                if (Number.isInteger(n)) return n.toString();
                return n.toFixed(4).replace(/\.?0+$/, '').replace('.', ',');
            };
            this.edit = {
                ...row,
                min_stock: formatNum(row.min_stock),
            };
            this.showEditModal = true;
        },

        async saveEdit() {
            this.saving = true;
            const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
            try {
                const cleanMinStock = String(this.edit.min_stock || '').replace(',', '.').trim();

                await fetch('{{ route('transactions.update-chemical') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
                    body: JSON.stringify({ chemical_id: this.edit.id, field: 'chemical_name', value: this.edit.name })
                });

                await fetch('{{ route('transactions.update-chemical') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
                    body: JSON.stringify({ chemical_id: this.edit.id, field: 'unit', value: this.edit.unit })
                });

                await fetch('{{ route('transactions.update-minimum-stock') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
                    body: JSON.stringify({ chemical_id: this.edit.id, minimum_stock: cleanMinStock })
                });

                const nameEl = document.getElementById('ws-name-' + this.edit.id);
                if (nameEl) nameEl.textContent = this.edit.name;

                const unitEl = document.getElementById('ws-unit-' + this.edit.id);
                if (unitEl) unitEl.textContent = this.edit.unit || '-';

                const minEl = document.getElementById('ws-minstock-' + this.edit.id);
                if (minEl) {
                    const clean = String(this.edit.min_stock || '').replace(',', '.');
                    const ms = parseFloat(clean) || 0;
                    if (ms > 0) {
                        minEl.textContent = Number.isInteger(ms)
                            ? ms.toString()
                            : ms.toFixed(4).replace(/\.?0+$/, '').replace('.', ',');
                    } else {
                        minEl.textContent = '';
                    }
                }

                this.showEditModal = false;
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
            this.deleting = true;
            const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
            try {
                const resp = await fetch(`/transactions/chemicals/${this.deleteTarget.id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' }
                });
                if (resp.ok) {
                    this.deletedIds.push(this.deleteTarget.id);
                    this.showDeleteModal = false;
                } else {
                    alert('Gagal menghapus. Silakan coba lagi.');
                }
            } catch(e) {
                alert('Error saat menghapus.');
            } finally {
                this.deleting = false;
            }
        }
    }
}
</script>
@endpush

@endsection

