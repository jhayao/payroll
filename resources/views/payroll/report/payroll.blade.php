<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Payroll Report</title>

    <style>
        table {
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #cccccc;
        }

        th, td {
            padding-left: 2px;
            padding-right: 2px;
            padding-top: 1px;
            padding-bottom: 1px;
        }

        .text-center {
            text-align: center;
        }

        .text-end {
            text-align: right;
        }

        .b {
            font-weight: bold;
        }

        .mb-2 {
            margin-bottom: 20px;
        }

        .mb-3 {
            margin-bottom: 30px;
        }
    </style>

</head>
<body>
    
    
        <div class="text-center b">Payroll for {{ $payroll->department->name }}</div>
        
        @php
            $from = \Carbon\Carbon::parse($payroll->date_from);
            $to = \Carbon\Carbon::parse($payroll->date_to);
        @endphp

        <div class="text-center mb-3">
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
        
        <table>
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Employee</th>
                    <th class="text-end">No. of Days</th>
                    <th class="text-end">Daily Rate</th>
                    <th class="text-end">Overtime (Minutes)</th>
                    <th class="text-end">Overtime Pay</th>
                    <th class="text-end">Allowances</th>
                    <th class="text-end">Gross Pay</th>
                    <th class="text-end">Deductions</th>
                    <th class="text-end">Net Pay</th>
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
                    </tr>    
                @endforeach
            </tbody>
        </table>

</body>
</html>