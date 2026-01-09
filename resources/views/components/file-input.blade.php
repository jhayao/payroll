@props([
    'label' => null,
    'name',
    'accept' => null,
    'wrapperClass' => 'mb-6',
    'brightness' => 1.0, 
])

@php
    $hasError = $errors->has($name);
@endphp

<div class="{{ $wrapperClass }}" x-data="fileInput({ brightness: {{ $brightness }} })">

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

    <div class="flex gap-2">
        {{-- Custom file input --}}
        <label
            for="{{ $name }}"
            class="flex-1 flex items-center justify-between px-4 py-2 bg-gray-50 text-sm text-gray-900 border border-gray-300 rounded cursor-pointer hover:bg-gray-100 dark:bg-gray-700 dark:text-gray-400 dark:border-gray-600 dark:hover:bg-gray-600"
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

        {{-- Camera Button --}}
        <button 
            type="button" 
            @click="openCamera"
            class="px-4 py-2 bg-blue-600 text-white text-sm rounded hover:bg-blue-700 focus:outline-none"
            title="Take Photo"
        >
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z" />
              <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0ZM18.75 10.5h.008v.008h-.008V10.5Z" />
            </svg>
        </button>
    </div>

    {{-- Validation error --}}
    @if($hasError)
        <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $errors->first($name) }}</p>
    @endif

    {{-- Camera Modal --}}
    <div 
        x-show="showCamera" 
        style="display: none;"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75"
    >
        <div class="bg-white p-4 rounded-lg shadow-lg dark:bg-gray-800 max-w-lg w-full">
            <div class="mb-4 font-bold text-lg dark:text-white">Take Photo</div>
            <div class="relative bg-black rounded overflow-hidden mb-4">
                <video 
                    x-ref="video" 
                    class="w-full h-64 object-cover" 
                    autoplay 
                    playsinline
                    :style="`filter: brightness(${brightness})`"
                ></video>
            </div>
            <div class="flex justify-end gap-2">
                <button 
                    type="button" 
                    @click="closeCamera" 
                    class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400"
                >
                    Cancel
                </button>
                <button 
                    type="button" 
                    @click="capture" 
                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
                >
                    Capture
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('fileInput', ({ brightness }) => ({
            fileName: null,
            fileUrl: null,
            showCamera: false,
            stream: null,
            brightness: brightness || 1.0,
            
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
            },

            async openCamera() {
                this.showCamera = true;
                try {
                    this.stream = await navigator.mediaDevices.getUserMedia({ video: true });
                    this.$refs.video.srcObject = this.stream;
                } catch (err) {
                    console.error("Error accessing camera: ", err);
                    alert("Cannot access camera. Please allow camera permissions.");
                    this.showCamera = false;
                }
            },

            closeCamera() {
                this.showCamera = false;
                if (this.stream) {
                    this.stream.getTracks().forEach(track => track.stop());
                    this.stream = null;
                }
            },

            capture() {
                if (!this.stream) return;
                
                const video = this.$refs.video;
                const canvas = document.createElement('canvas');
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                const ctx = canvas.getContext('2d');
                
                // Apply brightness filter to context
                ctx.filter = `brightness(${this.brightness})`;
                
                ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                
                canvas.toBlob((blob) => {
                    if (blob) {
                        const file = new File([blob], "camera_capture.jpg", { type: "image/jpeg" });
                        const dataTransfer = new DataTransfer();
                        dataTransfer.items.add(file);
                        this.$refs.input.files = dataTransfer.files;
                        this.updateFile();
                        this.closeCamera();
                    }
                }, 'image/jpeg', 0.95);
            }
        }))
    })
</script>
