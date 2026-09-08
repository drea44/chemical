@props([
    'label'   => '',
    'value'   => '',
    'icon'    => 'box',
    'color'   => 'blue',
    'trend'   => null,
    'trendUp' => null,
    'href'    => null,
])

@php
    $colors = [
        'blue'   => ['bg' => 'bg-blue-50',   'icon' => 'text-blue-600',  'border' => 'border-blue-100'],
        'green'  => ['bg' => 'bg-green-50',  'icon' => 'text-green-600', 'border' => 'border-green-100'],
        'yellow' => ['bg' => 'bg-yellow-50', 'icon' => 'text-yellow-600','border' => 'border-yellow-100'],
        'red'    => ['bg' => 'bg-red-50',    'icon' => 'text-red-600',   'border' => 'border-red-100'],
        'orange' => ['bg' => 'bg-orange-50', 'icon' => 'text-orange-600','border' => 'border-orange-100'],
        'gray'   => ['bg' => 'bg-gray-50',   'icon' => 'text-gray-600',  'border' => 'border-gray-100'],
    ];
    $c = $colors[$color] ?? $colors['blue'];
    $tag = $href ? 'a' : 'div';
@endphp

<{{ $tag }} @if($href) href="{{ $href }}" @endif
    class="bg-white border border-gray-200 rounded-lg p-5 flex items-start gap-4 {{ $href ? 'hover:shadow-md transition-shadow cursor-pointer' : '' }}">
    <div class="w-10 h-10 rounded-lg {{ $c['bg'] }} {{ $c['border'] }} border flex items-center justify-center flex-shrink-0">
        <i data-lucide="{{ $icon }}" class="w-5 h-5 {{ $c['icon'] }}"></i>
    </div>
    <div class="flex-1 min-w-0">
        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">{{ $label }}</p>
        <p class="text-2xl font-bold text-gray-900 mt-0.5 leading-none">{{ $value }}</p>
        @if($trend)
        <p class="text-xs mt-1 {{ $trendUp ? 'text-green-600' : 'text-red-500' }}">
            <i data-lucide="{{ $trendUp ? 'trending-up' : 'trending-down' }}" class="w-3 h-3 inline"></i>
            {{ $trend }}
        </p>
        @endif
    </div>
</{{ $tag }}>
