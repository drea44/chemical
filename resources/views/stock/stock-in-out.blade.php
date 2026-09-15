@extends('layouts.app')

@php
    $title      = 'Stock In Out';
    $breadcrumb = [
        ['label' => 'Stock', 'url' => route('stock.index')],
        ['label' => 'Stock In Out', 'url' => '#'],
    ];
@endphp

@section('title', 'Stock In Out')

@section('content')

<div class="mb-5">
    <h1 class="text-xl font-bold text-gray-900">Stock In Out</h1>
    <p class="text-sm text-gray-500 mt-0.5">Record stock-in (incoming) or stock-out (outgoing) transactions for chemical substances.</p>
</div>

<form method="POST" action="{{ route('stock.stock-in-out.process') }}"
      x-data="stockInOutForm()"
      @submit="submitting = true">
    @csrf

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- LEFT: Transaction Form --}}
        <div class="lg:col-span-2">
            <div class="border-2 rounded-lg p-6"
                 :class="transactionType === 'STOCK_OUT' ? 'border-orange-400' : 'border-gray-200'"
                 style="background:#fff;">

                {{-- Info banner --}}
                <div class="flex items-center gap-2 bg-blue-50 border border-blue-200 rounded px-4 py-2.5 mb-5">
                    <i data-lucide="info" class="w-4 h-4 text-blue-500 flex-shrink-0"></i>
                    <p class="text-xs text-blue-700 font-medium">Select the transaction type, then choose the chemical and enter the quantity.</p>
                </div>

                {{-- Transaction Type --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Transaction Type <span class="text-red-500">*</span></label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="relative flex items-center gap-2 border rounded px-4 py-2.5 cursor-pointer transition-all"
                               :class="transactionType === 'STOCK_IN' ? 'border-blue-500 bg-blue-50' : 'border-gray-300 bg-white hover:bg-gray-50'">
                            <input type="radio" name="transaction_type" value="STOCK_IN"
                                   x-model="transactionType"
                                   class="accent-blue-600"
                                   @change="transactionType = 'STOCK_IN'"
                                   required>
                            <span class="text-sm font-medium" :class="transactionType === 'STOCK_IN' ? 'text-blue-700' : 'text-gray-700'">
                                <i data-lucide="arrow-down-to-line" class="w-3.5 h-3.5 inline-block mr-1"></i>
                                Stock In (+)
                            </span>
                        </label>
                        <label class="relative flex items-center gap-2 border rounded px-4 py-2.5 cursor-pointer transition-all"
                               :class="transactionType === 'STOCK_OUT' ? 'border-orange-500 bg-orange-50' : 'border-gray-300 bg-white hover:bg-gray-50'">
                            <input type="radio" name="transaction_type" value="STOCK_OUT"
                                   x-model="transactionType"
                                   class="accent-orange-600"
                                   @change="transactionType = 'STOCK_OUT'">
                            <span class="text-sm font-medium" :class="transactionType === 'STOCK_OUT' ? 'text-orange-700' : 'text-gray-700'">
                                <i data-lucide="arrow-up-from-line" class="w-3.5 h-3.5 inline-block mr-1"></i>
                                Stock Out (-)
                            </span>
                        </label>
                    </div>
                    @error('transaction_type')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Target Substance + Current System Balance --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="chemical_id" class="block text-sm font-medium text-gray-700 mb-1">
                            Chemical Substance <span class="text-red-500">*</span>
                        </label>
                        <select id="chemical_id" name="chemical_id" required
                                @change="selectChemical($event)"
                                class="block w-full rounded border border-gray-300 px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 bg-white {{ $errors->has('chemical_id') ? 'border-red-300' : '' }}">
                            <option value="">— Select substance —</option>
                            @foreach($chemicals as $chem)
                            <option value="{{ $chem->id }}"
                                    data-stock="{{ $chem->current_stock }}"
                                    data-unit="{{ $chem->unit }}"
                                    @selected($selected?->id === $chem->id || old('chemical_id') == $chem->id)>
                                {{ $chem->chemical_name }} ({{ $chem->chemical_code }})
                            </option>
                            @endforeach
                        </select>
                        @error('chemical_id')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Current Stock Balance (Read-Only)
                        </label>
                        <input type="text" readonly
                               :value="selectedChemical ? selectedChemical.stock.toFixed(2) + ' ' + selectedChemical.unit : ''"
                               placeholder="Select a substance first"
                               class="block w-full rounded border border-gray-200 px-3 py-2 text-sm text-gray-700 bg-gray-50">
                    </div>
                </div>

                {{-- Quantity + Unit + Reason --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div>
                        <label for="quantity" class="block text-sm font-medium text-gray-700 mb-1">
                            Quantity <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="quantity" name="quantity" step="0.001" min="0.001"
                               value="{{ old('quantity') }}"
                               @input="qty = parseFloat($event.target.value) || 0"
                               placeholder="0.00"
                               class="block w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 {{ $errors->has('quantity') ? 'border-red-300' : '' }}"
                               required>
                        @error('quantity')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="adj_unit" class="block text-sm font-medium text-gray-700 mb-1">
                            Measurement Unit
                        </label>
                        <select id="adj_unit" name="unit"
                                class="block w-full rounded border border-gray-300 px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 bg-white">
                            <option value="kg">kg</option>
                            <option value="L">L</option>
                            <option value="g">g</option>
                            <option value="mL">mL</option>
                            <option value="pcs">pcs</option>
                        </select>
                    </div>
                    <div>
                        <label for="reason" class="block text-sm font-medium text-gray-700 mb-1">
                            Reason / Description <span class="text-red-500">*</span>
                        </label>
                        <select id="reason" name="reason" required
                                class="block w-full rounded border border-gray-300 px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 bg-white">
                            <option value="">— Select reason —</option>
                            <optgroup label="Stock In Reasons">
                                <option value="Purchase receipt" @selected(old('reason') === 'Purchase receipt')>Purchase receipt</option>
                                <option value="Return from lab" @selected(old('reason') === 'Return from lab')>Return from lab</option>
                                <option value="Transfer from another location" @selected(old('reason') === 'Transfer from another location')>Transfer from another location</option>
                                <option value="Initial inventory setup" @selected(old('reason') === 'Initial inventory setup')>Initial inventory setup</option>
                            </optgroup>
                            <optgroup label="Stock Out Reasons">
                                <option value="Lab usage" @selected(old('reason') === 'Lab usage')>Lab usage</option>
                                <option value="Disposal / expired" @selected(old('reason') === 'Disposal / expired')>Disposal / expired</option>
                                <option value="Transfer to another location" @selected(old('reason') === 'Transfer to another location')>Transfer to another location</option>
                                <option value="Spillage or breakage" @selected(old('reason') === 'Spillage or breakage')>Spillage or breakage</option>
                            </optgroup>
                            <option value="Other" @selected(old('reason') === 'Other')>Other</option>
                        </select>
                        @error('reason')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Reference Number + Notes --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="reference_number" class="block text-sm font-medium text-gray-700 mb-1">Reference / Batch Number</label>
                        <input type="text" id="reference_number" name="reference_number"
                               value="{{ old('reference_number') }}"
                               placeholder="e.g. LOT-2026-0892"
                               class="block w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                        @error('reference_number')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Performed By</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs">A</span>
                            <input type="text"
                                   value="{{ auth()->user()->name }}"
                                   readonly
                                   class="block w-full rounded border border-gray-200 pl-7 pr-3 py-2 text-sm text-gray-700 bg-gray-50">
                        </div>
                    </div>
                </div>

                {{-- Notes --}}
                <div class="mb-6">
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes & Explanation</label>
                    <textarea id="notes" name="notes" rows="3"
                              placeholder="e.g. Shipment received from supplier, condition good..."
                              class="block w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">{{ old('notes') }}</textarea>
                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-end gap-3">
                    <a href="{{ route('stock.stock-in-out') }}"
                       class="px-5 py-2 border border-gray-300 text-gray-600 text-sm font-medium rounded hover:bg-gray-50 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" :disabled="submitting"
                            class="px-5 py-2.5 text-white text-sm font-semibold rounded transition-colors disabled:opacity-50"
                            :class="transactionType === 'STOCK_OUT'
                                ? 'bg-orange-500 hover:bg-orange-600'
                                : 'bg-blue-600 hover:bg-blue-700'">
                        <span x-text="submitting ? 'Processing...' : (transactionType === 'STOCK_OUT' ? 'Confirm Stock Out' : 'Confirm Stock In')">Submit</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- RIGHT: Transaction Preview --}}
        <div class="lg:col-span-1">
            <div class="bg-white border border-gray-200 rounded-lg p-5">
                <h2 class="text-sm font-semibold text-gray-800 mb-5">Transaction Preview</h2>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">Transaction Type</span>
                        <span class="text-xs font-bold px-2 py-0.5 rounded"
                              :class="transactionType === 'STOCK_OUT' ? 'bg-orange-100 text-orange-700' : 'bg-blue-100 text-blue-700'"
                              x-text="transactionType === 'STOCK_OUT' ? 'STOCK OUT (-)' : 'STOCK IN (+)'">
                            —
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">Current Balance</span>
                        <span class="text-sm font-semibold text-gray-800">
                            <span x-text="selectedChemical ? selectedChemical.stock.toFixed(2) + ' ' + selectedChemical.unit : '—'">—</span>
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">Quantity Change</span>
                        <span class="text-sm font-semibold"
                              :class="transactionType === 'STOCK_OUT' ? 'text-red-500' : 'text-green-600'">
                            <span x-text="qty > 0 ? (transactionType === 'STOCK_OUT' ? '-' : '+') + qty.toFixed(2) + ' ' + (selectedChemical?.unit ?? '') : '—'">—</span>
                        </span>
                    </div>
                    <div class="border-t border-gray-100 pt-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-semibold text-gray-800">New Balance</span>
                            <span class="text-lg font-bold text-gray-800">
                                <span x-text="selectedChemical && qty > 0
                                    ? (transactionType === 'STOCK_OUT'
                                        ? Math.max(0, selectedChemical.stock - qty).toFixed(2)
                                        : (selectedChemical.stock + qty).toFixed(2)) + ' ' + selectedChemical.unit
                                    : '—'">—</span>
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Insufficient stock warning --}}
                <div x-show="transactionType === 'STOCK_OUT' && selectedChemical && qty > 0 && qty > selectedChemical.stock"
                     class="mt-4 bg-red-50 border border-red-200 rounded p-3">
                    <div class="flex items-start gap-2">
                        <i data-lucide="alert-circle" class="w-3.5 h-3.5 text-red-500 flex-shrink-0 mt-0.5"></i>
                        <p class="text-xs text-red-700">Insufficient stock. Requested quantity exceeds current balance.</p>
                    </div>
                </div>

                {{-- Large change warning --}}
                <div x-show="selectedChemical && qty > 0 && selectedChemical.stock > 0 && (qty / selectedChemical.stock) >= 0.20 && !(transactionType === 'STOCK_OUT' && qty > selectedChemical.stock)"
                     class="mt-4 bg-orange-50 border border-orange-200 rounded p-3">
                    <div class="flex items-start gap-2">
                        <i data-lucide="alert-triangle" class="w-3.5 h-3.5 text-orange-500 flex-shrink-0 mt-0.5"></i>
                        <p class="text-xs text-orange-700">Large transaction detected (≥20% change). This will be flagged in the audit trail.</p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</form>

@endsection

@push('scripts')
<script>
function stockInOutForm() {
    return {
        selectedChemical: @json($selected ? ['stock' => (float)$selected->current_stock, 'unit' => $selected->unit] : null),
        qty: 0,
        transactionType: '{{ old('transaction_type', 'STOCK_IN') }}',
        submitting: false,
        selectChemical(event) {
            const opt = event.target.selectedOptions[0];
            if (opt.value) {
                this.selectedChemical = { stock: parseFloat(opt.dataset.stock), unit: opt.dataset.unit };
            } else {
                this.selectedChemical = null;
            }
        },
    }
}
</script>
@endpush
