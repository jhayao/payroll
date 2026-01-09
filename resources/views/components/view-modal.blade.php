<div x-data="{ open: false }" class="inline-block">
    <!-- Trigger Button -->
    <button @click="open = true" type="button"
        class="flex items-center space-x-1 text-sm font-semibold text-gray-600 hover:text-gray-800 dark:text-gray-400">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
            class="size-4">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
        </svg>
        <span>{{ $title ?? 'View' }}</span>
    </button>

    <!-- Modal -->
    <div x-show="open" x-cloak x-transition.opacity
        class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50 text-left">
        <div @click.away="open = false"
            class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 max-w-xl w-full max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">{{ $title ?? 'View Details' }}</h2>
                <button @click="open = false"
                    class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="space-y-4">
                {{ $slot }}
            </div>

            <div class="mt-6 flex justify-end">
                <button @click="open = false" type="button"
                    class="px-4 py-2 text-sm bg-gray-200 dark:bg-gray-700 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>