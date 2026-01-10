<x-app-layout>
    <div class="py-14 max-w-7xl mx-auto">

        <x-breadcrumb :items="[
        ['label' => 'Positions', 'url' => route('positions')],
        ['label' => 'Add New Position']
    ]" />

        <div class="font-bold text-gray-700 text-xl dark:text-white">Add New Position</div>

        <div class="mt-6">

            <div
                class="bg-white border border-slate-200 dark:bg-gray-800 relative sm:rounded shadow-sm overflow-hidden">
                <div class="p-8">

                    <form action="{{ route('positions.save') }}" method="POST">
                        @csrf
                        <div class="font-medium">POSITION INFORMATION</div>

                        <div class="max-w-xl mt-6">

                            <x-input name="description" label="Description" value="{{ old('description') }}"
                                notice="(Required)" />

                            <x-input name="daily_rate" label="Daily Rate" value="{{ old('daily_rate') }}"
                                notice="(Required)" />

                            <div class="mt-4">
                                <label for="department_id"
                                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Department
                                    (Optional)</label>
                                <select id="department_id" name="department_id"
                                    class="bg-gray-50 border text-sm rounded block w-full p-2 text-gray-900 focus:ring-blue-500 focus:border-blue-500 border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                    <option value="">Select Department</option>
                                    @foreach($departments as $id => $name)
                                        <option value="{{ $id }}" {{ old('department_id') == $id ? 'selected' : '' }}>
                                            {{ $name }}</option>
                                    @endforeach
                                </select>
                                @error('department_id')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                                @enderror
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