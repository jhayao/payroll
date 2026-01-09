<x-app-layout>
    <div class="py-14 max-w-7xl mx-auto">
        <div class="flex items-center justify-between">
            <div class="font-bold text-gray-700 text-xl dark:text-white">Daily Time Record</div>
        </div>
        <div class="mt-6">

            @php
                $employeeList = collect();
                $employeeList->push([
                    'name' => '- Select -', 
                    'id' => ''
                ]);

                foreach ($employees as $e) {
                    $employeeList->push([
                        'id' => $e->id,
                        'name' => $e->fullname
                    ]);
                }

                $newEmployees = $employeeList->pluck('name', 'id');
            @endphp


            <!-- Start coding here -->
            <form action="{{ route('dtr.generate') }}" method="post">
                @csrf
                <div class="bg-white border border-slate-300 rounded p-4">
                    <div class="md:grid grid-cols-6 gap-4">
                        <x-select 
                            id="employee_id"
                            name="employee_id"
                            label="Employee"
                            :options="$newEmployees"
                            wrapperClass="mb-3 md:mb-0 col-span-2"
                        />
                    
                        @php
                            $today = Carbon\Carbon::today()->format('Y-m-d');
                            $startOfMonth = Carbon\Carbon::now()->startOfMonth()->format('Y-m-d');
                        @endphp

                        
                        <x-input 
                            type="date"
                            id="date_from"
                            name="date_from"
                            label="From"
                            value="{{ $startOfMonth }}"
                            max="{{ $today }}"
                            wrapperClass="mb-3 md:mb-0"
                        />

                        <x-input 
                            type="date"
                            id="date_to"
                            name="date_to"
                            label="To"
                            min="{{ $startOfMonth }}"
                            max="{{ $today }}"
                            value="{{ $today }}"
                            wrapperClass="mb-0"
                        />
                        
                    </div>

                    <div class="mt-4">
                        <x-primary-button 
                            id="loadDTR"
                            type="button"
                        >
                            Load DTR
                        </x-primary-button>
                        <x-primary-button>
                            Generate Report
                        </x-primary-button>
                    </div>

                </div>
            </form>

            <div class="mt-6" id="dtrContainer"></div>

        </div>
        
    </div>

    <script>
        let table = new DataTable('#myTable', {
            lengthChange: false,
            ordering: false,
            paginate: false
        });

        document.getElementById('date_from').addEventListener('change', function () {
            document.getElementById('date_to').min = this.value;
        });

        $('#loadDTR').on('click', function() {
            let id = $('#employee_id').val();
            let from = $('#date_from').val();
            let to = $('#date_to').val();

            if (id === '') {
                Swal.fire({
                    title: 'Error',
                    text: 'Please select employee.',
                })
                return;
            }

            if (!from || !to) {
                Swal.fire({
                    title: 'Error',
                    text: 'Please select date range.'
                });
                return;
            }

            $('#dtrContainer').html(`
                <div class="text-center py-6 text-gray-500 border border-slate-300 bg-white sm:rounded shadow-sm overflow-hidden">
                    Loading DTR records...
                </div>
            `);

            $.ajax({
                url: '/api/dtr/view',
                method: 'POST',
                data: {
                    employee_id: id,
                    date_from: from,
                    date_to: to
                },
                success: function (html) {
                    // ✅ HTML response
                    $('#dtrContainer').html(html);
                },
                error: function (xhr) {
                    let message = 'Something went wrong.';

                    if (xhr.status === 422) {
                        message = 'Invalid input. Please check your selections.';
                    } else if (xhr.status === 404) {
                        message = 'Employee or DTR not found.';
                    }

                    $('#dtrContainer').html(message);
                }
            });

        });

    </script>

</x-app-layout>