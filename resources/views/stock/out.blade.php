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

<div class="max-w-3xl">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Stock Out</h1>
            <p class="text-sm text-gray-500 mt-0.5">Record outgoing chemical inventory</p>
        </div>
        <x-button href="{{ route('stock.index') }}" variant="secondary" icon="arrow-left" size="sm">Back</x-button>
    </div>

    <form method="POST" action="{{ route('stock.out.process') }}"
          class="space-y-5"
          x-data="stockOutForm()"
          @submit="submitting = true">
        @csrf

        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-red-50">
                <h2 class="text-sm font-semibold text-red-800 flex items-center gap-2">
                    <i data-lucide="arrow-up-circle" class="w-4 h-4 text-red-600"></i>Stock Out Details
                </h2>
            </div>
            <div class="px-6 py-5 space-y-4">

                <div class="space-y-1">
                    <label for="chemical_id" class="block text-sm font-medium text-gray-700">Chemical <span class="text-red-500">*</span></label>
                    <select id="chemical_id" name="chemical_id" required
                            @change="selectChemical($event)"
                            class="block w-full rounded border border-gray-300 px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 bg-white {{ $errors->has('chemical_id') ? 'border-red-300' : '' }}">
                        <option value="">— Select Chemical —</option>
                        @foreach($chemicals as $chem)
                        <option value="{{ $chem->id }}"
                                data-stock="{{ $chem->current_stock }}"
                                data-unit="{{ $chem->unit }}"
                                @selected($selected?->id === $chem->id || old('chemical_id') == $chem->id)>
                            {{ $chem->chemical_name }} ({{ $chem->chemical_code }}) — {{ $chem->current_stock }} {{ $chem->unit }} available
                        </option>
                        @endforeach
                    </select>
                    @error('chemical_id')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div x-show="selectedChemical" class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide">Current Stock</p>
                            <p class="text-xl font-bold text-gray-900">
                                <span x-text="selectedChemical?.stock"></span>
                                <span class="text-sm text-gray-500" x-text="selectedChemical?.unit"></span>
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide">After Stock Out</p>
                            <p class="text-xl font-bold" :class="afterStockRaw() < 0 ? 'text-red-600' : 'text-gray-900'">
                                <span x-text="afterStock()"></span>
                                <span class="text-sm" x-text="selectedChemical?.unit"></span>
                            </p>
                        </div>
                    </div>
                    <div x-show="afterStockRaw() < 0" class="mt-3 flex items-center gap-2 text-red-600 bg-red-50 border border-red-200 rounded p-3">
                        <i data-lucide="alert-triangle" class="w-4 h-4 flex-shrink-0"></i>
                        <p class="text-xs font-medium">Requested quantity exceeds available stock. This transaction will be rejected.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label for="quantity" class="block text-sm font-medium text-gray-700">Quantity <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input type="number" id="quantity" name="quantity" step="0.001" min="0.001"
                                   value="{{ old('quantity') }}"
                                   @input="qty = parseFloat($event.target.value) || 0"
                                   placeholder="0.000"
                                   class="block w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 pr-16"
                                   required>
                            <span x-text="selectedChemical?.unit || 'unit'" class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-400"></span>
                        </div>
                        @error('quantity')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <x-form-input label="Reference Number" name="reference_number" :value="old('reference_number')" placeholder="e.g. REQ-2024-001" />
                </div>

                <div class="space-y-1">
                    <label for="reason" class="block text-sm font-medium text-gray-700">Reason / Purpose <span class="text-red-500">*</span></label>
                    <select id="reason" name="reason" required class="block w-full rounded border border-gray-300 px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 bg-white">
                        <option value="">— Select reason —</option>
                        <option value="Lab experiment">Lab experiment</option>
                        <option value="Production use">Production use</option>
                        <option value="Quality control testing">Quality control testing</option>
                        <option value="Disposal (expired)">Disposal (expired)</option>
                        <option value="Transfer to another lab">Transfer to another lab</option>
                        <option value="Other">Other</option>
                    </select>
                    @error('reason')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="space-y-1">
                    <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                    <textarea id="notes" name="notes" rows="2" placeholder="Optional additional information..."
                              class="block w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <x-button href="{{ route('stock.out') }}" variant="secondary">Reset</x-button>
            <button type="submit" :disabled="submitting || afterStockRaw() < 0"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white font-semibold rounded text-sm transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                <i data-lucide="check" class="w-4 h-4"></i>
                <span x-text="submitting ? 'Processing...' : 'Confirm Stock Out'"></span>
            </button>
        </div>
    </form>
</div>

@endsection

@push('scripts')
<script>
function stockOutForm() {
    return {
        selectedChemical: @json($selected ? ['stock' => $selected->current_stock, 'unit' => $selected->unit] : null),
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
            return this.afterStockRaw().toFixed(3);
        }
    }
}
</script>
@endpush
