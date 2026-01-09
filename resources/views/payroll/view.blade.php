<x-app-layout>

    <div class="py-14 max-w-7xl mx-auto">

        <x-breadcrumb :items="[
            ['label' => 'Payroll', 'url' => route('payroll')],
            ['label' => 'View Payroll']
        ]" />
        
        <div class="font-bold text-gray-700 text-xl dark:text-white">View Payroll</div>
        
        <div class="mt-6">

            <div class="bg-white border border-slate-200 dark:bg-gray-800 relative sm:rounded shadow-sm overflow-hidden">
                <div class="p-6">

                    <div class="md:flex items-center justify-between mb-3">
                        <div class="mb-3 md:mb-0">
                            <div class="text-2xl font-meduim">Payroll for {{ $payroll->department->name }}</div>
                            
                            @php
                                $from = \Carbon\Carbon::parse($payroll->date_from);
                                $to = \Carbon\Carbon::parse($payroll->date_to);
                            @endphp

                            <div class="text-gray-600">
                                @if ($from->equalTo($to))
                                    {{-- Same day --}}
                                    Period {{ $from->format('F d, Y') }}
                                @elseif ($from->format('F Y') === $to->format('F Y'))
                                    {{-- Same month and year --}}
                                    Period from {{ $from->format('F d') }}–{{ $to->format('d, Y') }}
                                @else
                                    {{-- Different month/year --}}
                                    Period from {{ $from->format('F d, Y') }} to {{ $to->format('F d, Y') }}
                                @endif
                            </div>
                        </div>

                        <div class="mb-3 md:mb-0 flex items-center space-x-2">
                            <form action="{{ route('payroll.generate-report') }}" method="post">
                                @csrf
                                <input type="hidden" name="payroll_id" value="{{ $payroll->id }}" />
                                <x-primary-button>Generate Report</x-primary-button>    
                            </form>
                            <form action="{{ route('payroll.generate-payslip') }}" method="post">
                                @csrf
                                <input type="hidden" name="payroll_id" value="{{ $payroll->id }}" />
                                <x-primary-button>Generate Payslips</x-primary-button>
                            </form>
                        </div>
                        
                    </div>

                    <div class="overflow-x-auto w-full">
                        <table id="myTable" class="border">
                            <thead>
                                <tr>
                                    <th>
                                        <span class="text-end">
                                            No.
                                        </span>
                                    </th>
                                    <th>
                                        <span class="flex items-center">Employee</span>
                                    </th>
                                    <th>
                                        <span class="text-end">Days of Duty</span>
                                    </th>
                                    <th>
                                        <span class="text-end">Rate</span>
                                    </th>
                                    <th class="text-end">Overtime (Minutes)</th>
                                    <th class="text-end">Overtime Pay</th>
                                    <th class="text-end">Allowances</th>
                                    <th>
                                        <span class="text-end">Salary</span>
                                    </th>
                                    <th class="flex flex-row-reverse">
                                        <span>Deductions</span>
                                    </th>
                                    <th>
                                        <span class="text-end">Net Pay</span>
                                    </th>
                                    <th>
                                        <span>Action</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>

                                @php $count = 1; @endphp

                                @foreach ($payroll->items as $e)
                                    <tr>
                                        <td class="text-end">{{ $count }}.</td>
                                        <td>{{ $e->employee->full_name }}</td>
                                        <td class="text-end">{{ $e->num_of_days }}</td>
                                        <td class="text-end">{{ number_format($e->daily_rate, 2) }}</td>
                                        <td class="text-end">{{ $e->overtime }}</td>
                                        <td class="text-end">{{ $e->formatted_overtime_pay }}</td>
                                        <td class="text-end">{{ $e->formatted_total_allowance }}</td>
                                        <td class="text-end">{{ $e->formatted_gross_pay }}</td>
                                        <td class="text-end">{{ $e->formatted_total_deduction }}</td>
                                        <td class="text-end">{{ $e->formatted_net_pay }}</td>
                                        <td>
                                            <a href="{{ route('payroll.item', [$payroll->id, $e->id]) }}" class="flex items-center space-x-1 text-sm font-semibold text-gray-600 hover:text-gray-800">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                                </svg>
                                                <span>View</span>
                                            </a>
                                        </td>
                                    </tr>    
                                @endforeach
                            </tbody>
                        </table>
                    </div>

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