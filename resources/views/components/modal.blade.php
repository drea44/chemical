@props([
    'id'          => null,
    'title'       => 'Confirm',
    'triggerText' => 'Open',
    'triggerVariant' => 'primary',
    'size'        => 'md',
])

@php
    $id = $id ?? 'modal-' . uniqid();
    $maxWidth = ['sm' => 'max-w-sm', 'md' => 'max-w-lg', 'lg' => 'max-w-2xl', 'xl' => 'max-w-4xl'][$size] ?? 'max-w-lg';
@endphp

<div x-data="{ open: false }" id="{{ $id }}-wrapper">
    <!-- Trigger -->
    <x-button variant="{{ $triggerVariant }}" @click="open = true">{{ $triggerText }}</x-button>

    <!-- Overlay -->
    <div x-show="open" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         style="background:rgba(0,0,0,0.4);"
         @click.self="open = false">

        <div class="bg-white rounded-lg shadow-xl w-full {{ $maxWidth }}"
             x-transition:enter="ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">

            <!-- Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <h3 class="text-base font-semibold text-gray-900">{{ $title }}</h3>
                <button @click="open = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <!-- Body -->
            <div class="px-6 py-5">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
