<div 
    x-data="{ open: false }" 
    class="inline-block"
    x-init="
        @if ($errors->any())
            open = true
        @endif
    "
>

    <button 
        @click="open = true"
        class="flex items-center space-x-1 text-blue-600 hover:text-blue-800 text-sm font-medium">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
        </svg>
        <span>Add</span>
    </button>

    <!-- Modal -->
    <div 
        x-show="open" 
        x-cloak
        x-transition.opacity
        @keydown.escape.window="open = false"
        class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50"
    >
        <div 
            @click.away="open = true" 
            class="bg-white dark:bg-gray-800 rounded-lg shadow-lg px-6 py-4 max-w-xl w-full"
        >
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">{{ $title }}</h2>
            <div class="border-t my-2"></div>
            
            <form action="{{ $action }}" method="POST">
                @csrf
                <div class="py-4">
                    {{ $slot }}
                </div>
                
                <div class="border-t my-2"></div>

                <div class="mt-4 flex justify-end gap-2">
                    <!-- Cancel -->
                    <button 
                        @click="open = false" 
                        type="button" 
                        class="px-4 py-2 text-sm bg-gray-200 dark:bg-gray-700 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600"
                    >
                        Cancel
                    </button>
                    <x-primary-button>Save</x-primary-button>
                </div>
            </form>
            
        </div>
    </div>
</div>

