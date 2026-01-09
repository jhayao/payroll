@props([
    'label' => null,
    'name',
    'type' => 'text',
    'disabled' => false,
    'readonly' => false,
    'wrapperClass' => 'mb-6',
    'notice' => null
])

@php
    $inputId = $attributes->get('id', $name);
    $hasError = $errors->has($name);
@endphp

<div class="{{ $wrapperClass }}">
    @if($label)
        <label for="{{ $inputId }}" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
            {{ $label }} 
            @if ($notice)
            <span class="text-gray-500">{{ $notice }}</span>
            @endif
        </label>
    @endif

    <input
        id="{{ $inputId }}"
        name="{{ $name }}"
        type="{{ $type }}"
        {{ $disabled ? 'disabled' : '' }}
        {{ $readonly ? 'readonly' : '' }}
        {{ $attributes->class([
            'bg-gray-50 border text-sm rounded block w-full p-2',
            'text-gray-900 focus:ring-blue-500 focus:border-blue-500 border-gray-300',
            'dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500',
            'border-red-500 focus:border-red-500 focus:ring-red-500' => $hasError,
        ]) }}
        value="{{ old($name, $attributes->get('value')) }}"
    >

    @if($hasError)
        <p class="mt-2 text-sm text-red-600 dark:text-red-500">
            {{ $errors->first($name) }}
        </p>
    @endif
</div>
