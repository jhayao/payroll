<x-app-layout>

    <div class="py-14 max-w-7xl mx-auto">
        
        <div class="mb-2">Today, {{ Carbon\Carbon::now()->format('l, F d, Y') }}</div>
        <h1 class="text-2xl font-bold mb-6">Dashboard</h1>

        <!-- SUMMARY CARDS -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @php
                $cards = [
                    ['label' => 'Employees', 'count' => $employeesCount, 'route' => route('employees')],
                    ['label' => 'Departments', 'count' => $departmentsCount, 'route' => route('departments')],
                    ['label' => 'Positions', 'count' => $positionsCount, 'route' => route('positions')],
                    ['label' => 'Shifts', 'count' => $shiftsCount, 'route' => route('shifts')],
                ];
            @endphp

           @foreach ($cards as $card)
                @php
                    $canClick = in_array(auth()->user()->role, ['admin', 'hr']);
                @endphp

                <div
                    class="bg-white rounded shadow p-4 {{ $canClick ? 'cursor-pointer hover:bg-gray-50' : '' }}"
                    {{ $canClick ? "onclick=window.location.href='{$card['route']}'" : '' }}
                    role="{{ $canClick ? 'button' : 'presentation' }}"
                >
                    <p class="text-sm text-gray-500">{{ $card['label'] }}</p>
                    <p class="text-2xl font-bold">{{ $card['count'] }}</p>
                </div>
            @endforeach

        </div>

        <!-- RECENT EMPLOYEES -->
        <div class="bg-white rounded shadow p-4 mt-6">
            <h2 class="font-bold mb-4">Recent Employees</h2>
            <div class="relative overflow-x-auto bg-neutral-primary-soft shadow-xs rounded-base border border-default">
                <table class="w-full text-sm text-left rtl:text-right text-body">
                    <thead class="text-sm text-body bg-neutral-secondary-soft border-b rounded-base border-default">
                        <tr>
                            <th scope="col" class="px-6 py-3 font-medium">Name</th>
                            <th scope="col" class="px-6 py-3 font-medium">Department</th>
                            <th scope="col" class="px-6 py-3 font-medium">Position</th>
                            <th scope="col" class="px-6 py-3 font-medium">Rate</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recentEmployees as $emp)
                            <tr class="bg-neutral-primary border-b border-default">
                                <th scope="row" class="px-6 py-4 font-medium text-heading whitespace-nowrap">
                                    <div class="flex items-center space-x-2">
                                        <img src="{{ asset($emp->photo_2x2) }}" class="w-10 h-10 rounded-full" />
                                        <div>
                                            <div class="font-medium">{{ $emp->full_name }}</div>
                                            <div class="text-gray-700">{{ $emp->id }}</div>
                                        </div>
                                    </div>
                                </th>
                                <td class="px-6 py-4">{{ $emp->department->name ?? '-' }}</td>
                                <td class="px-6 py-4">{{ $emp->position->description ?? '-' }}</td>
                                <td class="px-6 py-4">{{ $emp->position->formatted_daily_rate ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
            <div class="bg-white p-4 rounded shadow">
                <h3 class="font-bold mb-2">Employees per Department</h3>
                @foreach ($employeesPerDept as $dept)
                    <p class="text-sm">{{ $dept->name }}: {{ $dept->employees_count }}</p>
                @endforeach
            </div>

            <div class="bg-white p-4 rounded shadow">
                <h3 class="font-bold mb-2">Employees per Position</h3>
                @foreach ($employeesPerPosition as $pos)
                    <p class="text-sm">{{ $pos->description }}: {{ $pos->employees_count }}</p>
                @endforeach
            </div>
        </div>



    </div>
</x-app-layout>