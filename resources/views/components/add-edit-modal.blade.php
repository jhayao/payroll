<div 
    x-data="{ open: false }" 
    class="inline-block"
    x-init="
        @if ($errors->any())
            open = true
        @endif
    "
>
    @if ($type === 'button')
        <x-primary-button
            @click="open = true"
            type="button"
            class="w-full">
            <span>{{ $label ?? 'Add New' }}</span>
        </x-primary-button>
    @else
        <button 
            @click="open = true" 
            type="button" 
            class="flex items-center space-x-1 text-sm font-semibold text-blue-600 hover:text-blue-800 dark:text-blue-500"
        >
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
            </svg>
            <span>{{ $label ?? 'Edit' }}</span>
        </button>
    @endif

    <!-- Modal -->
    <div 
        x-show="open" 
        x-cloak
        x-transition.opacity
        class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50"
    >
        <div 
            @click.away="open = true" 
            class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 max-w-xl w-full"
        >
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">{{ $title }}</h2>
            
            <form action="{{ $action }}" method="POST">
                @csrf
                <div>
                    {{ $slot }}
                </div>
                <div class="mt-4 flex justify-end gap-2">
                    <!-- Cancel -->
                    <button 
                        @click="open = false" 
                        type="button" 
                        class="px-4 py-2 text-sm bg-gray-200 dark:bg-gray-700 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600"
                    >
                        Cancel
                    </button>
                    <x-primary-button>{{ $type === 'button' ? 'Save':'Save changes' }}</x-primary-button>
                </div>
            </form>
        </div>
    </div>
</div>

