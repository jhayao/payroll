@props(['href'])

<a href="{{ $href }}" class="inline-flex items-center px-2 py-1 text-xs font-medium text-center text-white bg-blue-700 rounded hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
    {{ $slot }}        
</a>