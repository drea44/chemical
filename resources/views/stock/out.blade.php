@extends('layouts.app')

@php
    $title      = 'Stock Out';
    $breadcrumb = [
        ['label' => 'Stock', 'url' => route('stock.index')],
        ['label' => 'Stock Out', 'url' => '#'],
    ];
@endphp

@section('title', 'Stock Out')

@section('content')

<div class="mb-5">
    <h1 class="text-xl font-bold text-gray-900">Record Stock Out</h1>
    <p class="text-sm text-gray-500 mt-0.5">Remove authorized volume or mass of substances from enterprise inventory.</p>
</div>

<form method="POST" action="{{ route('stock.out.process') }}"
      x-data="stockOutForm()"
      @submit="submitting = true">
    @csrf

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- LEFT: Form --}}
        <div class="lg:col-span-2">
            <div class="bg-white border border-gray-200 rounded-lg p-6">

                {{-- Chemical + Batch --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="chemical_id" class="block text-sm font-medium text-gray-700 mb-1">
                            Chemical Substance
                        </label>
                        <select id="chemical_id" name="chemical_id" required
                                @change="selectChemical($event)"
                                class="block w-full rounded border border-gray-300 px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 bg-white {{ $errors->has('chemical_id') ? 'border-red-300' : '' }}">
                            <option value="">— Select chemical —</option>
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
                        <label for="reference_number" class="block text-sm font-medium text-gray-700 mb-1">
                            Batch / Lot Number
                        </label>
                        <input type="text" id="reference_number" name="reference_number"
                               value="{{ old('reference_number') }}"
                               placeholder="e.g. LOT-2024-1105"
                               class="block w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    </div>
                </div>

                {{-- Quantity + Unit + Purpose --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div>
                        <label for="quantity" class="block text-sm font-medium text-gray-700 mb-1">
                            Quantity to Remove
                        </label>
                        <input type="number" id="quantity" name="quantity" step="0.001" min="0.001"
                               value="{{ old('quantity') }}"
                               @input="qty = parseFloat($event.target.value) || 0"
                               placeholder="0"
                               class="block w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 {{ $errors->has('quantity') ? 'border-red-300' : '' }}"
                               required>
                        @error('quantity')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="unit_out" class="block text-sm font-medium text-gray-700 mb-1">
                            Measurement Unit
                        </label>
                        <select id="unit_out" name="unit"
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
                            Intended Purpose / Usage
                        </label>
                        <select id="reason" name="reason" required
                                class="block w-full rounded border border-gray-300 px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 bg-white">
                            <option value="">— Select —</option>
                            <option value="Laboratory Testing" @selected(old('reason') === 'Laboratory Testing')>Laboratory Testing</option>
                            <option value="Lab experiment" @selected(old('reason') === 'Lab experiment')>Lab experiment</option>
                            <option value="Production use" @selected(old('reason') === 'Production use')>Production use</option>
                            <option value="Quality control testing" @selected(old('reason') === 'Quality control testing')>Quality control testing</option>
                            <option value="Disposal (expired)" @selected(old('reason') === 'Disposal (expired)')>Disposal (expired)</option>
                            <option value="Transfer to another lab" @selected(old('reason') === 'Transfer to another lab')>Transfer to another lab</option>
                            <option value="Other" @selected(old('reason') === 'Other')>Other</option>
                        </select>
                        @error('reason')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Authorized User + Removal Timestamp --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Authorized User</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs">A</span>
                            <input type="text" name="authorized_user"
                                   value="{{ auth()->user()->name }}"
                                   readonly
                                   class="block w-full rounded border border-gray-200 pl-7 pr-3 py-2 text-sm text-gray-700 bg-gray-50">
                        </div>
                    </div>
                    <div>
                        <label for="removal_date" class="block text-sm font-medium text-gray-700 mb-1">Removal Timestamp</label>
                        <div class="relative">
                            <i data-lucide="calendar" class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-400"></i>
                            <input type="date" id="removal_date" name="removal_date"
                                   value="{{ old('removal_date', date('Y-m-d')) }}"
                                   class="block w-full rounded border border-gray-300 pl-8 pr-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                        </div>
                    </div>
                </div>

                {{-- Operational Notes --}}
                <div class="mb-6">
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Operational & Safety Notes</label>
                    <textarea id="notes" name="notes" rows="3"
                              placeholder="e.g. Required for standard organic synthesis batch..."
                              class="block w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">{{ old('notes') }}</textarea>
                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-end gap-3">
                    <a href="{{ route('stock.out') }}"
                       class="px-5 py-2 border border-gray-300 text-gray-600 text-sm font-medium rounded hover:bg-gray-50 transition-colors">
                        Cancel
                    </a>
                    <button type="submit"
                            :disabled="submitting || afterStockRaw() < 0"
                            class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                        <span x-text="submitting ? 'Processing...' : 'Confirm Stock Out'"></span>
                    </button>
                </div>
            </div>
        </div>

        {{-- RIGHT: Removal Summary --}}
        <div class="lg:col-span-1 space-y-4">

            {{-- Summary Card --}}
            <div class="bg-white border border-gray-200 rounded-lg p-5">
                <h2 class="text-sm font-semibold text-gray-800 mb-4">Removal Summary</h2>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">Current Stock Level</span>
                        <span class="text-sm font-semibold text-gray-800">
                            <span x-text="selectedChemical ? selectedChemical.stock.toFixed(0) : '—'">—</span>
                            <span x-text="selectedChemical?.unit ?? ''"></span>
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">Quantity to Remove</span>
                        <span class="text-sm font-semibold text-red-500">
                            <span x-text="qty > 0 ? '-' + qty.toFixed(0) : '—'">—</span>
                            <span x-show="qty > 0" x-text="selectedChemical?.unit ?? ''"></span>
                        </span>
                    </div>
                    <div class="border-t border-gray-100 pt-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-semibold text-gray-800">Remaining Stock</span>
                            <span class="text-lg font-bold text-gray-800">
                                <span x-text="selectedChemical ? afterStockRaw().toFixed(0) : '—'">—</span>
                                <span x-text="selectedChemical?.unit ?? ''"></span>
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between pt-1">
                        <span class="text-sm text-gray-500">Estimated Status</span>
                        <span class="text-xs font-bold px-2 py-0.5 rounded"
                              :class="afterStockRaw() < 0
                                  ? 'bg-red-100 text-red-700'
                                  : afterStockRaw() < 10
                                  ? 'bg-orange-100 text-orange-700'
                                  : 'bg-green-100 text-green-700'"
                              x-text="afterStockRaw() < 0 ? 'INSUFFICIENT' : afterStockRaw() < 10 ? 'LOW LEVEL' : 'SAFE LEVEL'">
                            SAFE LEVEL
                        </span>
                    </div>
                </div>
            </div>

            {{-- Regulatory Notice --}}
            <div x-show="selectedChemical" class="bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex items-start gap-2">
                    <i data-lucide="alert-triangle" class="w-4 h-4 text-red-500 flex-shrink-0 mt-0.5"></i>
                    <div>
                        <p class="text-xs font-semibold text-red-700 mb-1">Regulatory Notice</p>
                        <p class="text-xs text-red-600">
                            This substance is subject to chemical tracking compliance.
                            <span x-text="'{{ auth()->user()->name }}'"></span>
                            will be noted as the dispatcher under applicable OSHA regulations.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Insufficient Warning --}}
            <div x-show="selectedChemical && afterStockRaw() < 0"
                 class="bg-red-50 border border-red-300 rounded-lg p-4">
                <div class="flex items-center gap-2">
                    <i data-lucide="x-circle" class="w-4 h-4 text-red-500 flex-shrink-0"></i>
                    <p class="text-xs font-semibold text-red-700">Quantity exceeds available stock. Transaction will be rejected.</p>
                </div>
            </div>
        </div>

    </div>
</form>

@endsection

@push('scripts')
<script>
function stockOutForm() {
    return {
        selectedChemical: @json($selected ? ['stock' => (float)$selected->current_stock, 'unit' => $selected->unit] : null),
        qty: 0,
        submitting: false,
        selectChemical(event) {
            const opt = event.target.selectedOptions[0];
            if (opt.value) {
                this.selectedChemical = { stock: parseFloat(opt.dataset.stock), unit: opt.dataset.unit };
            } else {
                this.selectedChemical = null;
            }
        },
        afterStockRaw() {
            if (!this.selectedChemical) return 0;
            return this.selectedChemical.stock - this.qty;
        },
        afterStock() {
            if (!this.selectedChemical) return '—';
            return this.afterStockRaw().toFixed(2);
        }
    }
}
</script>
@endpush
