@extends('layouts.app')

@php
    $title      = 'Stock Adjustment';
    $breadcrumb = [
        ['label' => 'Stock', 'url' => route('stock.index')],
        ['label' => 'Stock Adjustment', 'url' => '#'],
    ];
    $chemicalsData = [];
    foreach ($chemicals as $c) {
        $chemicalsData[] = [
            'id'    => (int) $c->id,
            'name'  => $c->chemical_name,
            'code'  => $c->chemical_code,
            'stock' => (float) $c->current_stock,
            'unit'  => $c->unit ?? 'kg',
        ];
    }
    $chemicalsJson = json_encode($chemicalsData);
    $selectedJson  = $selected ? json_encode([
        'id'    => (int) $selected->id,
        'name'  => $selected->chemical_name,
        'code'  => $selected->chemical_code,
        'stock' => (float) $selected->current_stock,
        'unit'  => $selected->unit ?? 'kg',
    ]) : 'null';
@endphp

@section('title', 'Stock Adjustment')

@push('styles')
<style>
    .sio-container {
        max-width: 1180px;
        margin: 0 auto;
        padding-bottom: 3rem;
    }
    .sio-layout {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
        width: 100%;
    }
    @media (min-width: 900px) {
        .sio-layout {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 340px;
            gap: 1.5rem;
            align-items: start;
        }
    }
    .sio-form-card {
        background: #fff;
        border: 2px solid #ca6a04;
        border-radius: 1rem;
        padding: 2rem;
        min-width: 0;
        box-sizing: border-box;
        box-shadow: 0 1px 3px 0 rgba(0,0,0,.04);
        position: relative;
    }
    .sio-preview-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 1rem;
        padding: 1.75rem;
        box-shadow: 0 1px 3px 0 rgba(0,0,0,.07);
        box-sizing: border-box;
    }
    @media (min-width: 900px) {
        .sio-preview-card {
            position: sticky;
            top: 1.5rem;
        }
    }
    .sio-banner {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        background: #fef3c7;
        border-radius: 0.5rem;
        padding: 0.875rem 1rem;
        margin-bottom: 1.5rem;
    }
    .sio-row-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.25rem;
        margin-bottom: 1.25rem;
    }
    @media (max-width: 639px) {
        .sio-row-2 { grid-template-columns: 1fr; }
    }
    .sio-row-3 {
        display: grid;
        grid-template-columns: 140px 120px 1fr;
        gap: 1rem;
        margin-bottom: 1.25rem;
    }
    @media (max-width: 767px) {
        .sio-row-3 { grid-template-columns: 1fr 1fr; }
        .sio-row-3 > div:last-child { grid-column: 1 / -1; }
    }
    @media (max-width: 639px) {
        .sio-row-3 { grid-template-columns: 1fr; }
        .sio-row-3 > div:last-child { grid-column: auto; }
    }
    .sio-dir-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
        margin-bottom: 1.25rem;
    }
    @media (max-width: 639px) {
        .sio-dir-grid { grid-template-columns: 1fr; }
    }
    .sio-dir-btn {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        border: 1.5px solid #d1d5db;
        border-radius: 0.625rem;
        padding: 0.75rem 1rem;
        min-height: 3rem;
        height: auto;
        box-sizing: border-box;
        cursor: pointer;
        transition: border-color .15s, background .15s;
        background: #fff;
        user-select: none;
    }
    .sio-dir-btn > span:last-child {
        line-height: 1.35;
        white-space: normal;
        word-break: break-word;
    }
    .sio-dir-btn.active-inc {
        border-color: #3b82f6;
        background: rgba(59,130,246,.05);
    }
    .sio-dir-btn.active-dec {
        border-color: #f87171;
        background: #fff;
    }
    .sio-radio {
        width: 1.125rem; height: 1.125rem;
        border-radius: 50%;
        border: 2px solid #d1d5db;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
        transition: border-color .15s;
    }
    .sio-radio-dot {
        width: 0.5rem; height: 0.5rem;
        border-radius: 50%;
    }
    .sio-input {
        width: 100%;
        height: 2.75rem;
        border: 1.5px solid #d1d5db;
        border-radius: 0.5rem;
        padding: 0 0.875rem;
        font-size: 0.875rem;
        color: #111827;
        background: #fff;
        outline: none;
        transition: border-color .15s, box-shadow .15s;
        box-sizing: border-box;
    }
    textarea.sio-input {
        height: auto;
        min-height: 5.5rem;
        padding: 0.75rem 0.875rem;
        line-height: 1.4;
    }
    .sio-input:focus {
        border-color: #ca6a04;
        box-shadow: 0 0 0 3px rgba(202,106,4,.12);
    }
    .sio-input-ro {
        background: #f9fafb;
        border-color: #e5e7eb;
        color: #4b5563;
        cursor: default;
    }

    .sio-dropdown {
        position: relative;
        width: 100%;
    }
    .sio-dropdown-trigger {
        width: 100%;
        height: 2.75rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #fff;
        border: 1.5px solid #d1d5db;
        border-radius: 0.5rem;
        padding: 0 0.875rem;
        font-size: 0.875rem;
        color: #111827;
        cursor: pointer;
        outline: none;
        transition: border-color .15s, box-shadow .15s;
        user-select: none;
        box-sizing: border-box;
        text-align: left;
    }

    .sio-container svg {
        display: inline-block !important;
        vertical-align: middle !important;
        flex-shrink: 0 !important;
        max-width: 1.25rem !important;
        max-height: 1.25rem !important;
    }

    .sio-dropdown-trigger svg,
    .sio-dir-btn svg,
    .sio-banner svg,
    .sio-icon-left-icon svg,
    .sio-alert svg {
        width: 1rem !important;
        height: 1rem !important;
        min-width: 1rem !important;
        min-height: 1rem !important;
        max-width: 1rem !important;
        max-height: 1rem !important;
        flex-shrink: 0 !important;
        display: inline-block !important;
    }

    button.sio-dropdown-trigger > svg,
    .sio-dropdown-chevron {
        width: 1rem !important;
        height: 1rem !important;
        min-width: 1rem !important;
        min-height: 1rem !important;
        max-width: 1rem !important;
        max-height: 1rem !important;
        color: #6b7280;
        flex-shrink: 0 !important;
        display: inline-block !important;
        transition: transform 0.15s ease;
    }
    .sio-dropdown-chevron.is-open {
        transform: rotate(180deg) !important;
    }
    .sio-dropdown-trigger:focus,
    .sio-dropdown.is-open .sio-dropdown-trigger {
        border-color: #ca6a04;
        box-shadow: 0 0 0 3px rgba(202,106,4,.12);
    }
    .sio-dropdown-menu {
        position: absolute;
        top: calc(100% + 4px);
        left: 0;
        right: 0;
        background: #fff;
        border: 1.5px solid #d1d5db;
        border-radius: 0.625rem;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.12), 0 8px 10px -6px rgba(0, 0, 0, 0.08);
        z-index: 50;
        overflow: hidden;
    }
    .sio-search-box {
        padding: 0.5rem;
        border-bottom: 1px solid #f3f4f6;
        background: #fafafa;
        position: sticky;
        top: 0;
        z-index: 10;
    }
    .sio-search-input {
        width: 100%;
        height: 2.125rem;
        padding: 0 1.75rem 0 0.625rem;
        font-size: 0.8125rem;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        outline: none;
        background: #fff;
        box-sizing: border-box;
    }
    .sio-search-input:focus {
        border-color: #ca6a04;
    }
    .sio-options-list {
        max-height: 220px;
        overflow-y: auto;
        padding: 0.25rem 0;
    }
    .sio-option-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.5rem 0.875rem;
        font-size: 0.875rem;
        color: #374151;
        cursor: pointer;
        transition: background .12s, color .12s;
        user-select: none;
    }
    .sio-option-item:hover {
        background: #fef3c7;
        color: #92400e;
    }
    .sio-option-item.is-active {
        background: #fde68a;
        color: #92400e;
        font-weight: 600;
    }

    .sio-icon-left {
        position: relative;
    }
    .sio-icon-left-icon {
        pointer-events: none;
        position: absolute;
        top: 0;
        bottom: 0;
        left: 0;
        display: flex;
        align-items: center;
        padding-left: 0.75rem;
        color: #9ca3af;
    }
    .sio-icon-left .sio-input {
        padding-left: 2.25rem;
    }
    .sio-label {
        display: block;
        font-size: 0.8125rem;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 0.375rem;
    }
    .sio-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 0.875rem;
        padding-top: 1.25rem;
        margin-top: 1.5rem;
        border-top: 1px solid #f3f4f6;
    }
    .sio-btn-cancel {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        height: 2.625rem;
        padding: 0 1.5rem;
        border: 1.5px solid #d1d5db;
        background: #fff;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        font-weight: 500;
        color: #374151;
        text-decoration: none;
        cursor: pointer;
        transition: background .15s, border-color .15s;
    }
    .sio-btn-cancel:hover { background: #f9fafb; border-color: #9ca3af; }
    .sio-btn-submit {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        height: 2.625rem;
        padding: 0 1.75rem;
        border: none;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        font-weight: 600;
        color: #fff;
        background: #ca6a04;
        cursor: pointer;
        transition: background .15s;
    }
    .sio-btn-submit:hover:not(:disabled) { background: #b45d03; }
    .sio-btn-submit:disabled { opacity: .5; cursor: not-allowed; }

    .sio-preview-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 0.875rem;
    }
    .sio-preview-label { color: #6b7280; font-size: 0.875rem; }
    .sio-preview-val { color: #111827; font-weight: 600; font-size: 0.875rem; }
    .sio-preview-divider { border: none; border-top: 1px solid #f3f4f6; margin: 1rem 0; }
    .sio-alert {
        display: flex; align-items: flex-start; gap: 0.5rem;
        border-radius: 0.5rem;
        padding: 0.625rem 0.75rem;
        margin-top: 1rem;
    }
    .sio-alert-red { background: #fef2f2; border: 1px solid #fecaca; }
    .sio-alert-amber { background: #fffbeb; border: 1px solid #fde68a; }
    [x-cloak] { display: none !important; }
</style>
@endpush

@section('content')

<div class="sio-container">

<div style="margin-bottom:1.5rem;">
    <h1 style="font-size:1.375rem;font-weight:700;color:#111827;margin:0 0 0.25rem;">Stock Adjustment</h1>
    <p style="font-size:0.875rem;color:#6b7280;margin:0;">Perform direct physical counts reconciliation, write-offs, or safety disposals.</p>
</div>

<form method="POST" action="{{ route('stock.stock-in-out.process') }}"
      x-data="stockAdjForm()"
      x-ref="formEl"
      @submit.prevent="submitForm()">
    @csrf

    <input type="hidden" name="transaction_type" :value="direction === 'increase' ? 'STOCK_IN' : 'STOCK_OUT'">
    <input type="hidden" name="reference_number" value="{{ old('reference_number', 'ADJ-' . date('Ymd') . '-' . strtoupper(\Illuminate\Support\Str::random(4))) }}">

    <div class="sio-layout">

        <div class="sio-form-card">

            <div class="sio-banner">
                <svg style="width:1.125rem;height:1.125rem;flex-shrink:0;color:#b45309;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                <p style="font-size:0.8125rem;font-weight:500;color:#92400e;margin:0;line-height:1.4;">
                    Direct balance corrections bypass normal supplier stock workflows. Be precise.
                </p>
            </div>

            <div class="sio-row-2" :style="chemOpen ? 'position: relative; z-index: 30;' : ''">
                <div>
                    <label class="sio-label" for="chemical_id">Target Substance <span style="color:#ef4444;">*</span></label>

                    <div class="sio-dropdown" :class="chemOpen ? 'is-open' : ''" @click.outside="chemOpen = false" @keydown.escape="chemOpen = false">
                        <input type="hidden" name="chemical_id" :value="selectedChemicalId" id="chemical_id">

                        <button type="button"
                                @click="chemOpen = !chemOpen; if(chemOpen) { reasonOpen = false; unitOpen = false; $nextTick(() => $refs.chemSearchInput?.focus()); }"
                                class="sio-dropdown-trigger"
                                :style="(chemError || '{{ $errors->has('chemical_id') ? '1' : '' }}') ? 'border-color: #ef4444;' : ''">
                            <span style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"
                                  :style="selectedChemical ? 'color: #111827; font-weight: 500;' : 'color: #6b7280;'"
                                  x-text="selectedChemical ? (selectedChemical.name + ' (' + selectedChemical.code + ')') : '— Select substance —'"></span>
                            <svg width="16" height="16"
                                 class="sio-dropdown-chevron"
                                 :class="chemOpen ? 'is-open' : ''"
                                 style="margin-left:0.5rem;"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <div x-show="chemOpen" x-cloak
                             class="sio-dropdown-menu">

                            <div class="sio-search-box">
                                <div style="position: relative;">
                                    <input type="text"
                                           x-ref="chemSearchInput"
                                           x-model="chemSearch"
                                           placeholder="Type to filter substances..."
                                           class="sio-search-input"
                                           @click.stop>
                                    <svg style="position: absolute; right: 0.625rem; top: 50%; transform: translateY(-50%); width: 0.875rem; height: 0.875rem; color: #9ca3af; pointer-events: none;"
                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                </div>
                            </div>

                            <div class="sio-options-list">
                                <template x-for="chem in filteredChemicals" :key="chem.id">
                                    <div @click="selectChemical(chem)"
                                         class="sio-option-item"
                                         :class="selectedChemicalId == chem.id ? 'is-active' : ''">
                                        <div>
                                            <span x-text="chem.name" style="font-weight: 500;"></span>
                                            <span x-text="'(' + chem.code + ')'" style="color: #6b7280; font-size: 0.75rem; margin-left: 0.25rem;"></span>
                                        </div>
                                        <span x-text="chem.stock.toFixed(2) + ' ' + chem.unit" style="font-size: 0.75rem; color: #6b7280; font-family: monospace;"></span>
                                    </div>
                                </template>
                                <div x-show="filteredChemicals.length === 0" style="padding: 1rem; text-align: center; color: #9ca3af; font-size: 0.8125rem;">
                                    No substances found.
                                </div>
                            </div>
                        </div>
                    </div>

                    <p x-show="chemError" x-text="chemError" style="font-size:0.75rem;color:#dc2626;margin-top:0.25rem;" x-cloak></p>
                    @error('chemical_id')<p style="font-size:0.75rem;color:#dc2626;margin-top:0.25rem;">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="sio-label">Current System Balance (Read-Only)</label>
                    <input type="text" readonly
                           class="sio-input sio-input-ro"
                           :value="selectedChemical ? selectedChemical.stock.toFixed(2) + ' ' + (selectedUnit || selectedChemical.unit) : ''"
                           placeholder="Select a substance first">
                </div>
            </div>

            <div style="margin-bottom:1.25rem;">
                <label class="sio-label">Adjustment Direction</label>
                <div class="sio-dir-grid">

                    <div class="sio-dir-btn" :class="direction==='increase' ? 'active-inc' : ''" @click="direction='increase'">
                        <span class="sio-radio" :style="direction==='increase' ? 'border-color:#3b82f6;' : ''">
                            <span class="sio-radio-dot" x-show="direction==='increase'" style="background:#3b82f6;"></span>
                        </span>
                        <span style="font-size:0.875rem;" :style="direction==='increase' ? 'color:#1d4ed8;font-weight:600;' : 'color:#374151;font-weight:500;'">
                            Increase (+) / Refill / Surplus Found
                        </span>
                    </div>

                    <div class="sio-dir-btn" :class="direction==='decrease' ? 'active-dec' : ''" @click="direction='decrease'">
                        <span class="sio-radio" :style="direction==='decrease' ? 'border-color:#ef4444;' : ''">
                            <span class="sio-radio-dot" x-show="direction==='decrease'" style="background:#ef4444;"></span>
                        </span>
                        <span style="font-size:0.875rem;" :style="direction==='decrease' ? 'color:#dc2626;font-weight:600;' : 'color:#374151;font-weight:500;'">
                            Decrease (-) / Spillage / Damage Disposal
                        </span>
                    </div>
                </div>
            </div>

            <div class="sio-row-3" :style="(reasonOpen || unitOpen) ? 'position: relative; z-index: 20;' : ''">
                <div>
                    <label class="sio-label" for="quantity">Adjustment Quantity <span style="color:#ef4444;">*</span></label>
                    <input type="number" id="quantity" name="quantity" step="0.01" min="0.001"
                           x-model.number="qty"
                           value="{{ old('quantity') }}"
                           placeholder="3.00"
                           class="sio-input"
                           style="{{ $errors->has('quantity') ? 'border-color:#f87171;' : '' }}"
                           required>
                    @error('quantity')<p style="font-size:0.75rem;color:#dc2626;margin-top:0.25rem;">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="sio-label" for="unit">Measurement Unit</label>

                    <div class="sio-dropdown" :class="unitOpen ? 'is-open' : ''" @click.outside="unitOpen = false" @keydown.escape="unitOpen = false">
                        <input type="hidden" name="unit" :value="selectedUnit" id="unit">
                        <button type="button"
                                @click="unitOpen = !unitOpen; if(unitOpen) { chemOpen = false; reasonOpen = false; }"
                                class="sio-dropdown-trigger">
                            <span x-text="selectedUnit" style="font-weight: 500; color: #111827;"></span>
                            <svg width="16" height="16"
                                 class="sio-dropdown-chevron"
                                 :class="unitOpen ? 'is-open' : ''"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="unitOpen" x-cloak class="sio-dropdown-menu">
                            <div class="sio-options-list">
                                <template x-for="u in ['kg', 'g', 'L', 'mL', 'pcs']" :key="u">
                                    <div @click="selectUnit(u)"
                                         class="sio-option-item"
                                         :class="selectedUnit === u ? 'is-active' : ''">
                                        <span x-text="u"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
                <div>
                    <label class="sio-label" for="reason">Primary Reason Code <span style="color:#ef4444;">*</span></label>

                    <div class="sio-dropdown" :class="reasonOpen ? 'is-open' : ''" @click.outside="reasonOpen = false" @keydown.escape="reasonOpen = false">
                        <input type="hidden" name="reason" :value="selectedReason" id="reason">
                        <button type="button"
                                @click="reasonOpen = !reasonOpen; if(reasonOpen) { chemOpen = false; unitOpen = false; }"
                                class="sio-dropdown-trigger"
                                :style="(reasonError || '{{ $errors->has('reason') ? '1' : '' }}') ? 'border-color: #ef4444;' : ''">
                            <span style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"
                                  :style="selectedReason ? 'color: #111827; font-weight: 500;' : 'color: #6b7280;'"
                                  x-text="selectedReasonLabel"></span>
                            <svg width="16" height="16"
                                 class="sio-dropdown-chevron"
                                 :class="reasonOpen ? 'is-open' : ''"
                                 style="margin-left:0.5rem;"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="reasonOpen" x-cloak class="sio-dropdown-menu">
                            <div class="sio-options-list">
                                <template x-for="r in reasons" :key="r.value">
                                    <div @click="selectReason(r.value)"
                                         class="sio-option-item"
                                         :class="selectedReason === r.value ? 'is-active' : ''">
                                        <span x-text="r.label"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                    <p x-show="reasonError" x-text="reasonError" style="font-size:0.75rem;color:#dc2626;margin-top:0.25rem;" x-cloak></p>
                    @error('reason')<p style="font-size:0.75rem;color:#dc2626;margin-top:0.25rem;">{{ $message }}</p>@enderror
                </div>
            </div>

            <div style="margin-bottom:1.25rem;">
                <label class="sio-label" for="notes">Audit Notes & Explanation</label>
                <textarea id="notes" name="notes" rows="3"
                          placeholder="Periodic stock count audit revealed 3 kg discrepancy in Cabinet A-2. Storage container seal in..."
                          class="sio-input"
                          style="resize:vertical;min-height:4.5rem;">{{ old('notes') }}</textarea>
            </div>

            <div class="sio-row-2" style="margin-bottom:1.5rem;">
                <div>
                    <label class="sio-label">Authorized Investigator</label>
                    <div class="sio-icon-left">
                        <span class="sio-icon-left-icon">
                            <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </span>
                        <input type="text" readonly class="sio-input sio-input-ro"
                               value="{{ auth()->user()->name }} ({{ match(auth()->user()->role) { 'ADMIN' => 'Lab Administrator', 'STOCK_MANAGER' => 'Stock Manager', 'AUDITOR' => 'Auditor', default => 'Lab Analyst' } }})">
                    </div>
                </div>
                <div>
                    <label class="sio-label" for="verification_date">Verification Date</label>
                    <div class="sio-icon-left">
                        <span class="sio-icon-left-icon">
                            <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </span>
                        <input type="date" id="verification_date" name="verification_date"
                               value="{{ old('verification_date', date('Y-m-d')) }}"
                               class="sio-input">
                    </div>
                </div>
            </div>

            <div class="sio-actions">
                <a href="{{ route('stock.index') }}" class="sio-btn-cancel">Cancel</a>
                <button type="submit" class="sio-btn-submit" :disabled="submitting">
                    <span x-text="submitting ? 'Submitting...' : 'Submit Adjustment'">Submit Adjustment</span>
                </button>
            </div>
        </div>

        <div class="sio-preview-card">
            <h2 style="font-size:1rem;font-weight:700;color:#111827;margin:0 0 1.25rem;">Adjustment Preview</h2>

            <div style="display:flex;flex-direction:column;gap:0.75rem;">

                <div class="sio-preview-row">
                    <span class="sio-preview-label">Before Level</span>
                    <span class="sio-preview-val"
                          x-text="selectedChemical ? selectedChemical.stock.toFixed(2) + ' ' + (selectedUnit || selectedChemical.unit) : '—'">—</span>
                </div>

                <div class="sio-preview-row">
                    <span class="sio-preview-label">Adjustment Amount</span>
                    <span style="font-size:0.875rem;font-weight:700;color:#ca6a04;"
                          x-text="qty > 0 ? (direction==='decrease'?'-':'+')+qty.toFixed(2)+' '+(selectedUnit||selectedChemical?.unit||'') : '—'">—</span>
                </div>

                <hr class="sio-preview-divider">

                <div class="sio-preview-row">
                    <span style="font-weight:700;color:#111827;font-size:0.875rem;">After Level</span>
                    <span style="font-size:1rem;font-weight:700;color:#111827;"
                          x-text="selectedChemical && qty > 0
                            ? (direction==='decrease'
                                ? Math.max(0,selectedChemical.stock-qty).toFixed(2)
                                : (selectedChemical.stock+qty).toFixed(2)) + ' ' + (selectedUnit||selectedChemical.unit)
                            : (selectedChemical ? selectedChemical.stock.toFixed(2)+' '+(selectedUnit||selectedChemical.unit) : '—')">—</span>
                </div>

                <div class="sio-preview-row">
                    <span class="sio-preview-label">Type</span>
                    <span style="font-size:0.75rem;font-weight:700;letter-spacing:.05em;"
                          :style="direction==='decrease' ? 'color:#ef4444;' : 'color:#16a34a;'"
                          x-text="direction==='decrease' ? 'DECREASE (-)' : 'INCREASE (+)'">DECREASE (-)</span>
                </div>

            </div>

            <div x-show="direction==='decrease' && selectedChemical && qty > selectedChemical.stock"
                 x-cloak class="sio-alert sio-alert-red">
                <svg style="width:1rem;height:1rem;flex-shrink:0;color:#ef4444;margin-top:0.125rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p style="font-size:0.75rem;color:#b91c1c;margin:0;line-height:1.4;">Insufficient stock. Requested reduction exceeds current system balance.</p>
            </div>

            <div x-show="selectedChemical && qty > 0 && selectedChemical.stock > 0 && (qty/selectedChemical.stock) >= 0.20 && !(direction==='decrease' && qty > selectedChemical.stock)"
                 x-cloak class="sio-alert sio-alert-amber">
                <svg style="width:1rem;height:1rem;flex-shrink:0;color:#d97706;margin-top:0.125rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <p style="font-size:0.75rem;color:#92400e;margin:0;line-height:1.4;">Significant adjustment (≥20% of stock). This will be logged in the audit trail.</p>
            </div>

        </div>

    </div>
</form>

</div>

@endsection

@push('scripts')
<script>
function stockAdjForm() {
    const chemicalsData = {!! $chemicalsJson !!};

    const reasonsData = [
        { value: 'Physical Count Correction', label: 'Physical Count Correction' },
        { value: 'Spillage or breakage', label: 'Spillage / Damage Disposal' },
        { value: 'Evaporation / natural loss', label: 'Evaporation / Natural Loss' },
        { value: 'Contamination', label: 'Contamination' },
        { value: 'Lab usage', label: 'Lab usage' },
        { value: 'Disposal / expired', label: 'Disposal / expired' },
        { value: 'Purchase receipt', label: 'Purchase receipt' },
        { value: 'Return from lab', label: 'Return from lab' },
        { value: 'Transfer to another location', label: 'Transfer to another location' },
        { value: 'Initial inventory setup', label: 'Initial inventory setup' },
        { value: 'Other', label: 'Other' },
    ];

    const initialChemId = '{{ old('chemical_id', $selected?->id ?? '') }}';
    let initialChem = {!! $selectedJson !!};
    if (!initialChem && initialChemId) {
        initialChem = chemicalsData.find(c => c.id == initialChemId) || null;
    }

    return {
        chemicals: chemicalsData,
        reasons: reasonsData,
        selectedChemicalId: initialChemId,
        selectedChemical: initialChem,
        selectedUnit: '{{ old('unit', $selected?->unit ?? 'kg') }}',
        selectedReason: '{{ old('reason', '') }}',
        qty: {{ old('quantity') !== null && old('quantity') !== '' ? (float)old('quantity') : 0 }},
        direction: '{{ old('transaction_type') === 'STOCK_IN' ? 'increase' : 'decrease' }}',
        submitting: false,

        chemOpen: false,
        chemSearch: '',
        reasonOpen: false,
        unitOpen: false,

        chemError: '',
        reasonError: '',

        get filteredChemicals() {
            if (!this.chemSearch.trim()) return this.chemicals;
            const q = this.chemSearch.toLowerCase();
            return this.chemicals.filter(c =>
                c.name.toLowerCase().includes(q) || c.code.toLowerCase().includes(q)
            );
        },

        get selectedReasonLabel() {
            if (!this.selectedReason) return '— Select reason —';
            const found = this.reasons.find(r => r.value === this.selectedReason);
            return found ? found.label : this.selectedReason;
        },

        selectChemical(chem) {
            this.selectedChemicalId = chem.id;
            this.selectedChemical = chem;
            this.selectedUnit = chem.unit || 'kg';
            this.chemOpen = false;
            this.chemSearch = '';
            this.chemError = '';
        },

        selectReason(val) {
            this.selectedReason = val;
            this.reasonOpen = false;
            this.reasonError = '';
        },

        selectUnit(u) {
            this.selectedUnit = u;
            this.unitOpen = false;
        },

        submitForm() {
            this.chemError = '';
            this.reasonError = '';

            if (!this.selectedChemicalId) {
                this.chemError = 'Please select a target substance.';
                this.chemOpen = true;
                return;
            }
            if (!this.selectedReason) {
                this.reasonError = 'Please select a primary reason code.';
                this.reasonOpen = true;
                return;
            }
            if (!this.qty || this.qty <= 0) {
                const qEl = document.getElementById('quantity');
                if (qEl) qEl.focus();
                return;
            }

            this.submitting = true;
            this.$refs.formEl.submit();
        }
    };
}
</script>
@endpush

