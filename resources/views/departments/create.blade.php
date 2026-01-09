<x-app-layout>
    <div class="py-14 max-w-7xl mx-auto">

        <x-breadcrumb :items="[
            ['label' => 'Departments', 'url' => route('departments')],
            ['label' => 'Add New Department']
        ]" />

        <div class="font-bold text-gray-700 text-xl dark:text-white">Add New Department</div>

        <div class="mt-6">

            <div class="bg-white border border-slate-200 dark:bg-gray-800 relative sm:rounded shadow-sm overflow-hidden">
                <div class="p-8">

                    <form action="{{ route('departments.save') }}" method="POST">
                        @csrf
                        <div class="font-medium">DEPARTMENT INFORMATION</div>

                        <div class="max-w-xl mt-6">

                            <x-input 
                                name="name"
                                label="Name"
                                value="{{ old('name') }}"
                                notice="(Required)"
                            />

                            <x-input 
                                name="abbr"
                                label="Abbreviation"
                                value="{{ old('abbr') }}"
                                notice="(Required)"
                            />
                            
                        </div>
                    
                        <div class="flex items-center space-x-2 mt-6">
                            <x-primary-button>
                                Save
                            </x-primary-button>
                            <x-secondary-button onclick="window.location.href='{{ route('departments') }}'">
                                Cancel
                            </x-secondary-button>
                        </div>
                    
                    </form>

                </div>
            </div>

        </div>
        
    </div>
</x-app-layout>