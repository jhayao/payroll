@props([
    'label' => null,
    'name',
    'options' => [], // key => label
    'wrapperClass' => 'mb-6',
])

@php
    $hasError = $errors->has($name);
@endphp

<div class="{{ $wrapperClass }}">
    @if($label)
        <p class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ $label }}</p>
    @endif

    @foreach($options as $key => $text)
        <div class="flex items-center mb-1">
            <input
                id="{{ $name . '_' . $key }}"
                type="radio"
                name="{{ $name }}"
                value="{{ $key }}"
                {{ old($name) == $key ? 'checked' : '' }}
                {{ $attributes->class([
                    'w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500',
                    'dark:bg-gray-700 dark:border-gray-600 dark:focus:ring-blue-600',
                    'border-red-500 focus:ring-red-500' => $hasError
                ]) }}
            >
            <label for="{{ $name . '_' . $key }}" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                {{ $text }}
            </label>
        </div>
    @endforeach

    @if($hasError)
        <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $errors->first($name) }}</p>
    @endif
</div>
