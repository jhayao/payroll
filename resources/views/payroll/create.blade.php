<x-app-layout>
    <div class="py-14 max-w-7xl mx-auto">

        <x-breadcrumb :items="[
        ['label' => 'Payroll', 'url' => route('payroll')],
        ['label' => 'Create Payroll']
    ]" />

        <div class="font-bold text-gray-700 text-xl dark:text-white">Create Payroll</div>

        <div class="mt-6">

            <div
                class="bg-white border border-slate-200 dark:bg-gray-800 relative sm:rounded shadow-sm overflow-hidden">
                <div class="p-8">

                    <form action="{{ route('payroll.save') }}" method="POST">
                        @csrf


                        <div class="mb-4">
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">Generation
                                Type</label>
                            <div class="flex items-center space-x-4">
                                <div class="flex items-center">
                                    <input id="type-all" type="radio" value="all" name="generation_type"
                                        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                                        checked>
                                    <label for="type-all"
                                        class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">All
                                        Employees</label>
                                </div>
                                <div class="flex items-center">
                                    <input id="type-project" type="radio" value="project" name="generation_type"
                                        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                    <label for="type-project"
                                        class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">By
                                        Project</label>
                                </div>
                                <div class="flex items-center">
                                    <input id="type-individual" type="radio" value="individual" name="generation_type"
                                        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                    <label for="type-individual"
                                        class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">Individual
                                        Employee</label>
                                </div>
                            </div>
                        </div>

                        <div id="project-wrapper" class="hidden mb-4">
                            <x-select name="project_id" label="Project" :options="$projects" />
                        </div>

                        <x-select name="department" label="Department" :options="$departments" />

                        <div id="salary-type-wrapper" class="mb-4">
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">Salary Period
                                / Type</label>
                            <select name="salary_type"
                                class="bg-gray-50 border text-sm rounded block w-full p-2 text-gray-900 focus:ring-blue-500 focus:border-blue-500 border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                <option value="">All Types</option>
                                <option value="weekly">Weekly</option>
                                <option value="semi_monthly">Semi-monthly</option>
                            </select>
                        </div>

                        <div id="employee-wrapper" class="hidden mb-6">
                            <label for="employee_id"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Employee</label>
                            <select id="employee_id" name="employee_id"
                                class="bg-gray-50 border text-sm rounded block w-full p-2 text-gray-900 focus:ring-blue-500 focus:border-blue-500 border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                <option value="">Select Employee</option>
                                <!-- Options populated by JS -->
                            </select>
                        </div>

                        <div class="grid md:grid-cols-2 gap-4">
                            <x-input type="date" max="{{ now()->format('Y-m-d') }}" name="date_from" label="From"
                                value="{{ old('date_from') }}" notice="(Required)" />
                            <x-input type="date" max="{{ now()->format('Y-m-d') }}" name="date_to" label="To"
                                value="{{ old('date_to') }}" notice="(Required)" />
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
            $(document).ready(function () {
                $('#date_to').attr('min', $('#date_from').val());
                $('#date_from').change(function () {
                    $('#date_to').attr('min', $(this).val());
                });

                const employees = @json($employees);
                const departmentSelect = document.getElementById('department');
                const employeeSelect = document.getElementById('employee_id');
                const employeeWrapper = document.getElementById('employee-wrapper');
                const salaryTypeWrapper = document.getElementById('salary-type-wrapper');
                const salaryTypeSelect = document.querySelector('select[name="salary_type"]');
                const typeRadios = document.querySelectorAll('input[name="generation_type"]');

                const projectWrapper = document.getElementById('project-wrapper');

                function toggleEmployeeSelect() {
                    const type = document.querySelector('input[name="generation_type"]:checked').value;

                    // Reset visibility
                    employeeWrapper.classList.add('hidden');
                    projectWrapper.classList.add('hidden');
                    salaryTypeWrapper.classList.remove('hidden'); // Default show salary type

                    if (type === 'individual') {
                        employeeWrapper.classList.remove('hidden');
                        salaryTypeWrapper.classList.add('hidden');
                        salaryTypeSelect.value = '';
                        filterEmployees();
                    } else if (type === 'project') {
                        projectWrapper.classList.remove('hidden');
                    }
                }

                function filterEmployees() {
                    const selectedDeptId = departmentSelect.value;
                    const selectedSalaryType = salaryTypeSelect.value;

                    employeeSelect.innerHTML = '<option value="">Select Employee</option>';

                    if (!selectedDeptId) return;

                    const filtered = employees.filter(e => {
                        const deptMatch = e.department_id == selectedDeptId;
                        const typeMatch = !selectedSalaryType || e.salary_type === selectedSalaryType;
                        return deptMatch && typeMatch;
                    });

                    filtered.forEach(e => {
                        const option = document.createElement('option');
                        option.value = e.id;
                        option.textContent = e.fullname;
                        employeeSelect.appendChild(option);
                    });
                }

                typeRadios.forEach(radio => radio.addEventListener('change', toggleEmployeeSelect));
                departmentSelect.addEventListener('change', filterEmployees);
                salaryTypeSelect.addEventListener('change', filterEmployees);

                // Initial run
                toggleEmployeeSelect();
            });
        </script>
    @endpush

</x-app-layout>