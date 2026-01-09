@props([
    'id' => 'drawer-example',
    'title' => 'Drawer',
    'placement' => 'right',
    'width' => 'sm:w-96'
])

<div 
    id="{{ $id }}"
    class="fixed top-0 
           {{ $placement === 'right' ? 'right-0 translate-x-full' : 'left-0 -translate-x-full' }} 
           z-50 w-full {{ $width }} h-screen p-0 overflow-y-auto transition-transform 
           bg-white dark:bg-gray-800"
    tabindex="-1"
    aria-labelledby="{{ $id }}-label">

    <h5 id="{{ $id }}-label" class="p-4 text-base font-semibold text-gray-500 uppercase dark:text-gray-400">
        {{ $title }}
    </h5>

    <button 
        type="button" 
        data-drawer-hide="{{ $id }}" 
        aria-controls="{{ $id }}"
        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm 
               w-8 h-8 absolute top-2.5 
               {{ $placement === 'right' ? 'right-2.5' : 'left-2.5' }} 
               inline-flex items-center justify-center dark:hover:bg-gray-600 dark:hover:text-white">
        ✕
    </button>

    <div class="py-4 overflow-y-auto">
        {{ $slot }}
    </div>
</div>
