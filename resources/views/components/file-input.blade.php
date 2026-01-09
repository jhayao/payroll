@props([
    'label' => null,
    'name',
    'accept' => null,
    'wrapperClass' => 'mb-6',
])

@php
    $hasError = $errors->has($name);
@endphp

<div class="{{ $wrapperClass }}" x-data="fileInput()">

    <template x-if="fileUrl">
        <div class="mb-3">
            <img :src="fileUrl" alt="Preview" class="object-cover rounded border">
        </div>
    </template>

    {{-- Label --}}
    @if($label)
        <label for="{{ $name }}" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
            {{ $label }}
        </label>
    @endif

    {{-- Custom file input --}}
    <label
        for="{{ $name }}"
        class="flex items-center justify-between px-4 py-2 bg-gray-50 text-sm text-gray-900 border border-gray-300 rounded cursor-pointer hover:bg-gray-100 dark:bg-gray-700 dark:text-gray-400 dark:border-gray-600 dark:hover:bg-gray-600"
    >
        <span class="truncate" x-text="fileName || 'Choose file..'">Choose file..</span>
        <input
            type="file"
            name="{{ $name }}"
            id="{{ $name }}"
            @if($accept) accept="{{ $accept }}" @endif
            class="hidden"
            x-ref="input"
            @change="updateFile"
            {{ $attributes }}
        />
    </label>

    {{-- Validation error --}}
    @if($hasError)
        <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $errors->first($name) }}</p>
    @endif
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('fileInput', () => ({
            fileName: null,
            fileUrl: null,
            updateFile() {
                const file = this.$refs.input.files[0];
                if (file) {
                    this.fileName = file.name;
                    if (file.type.startsWith('image/')) {
                        this.fileUrl = URL.createObjectURL(file);
                    } else {
                        this.fileUrl = null;
                    }
                } else {
                    this.fileName = null;
                    this.fileUrl = null;
                }
            }
        }))
    })
</script>
