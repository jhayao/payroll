<x-employee-layout>
    <div class="py-14 max-w-7xl mx-auto">

        <div class="font-bold text-gray-700 text-xl dark:text-white">Profile</div>

        <div class="mt-6">

            <div
                class="border border-slate-300 bg-white dark:bg-gray-800 relative sm:rounded shadow-sm overflow-hidden">
                <div class="p-6">
                    <div class="md:flex gap-6">

                        <div class="mb-6 md:mb-0">
                            <img src="{{ asset($employee->photo_2x2) }}" class="w-24 h-24 xl:w-28 xl:h-28" />
                        </div>

                        <div class="flex-1">

                            <div class="grid lg:grid-cols-3 xl:grid-cols-4 gap-2">

                                <x-input name="employee_id" label="Employee ID" value="{{ $employee->employee_id }}"
                                    :readonly="true" />

                                <x-input name="lastname" label="Last Name"
                                    value="{{ old('lastname', $employee->lastname) }}" :readonly="true" />

                                <x-input name="firstname" label="First Name"
                                    value="{{ old('firstname', $employee->firstname) }}" :readonly="true" />

                                <x-input name="middlename" label="Middle Name"
                                    value="{{ old('middlename', $employee->middlename) }}" :readonly="true" />

                                <x-input name="suffix" label="Suffix" value="{{ old('suffix', $employee->suffix) }}"
                                    :readonly="true" />

                            </div>

                            <div class="grid lg:grid-cols-3 xl:grid-cols-4 gap-2">

                                <x-input name="sex" label="Sex" value="{{ old('sex', $employee->sex) }}"
                                    :readonly="true" />

                                <x-input name="purok" label="Purok" value="{{ old('purok', $employee->purok) }}"
                                    :readonly="true" />

                                <x-input name="barangay" label="Barangay"
                                    value="{{ old('barangay', $employee->barangay) }}" :readonly="true" />

                                <x-input name="city" label="City" value="{{ old('city', $employee->city) }}"
                                    :readonly="true" />

                                <x-input name="mobile_no" label="Mobile Number"
                                    value="{{ old('mobile_no', $employee->mobile_no) }}" :readonly="true" />

                            </div>

                            <div class="grid lg:grid-cols-3 xl:grid-cols-4 gap-2">
                                <x-input name="position" label="Position" value="{{ $employee->position->description }}"
                                    :readonly="true" />

                                <x-input name="department" label="Department" value="{{ $employee->department->name }}"
                                    :readonly="true" />

                                <x-input name="email" label="Email" value="{{ old('email', $employee->email) }}"
                                    :readonly="true" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="mt-6">

            <div
                class="border border-slate-300 bg-white dark:bg-gray-800 relative sm:rounded shadow-sm overflow-hidden">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        Change Password
                    </h3>

                    <form action="{{ route('employee.password-update') }}" method="post"
                        class="relative bg-white rounded shadow-sm dark:bg-gray-700">
                        @csrf

                        <x-input type="password" name="current_password" label="Current Password" notice="(Required)"
                            wrapperClass="mb-4 max-w-sm" />

                        <x-input type="password" name="password" label="New Password" notice="(Required)"
                            wrapperClass="mb-4 max-w-sm" />

                        <x-input type="password" name="password_confirmation" label="Confirm Password"
                            notice="(Required)" wrapperClass="mb-1 max-w-sm" />

                        <div class="mt-6">
                            <x-primary-button>Update</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>

        </div>

    </div>

</x-employee-layout>