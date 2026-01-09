@props([
    'icon' => null,
    'label' => null,
    'href' => '#',
    'color' => 'blue',
])

<a href="{{ $href }}"
   class="inline-flex items-center gap-1 px-2 py-1 text-sm font-medium rounded-md text-white bg-{{ $color }}-600 hover:bg-{{ $color }}-700 transition">
    @if ($icon)
        {{-- If using Heroicons or other Blade icon sets --}}
        <x-dynamic-component :component="$icon" class="w-4 h-4" />
    @endif

    @if ($label)
        <span>{{ $label }}</span>
    @endif

    {{-- For custom content (optional) --}}
    {{ $slot }}
</a>
