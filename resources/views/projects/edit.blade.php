<x-app-layout>
    <div class="py-14 max-w-7xl mx-auto">
        <x-breadcrumb :items="[
        ['label' => 'Projects', 'url' => route('projects')],
        ['label' => 'Edit Project']
    ]" />

        <div class="font-bold text-gray-700 text-xl dark:text-white">Edit Project</div>

        <div class="mt-6">
            <div
                class="bg-white border border-slate-200 dark:bg-gray-800 relative sm:rounded shadow-sm overflow-hidden">
                <div class="p-8">
                    <form action="{{ route('projects.update', $project->id) }}" method="POST">
                        @csrf
                        <div class="font-medium">PROJECT INFORMATION</div>

                        <div class="max-w-2xl mt-6 space-y-4">
                            <x-input name="name" label="Project Name" value="{{ old('name', $project->name) }}"
                                notice="(Required)" />

                            <x-textarea name="description" label="Description"
                                value="{{ old('description', $project->description) }}" notice="(Optional)" />

                            <div class="grid md:grid-cols-2 gap-4">
                                <x-select name="status" label="Status" :options="[
        'active' => 'Active',
        'on_hold' => 'On Hold',
        'completed' => 'Completed'
    ]" :selected="old('status', $project->status)" />

                                <x-select name="time_keeper_id" label="Time Keeper"
                                    :options="$employees->pluck('full_name', 'id')->prepend('Select Time Keeper', '')"
                                    :selected="old('time_keeper_id', $project->time_keeper_id)" notice="(Optional)" />
                            </div>

                            <div class="grid md:grid-cols-2 gap-4">
                                <x-input type="date" name="start_date" label="Start Date"
                                    value="{{ old('start_date', $project->start_date?->format('Y-m-d')) }}"
                                    notice="(Optional)" />

                                <x-input type="date" name="end_date" label="End Date"
                                    value="{{ old('end_date', $project->end_date?->format('Y-m-d')) }}"
                                    notice="(Optional)" />
                            </div>
                        </div>

                        <div class="flex items-center space-x-2 mt-6">
                            <x-primary-button>Update</x-primary-button>
                            <x-secondary-button
                                onclick="window.location.href='{{ route('projects.view', $project->id) }}'">Cancel</x-secondary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>