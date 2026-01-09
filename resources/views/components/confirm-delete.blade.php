<div x-data="{ open: false }" class="inline-block">
    <!-- Trigger -->
    <button 
        @click="open = true" 
        type="button" 
        class="flex items-center space-x-1 text-sm font-semibold text-red-600 hover:text-red-800 dark:text-red-500"
    >
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
        </svg>
        <span>{{ $label ?? 'Delete' }}</span>
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
            @click.away="open = false" 
            class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 max-w-sm w-full"
        >
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Confirm Delete</h2>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                {{ $message ?? 'Are you sure you want to delete this item? This action cannot be undone.' }}
            </p>

            <div class="mt-4 flex justify-end gap-2">
                <!-- Cancel -->
                <button 
                    @click="open = false" 
                    type="button" 
                    class="px-4 py-2 text-sm bg-gray-200 dark:bg-gray-700 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600"
                >
                    Cancel
                </button>

                <!-- Confirm Delete -->
                <form action="{{ $action }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button 
                        type="submit" 
                        class="px-4 py-2 text-sm bg-red-600 text-white rounded-lg hover:bg-red-700"
                    >
                        Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
