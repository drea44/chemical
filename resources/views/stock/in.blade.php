@extends('layouts.app')

@php
    $title      = 'Stock In';
    $breadcrumb = [
        ['label' => 'Stock', 'url' => route('stock.index')],
        ['label' => 'Stock In', 'url' => '#'],
    ];
@endphp

@section('title', 'Stock In')

@section('content')

<div class="mb-5">
    <h1 class="text-xl font-bold text-gray-900">Stock Intake Ledger</h1>
    <p class="text-sm text-gray-500 mt-0.5">Register incoming chemical shipments to store locations. Updates stock metrics in real-time.</p>
</div>

<form method="POST" action="{{ route('stock.in.process') }}"
      x-data="stockInForm()"
      @submit="submitting = true">
    @csrf

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        <div class="lg:col-span-2">
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <h2 class="text-sm font-semibold text-gray-800 mb-5">Intake Details</h2>

                <div class="mb-4">
                    <label for="chemical_id" class="block text-sm font-medium text-gray-700 mb-1">
                        Chemical Substance <span class="text-red-500">*</span>
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

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="reference_number" class="block text-sm font-medium text-gray-700 mb-1">
                            Batch/Lot Number <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="reference_number" name="reference_number"
                               value="{{ old('reference_number') }}"
                               placeholder="e.g. LOT-2024-0892"
                               class="block w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                        @error('reference_number')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="intake_date" class="block text-sm font-medium text-gray-700 mb-1">
                            Date of Intake <span class="text-red-500">*</span>
                        </label>
                        <input type="date" id="intake_date" name="intake_date"
                               value="{{ old('intake_date', date('Y-m-d')) }}"
                               class="block w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="quantity" class="block text-sm font-medium text-gray-700 mb-1">
                            Intake Quantity <span class="text-red-500">*</span>
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
                        <label for="unit" class="block text-sm font-medium text-gray-700 mb-1">
                            Measurement Unit <span class="text-red-500">*</span>
                        </label>
                        <select id="unit" name="unit"
                                class="block w-full rounded border border-gray-300 px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 bg-white">
                            <option value="kg" @selected(old('unit') === 'kg')>kg</option>
                            <option value="L" @selected(old('unit') === 'L')>L</option>
                            <option value="g" @selected(old('unit') === 'g')>g</option>
                            <option value="mL" @selected(old('unit') === 'mL')>mL</option>
                            <option value="pcs" @selected(old('unit') === 'pcs')>pcs</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="reason" class="block text-sm font-medium text-gray-700 mb-1">
                            Authorized Supplier <span class="text-red-500">*</span>
                        </label>
                        <select id="reason" name="reason" required
                                class="block w-full rounded border border-gray-300 px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 bg-white">
                            <option value="">— Select supplier —</option>
                            <option value="Purchase receipt" @selected(old('reason') === 'Purchase receipt')>Purchase receipt</option>
                            <option value="Return from lab" @selected(old('reason') === 'Return from lab')>Return from lab</option>
                            <option value="Transfer from another location" @selected(old('reason') === 'Transfer from another location')>Transfer from another location</option>
                            <option value="Initial inventory setup" @selected(old('reason') === 'Initial inventory setup')>Initial inventory setup</option>
                            <option value="Other" @selected(old('reason') === 'Other')>Other</option>
                        </select>
                        @error('reason')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="target_location" class="block text-sm font-medium text-gray-700 mb-1">
                            Target Location <span class="text-red-500">*</span>
                        </label>
                        <select id="target_location" name="target_location"
                                class="block w-full rounded border border-gray-300 px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 bg-white">
                            <option value="">— Select location —</option>
                            <option value="Lab A Cabinet 1">Lab A Cabinet 1</option>
                            <option value="Lab A Cabinet 2">Lab A Cabinet 2</option>
                            <option value="Lab A Cabinet 3">Lab A Cabinet 3</option>
                            <option value="Lab B Cabinet 1">Lab B Cabinet 1</option>
                            <option value="Storage Room A">Storage Room A</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Receiving Notes</label>
                    <textarea id="notes" name="notes" rows="4"
                              placeholder="e.g. Received shipment in good condition, seal inspected, compliance certificate uploaded."
                              class="block w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>

        <div class="lg:col-span-1 space-y-4">
            <div class="bg-white border border-gray-200 rounded-lg p-5">
                <h2 class="text-sm font-semibold text-gray-800 mb-5">Transaction Preview</h2>

                <div class="space-y-3 mb-5">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">Current Balance</span>
                        <span class="text-sm font-semibold text-gray-800">
                            <span x-text="selectedChemical ? selectedChemical.stock.toFixed(2) : '—'">—</span>
                            <span x-text="selectedChemical?.unit ?? ''"></span>
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">Adding Intake</span>
                        <span class="text-sm font-semibold text-green-600">
                            <span x-text="qty > 0 ? '+' + qty.toFixed(2) : '—'">—</span>
                            <span x-show="qty > 0" x-text="selectedChemical?.unit ?? ''"></span>
                        </span>
                    </div>
                    <div class="border-t border-gray-100 pt-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-semibold text-gray-800">New Projected Balance</span>
                            <span class="text-lg font-bold text-green-500">
                                <span x-text="selectedChemical ? (selectedChemical.stock + qty).toFixed(2) : '—'">—</span>
                                <span x-text="selectedChemical?.unit ?? ''"></span>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="border border-gray-100 rounded p-3 mb-5">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">Compliance Checks</p>
                    <div class="space-y-1.5">
                        <div class="flex items-center gap-2 text-xs text-green-600">
                            <i data-lucide="check" class="w-3.5 h-3.5"></i>
                            <span>OSHA labeling verified</span>
                        </div>
                        <div class="flex items-center gap-2 text-xs text-green-600">
                            <i data-lucide="check" class="w-3.5 h-3.5"></i>
                            <span>EPA MSDS sheet linked</span>
                        </div>
                    </div>
                </div>

                <button type="submit" :disabled="submitting"
                        class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded transition-colors disabled:opacity-50 mb-2">
                    <span x-text="submitting ? 'Processing...' : 'Confirm & Record Stock In'"></span>
                </button>
                <a href="{{ route('stock.in') }}"
                   class="block w-full py-2.5 border border-gray-300 text-gray-600 text-sm font-medium rounded text-center hover:bg-gray-50 transition-colors">
                    Discard Transaction
                </a>
            </div>
        </div>

    </div>
</form>

@endsection

@push('scripts')
<script>
function stockInForm() {
    return {
        selectedChemical: @json($selected ? ['stock' => (float)$selected->current_stock, 'unit' => $selected->unit] : null),
        qty: 0,
        submitting: false,
        selectChemical(event) {
            const opt = event.target.selectedOptions[0];
            if (opt.value) {
                this.selectedChemical = {
                    stock: parseFloat(opt.dataset.stock),
                    unit: opt.dataset.unit,
                };
            } else {
                this.selectedChemical = null;
            }
        },
    }
}
</script>
@endpush
