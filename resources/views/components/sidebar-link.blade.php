@php
    // Compare current request URL with the given href
    $isActive = request()->url() === url($href);
    // Alternative kung gusto nimo prefix-based matching:
    // $isActive = request()->is(ltrim($href, '/').'*');
@endphp

<a href="{{ $href }}"
    @class([
        'flex items-center px-4 py-2 group transition-colors duration-150 ease-in-out',
        'bg-gray-200 dark:bg-gray-900 text-gray-900 dark:text-white font-semibold' => $isActive,
        'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700' => !$isActive,
    ])>
    
    {{-- Icon --}}
    {!! $icon !!}

    {{-- Text slot --}}
    <div class="flex-1 ms-3 flex items-center justify-between">
        {{ $slot }}
    </div>
</a>
