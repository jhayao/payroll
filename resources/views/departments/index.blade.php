<x-app-layout>
    <div class="py-14 max-w-7xl mx-auto">
        <div class="flex items-center justify-between">
            <div class="font-bold text-gray-700 text-xl dark:text-white">Departments</div>
            <div class="flex items-center space-x-1">
                <x-button-link href="{{ route('departments.add') }}">Add New</x-button-link> 
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
                                    <span>Abbreviation</span>
                                </th>
                                <th>
                                    <span class="flex items-center">Action</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($departments as $e)
                            <tr class="border-t">
                                <td>{{ $e->name }}</td>
                                <td>{{ $e->abbr }}</td>
                                <td>
                                    <div class="flex items-center space-x-3">
                                        <a href="{{ route('departments.edit', $e->id) }}" class="flex items-center space-x-1 text-sm font-semibold text-blue-600 hover:text-blue-800">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                            </svg>
                                            <span>Edit</span>
                                        </a>
     
                                        @if ($e->employees_count === 0)
                                            <x-confirm-delete 
                                                action="{{ route('departments.delete', $e->id) }}"
                                            />
                                        @else
                                            <span class="text-xs text-gray-400 italic">
                                                In use
                                            </span>
                                        @endif
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
            ordering: false,
            paginate: false
        });
    </script>

</x-app-layout>