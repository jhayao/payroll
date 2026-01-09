@props([
    'label' => null,
    'name',
    'checked' => false,
    'value' => 1,
    'wrapperClass' => 'mb-4',
])

@php
    $hasError = $errors->has($name);
@endphp

<div class="{{ $wrapperClass }}">
    <div class="flex items-center">
        <input 
            id="{{ $name }}" 
            type="checkbox" 
            name="{{ $name }}" 
            value="{{ $value }}"
            {{ old($name, $checked) ? 'checked' : '' }}
            {{ $attributes->class([
                'w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500',
                'dark:bg-gray-700 dark:border-gray-600 dark:focus:ring-blue-600',
                'border-red-500 focus:ring-red-500' => $hasError
            ]) }}
        >
        @if($label)
            <label for="{{ $name }}" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                {{ $label }}
            </label>
        @endif
    </div>

    @if($hasError)
        <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $errors->first($name) }}</p>
    @endif
</div>
