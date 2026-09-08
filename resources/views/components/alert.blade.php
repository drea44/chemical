@props(['type' => 'success', 'message' => '', 'class' => ''])

@php
    $config = [
        'success' => ['bg' => 'bg-green-50',  'border' => 'border-green-200', 'text' => 'text-green-800',  'icon' => 'check-circle',    'iconColor' => 'text-green-500'],
        'error'   => ['bg' => 'bg-red-50',    'border' => 'border-red-200',   'text' => 'text-red-800',    'icon' => 'x-circle',        'iconColor' => 'text-red-500'],
        'warning' => ['bg' => 'bg-yellow-50', 'border' => 'border-yellow-200','text' => 'text-yellow-800', 'icon' => 'alert-triangle',  'iconColor' => 'text-yellow-500'],
        'info'    => ['bg' => 'bg-blue-50',   'border' => 'border-blue-200',  'text' => 'text-blue-800',   'icon' => 'info',            'iconColor' => 'text-blue-500'],
    ];
    $c = $config[$type] ?? $config['info'];
@endphp

<div x-data="{ show: true }" x-show="show" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
     class="flex items-start gap-3 p-4 rounded-lg border {{ $c['bg'] }} {{ $c['border'] }} {{ $class }}">
    <i data-lucide="{{ $c['icon'] }}" class="w-5 h-5 flex-shrink-0 mt-0.5 {{ $c['iconColor'] }}"></i>
    <p class="text-sm {{ $c['text'] }} flex-1">{{ $message }}</p>
    <button @click="show = false" class="flex-shrink-0 {{ $c['iconColor'] }} hover:opacity-70 transition-opacity">
        <i data-lucide="x" class="w-4 h-4"></i>
    </button>
</div>
