<x-app-layout>
    <div class="py-14 max-w-7xl mx-auto">

        <x-breadcrumb :items="[
        ['label' => 'Employee List', 'url' => route('employees')],
        ['label' => 'Add New Employee']
    ]" />

        <div class="font-bold text-gray-700 text-xl dark:text-white">Add New Employee</div>

        <div class="mt-6">

            <div
                class="bg-white border border-slate-200 dark:bg-gray-800 relative sm:rounded shadow-sm overflow-hidden">
                <div class="p-8">

                    <form action="{{ route('employees.save') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="font-medium">EMPLOYEE INFORMATION</div>

                        <div class="md:grid grid-cols-4 xl:grid-cols-4 gap-4 mt-6">

                            <x-input name="lastname" label="Last Name" value="{{ old('lastname') }}"
                                notice="(Required)" />

                            <x-input name="firstname" label="First Name" value="{{ old('firstname') }}"
                                notice="(Required)" />

                            <x-input name="middlename" label="Middle Name" value="{{ old('middlename') }}" />

                            <x-input name="suffix" label="Suffix" value="{{ old('suffix') }}" />

                        </div>

                        <div class="md:grid grid-cols-4 xl:grid-cols-4 gap-4">
                            <x-select name="sex" label="Sex" wrapperClass="col-span-2 mb-6 md:mb-0" :options="[
        'Male' => 'Male',
        'Female' => 'Female'
    ]" />

                            <x-input name="mobile_no" label="Mobile Number" value="{{ old('mobile_no') }}"
                                notice="(Required)" wrapperClass="col-span-2 mb-6 md:mb-0" />

                        </div>
                        <div class="md:grid grid-cols-4 xl:grid-cols-3 gap-4 mt-6">
                            <x-input name="purok" label="Purok" value="{{ old('purok') }}" notice="(Required)" />

                            <x-input name="barangay" label="Barangay" value="{{ old('barangay') }}"
                                notice="(Required)" />

                            <x-input name="city" label="City" value="{{ old('city') }}" notice="(Required)" />
                        </div>
                        <div class="md:grid grid-cols-4 xl:grid-cols-2 gap-4">
                            <!-- Department Select -->
                            <div class="mb-6 md:mb-0">
                                <label for="department"
                                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Department</label>
                                <select id="department" name="department"
                                    class="bg-gray-50 border text-sm rounded block w-full p-2 text-gray-900 focus:ring-blue-500 focus:border-blue-500 border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                    <option value="">Select Department</option>
                                    @foreach($departments as $id => $name)
                                        <option value="{{ $id }}" {{ old('department') == $id ? 'selected' : '' }}>{{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('department')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Position Select -->
                            <div class="mb-6 md:mb-0">
                                <label for="position"
                                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Position</label>
                                <select id="position" name="position"
                                    class="bg-gray-50 border text-sm rounded block w-full p-2 text-gray-900 focus:ring-blue-500 focus:border-blue-500 border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                    <option value="">Select Position</option>
                                    @foreach($positions as $position)
                                        <option value="{{ $position->id }}"
                                            data-department-id="{{ $position->department_id }}" {{ old('position') == $position->id ? 'selected' : '' }}>
                                            {{ $position->description }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('position')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <x-input name="custom_daily_rate" label="Custom Daily Rate"
                                value="{{ old('custom_daily_rate') }}" notice="(Optional - Overrides Position Rate)" />
                        </div>

                        <script>
                            document.addEventListener('DOMContentLoaded', function () {
                                const departmentSelect = document.getElementById('department');
                                const positionSelect = document.getElementById('position');
                                const allPositions = Array.from(positionSelect.querySelectorAll('option')).filter(opt => opt.value);

                                function filterPositions() {
                                    const selectedDeptId = departmentSelect.value;
                                    const currentPositionId = positionSelect.value;

                                    // Clear current options except the placeholder
                                    positionSelect.innerHTML = '<option value="">Select Position</option>';

                                    const filtered = allPositions.filter(opt => {
                                        const deptId = opt.getAttribute('data-department-id');
                                        // Strict filter: Only show if deptId matches selectedDeptId
                                        // Use loose equality (==) to match "1" with 1, and "" with null/empty
                                        return deptId == selectedDeptId;
                                    });

                                    filtered.forEach(opt => {
                                        positionSelect.appendChild(opt);
                                    });

                                    // Restore selection if valid
                                    if (currentPositionId && filtered.some(opt => opt.value == currentPositionId)) {
                                        positionSelect.value = currentPositionId;
                                    } else {
                                        positionSelect.value = "";
                                    }
                                }

                                departmentSelect.addEventListener('change', filterPositions);

                                // Run on load to filter based on old input or default
                                filterPositions();
                            });
                        </script>



                        <div class="md:grid grid-cols-4 xl:grid-cols-2 gap-4">
                            <x-input name="email" label="Email" value="{{ old('email') }}" notice="(Optional)" />

                            <x-input type="password" name="password" label="Password" value="{{ old('password') }}"
                                notice="(Optional)" />
                        </div>

                        <div class="font-semibold mt-12">Choose Photos for Face Recognition.</div>
                        <hr class="my-4">
                        <div class="md:grid grid-cols-3 gap-4">
                            <div class="">
                                <div class="mb-3">
                                    <div>Photo 1 (Proper Lighting)</div>
                                    <div class="text-gray-600 text-sm">
                                        <div>Front-facing photo in normal lighting.</div>
                                        <div>Make sure face is centered and visible.</div>
                                    </div>
                                </div>
                                <x-file-input name="photo" :value="'images/unknown.jpg'" :preview="true"
                                    accept="image/*" wrapperClass="mb-4 sm:mb-0" brightness="1.0" />
                            </div>
                            <div class="">
                                <div class="mb-3">
                                    <div>Photo 2 (Lighter than Photo 1)</div>
                                    <div class="text-gray-600 text-sm">
                                        <div>Brighter lighting, avoid over-exposure.</div>
                                        <div>Make sure face is centered and visible.</div>
                                    </div>
                                </div>
                                <x-file-input name="photo2" :value="'images/unknown.jpg'" :preview="true"
                                    accept="image/*" wrapperClass="mb-4 sm:mb-0" brightness="1.5" />
                            </div>
                            <div class="">
                                <div class="mb-3">
                                    <div>Photo 3 (Darker than Photo 1)</div>
                                    <div class="text-gray-600 text-sm">
                                        <div>Slightly dimmer lighting.</div>
                                        <div>Make sure face is centered and visible.</div>
                                    </div>
                                </div>
                                <x-file-input name="photo3" :value="'images/unknown.jpg'" :preview="true"
                                    accept="image/*" wrapperClass="mb-4 sm:mb-0" brightness="0.6" />
                            </div>
                        </div>

                        <div class="mt-10">
                            <x-primary-button>
                                Save Profile
                            </x-primary-button>
                        </div>

                    </form>

                </div>
            </div>

        </div>

    </div>
</x-app-layout>