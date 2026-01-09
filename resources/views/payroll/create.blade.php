<x-app-layout>
    <div class="py-14 max-w-7xl mx-auto">

        <x-breadcrumb :items="[
            ['label' => 'Payroll', 'url' => route('payroll')],
            ['label' => 'Create Payroll']
        ]" />

        <div class="font-bold text-gray-700 text-xl dark:text-white">Create Payroll</div>
        
        <div class="mt-6">

            <div class="bg-white border border-slate-200 dark:bg-gray-800 relative sm:rounded shadow-sm overflow-hidden">
                <div class="p-8">

                    <form action="{{ route('payroll.save') }}" method="POST">
                        @csrf
                        
                        <div class="max-w-xl">

                            <x-select 
                                name="department"
                                label="Department"
                                :options="$departments"
                            />

                            <div class="grid md:grid-cols-2 gap-4">
                                <x-input 
                                    type="date"
                                    max="{{ now()->format('Y-m-d') }}"
                                    name="date_from"
                                    label="From"
                                    value="{{ old('date_from') }}"
                                    notice="(Required)"
                                />
                                <x-input 
                                    type="date"
                                    max="{{ now()->format('Y-m-d') }}"
                                    name="date_to"
                                    label="To"
                                    value="{{ old('date_to') }}"
                                    notice="(Required)"
                                />
                            </div>

                        </div>
                    
                        <div class="flex items-center space-x-2 mt-6">
                            <x-primary-button>
                                Create
                            </x-primary-button>
                            <x-secondary-button onclick="window.location.href='{{ route('payroll') }}'">
                                Cancel
                            </x-secondary-button>
                        </div>
                    
                    </form>

                </div>
            </div>
        </div>
        
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                $('#date_to').attr('min', $('#date_from').val());
                $('#date_from').change(function() {
                    $('#date_to').attr('min', $(this).val());
                });
            });
        </script>
    @endpush

</x-app-layout>