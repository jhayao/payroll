<x-app-layout>
    <div class="py-14 max-w-7xl mx-auto">

        <x-breadcrumb :items="[
        ['label' => 'Shifts', 'url' => route('shifts')],
        ['label' => 'Add New Shift']
    ]" />

        <div class="font-bold text-gray-700 text-xl dark:text-white">Add New Shift</div>

        <div class="mt-6">

            <div
                class="bg-white border border-slate-200 dark:bg-gray-800 relative sm:rounded shadow-sm overflow-hidden">
                <div class="p-8">

                    <form action="{{ route('shifts.save') }}" method="POST">
                        @csrf
                        <div class="font-medium mb-1">SHIFT INFORMATION</div>
                        <div class="text-gray-600 text-sm">The specified time must be in 24-hour format.</div>

                        <div class="max-w-xl mt-6">

                            <x-input name="name" label="Name" value="{{ old('name') }}" notice="(Required)" />

                            <div class="mt-4 mb-2" x-data="{ isHoliday: false }">
                                <label class="inline-flex items-center">
                                    <input type="checkbox" name="is_holiday" value="1" x-model="isHoliday"
                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                    <span class="ml-2 text-sm text-gray-600">Is Holiday?</span>
                                </label>

                                <div x-show="isHoliday" class="mt-4">
                                    <x-input name="rate_percentage" label="Rate Percentage (%)" type="number" value="{{ old('rate_percentage', 100) }}"
                                        notice="(e.g. 200 for Double Pay)" />
                                </div>
                            </div>

                            <div class="grid md:grid-cols-2 gap-4">
                                <x-input name="am_in" label="AM In" type="time" value="{{ old('am_in') }}"
                                    notice="(Required)" />
                                <x-input name="am_out" label="AM Out" type="time" value="{{ old('am_out') }}"
                                    notice="(Required)" />
                            </div>

                            <div class="grid md:grid-cols-2 gap-4">
                                <x-input name="pm_in" label="PM In" type="time" value="{{ old('pm_in') }}"
                                    notice="(Required)" />
                                <x-input name="pm_out" label="PM Out" type="time" value="{{ old('pm_out') }}"
                                    notice="(Required)" />
                            </div>

                            <!--
                            <div class="grid md:grid-cols-2 gap-4">
                                <x-input 
                                    name="in_out_interval"
                                    label="IN-OUT Interval in Minutes"
                                    value="{{ old('in_out_interval') }}"
                                    notice="(Required)"
                                />
                                <x-input 
                                    name="out_in_interval"
                                    label="OUT-IN Interval in Minutes"
                                    value="{{ old('out_in_interval') }}"
                                    notice="(Required)"
                                />
                            </div>
                            -->
                        </div>

                        <div class="flex items-center space-x-2 mt-6">
                            <x-primary-button>
                                Save
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