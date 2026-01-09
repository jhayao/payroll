<nav class="flex mb-3" aria-label="Breadcrumb"> 
    <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">

        @foreach($items as $index => $item)
        <li class="inline-flex items-center">
            @if($index === 0)
                {{-- Home Icon --}}
                <a href="{{ $item['url'] ?? '#' }}" 
                    class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                    {{ $item['label'] }}
                </a>
            @elseif($loop->last)
                {{-- Last Item (current page) --}}
                <div class="flex items-center">
                    <svg class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                    </svg>
                    <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2 dark:text-gray-400">
                        {{ $item['label'] }}
                    </span>
                </div>
            @else
                {{-- Middle Items --}}
                <div class="flex items-center">
                    <svg class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                    </svg>
                    <a href="{{ $item['url'] ?? '#' }}" 
                    class="ms-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ms-2 dark:text-gray-400 dark:hover:text-white">
                        {{ $item['label'] }}
                    </a>
                </div>
            @endif
        </li>
        @endforeach

    </ol>
</nav>
