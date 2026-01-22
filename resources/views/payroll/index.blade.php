<x-app-layout>
    <div class="py-14 max-w-7xl mx-auto">
        <div class="flex items-center justify-between">
            <div class="font-bold text-gray-700 text-xl dark:text-white">Payroll</div>
            <div class="flex items-center space-x-1">
                <x-button-link href="{{ route('payroll.create') }}">Create</x-button-link>
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
                                    <span class="flex items-center">Generation Type</span>
                                </th>
                                <th>
                                    <span>From</span>
                                </th>
                                <th>
                                    <span>To</span>
                                </th>
                                <th>
                                    <span>Status</span>
                                </th>
                                <th>
                                    <span class="flex items-center">Action</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($payrolls as $e)
                                <tr class="border-t">
                                    <td>{{ $e->generation_info }}</td>
                                    <td>{{ $e->formatted_date_from }}</td>
                                    <td>{{ $e->formatted_date_to }}</td>
                                    <td>{{ $e->status }}</td>
                                    <td>
                                        <div class="flex items-center space-x-3">
                                            <a href="{{ route('payroll.view', $e->id) }}"
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
            ordering: false
        });
    </script>

</x-app-layout>