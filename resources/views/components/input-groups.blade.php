@props([
    'label' => null,
    'names' => [], // array of field names
    'wrapperClass' => 'mb-6',
])

<div class="{{ $wrapperClass }}">
    @if($label)
        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ $label }}</label>
    @endif

    @php
        $colCount = count($names);
        $gridCols = match(true) {
            $colCount >= 4 => 'grid-cols-4',
            $colCount === 3 => 'grid-cols-3',
            $colCount === 2 => 'grid-cols-2',
            default => 'grid-cols-1',
        };
    @endphp

    <div class="grid {{ $gridCols }} gap-2">
        @foreach($names as $field)
            @php
                $hasError = $errors->has($field);
            @endphp
            <div class="flex flex-col">
                <input
                    name="{{ $field }}"
                    value="{{ old($field) }}"
                    {{ $attributes->class([
                        'bg-gray-50 border text-sm rounded w-full p-2.5',
                        'text-gray-900 focus:ring-blue-500 focus:border-blue-500 border-gray-300',
                        'dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500',
                        'border-red-500 focus:border-red-500 focus:ring-red-500' => $hasError,
                    ]) }}
                    placeholder="{{ ucfirst(str_replace('_', ' ', $field)) }}"
                >

                @if($hasError)
                    <p class="mt-1 text-sm text-red-600 dark:text-red-500">
                        {{ $errors->first($field) }}
                    </p>
                @endif
            </div>
        @endforeach
    </div>
</div>
