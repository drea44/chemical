@props([
    'type'    => 'button',
    'variant' => 'primary',
    'size'    => 'md',
    'icon'    => null,
    'href'    => null,
])

@php
    $variants = [
        'primary'   => 'bg-blue-600 hover:bg-blue-700 text-white border border-blue-600',
        'secondary' => 'bg-white hover:bg-gray-50 text-gray-700 border border-gray-300',
        'danger'    => 'bg-red-600 hover:bg-red-700 text-white border border-red-600',
        'ghost'     => 'bg-transparent hover:bg-gray-100 text-gray-600 border border-transparent',
        'success'   => 'bg-green-600 hover:bg-green-700 text-white border border-green-600',
    ];
    $sizes = [
        'sm' => 'px-3 py-1.5 text-xs',
        'md' => 'px-4 py-2 text-sm',
        'lg' => 'px-5 py-2.5 text-sm',
    ];
    $base    = 'inline-flex items-center gap-2 font-medium rounded transition-colors duration-150 disabled:opacity-50 disabled:cursor-not-allowed';
    $classes = $base . ' ' . ($variants[$variant] ?? $variants['primary']) . ' ' . ($sizes[$size] ?? $sizes['md']);
    $tag     = $href ? 'a' : 'button';
@endphp

<{{ $tag }}
    @if($href) href="{{ $href }}" @else type="{{ $type }}" @endif
    {{ $attributes->merge(['class' => $classes]) }}>
    @if($icon)
        <i data-lucide="{{ $icon }}" class="w-4 h-4 flex-shrink-0"></i>
    @endif
    {{ $slot }}
</{{ $tag }}>
