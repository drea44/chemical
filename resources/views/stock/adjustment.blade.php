@extends('layouts.app')

@php
    $title      = 'Stock Adjustment';
    $breadcrumb = [
        ['label' => 'Stock', 'url' => route('stock.index')],
        ['label' => 'Adjustment', 'url' => '#'],
    ];
@endphp

@section('title', 'Stock Adjustment')

@section('content')

<div class="mb-5">
    <h1 class="text-xl font-bold text-gray-900">Stock Adjustment</h1>
    <p class="text-sm text-gray-500 mt-0.5">Perform direct physical counts reconciliation, write-offs, or safety disposals.</p>
</div>

<form method="POST" action="{{ route('stock.adjustment.process') }}"
      x-data="adjustmentForm()"
      @submit="submitting = true">
    @csrf

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- LEFT: Adjustment Form --}}
        <div class="lg:col-span-2">
            <div class="border-2 rounded-lg p-6"
                 :class="direction === 'decrease' ? 'border-orange-400' : 'border-gray-200'"
                 style="background:#fff;">

                {{-- Warning banner --}}
                <div class="flex items-center gap-2 bg-orange-50 border border-orange-200 rounded px-4 py-2.5 mb-5">
                    <i data-lucide="alert-triangle" class="w-4 h-4 text-orange-500 flex-shrink-0"></i>
                    <p class="text-xs text-orange-700 font-medium">Direct balance corrections bypass normal supplier stock workflows. Be precise.</p>
                </div>

                {{-- Target Substance + Current System Balance --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="chemical_id" class="block text-sm font-medium text-gray-700 mb-1">
                            Target Substance
                        </label>
                        <select id="chemical_id" name="chemical_id" required
                                @change="selectChemical($event)"
                                class="block w-full rounded border border-gray-300 px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 bg-white">
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
                            Current System Balance (Read-Only)
                        </label>
                        <input type="text" readonly
                               :value="selectedChemical ? selectedChemical.stock.toFixed(2) + ' ' + selectedChemical.unit : ''"
                               placeholder="Select a substance first"
                               class="block w-full rounded border border-gray-200 px-3 py-2 text-sm text-gray-700 bg-gray-50">
                    </div>
                </div>

                {{-- Adjustment Direction --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Adjustment Direction</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="relative flex items-center gap-2 border rounded px-4 py-2.5 cursor-pointer transition-all"
                               :class="direction === 'increase' ? 'border-blue-500 bg-blue-50' : 'border-gray-300 bg-white hover:bg-gray-50'">
                            <input type="radio" name="direction" value="increase"
                                   x-model="direction"
                                   class="accent-blue-600">
                            <span class="text-sm font-medium" :class="direction === 'increase' ? 'text-blue-700' : 'text-gray-700'">
                                Increase (+) / Refill / Surplus Found
                            </span>
                        </label>
                        <label class="relative flex items-center gap-2 border rounded px-4 py-2.5 cursor-pointer transition-all"
                               :class="direction === 'decrease' ? 'border-orange-500 bg-orange-50' : 'border-gray-300 bg-white hover:bg-gray-50'">
                            <input type="radio" name="direction" value="decrease"
                                   x-model="direction"
                                   class="accent-orange-600">
                            <span class="text-sm font-medium" :class="direction === 'decrease' ? 'text-orange-700' : 'text-gray-700'">
                                Decrease (-) / Spillage / Damage Disposal
                            </span>
                        </label>
                    </div>
                </div>

                {{-- Adjustment Quantity + Unit + Reason --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div>
                        <label for="adjusted_stock" class="block text-sm font-medium text-gray-700 mb-1">
                            Adjustment Quantity
                        </label>
                        <input type="number" id="adjusted_stock" name="adjusted_stock" step="0.01" min="0"
                               value="{{ old('adjusted_stock') }}"
                               @input="adjustQty = parseFloat($event.target.value) || 0"
                               placeholder="0.00"
                               class="block w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 {{ $errors->has('adjusted_stock') ? 'border-red-300' : '' }}"
                               required>
                        @error('adjusted_stock')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
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
                            Primary Reason Code
                        </label>
                        <select id="reason" name="reason" required
                                class="block w-full rounded border border-gray-300 px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 bg-white">
                            <option value="">— Select reason —</option>
                            <option value="Physical Count Correction" @selected(old('reason') === 'Physical Count Correction')>Physical Count Correct...</option>
                            <option value="Evaporation / natural loss" @selected(old('reason') === 'Evaporation / natural loss')>Evaporation / natural loss</option>
                            <option value="Spillage or breakage" @selected(old('reason') === 'Spillage or breakage')>Spillage or breakage</option>
                            <option value="Contamination" @selected(old('reason') === 'Contamination')>Contamination</option>
                            <option value="System correction" @selected(old('reason') === 'System correction')>System correction</option>
                            <option value="Other" @selected(old('reason') === 'Other')>Other</option>
                        </select>
                        @error('reason')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Audit Notes --}}
                <div class="mb-4">
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Audit Notes & Explanation</label>
                    <textarea id="notes" name="notes" rows="3"
                              placeholder="e.g. Periodic stock count audit revealed discrepancy..."
                              class="block w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">{{ old('notes') }}</textarea>
                </div>

                {{-- Authorized Investigator + Verification Date --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Authorized Investigator</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs">A</span>
                            <input type="text" name="authorized_investigator"
                                   value="{{ auth()->user()->name }}"
                                   readonly
                                   class="block w-full rounded border border-gray-200 pl-7 pr-3 py-2 text-sm text-gray-700 bg-gray-50">
                        </div>
                    </div>
                    <div>
                        <label for="verification_date" class="block text-sm font-medium text-gray-700 mb-1">Verification Date</label>
                        <div class="relative">
                            <i data-lucide="calendar" class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-400"></i>
                            <input type="date" id="verification_date" name="verification_date"
                                   value="{{ old('verification_date', date('Y-m-d')) }}"
                                   class="block w-full rounded border border-gray-300 pl-8 pr-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-end gap-3">
                    <a href="{{ route('stock.adjustment') }}"
                       class="px-5 py-2 border border-gray-300 text-gray-600 text-sm font-medium rounded hover:bg-gray-50 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" :disabled="submitting"
                            class="px-5 py-2.5 text-white text-sm font-semibold rounded transition-colors disabled:opacity-50"
                            :class="direction === 'decrease'
                                ? 'bg-orange-500 hover:bg-orange-600'
                                : 'bg-blue-600 hover:bg-blue-700'">
                        <span x-text="submitting ? 'Processing...' : 'Submit Adjustment'"></span>
                    </button>
                </div>
            </div>
        </div>

        {{-- RIGHT: Adjustment Preview --}}
        <div class="lg:col-span-1">
            <div class="bg-white border border-gray-200 rounded-lg p-5">
                <h2 class="text-sm font-semibold text-gray-800 mb-5">Adjustment Preview</h2>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">Before Level</span>
                        <span class="text-sm font-semibold text-gray-800">
                            <span x-text="selectedChemical ? selectedChemical.stock.toFixed(2) + ' ' + selectedChemical.unit : '—'">—</span>
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">Adjustment Amount</span>
                        <span class="text-sm font-semibold"
                              :class="direction === 'decrease' ? 'text-red-500' : 'text-green-600'">
                            <span x-text="adjustQty > 0 ? (direction === 'decrease' ? '-' : '+') + adjustQty.toFixed(2) + ' ' + (selectedChemical?.unit ?? '') : '—'">—</span>
                        </span>
                    </div>
                    <div class="border-t border-gray-100 pt-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-semibold text-gray-800">After Level</span>
                            <span class="text-lg font-bold text-gray-800">
                                <span x-text="selectedChemical && adjustQty > 0
                                    ? (direction === 'decrease'
                                        ? Math.max(0, selectedChemical.stock - adjustQty).toFixed(2)
                                        : (selectedChemical.stock + adjustQty).toFixed(2)) + ' ' + selectedChemical.unit
                                    : '—'">—</span>
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between pt-1">
                        <span class="text-sm text-gray-500">Type</span>
                        <span class="text-xs font-bold px-2 py-0.5 rounded"
                              :class="direction === 'decrease' ? 'text-red-600' : 'text-green-700'"
                              x-text="direction === 'decrease' ? 'DECREASE (-)' : 'INCREASE (+)'">
                            —
                        </span>
                    </div>
                </div>

                {{-- Large adjustment warning --}}
                <div x-show="selectedChemical && adjustQty > 0 && selectedChemical.stock > 0 && (adjustQty / selectedChemical.stock) >= 0.20"
                     class="mt-4 bg-orange-50 border border-orange-200 rounded p-3">
                    <div class="flex items-start gap-2">
                        <i data-lucide="alert-triangle" class="w-3.5 h-3.5 text-orange-500 flex-shrink-0 mt-0.5"></i>
                        <p class="text-xs text-orange-700">Large adjustment detected (≥20% change). This will be flagged in the audit trail.</p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</form>

@endsection

@push('scripts')
<script>
function adjustmentForm() {
    return {
        selectedChemical: @json($selected ? ['stock' => (float)$selected->current_stock, 'unit' => $selected->unit] : null),
        adjustQty: 0,
        direction: 'decrease',
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
