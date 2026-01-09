<x-app-layout>
    <div class="py-14 max-w-7xl mx-auto">
        <x-breadcrumb :items="[
            ['label' => 'Projects', 'url' => route('projects')],
            ['label' => $project->name]
        ]" />

        <div class="font-bold text-gray-700 text-xl dark:text-white">Project Details</div>

        <div class="mt-6 space-y-6">
            <!-- Project Info Card -->
            <div class="bg-white border border-slate-200 relative sm:rounded shadow-sm overflow-hidden">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="text-lg font-medium">{{ $project->name }}</div>
                        <div class="flex items-center space-x-2">
                            <x-primary-button onclick="window.location.href='{{ route('projects.edit', $project->id) }}'">Edit</x-primary-button>
                            <form action="{{ route('projects.delete', $project->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this project?');">
                                @csrf
                                @method('DELETE')
                                <x-danger-button type="submit">Delete</x-danger-button>
                            </form>
                        </div>
                    </div>

                    <div class="grid md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="font-medium">Status:</span>
                            <span class="ml-2 px-2 py-1 text-xs rounded {{ 
                                $project->status === 'active' ? 'bg-green-100 text-green-800' : 
                                ($project->status === 'completed' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800') 
                            }}">
                                {{ ucfirst($project->status) }}
                            </span>
                        </div>
                        <div><span class="font-medium">Time Keeper:</span> {{ $project->timeKeeper?->full_name ?? 'Not assigned' }}</div>
                        @if($project->start_date)
                            <div><span class="font-medium">Start Date:</span> {{ $project->start_date->format('M d, Y') }}</div>
                        @endif
                        @if($project->end_date)
                            <div><span class="font-medium">End Date:</span> {{ $project->end_date->format('M d, Y') }}</div>
                        @endif
                    </div>

                    @if($project->description)
                        <div class="mt-4">
                            <div class="font-medium">Description:</div>
                            <div class="text-gray-700 mt-1">{{ $project->description }}</div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Assigned Employees Card -->
            <div class="bg-white border border-slate-200 relative sm:rounded shadow-sm overflow-hidden">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="text-lg font-medium">Assigned Employees ({{ $project->employees->count() }})</div>
                        
                        <form action="{{ route('projects.assign-employee', $project->id) }}" method="POST" class="flex items-center space-x-2">
                            @csrf
                            <select name="employee_id" class="border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Select Employee</option>
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}">{{ $emp->full_name }} ({{ $emp->position?->description ?? 'No Position' }})</option>
                                @endforeach
                            </select>
                            <x-primary-button type="submit">Assign</x-primary-button>
                        </form>
                    </div>

                    @if($project->employees->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50 border-b">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Position</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Department</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Assigned Date</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($project->employees as $employee)
                                        <tr class="border-b">
                                            <td class="px-4 py-3">{{ $employee->full_name }}</td>
                                            <td class="px-4 py-3">{{ $employee->position?->description ?? 'N/A' }}</td>
                                            <td class="px-4 py-3">{{ $employee->department?->name ?? 'N/A' }}</td>
                                            <td class="px-4 py-3">{{ \Carbon\Carbon::parse($employee->pivot->assigned_at)->format('M d, Y') }}</td>
                                            <td class="px-4 py-3">
                                                <form action="{{ route('projects.remove-employee', [$project->id, $employee->id]) }}" method="POST" onsubmit="return confirm('Remove this employee from the project?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-semibold">Remove</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-gray-500 text-center py-8">No employees assigned yet</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
