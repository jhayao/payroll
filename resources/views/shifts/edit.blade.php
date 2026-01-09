<x-app-layout>
    <div class="py-14 max-w-7xl mx-auto">

        <x-breadcrumb :items="[
            ['label' => 'Shifts', 'url' => route('shifts')],
            ['label' => 'Edit Shift']
        ]" />

        <div class="font-bold text-gray-700 text-xl dark:text-white">Edit Shift</div>
        
        <div class="mt-6">

            <div class="bg-white border border-slate-200 dark:bg-gray-800 relative sm:rounded shadow-sm overflow-hidden">
                <div class="p-8">

                    <form action="{{ route('shifts.update', $shift->id) }}" method="POST">
                        @csrf
                        <div class="font-medium mb-1">SHIFT INFORMATION</div>
                        <div class="text-gray-600 text-sm">The specified time must be in 24-hour format.</div>

                        <div class="max-w-xl mt-6">

                            <x-input 
                                name="name"
                                label="Name"
                                value="{{ old('name', $shift->name) }}"
                                notice="(Required)"
                            />

                            <div class="grid md:grid-cols-2 gap-4">
                                <x-input 
                                    name="am_in"
                                    label="AM In"
                                    value="{{ old('am_in', $shift->am_in) }}"
                                    notice="(Required)"
                                />
                                <x-input 
                                    name="am_out"
                                    label="AM Out"
                                    value="{{ old('am_out', $shift->am_out) }}"
                                    notice="(Required)"
                                />
                            </div>

                            <div class="grid md:grid-cols-2 gap-4">
                                <x-input 
                                    name="pm_in"
                                    label="PM In"
                                    value="{{ old('pm_in', $shift->pm_in) }}"
                                    notice="(Required)"
                                />
                                <x-input 
                                    name="pm_out"
                                    label="PM Out"
                                    value="{{ old('pm_out', $shift->pm_out) }}"
                                    notice="(Required)"
                                />
                            </div>

                            <!--
                            <div class="grid md:grid-cols-2 gap-4">
                                <x-input 
                                    name="in_out_interval"
                                    label="IN-OUT Interval in Minutes"
                                    value="{{ old('in_out_interval', $shift->in_out_interval) }}"
                                    notice="(Required)"
                                />
                                <x-input 
                                    name="out_in_interval"
                                    label="OUT-IN Interval in Minutes"
                                    value="{{ old('out_in_interval', $shift->out_in_interval) }}"
                                    notice="(Required)"
                                />
                            </div>
                            -->
                        </div>
                    
                        <div class="flex items-center space-x-2 mt-6">
                            <x-primary-button>
                                Save changes
                            </x-primary-button>
                            <x-secondary-button onclick="window.location.href='{{ route('shifts') }}'">
                                Cancel
                            </x-secondary-button>
                        </div>
                    
                    </form>

                </div>
            </div>

        </div>
        
    </div>
</x-app-layout>