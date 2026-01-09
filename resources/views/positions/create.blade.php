<x-app-layout>
    <div class="py-14 max-w-7xl mx-auto">

        <x-breadcrumb :items="[
            ['label' => 'Positions', 'url' => route('positions')],
            ['label' => 'Add New Position']
        ]" />

        <div class="font-bold text-gray-700 text-xl dark:text-white">Add New Position</div>

        <div class="mt-6">

            <div class="bg-white border border-slate-200 dark:bg-gray-800 relative sm:rounded shadow-sm overflow-hidden">
                <div class="p-8">

                    <form action="{{ route('positions.save') }}" method="POST">
                        @csrf
                        <div class="font-medium">POSITION INFORMATION</div>

                        <div class="max-w-xl mt-6">

                            <x-input 
                                name="description"
                                label="Description"
                                value="{{ old('description') }}"
                                notice="(Required)"
                            />

                            <div class="grid md:grid-cols-2 gap-4">
                                <x-input 
                                    name="daily_rate"
                                    label="Daily Rate"
                                    value="{{ old('daily_rate') }}"
                                    notice="(Required)"
                                />
                                <x-input 
                                    name="hourly_rate"
                                    label="Hourly Rate"
                                    value="{{ old('hourly_rate') }}"
                                    notice="(Required)"
                                />
                            </div>

                            <div class="grid md:grid-cols-2 gap-4">
                                <x-input 
                                    name="minutely_rate"
                                    label="Minutely Rate"
                                    value="{{ old('minutely_rate') }}"
                                    notice="(Required)"
                                />
                                <x-input 
                                    name="holiday_rate"
                                    label="Holiday Rate"
                                    value="{{ old('holiday_rate') }}"
                                    notice="(Required)"
                                />
                            </div>


                        </div>
                    
                        <div class="flex items-center space-x-2 mt-6">
                            <x-primary-button>
                                Save
                            </x-primary-button>
                            <x-secondary-button onclick="window.location.href='{{ route('positions') }}'">
                                Cancel
                            </x-secondary-button>
                        </div>
                    
                    </form>

                </div>
            </div>

        </div>
        
    </div>
</x-app-layout>