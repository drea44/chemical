@extends('layouts.app')

@php
    $title      = 'Settings';
    $breadcrumb = [['label' => 'Settings', 'url' => route('settings.index')]];
    $s = fn($key, $default = '') => $settings[$key]?->value ?? $default;
    $activeTab = request('tab', 'general');
@endphp

@section('title', 'Settings')

@section('content')

<div class="grid grid-cols-1 lg:grid-cols-4 gap-5">

    {{-- LEFT: Sidebar Nav --}}
    <div class="lg:col-span-1">
        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
            @foreach([
                ['tab' => 'general',      'label' => 'General Settings',      'icon' => 'settings'],
                ['tab' => 'notifications','label' => 'Notifications',          'icon' => 'bell'],
                ['tab' => 'thresholds',   'label' => 'Stock Thresholds',       'icon' => 'bar-chart-2'],
                ['tab' => 'export',       'label' => 'Data Export & Backup',   'icon' => 'download'],
                ['tab' => 'integration',  'label' => 'System Integration',     'icon' => 'plug'],
            ] as $nav)
            <a href="{{ route('settings.index', ['tab' => $nav['tab']]) }}"
               class="flex items-center gap-3 px-4 py-3 text-sm font-medium transition-colors border-b border-gray-100 last:border-0
                      {{ $activeTab === $nav['tab']
                          ? 'bg-blue-600 text-white'
                          : 'text-gray-600 hover:bg-gray-50' }}">
                <i data-lucide="{{ $nav['icon'] }}" class="w-4 h-4 flex-shrink-0"></i>
                {{ $nav['label'] }}
            </a>
            @endforeach
        </div>
    </div>

    {{-- RIGHT: Content Area --}}
    <div class="lg:col-span-3">
        <form method="POST" action="{{ route('settings.update') }}" class="space-y-5">
            @csrf @method('PUT')
            <input type="hidden" name="tab" value="{{ $activeTab }}">

            @if($activeTab === 'general')
            {{-- General Configuration --}}
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <h2 class="text-base font-bold text-gray-900 mb-1">General Configuration</h2>
                <p class="text-xs text-gray-500 mb-6">Basic parameters for terminal identification and localized metrics.</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Application Name</label>
                        <input type="text" name="system_name"
                               value="{{ $s('system_name', 'Chemical Stock OS') }}"
                               class="block w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Organization Name</label>
                        <input type="text" name="organization"
                               value="{{ $s('organization', 'Contra Labs Enterprise') }}"
                               placeholder="Your organization name"
                               class="block w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-semibold text-gray-400 uppercase tracking-widest mb-1">Standard Unit</label>
                        <div class="relative">
                            <select name="default_unit"
                                    class="block w-full appearance-none rounded border border-gray-300 px-3 py-2 pr-8 text-sm text-gray-700 bg-white focus:border-blue-500 focus:outline-none">
                                <option value="L_kg" @selected($s('default_unit') === 'L_kg')>Liters (L) & Kilograms (kg)</option>
                                <option value="mL_g" @selected($s('default_unit') === 'mL_g')>Milliliters (mL) & Grams (g)</option>
                                <option value="custom">Custom</option>
                            </select>
                            <i data-lucide="chevron-down" class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"></i>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-400 uppercase tracking-widest mb-1">Date Display Template</label>
                        <div class="relative">
                            <select name="date_format"
                                    class="block w-full appearance-none rounded border border-gray-300 px-3 py-2 pr-8 text-sm text-gray-700 bg-white focus:border-blue-500 focus:outline-none">
                                <option value="d-M-Y" @selected($s('date_format') === 'd-M-Y')>DD-MMM-YYYY (e.g. 12-Dec-2026)</option>
                                <option value="Y-m-d" @selected($s('date_format') === 'Y-m-d')>YYYY-MM-DD</option>
                                <option value="d/m/Y" @selected($s('date_format') === 'd/m/Y')>DD/MM/YYYY</option>
                                <option value="m/d/Y" @selected($s('date_format') === 'm/d/Y')>MM/DD/YYYY (US)</option>
                            </select>
                            <i data-lucide="chevron-down" class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Notification Hub --}}
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <h2 class="text-base font-bold text-gray-900 mb-1">Notification Hub</h2>
                <p class="text-xs text-gray-500 mb-6">System status changes and critical warning channels routing configuration.</p>

                <div class="space-y-5">
                    @foreach([
                        ['key' => 'low_stock_alert',
                         'label' => 'Low Stock Level Alerts',
                         'desc'  => 'Notify workspace supervisor when chemical quantities drop below customized thresholds.',
                         'default' => '1'],
                        ['key' => 'critical_stock_alert',
                         'label' => 'Critical Risk Alert',
                         'desc'  => 'Trigger high-importance system alarms on emergency storage containment depletion.',
                         'default' => '1'],
                        ['key' => 'expiry_alert',
                         'label' => 'Near Expiry Alerts (30 Days)',
                         'desc'  => 'Generate daily bulletins for compounds scheduled to expire within a 30-day window.',
                         'default' => '1'],
                        ['key' => 'expired_block',
                         'label' => 'Expired Substance Block',
                         'desc'  => 'Instantly notify terminal of automatically disabled compounds.',
                         'default' => '0'],
                        ['key' => 'epa_email_audit',
                         'label' => 'EPA/OSHA Email Auditing',
                         'desc'  => 'Email daily transaction logs for governmental regulatory audit records.',
                         'default' => '0'],
                    ] as $item)
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1">
                            <p class="text-sm font-semibold text-gray-800">{{ $item['label'] }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">{{ $item['desc'] }}</p>
                        </div>
                        {{-- iOS-style toggle --}}
                        <label class="relative inline-flex items-center cursor-pointer flex-shrink-0 mt-0.5">
                            <input type="checkbox" name="{{ $item['key'] }}" value="1"
                                   {{ $s($item['key'], $item['default']) === '1' ? 'checked' : '' }}
                                   class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-300 peer-checked:bg-blue-600 rounded-full transition-colors duration-200 peer-focus:ring-2 peer-focus:ring-blue-500/30"></div>
                            <div class="absolute left-0.5 top-0.5 bg-white w-5 h-5 rounded-full shadow transition-transform duration-200 peer-checked:translate-x-5"></div>
                        </label>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Standard Stock Warning Metrics --}}
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <h2 class="text-base font-bold text-gray-900 mb-1">Standard Stock Warning Metrics</h2>
                <p class="text-xs text-gray-500 mb-6">Unified parameters used to trigger critical status indicators across the entire workspace registry.</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-8">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Global Low Stock Trigger (%)</label>
                        <div class="flex items-center gap-0">
                            <input type="number" name="low_stock_threshold"
                                   value="{{ $s('low_stock_threshold', '15') }}"
                                   class="block w-full rounded-l border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                            <span class="px-3 py-2 bg-gray-50 border border-l-0 border-gray-300 rounded-r text-xs text-gray-500 whitespace-nowrap">
                                OF MAX CAPACITY
                            </span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Global Critical Depletion Trigger (%)</label>
                        <div class="flex items-center gap-0">
                            <input type="number" name="critical_stock_threshold"
                                   value="{{ $s('critical_stock_threshold', '5') }}"
                                   class="block w-full rounded-l border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                            <span class="px-3 py-2 bg-gray-50 border border-l-0 border-gray-300 rounded-r text-xs text-gray-500 whitespace-nowrap">
                                OF MAX CAPACITY
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                    <a href="{{ route('settings.index') }}"
                       class="px-5 py-2 border border-gray-300 text-gray-600 text-sm font-medium rounded hover:bg-gray-50 transition-colors">
                        Reset to Default
                    </a>
                    <button type="submit"
                            class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded transition-colors">
                        Commit Changes
                    </button>
                </div>
            </div>

            @elseif($activeTab === 'notifications')
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <h2 class="text-base font-bold text-gray-900 mb-1">Notification Settings</h2>
                <p class="text-xs text-gray-500 mb-4">Configure how and when system notifications are delivered.</p>
                <p class="text-sm text-gray-400 italic">Select "General Settings" tab to manage notification toggles.</p>
            </div>

            @elseif($activeTab === 'thresholds')
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <h2 class="text-base font-bold text-gray-900 mb-1">Stock Thresholds</h2>
                <p class="text-xs text-gray-500 mb-6">Set thresholds for low stock and expiry warnings.</p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Low Stock Threshold (%)</label>
                        <input type="number" name="low_stock_threshold" value="{{ $s('low_stock_threshold', '25') }}"
                               class="block w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
                        <p class="text-xs text-gray-400 mt-1">% of minimum stock to trigger LOW alert</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Critical Threshold (%)</label>
                        <input type="number" name="critical_stock_threshold" value="{{ $s('critical_stock_threshold', '10') }}"
                               class="block w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
                        <p class="text-xs text-gray-400 mt-1">% of minimum stock to trigger CRITICAL alert</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Expiry Warning (days)</label>
                        <input type="number" name="expiry_warning_days" value="{{ $s('expiry_warning_days', '30') }}"
                               class="block w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
                        <p class="text-xs text-gray-400 mt-1">Days before expiry to show warning</p>
                    </div>
                </div>
                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                    <a href="{{ route('settings.index', ['tab' => 'thresholds']) }}"
                       class="px-5 py-2 border border-gray-300 text-gray-600 text-sm font-medium rounded hover:bg-gray-50">Reset to Default</a>
                    <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded">Commit Changes</button>
                </div>
            </div>

            @else
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <h2 class="text-base font-bold text-gray-900 mb-4">{{ ucfirst($activeTab) }} Settings</h2>
                <p class="text-sm text-gray-400 italic">This section is coming soon.</p>
            </div>
            @endif

        </form>
    </div>
</div>

@endsection
