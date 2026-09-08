@extends('layouts.app')

@php
    $title      = 'System Settings';
    $breadcrumb = [['label' => 'Settings', 'url' => route('settings.index')]];
    $s = fn($key, $default = '') => $settings[$key]?->value ?? $default;
@endphp

@section('title', 'System Settings')

@section('content')

<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-xl font-bold text-gray-900">System Settings</h1>
        <p class="text-sm text-gray-500 mt-0.5">Configure application behaviour and alert thresholds</p>
    </div>
</div>

<form method="POST" action="{{ route('settings.update') }}" class="space-y-5 max-w-3xl">
    @csrf @method('PUT')

    <!-- General -->
    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
            <h2 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                <i data-lucide="building-2" class="w-4 h-4 text-blue-500"></i>General
            </h2>
        </div>
        <div class="px-6 py-5 grid grid-cols-1 md:grid-cols-2 gap-4">
            <x-form-input label="System Name" name="system_name" :value="$s('system_name', 'Chemical Stock OS')" />
            <x-form-input label="Organization" name="organization" :value="$s('organization')" placeholder="Your lab or company name" />
            <x-form-select label="Timezone" name="timezone" :selected="$s('timezone', 'Asia/Jakarta')"
                :options="['Asia/Jakarta' => 'Asia/Jakarta (WIB)', 'Asia/Makassar' => 'Asia/Makassar (WITA)', 'Asia/Jayapura' => 'Asia/Jayapura (WIT)', 'UTC' => 'UTC']" />
            <x-form-select label="Date Format" name="date_format" :selected="$s('date_format', 'd M Y')"
                :options="['d M Y' => '01 Jan 2024', 'Y-m-d' => '2024-01-01', 'd/m/Y' => '01/01/2024', 'm/d/Y' => '01/01/2024 (US)']" />
        </div>
    </div>

    <!-- Stock Thresholds -->
    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
            <h2 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                <i data-lucide="sliders-horizontal" class="w-4 h-4 text-blue-500"></i>Stock Thresholds
            </h2>
        </div>
        <div class="px-6 py-5 grid grid-cols-1 md:grid-cols-3 gap-4">
            <x-form-input label="Low Stock Threshold (%)" name="low_stock_threshold" type="number"
                :value="$s('low_stock_threshold', '25')" hint="% of minimum stock to trigger LOW alert" />
            <x-form-input label="Critical Stock Threshold (%)" name="critical_stock_threshold" type="number"
                :value="$s('critical_stock_threshold', '10')" hint="% of minimum stock to trigger CRITICAL alert" />
            <x-form-input label="Expiry Warning (days)" name="expiry_warning_days" type="number"
                :value="$s('expiry_warning_days', '30')" hint="Days before expiry to show warning" />
        </div>
    </div>

    <!-- Alerts -->
    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
            <h2 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                <i data-lucide="bell" class="w-4 h-4 text-blue-500"></i>Alerts & Notifications
            </h2>
        </div>
        <div class="px-6 py-5 space-y-4">
            @foreach([
                ['key' => 'low_stock_alert',     'label' => 'Low Stock Alerts',     'desc' => 'Show dashboard alerts when chemicals fall below minimum threshold'],
                ['key' => 'critical_stock_alert', 'label' => 'Critical Stock Alerts','desc' => 'Highlight critical stock situations prominently on dashboard'],
                ['key' => 'expiry_alert',         'label' => 'Expiry Alerts',        'desc' => 'Warn when chemicals are approaching their expiry date'],
                ['key' => 'email_notification',   'label' => 'Email Notifications',  'desc' => 'Send email notifications for critical alerts (requires mail config)'],
            ] as $alert)
            <label class="flex items-start gap-4 cursor-pointer group">
                <div class="relative mt-0.5">
                    <input type="checkbox" name="{{ $alert['key'] }}" id="{{ $alert['key'] }}"
                           {{ $s($alert['key'], '1') === '1' ? 'checked' : '' }}
                           class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-800 group-hover:text-blue-600 transition-colors">{{ $alert['label'] }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $alert['desc'] }}</p>
                </div>
            </label>
            @endforeach
        </div>
    </div>

    <!-- Security -->
    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
            <h2 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                <i data-lucide="shield-check" class="w-4 h-4 text-blue-500"></i>Security
            </h2>
        </div>
        <div class="px-6 py-5 space-y-4">
            <x-form-input label="Session Timeout (minutes)" name="session_timeout" type="number"
                :value="$s('session_timeout', '120')" hint="Auto-logout after inactivity" />
            <label class="flex items-start gap-4 cursor-pointer">
                <input type="checkbox" name="two_factor_auth" id="two_factor_auth"
                       {{ $s('two_factor_auth', '0') === '1' ? 'checked' : '' }}
                       class="mt-0.5 w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                <div>
                    <p class="text-sm font-medium text-gray-800">Two-Factor Authentication</p>
                    <p class="text-xs text-gray-400 mt-0.5">Require 2FA for all admin accounts (not yet implemented)</p>
                </div>
            </label>
        </div>
    </div>

    <div class="flex justify-end gap-3 pb-4">
        <x-button href="{{ route('settings.index') }}" variant="secondary">Discard</x-button>
        <x-button type="submit" icon="save">Save Settings</x-button>
    </div>
</form>

@endsection
