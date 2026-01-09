<x-employee-layout>

    <div class="py-14 max-w-7xl mx-auto">

        <div class="p-6 border border-slate-200 bg-white rounded shadow-sm mb-6">
            <div class="font-medium mb-2">Welcome!</div>
            <div class="font-semibold text-xl">{{ auth()->user()->full_name }}</div>
            <div class="font-medium">Today, {{ Carbon\Carbon::now()->format('l, F d, Y') }}</div>
        </div>

        <div class="p-6 border border-slate-200 bg-white rounded shadow-sm mb-6">
            <div class="font-medium mb-4">Recent Logs</div>
            <div class="relative overflow-x-auto border border-default">
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
                        </tr>

                        <tr>
                            <th scope="col" class="px-3 py-2 font-medium text-center border">IN</th>
                            <th scope="col" class="px-3 py-2 font-medium text-center border">OUT</th>
                            
                            <th scope="col" class="px-3 py-2 font-medium text-center border">IN</th>
                            <th scope="col" class="px-3 py-2 font-medium text-center border">OUT</th>
                            
                            <th scope="col" class="px-3 py-2 font-medium text-center border">IN</th>
                            <th scope="col" class="px-3 py-2 font-medium text-center border">OUT</th>

                        </tr>
                    </thead>
                    <tbody>

                        @forelse ($dtr as $l)
                            <tr class="bg-neutral-primary border-b border-default">
                                <td class="px-3 py-2 text-center border">{{ Carbon\Carbon::parse($l->log_date)->format('F d, Y') }}</td>
                                <td class="px-3 py-2 text-center border">{{ $l->formatted_am_in }}</td>
                                <td class="px-3 py-2 text-center border">{{ $l->formatted_am_out }}</td>
                                <td class="px-3 py-2 text-center border">{{ $l->formatted_pm_in }}</td>
                                <td class="px-3 py-2 text-center border">{{ $l->formatted_pm_out }}</td>
                                <td class="px-3 py-2 text-center border">{{ $l->formatted_ot_in }}</td>
                                <td class="px-3 py-2 text-center border">{{ $l->formatted_ot_out }}</td>
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

</x-employee-layout>