<x-app-layout>
    <div class="py-14 max-w-7xl mx-auto">

        <x-breadcrumb :items="[
            ['label' => 'Employee List', 'url' => route('employees')],
            ['label' => 'View Employee']
        ]" />

        <div class="font-bold text-gray-700 text-xl dark:text-white">View Employee</div>

        <div class="mt-6">

            <div class="border border-slate-300 bg-white dark:bg-gray-800 relative sm:rounded shadow-sm overflow-hidden">
                <div class="px-8 py-6">

                    <div class="mb-4 border-b border-gray-200 dark:border-gray-700">
                        <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="default-tab" data-tabs-toggle="#default-tab-content" role="tablist">
                            <li class="me-2" role="presentation">
                                <button class="inline-block p-4 border-b-2 rounded-t-lg" id="profile-tab" data-tabs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="false">Profile</button>
                            </li>
                            <li class="me-2" role="presentation">
                                <button class="inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300" id="dashboard-tab" data-tabs-target="#dashboard" type="button" role="tab" aria-controls="dashboard" aria-selected="false">Face Regconition Photos</button>
                            </li>
                        </ul>
                    </div>
                    <div id="default-tab-content">
                        <div class="hidden p-4 rounded-lg dark:bg-gray-800" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                            <div>
                                <form action="{{ route('employees.update', $employee->id) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="font-medium">EMPLOYEE INFORMATION</div>
                                
                                    <div class="mt-3 md:flex gap-6">

                                        <div class="mb-6 md:mb-0">
                                            <img src="{{ asset($employee->photo_2x2) }}" class="w-24 h-24 xl:w-28 xl:h-28" />
                                        </div>

                                        <div class="flex-1">

                                            <div class="grid lg:grid-cols-3 xl:grid-cols-4 gap-2">
                                            
                                                <x-input 
                                                    name="employee_id"
                                                    label="Employee ID"
                                                    value="{{ $employee->employee_id }}"
                                                    :readonly="true"
                                                />

                                                <x-input 
                                                    name="lastname"
                                                    label="Last Name"
                                                    value="{{ old('lastname', $employee->lastname) }}"
                                                />

                                                <x-input 
                                                    name="firstname"
                                                    label="First Name"
                                                    value="{{ old('firstname', $employee->firstname) }}"
                                                />

                                                <x-input 
                                                    name="middlename"
                                                    label="Middle Name"
                                                    value="{{ old('middlename', $employee->middlename) }}"
                                                />

                                                <x-input 
                                                    name="suffix"
                                                    label="Suffix"
                                                    value="{{ old('suffix', $employee->suffix) }}"
                                                />

                                                <x-select 
                                                    name="sex"
                                                    label="Sex"
                                                    value="{{ $employee->sex }}"
                                                    :options="[
                                                        'Male' => 'Male',
                                                        'Female' => 'Female'
                                                    ]"
                                                />
                                            </div>

                                            <div class="grid lg:grid-cols-3 xl:grid-cols-4 gap-2">

                                                

                                                <x-input
                                                    name="purok"
                                                    label="Purok"
                                                    value="{{ old('purok', $employee->purok) }}"
                                                />

                                                <x-input
                                                    name="barangay"
                                                    label="Barangay"
                                                    value="{{ old('barangay', $employee->barangay) }}"
                                                />

                                                <x-input
                                                    name="city"
                                                    label="City"
                                                    value="{{ old('city', $employee->city) }}"
                                                />

                                                <x-input
                                                    name="mobile_no"
                                                    label="Mobile Number"
                                                    value="{{ old('mobile_no', $employee->mobile_no) }}"
                                                />

                                            </div>

                                            <div class="grid lg:grid-cols-3 xl:grid-cols-3 gap-2">
                                                
                                                <!-- Position Select (Swapped order intentionally or keeping layout? Original had Position first, let's keep grid layout but logic needs dept first for UX usually, but side-by-side matches) -->
                                                <!-- Actually, Department filter affects Position, so usually Department comes first. The original layout had Position then Department.
                                                     I will preserve the grid layout structure. The user sees them. I will implementing the filter regardless of order in DOM.
                                                 -->

                                                <div class="mb-6 md:mb-0">
                                                    <label for="department" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Department</label>
                                                    <select id="department" name="department" class="bg-gray-50 border text-sm rounded block w-full p-2 text-gray-900 focus:ring-blue-500 focus:border-blue-500 border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                                        <option value="">Select Department</option>
                                                        @foreach($departments as $id => $name)
                                                            <option value="{{ $id }}" {{ $employee->department_id == $id ? 'selected' : '' }}>{{ $name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="mb-6 md:mb-0">
                                                    <label for="position" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Position</label>
                                                    <select id="position" name="position" class="bg-gray-50 border text-sm rounded block w-full p-2 text-gray-900 focus:ring-blue-500 focus:border-blue-500 border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                                        <option value="">Select Position</option>
                                                        @foreach($positions as $position)
                                                            <option value="{{ $position->id }}" data-department-id="{{ $position->department_id }}" {{ $employee->position_id == $position->id ? 'selected' : '' }}>
                                                                {{ $position->description }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <x-input
                                                    name="email"
                                                    label="Email"
                                                    value="{{ old('email', $employee->email) }}"
                                                    notice="(Optional)"
                                                />

                                                <x-input 
                                                    name="custom_daily_rate"
                                                    label="Custom Daily Rate"
                                                    value="{{ old('custom_daily_rate', $employee->custom_daily_rate) }}"
                                                    notice="(Optional - Overrides Position Rate)"
                                                />
                                            </div>

                                            <script>
                                                document.addEventListener('DOMContentLoaded', function() {
                                                    const departmentSelect = document.getElementById('department');
                                                    const positionSelect = document.getElementById('position');
                                                    const allPositions = Array.from(positionSelect.querySelectorAll('option')).filter(opt => opt.value);

                                                    function filterPositions() {
                                                        const selectedDeptId = departmentSelect.value;
                                                        const currentPositionId = positionSelect.value; // Store currently selected value

                                                        // Clear current options except the placeholder
                                                        positionSelect.innerHTML = '<option value="">Select Position</option>';

                                                        const filtered = allPositions.filter(opt => {
                                                            const deptId = opt.getAttribute('data-department-id');
                                                            return deptId == selectedDeptId; 
                                                        });

                                                        filtered.forEach(opt => {
                                                            positionSelect.appendChild(opt);
                                                        });

                                                        // Restore selection if it's still valid in the filtered list
                                                        // Note: We need to check if the currentPositionId exists in the filtered options.
                                                        // If it doesn't, we might not want to select anything, or keep it blank.
                                                        // BUT: On initial load, we want to keep the employee's existing position even if the department logic is weird (though data should be consistent).
                                                        // However, if the user changes department, we probably want to reset position if it doesn't match.
                                                        
                                                        const exists = filtered.some(opt => opt.value == currentPositionId);
                                                        if (exists) {
                                                            positionSelect.value = currentPositionId;
                                                        } else {
                                                            positionSelect.value = "";
                                                        }
                                                    }

                                                    departmentSelect.addEventListener('change', filterPositions);
                                                    
                                                    // Trigger on load to filter relevant positions for the current department
                                                    // IMPORTANT: We must ensure we don't clear the key value on initial load.
                                                    // The 'selected' attribute in HTML handles the initial value.
                                                    // However, our script rebuilds the options.
                                                    // So we need to capture the value BEFORE filtering, which we do with `currentPositionId`.
                                                    filterPositions();
                                                });
                                            </script>



                                            <div class="flex items-center space-x-2 mt-10">
                                                <x-primary-button>
                                                    Update Profile
                                                </x-primary-button>
                                                <x-primary-button 
                                                    type="button" 
                                                    data-modal-target="change-password-modal" 
                                                    data-modal-toggle="change-password-modal">
                                                    Change Password
                                                </x-primary-button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="hidden p-4 rounded-lg dark:bg-gray-800" id="dashboard" role="tabpanel" aria-labelledby="dashboard-tab">
                            <div>

                                <div class="grid grid-cols-3 gap-4 mb-8">
                                    <img src="{{ asset($employee->photo_lg) }}" />
                                    <img src="{{ asset($employee->photo_lg2) }}" />
                                    <img src="{{ asset($employee->photo_lg3) }}" />
                                </div>

                                <form action="{{ route('employees.update-photo', $employee->id) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    
                                    <div class="font-semibold mb-6">To update Photos for Face Recognition.</div>
                                    <div class="md:grid xl:grid-cols-3 gap-4">
                                        <div class="">
                                            <div class="mb-3">
                                                <div>Photo 1 (Proper Lighting)</div>
                                                <div class="text-gray-600 text-sm">
                                                    <div>A clear, front-facing photo in normal lighting.</div>
                                                    <div>Make sure face is centered and visible.</div>
                                                </div>
                                            </div>
                                            <x-file-input 
                                                name="photo" 
                                                :value="'images/unknown.jpg'" 
                                                :preview="true" 
                                                accept="image/*"
                                                wrapperClass="mb-4 sm:mb-0"
                                            />
                                        </div>
                                        <div class="">
                                            <div class="mb-3">
                                                <div>Photo 2 (Lighter than Photo 1)</div>
                                                <div class="text-gray-600 text-sm">
                                                    <div>Another photo in brighter lighting (stronger light source).</div>
                                                    <div>Avoid overexposure — facial features should still be visible.</div>
                                                </div>
                                            </div>
                                            <x-file-input 
                                                name="photo2" 
                                                :value="'images/unknown.jpg'" 
                                                :preview="true" 
                                                accept="image/*"
                                                wrapperClass="mb-4 sm:mb-0"
                                            />
                                        </div>
                                        <div class="">
                                            <div class="mb-3">
                                                <div>Photo 3 (Darker than Photo 1)</div>
                                                <div class="text-gray-600 text-sm">
                                                    <div>A photo in slightly dimmer lighting.</div>
                                                    <div>Make sure face is centered and visible.</div>
                                                </div>
                                            </div>
                                            <x-file-input 
                                                name="photo3" 
                                                :value="'images/unknown.jpg'" 
                                                :preview="true" 
                                                accept="image/*"
                                                wrapperClass="mb-4 sm:mb-0"
                                            />
                                        </div>
                                    </div>

                                    <div class="mt-10">
                                        <x-primary-button>
                                            Update Photos
                                        </x-primary-button>
                                    </div>
                                
                                </form>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="mt-6">
                <div class="border border-slate-300 bg-white dark:bg-gray-800 relative sm:rounded shadow-sm overflow-hidden">
                    <div class="px-8 py-6">
                        <div class="text-lg font-medium mb-3">Official Time ({{ $employee->currentShift()->shift->name }})</div>
                        
                        <div class="relative overflow-x-auto bg-neutral-primary-soft shadow-xs rounded-base border border-default">
                            <table class="w-full text-sm text-left rtl:text-right text-body">
                                <thead class="text-sm text-body bg-neutral-secondary-soft border-b rounded-base border-default">
                                    <tr>
                                        <th scope="col" class="px-3 py-2 font-medium">
                                            AM In
                                        </th>
                                        <th scope="col" class="px-3 py-2 font-medium">
                                            AM Out
                                        </th>
                                        <th scope="col" class="px-3 py-2 font-medium">
                                            PM In
                                        </th>
                                        <th scope="col" class="px-3 py-2 font-medium">
                                            PM Out
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="bg-neutral-primary border-b border-default">
                                        <td class="px-3 py-2">
                                            {{ $employee->currentShift()->shift->formatted_am_in }}
                                        </td>
                                        <td class="px-3 py-2">
                                            {{ $employee->currentShift()->shift->formatted_am_out }}
                                        </td>
                                        <td class="px-3 py-2">
                                            {{ $employee->currentShift()->shift->formatted_pm_in }}
                                        </td>
                                        <td class="px-3 py-2">
                                            {{ $employee->currentShift()->shift->formatted_pm_out }}
                                        </td>
                                        <td class="px-3 py-2">
                                            <x-add-edit-modal
                                                type="link"
                                                title="Change Official Time"
                                                action="{{ route('employees.update-shift', $employee->currentShift()->id) }}"
                                            >
                                                <table class="w-full text-sm text-left rtl:text-right text-body">
                                                @foreach ($shifts as $shift)
                                                    <tr class="border-t border-b">
                                                        <td class="flex items-center space-x-2 p-2">
                                                            <input 
                                                                id="shift{{ $shift->id }}" 
                                                                type="radio" name="shift_id" 
                                                                value="{{ $shift->id }}"
                                                                @checked($shift->id === $employee->currentShift()->shift->id)
                                                            />
                                                            <label for="shift{{ $shift->id }}">{{ $shift->name }}</label>
                                                        </td>
                                                        <td class="p-2">{{ $shift->formatted_am_in }}</td>
                                                        <td class="p-2">{{ $shift->formatted_am_out }}</td>
                                                        <td class="p-2">{{ $shift->formatted_pm_in }}</td>
                                                        <td class="p-2">{{ $shift->formatted_pm_out }}</td>
                                                        <td class="p-2">
                                                            
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                </table>
                                            </x-add-edit-modal>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>

            <div class="mt-6">
                <div class="border border-slate-300 bg-white dark:bg-gray-800 relative sm:rounded shadow-sm overflow-hidden">
                    <div class="px-8 py-6">

                        <div class="flex items-center justify-between mb-3">
                            <div class="text-lg font-medium">Daily Time Record - {{ now()->format('F Y') }}</div>
                        </div>
                        
                         <div class="relative overflow-x-auto bg-neutral-primary-soft shadow-xs rounded-base border border-default">
                            <table id="myTable" class="w-full text-sm text-left rtl:text-right text-body">
                                <thead class="text-sm text-body bg-neutral-secondary-soft border-b rounded-base border-default">
                                    <tr class="border-b">
                                        <th scope="col" rowspan="2" class="px-3 py-2 font-medium text-center border">
                                            DATE
                                        </th>
                                        
                                        <th scope="col" colspan="2" class="px-3 py-2 font-medium text-center border">
                                            MORNING
                                        </th>
                                        <th scope="col" colspan="2" class="px-3 py-2 font-medium text-center border">
                                            AFTERNOON
                                        </th>
                                        <th scope="col" colspan="2" class="px-3 py-2 font-medium text-center border">
                                            OVER TIME
                                        </th>
                                        
                                        <th scope="col" colspan="2" class="px-3 py-2 font-medium text-center border">
                                            TARDINESS
                                        </th>
                                    </tr>

                                    <tr>
                                        <th scope="col" class="px-3 py-2 font-medium text-center border">IN</th>
                                        <th scope="col" class="px-3 py-2 font-medium text-center border">OUT</th>
                                        
                                        <th scope="col" class="px-3 py-2 font-medium text-center border">IN</th>
                                        <th scope="col" class="px-3 py-2 font-medium text-center border">OUT</th>
                                        
                                        <th scope="col" class="px-3 py-2 font-medium text-center border">IN</th>
                                        <th scope="col" class="px-3 py-2 font-medium text-center border">OUT</th>

                                        <th scope="col" class="px-3 py-2 font-medium text-center border">Hours</th>
                                        <th scope="col" class="px-3 py-2 font-medium text-center border">Mins</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    @php 
                                        $firstDay = Carbon\Carbon::now()->startOfMonth();
                                        $lastDay = Carbon\Carbon::now()->endOfMonth();
                                        $logs = $employee->dtrRange($firstDay->toDateString(), $lastDay->toDateString());
                                    @endphp

                                    
                                    @forelse ($logs as $l)
                                        <tr class="bg-neutral-primary border-b border-default">
                                            <td class="px-3 py-2 text-center border">{{ $l->log_date }}</td>
                                            <td class="px-3 py-2 text-center border">{{ $l->formatted_am_in }}</td>
                                            <td class="px-3 py-2 text-center border">{{ $l->formatted_am_out }}</td>
                                            <td class="px-3 py-2 text-center border">{{ $l->formatted_pm_in }}</td>
                                            <td class="px-3 py-2 text-center border">{{ $l->formatted_pm_out }}</td>
                                            <td class="px-3 py-2 text-center border">{{ $l->formatted_ot_in }}</td>
                                            <td class="px-3 py-2 text-center border">{{ $l->formatted_ot_out }}</td>
                                            <td class="px-3 py-2 text-center border">
                                                {{ $employee->dailyTardiness($l)['hour'] }}
                                            </td>
                                            <td class="px-3 py-2 text-center border">
                                                {{ $employee->dailyTardiness($l)['minutes'] }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td class="border px-3 py-2 text-center" colspan="9">
                                                No logs to show.
                                            </td>
                                        </tr>
                                    @endforelse
                                    
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Change password modal -->
    <div id="change-password-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-2xl max-h-full">
            <!-- Modal content -->
            <form action="{{ route('employees.update-password', $employee->id) }}" method="POST" class="relative bg-white rounded-lg shadow-sm dark:bg-gray-700">
                @csrf
                <!-- Modal header -->
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 border-gray-200">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                        Change Password
                    </h3>
                    <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="change-password-modal">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <!-- Modal body -->
                <div class="p-4 md:p-5">
                    <x-input 
                        type="password"
                        name="password"
                        label="New Password"
                        notice="(Required)"
                        wrapperClass="mb-1 max-w-sm"
                    />
                </div>
                <!-- Modal footer -->
                <div class="flex items-center space-x-2 p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
                    <x-primary-button>Update</x-primary-button>
                    <x-primary-button type="button" data-modal-hide="change-password-modal">Cancel</x-primary-button>
                </div>
            </form>
        </div>
    </div>

</x-app-layout>