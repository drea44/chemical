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

<div class="max-w-3xl">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Stock Adjustment</h1>
            <p class="text-sm text-gray-500 mt-0.5">Correct stock discrepancies with full audit trail</p>
        </div>
        <x-button href="{{ route('stock.index') }}" variant="secondary" icon="arrow-left" size="sm">Back</x-button>
    </div>

    <form method="POST" action="{{ route('stock.adjustment.process') }}"
          class="space-y-5"
          x-data="adjustmentForm()"
          @submit="submitting = true">
        @csrf

        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-yellow-50">
                <h2 class="text-sm font-semibold text-yellow-800 flex items-center gap-2">
                    <i data-lucide="sliders-horizontal" class="w-4 h-4 text-yellow-600"></i>Adjustment Details
                </h2>
            </div>
            <div class="px-6 py-5 space-y-4">

                <div class="space-y-1">
                    <label for="chemical_id" class="block text-sm font-medium text-gray-700">Chemical <span class="text-red-500">*</span></label>
                    <select id="chemical_id" name="chemical_id" required @change="selectChemical($event)"
                            class="block w-full rounded border border-gray-300 px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 bg-white">
                        <option value="">— Select Chemical —</option>
                        @foreach($chemicals as $chem)
                        <option value="{{ $chem->id }}"
                                data-stock="{{ $chem->current_stock }}"
                                data-unit="{{ $chem->unit }}"
                                @selected($selected?->id === $chem->id || old('chemical_id') == $chem->id)>
                            {{ $chem->chemical_name }} ({{ $chem->chemical_code }}) — current: {{ $chem->current_stock }} {{ $chem->unit }}
                        </option>
                        @endforeach
                    </select>
                    @error('chemical_id')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div x-show="selectedChemical" class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide">Previous Stock</p>
                            <p class="text-xl font-bold text-gray-900">
                                <span x-text="selectedChemical?.stock"></span>
                                <span class="text-sm text-gray-500" x-text="selectedChemical?.unit"></span>
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide">Difference</p>
                            <p class="text-xl font-bold" :class="diff() > 0 ? 'text-green-600' : diff() < 0 ? 'text-red-600' : 'text-gray-400'">
                                <span x-text="diff() > 0 ? '+' + diff().toFixed(3) : diff().toFixed(3)"></span>
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide">New Stock</p>
                            <p class="text-xl font-bold text-blue-600">
                                <span x-text="newStock >= 0 ? newStock.toFixed(3) : '—'"></span>
                                <span class="text-sm" x-text="selectedChemical?.unit"></span>
                            </p>
                        </div>
                    </div>
                    <div x-show="Math.abs(diff()) > 0 && selectedChemical?.stock > 0 && Math.abs(diff() / selectedChemical.stock) >= 0.20"
                         class="mt-3 flex items-center gap-2 text-orange-700 bg-orange-50 border border-orange-200 rounded p-3">
                        <i data-lucide="alert-triangle" class="w-4 h-4 flex-shrink-0"></i>
                        <p class="text-xs font-medium">Large adjustment detected (≥20% change). This will be flagged in the audit trail.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label for="adjusted_stock" class="block text-sm font-medium text-gray-700">Actual / Physical Count <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input type="number" id="adjusted_stock" name="adjusted_stock" step="0.001" min="0"
                                   value="{{ old('adjusted_stock', $selected?->current_stock) }}"
                                   @input="newStock = parseFloat($event.target.value) || 0"
                                   placeholder="Enter actual count"
                                   class="block w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 pr-16 {{ $errors->has('adjusted_stock') ? 'border-red-300' : '' }}"
                                   required>
                            <span x-text="selectedChemical?.unit || 'unit'" class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-400"></span>
                        </div>
                        @error('adjusted_stock')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <x-form-input label="Evidence / Reference" name="evidence" :value="old('evidence')" placeholder="e.g. Physical count sheet #001" />
                </div>

                <div class="space-y-1">
                    <label for="reason" class="block text-sm font-medium text-gray-700">Reason for Adjustment <span class="text-red-500">*</span></label>
                    <select id="reason" name="reason" required class="block w-full rounded border border-gray-300 px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 bg-white">
                        <option value="">— Select reason —</option>
                        <option value="Physical count discrepancy">Physical count discrepancy</option>
                        <option value="Evaporation / natural loss">Evaporation / natural loss</option>
                        <option value="Spillage or breakage">Spillage or breakage</option>
                        <option value="Contamination">Contamination</option>
                        <option value="System correction">System correction</option>
                        <option value="Other">Other</option>
                    </select>
                    @error('reason')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="space-y-1">
                    <label class="block text-sm font-medium text-gray-700">Notes</label>
                    <textarea name="notes" rows="2" placeholder="Additional context..."
                              class="block w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <x-button href="{{ route('stock.adjustment') }}" variant="secondary">Reset</x-button>
            <button type="submit" :disabled="submitting"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-yellow-600 hover:bg-yellow-700 text-white font-semibold rounded text-sm transition-colors disabled:opacity-50">
                <i data-lucide="check" class="w-4 h-4"></i>
                <span x-text="submitting ? 'Processing...' : 'Confirm Adjustment'"></span>
            </button>
        </div>
    </form>
</div>

@endsection

@push('scripts')
<script>
function adjustmentForm() {
    return {
        selectedChemical: @json($selected ? ['stock' => $selected->current_stock, 'unit' => $selected->unit] : null),
        newStock: {{ $selected?->current_stock ?? 0 }},
        submitting: false,
        selectChemical(event) {
            const opt = event.target.selectedOptions[0];
            if (opt.value) {
                this.selectedChemical = { stock: parseFloat(opt.dataset.stock), unit: opt.dataset.unit };
                this.newStock = parseFloat(opt.dataset.stock);
            } else {
                this.selectedChemical = null;
            }
        },
        diff() {
            if (!this.selectedChemical) return 0;
            return this.newStock - this.selectedChemical.stock;
        }
    }
}
</script>
@endpush
