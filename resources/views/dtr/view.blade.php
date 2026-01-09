<div class="mt-6">
    <div class="border border-slate-300 bg-white dark:bg-gray-800 relative sm:rounded shadow-sm overflow-hidden">
        <div class="px-8 py-6">

            <div class="mb-3">
                <div class="font-semibold text-lg">{{ $employee->fullname }}</div>
                @php
                    $date_from = \Carbon\Carbon::parse($from);
                    $date_to = \Carbon\Carbon::parse($to);
                @endphp
                <div class="font-medium mb-3">
                    @if ($date_from->equalTo($date_to))
                        {{-- Same day --}}
                        {{ $date_from->format('F d, Y') }}
                    @elseif ($date_from->format('F Y') === $date_to->format('F Y'))
                        {{-- Same month and year --}}
                        {{ $date_from->format('F d') }}–{{ $date_to->format('d, Y') }}
                    @else
                        {{-- Different month/year --}}
                        {{ $date_from->format('F d, Y') }} to {{ $date_to->format('F d, Y') }}
                    @endif
                </div>
            </div>
            
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
                            
                            <th scope="col" colspan="2" class="px-3 py-2 font-medium text-center border">
                                TARDINESS
                            </th>
                        </tr>

                        <tr>
                            <th scope="col" class="px-3 py-2 font-medium text-center border">IN</th>
                            <th scope="col" class="px-3 py-2 font-medium text-center border">OUT</th>
                            
                            <th scope="col" class="px-3 py-2 font-medium text-center border">IN</th>
                            <th scope="col" class="px-3 py-2 font-medium text-center border">OUT</th>
                            
                            <th scope="col" class="px-3 py-2 font-medium text-center border">IN</th>
                            <th scope="col" class="px-3 py-2 font-medium text-center border">OUT</th>

                            <th scope="col" class="px-3 py-2 font-medium text-center border">Hours</th>
                            <th scope="col" class="px-3 py-2 font-medium text-center border">Mins</th>

                        </tr>
                    </thead>
                    <tbody>
                        @php 
                            $logs = $employee->dtrRange($from, $to); 
                            $totalMinutes = $employee->tardiness($from, $to)['grandTotal'];
                            $hours = intdiv($totalMinutes, 60);
                            $minutes = $totalMinutes % 60;
                        @endphp
                        
                        @forelse ($logs as $l)
                            <tr class="bg-neutral-primary border-b border-default">
                                <td class="px-3 py-2 text-center border">{{ $l->log_date }}</td>
                                <td class="px-3 py-2 text-center border">{{ $l->formatted_am_in }}</td>
                                <td class="px-3 py-2 text-center border">{{ $l->formatted_am_out }}</td>
                                <td class="px-3 py-2 text-center border">{{ $l->formatted_pm_in }}</td>
                                <td class="px-3 py-2 text-center border">{{ $l->formatted_pm_out }}</td>
                                <td class="px-3 py-2 text-center border">{{ $l->formatted_ot_in }}</td>
                                <td class="px-3 py-2 text-center border">{{ $l->formatted_ot_out }}</td>
                                <td class="px-3 py-2 text-center border">
                                    {{ $employee->dailyTardiness($l)['hour'] }}
                                </td>
                                <td class="px-3 py-2 text-center border">
                                    {{ $employee->dailyTardiness($l)['minutes'] }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="border px-3 py-2 text-center" colspan="9">
                                    No logs to show.
                                </td>
                            </tr>
                        @endforelse
                        <tr>
                            <td colspan="7" class="px-3 py-2 text-center border font-semibold">TOTAL</td>
                            <td class="px-3 py-2 text-center border font-semibold">{{ $hours }}</td>
                            <td class="px-3 py-2 text-center border font-semibold">{{ $minutes }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>