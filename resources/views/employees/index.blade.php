<x-app-layout>
    <div class="py-14 max-w-7xl mx-auto">
        <div class="flex items-center justify-between">
            <div class="font-bold text-gray-700 text-xl dark:text-white">Employee List</div>
            <div class="flex items-center space-x-1">
                <x-button-link href="{{ route('employees.add') }}">Add New</x-button-link>
            </div>

        </div>
        <div class="mt-6">
            <!-- Start coding here -->
            <div class="bg-white border border-slate-300 rounded px-4 relative">
                <div class="overflow-x-auto w-full">
                    <table id="myTable">
                        <thead>
                            <tr>
                                <th>
                                    <span class="flex items-center">Name</span>
                                </th>
                                <th>
                                    <span class="flex items-center">Position</span>
                                </th>
                                <th>
                                    <span class="flex items-center">Deparment</span>
                                </th>
                                <th>
                                    <span class="text-end">Daily Rate</span>
                                </th>
                                <th>
                                    <span class="flex items-center">Action</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($employees as $e)
                                <tr class="border-t">
                                    <td>
                                        <div class="flex items-center space-x-2">
                                            <img src="{{ asset($e->photo_2x2) }}" class="w-12 h-12 rounded-full" />
                                            <div>
                                                <div class="font-medium">{{ $e->full_name }}</div>
                                                <div class="text-gray-700">{{ $e->id }}</div>
                                            </div>

                                        </div>
                                    </td>
                                    <td>{{ $e->position->description }}</td>
                                    <td>{{ $e->department->name }}
                                        {{ $e->department ? '(' . $e->department->abbr . ')' : '' }}
                                    </td>
                                    <td>{{ $e->formatted_daily_rate }}</td>
                                    <td>
                                        <div class="flex items-center space-x-2">
                                            <a href="{{ route('employees.view', $e->id) }}"
                                                class="flex items-center space-x-1 text-sm font-semibold text-gray-600 hover:text-gray-800">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                    stroke-width="1.5" stroke="currentColor" class="size-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                                </svg>
                                                <span>View</span>
                                            </a>
                                            <form action="{{ route('employees.delete', $e->id) }}" method="POST"
                                                onsubmit="return confirm('Are you sure you want to delete this employee?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="flex items-center space-x-1 text-sm font-semibold text-red-600 hover:text-red-800">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                        stroke-width="1.5" stroke="currentColor" class="size-4">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                    </svg>
                                                    <span>Delete</span>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <script>
        let table = new DataTable('#myTable', {
            lengthChange: false,
            columnDefs: [
                {
                    orderable: false,
                    targets: [1, 2, 4]
                }
            ]
        });
    </script>

</x-app-layout>