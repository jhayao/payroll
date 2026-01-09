<x-app-layout>
    <div class="py-14 max-w-7xl mx-auto">

        <x-breadcrumb :items="[
        ['label' => 'Employee List', 'url' => route('employees')],
        ['label' => 'Add New Employee']
    ]"/>

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
                                     notice="(Required)"/>

                            <x-input name="firstname" label="First Name" value="{{ old('firstname') }}"
                                     notice="(Required)"/>

                            <x-input name="middlename" label="Middle Name" value="{{ old('middlename') }}"/>

                            <x-input name="suffix" label="Suffix" value="{{ old('suffix') }}"/>

                        </div>

                        <div class="md:grid grid-cols-4 xl:grid-cols-4 gap-4">
                            <x-select name="sex" label="Sex"  wrapperClass="col-span-2 mb-6 md:mb-0" :options="[
                                'Male' => 'Male',
                                'Female' => 'Female'
                            ]"/>

                            <x-input name="mobile_no" label="Mobile Number" value="{{ old('mobile_no') }}"
                                     notice="(Required)" wrapperClass="col-span-2 mb-6 md:mb-0"/>

                        </div>
                        <div class="md:grid grid-cols-4 xl:grid-cols-3 gap-4 mt-6">
                            <x-input name="purok" label="Purok" value="{{ old('purok') }}" notice="(Required)"/>

                            <x-input name="barangay" label="Barangay" value="{{ old('barangay') }}"
                                     notice="(Required)"/>

                            <x-input name="city" label="City" value="{{ old('city') }}" notice="(Required)"/>
                        </div>
                        <div class="md:grid grid-cols-4 xl:grid-cols-2 gap-4">
                            <x-select name="position" label="Position" :options="$positions"/>

                            <x-select name="department" label="Department" :options="$departments"/>

                            <x-input name="custom_daily_rate" label="Custom Daily Rate" value="{{ old('custom_daily_rate') }}"
                                     notice="(Optional - Overrides Position Rate)"/>
                        </div>



                        <div class="md:grid grid-cols-4 xl:grid-cols-2 gap-4">
                            <x-input name="email" label="Email" value="{{ old('email') }}" notice="(Optional)"/>

                            <x-input type="password" name="password" label="Password" value="{{ old('password') }}"
                                     notice="(Optional)"/>
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
