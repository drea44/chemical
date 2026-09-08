@props(['label' => '', 'name' => '', 'type' => 'text', 'value' => '', 'placeholder' => '', 'required' => false, 'hint' => ''])

<div class="space-y-1">
    @if($label)
    <label for="{{ $name }}" class="block text-sm font-medium text-gray-700">
        {{ $label }}@if($required)<span class="text-red-500 ml-0.5">*</span>@endif
    </label>
    @endif
    <input
        type="{{ $type }}"
        id="{{ $name }}"
        name="{{ $name }}"
        value="{{ old($name, $value) }}"
        placeholder="{{ $placeholder }}"
        @if($required) required @endif
        {{ $attributes->merge(['class' => 'block w-full rounded border border-gray-300 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 transition-colors ' . ($errors->has($name) ? 'border-red-300 bg-red-50' : '')]) }}
    >
    @error($name)
        <p class="text-xs text-red-600 flex items-center gap-1"><i data-lucide="alert-circle" class="w-3 h-3"></i>{{ $message }}</p>
    @enderror
    @if($hint && !$errors->has($name))
        <p class="text-xs text-gray-400">{{ $hint }}</p>
    @endif
</div>
