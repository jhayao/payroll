@props([
    'label' => null,
    'name',
    'options' => [],
    'disabled' => false,
    'wrapperClass' => 'mb-6',
])

@php
    $selectId = $attributes->get('id', $name);
    $hasError = $errors->has($name);
@endphp

<div class="{{ $wrapperClass }}">
    @if($label)
        <label for="{{ $selectId }}" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
            {{ $label }}
        </label>
    @endif

    <select
        id="{{ $selectId }}"
        name="{{ $name }}"
        {{ $disabled ? 'disabled' : '' }}
        {{ $attributes->class([
            'bg-gray-50 border text-sm rounded block w-full p-2',
            'text-gray-900 focus:ring-blue-500 focus:border-blue-500 border-gray-300',
            'dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500',
            'border-red-500 focus:border-red-500 focus:ring-red-500' => $hasError,
        ]) }}
    >
        @foreach($options as $key => $value)
            <option value="{{ $key }}" {{ old($name, $attributes->get('value')) == $key ? 'selected' : '' }}>
                {{ $value }}
            </option>
        @endforeach
    </select>

    @if($hasError)
        <p class="mt-2 text-sm text-red-600 dark:text-red-500">
            {{ $errors->first($name) }}
        </p>
    @endif
</div>
