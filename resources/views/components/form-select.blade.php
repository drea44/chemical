@props(['label' => '', 'name' => '', 'required' => false, 'options' => [], 'selected' => '', 'placeholder' => 'Select...'])

<div class="space-y-1">
    @if($label)
    <label for="{{ $name }}" class="block text-sm font-medium text-gray-700">
        {{ $label }}@if($required)<span class="text-red-500 ml-0.5">*</span>@endif
    </label>
    @endif
    <select
        id="{{ $name }}"
        name="{{ $name }}"
        @if($required) required @endif
        {{ $attributes->merge(['class' => 'block w-full rounded border border-gray-300 px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 transition-colors bg-white ' . ($errors->has($name) ? 'border-red-300 bg-red-50' : '')]) }}
    >
        <option value="">{{ $placeholder }}</option>
        @foreach($options as $val => $label)
            <option value="{{ $val }}" @selected(old($name, $selected) == $val)>{{ $label }}</option>
        @endforeach
    </select>
    @error($name)
        <p class="text-xs text-red-600 flex items-center gap-1"><i data-lucide="alert-circle" class="w-3 h-3"></i>{{ $message }}</p>
    @enderror
</div>
