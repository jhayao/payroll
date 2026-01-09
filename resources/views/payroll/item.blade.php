<x-app-layout>

    <div class="py-14 max-w-7xl mx-auto">

        <x-breadcrumb :items="[
        ['label' => 'Payroll', 'url' => route('payroll')],
        ['label' => 'View Payroll', 'url' => route('payroll.view', $payroll->id)],
        ['label' => 'Item']
    ]" />

        <div class="font-bold text-gray-700 text-xl dark:text-white">Payroll Item</div>

        <div class="mt-6">

            <div
                class="bg-white border border-slate-200 dark:bg-gray-800 relative sm:rounded shadow-sm overflow-hidden">
                <div class="p-6">

                    <div class="text-lg font-meduim mb-1">Payroll for {{ $payroll->department->name }}</div>

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

                    <div class="border px-6 py-4 mt-6">
                        <div class="font-medium">{{ $payrollItem->employee->full_name }}</div>
                        <div class="font-medium text-gray-600 text-sm">{{ $payrollItem->employee_id }}</div>

                        <div class="lg:grid grid-cols-3 gap-4 mt-6">
                            <div class="border px-6 py-4">
                                <div class="flex items-center justify-between border-b border-dashed py-1">
                                    <span>Number of Days Worked:</span>
                                    <div class="font-medium">{{ $payrollItem->num_of_days }}</div>
                                </div>
                                <div class="flex items-center justify-between border-b border-dashed py-1">
                                    <span>Rate:</span>
                                    <div class="font-medium">{{ $payrollItem->formatted_daily_rate }}</div>
                                </div>
                                <div class="flex items-center justify-between border-b border-dashed py-1">
                                    <span>Overtime (Minutes):</span>
                                    <div class="font-medium">{{ $payrollItem->overtime }}</div>
                                </div>
                                <div class="flex items-center justify-between border-b border-dashed py-1">
                                    <span>Overtime Pay:</span>
                                    <div class="font-medium">{{ $payrollItem->formatted_overtime_pay }}</div>
                                </div>
                                <div class="flex items-center justify-between border-b border-dashed py-1">
                                    <span>Salary:</span>
                                    <div class="font-medium">{{ $payrollItem->formatted_gross_pay }}</div>
                                </div>
                                <div class="flex items-center justify-between border-b border-dashed py-1">
                                    <span>Allowances:</span>
                                    <div class="font-medium">{{ $payrollItem->formatted_total_allowance }}</div>
                                </div>
                                <div class="flex items-center justify-between border-b border-dashed py-1">
                                    <span>Deductions:</span>
                                    <div class="font-medium">{{ $payrollItem->formatted_total_deduction }}</div>
                                </div>
                                <div class="flex items-center justify-between py-2">
                                    <span class="text-lg">Net Pay:</span>
                                    <div class="text-lg font-medium">{{ $payrollItem->formatted_net_pay }}</div>
                                </div>
                            </div>

                            <div class="border px-6 py-4">
                                <div class="font-medium">Allowances</div>
                                <div class="border-t my-2"></div>
                                @forelse ($payrollItem->allowances as $a)
                                    <div class="flex items-center justify-between border-b border-dashed py-1">
                                        <div class="text-sm">
                                            <span>{{ $a->description }}</span>
                                            <div class="font-medium">{{ $a->formatted_amount }}</div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="flex items-center justify-between">
                                        <span class="text-gray-400">No allowance specified.</span>
                                    </div>
                                @endforelse
                            </div>

                            <div class="border px-6 py-4">
                                <div class="font-medium">Deductions</div>
                                <div class="border-t my-2"></div>
                                @forelse ($payrollItem->deductions as $d)
                                    <div class="flex items-center justify-between border-b border-dashed py-1">
                                        <div class="text-sm">
                                            <span>{{ $d->description }}</span>
                                            <div class="font-medium">{{ $d->formatted_amount }}</div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="flex items-center justify-between">
                                        <span class="text-gray-400">No deduction specified.</span>
                                    </div>
                                @endforelse
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>



</x-app-layout>