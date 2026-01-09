<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Payslips</title>

    <style>
        table, th, td {
            font-size: 12px;
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

        .p-3 {
            padding: 10px
        }

        .border {
            border: 1px solid #cccccc
        }

        .wrapper {
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: space-between
        }

        .card {
            width: 320px;
        }
    </style>

</head>
<body>
    
    
    <table width="100%" cellspacing="0" cellpadding="5">
        @foreach ($payroll->items->chunk(2) as $row)
            <tr>
                @foreach ($row as $item)
                    <td width="50%" valign="top">
                        <div class="card border p-3 mb-1">
                            <div class="text-center b" style="border-bottom:1px solid #cccccc">
                                PAYSLIP
                            </div>

                            <table width="100%">
                                <tr>
                                    <td>Date:</td>
                                    <td class="text-end">{{ $payroll->date_to->format('F d, Y') }}</td>
                                </tr>
                                <tr>
                                    <td>Employee:</td>
                                    <td class="text-end">{{ $item->employee->full_name }}</td>
                                </tr>
                                <tr>
                                    <td>Position:</td>
                                    <td class="text-end">{{ $item->employee->position->description }}</td>
                                </tr>
                                <tr>
                                    <td>Days of Duty:</td>
                                    <td class="text-end">{{ $item->num_of_days }}</td>
                                </tr>
                                <tr>
                                    <td>Rate:</td>
                                    <td class="text-end">{{ $item->employee->position->formatted_daily_rate }}</td>
                                </tr>
                                <tr>
                                    <td>Overtime:</td>
                                    <td class="text-end">{{ $item->formatted_overtime_pay}}</td>
                                </tr>
                                <tr>
                                    <td>Salary:</td>
                                    <td class="text-end">{{ $item->gross_pay}}</td>
                                </tr>

                                @foreach ($item->allowances as $a)
                                    <tr>
                                        <td>Add: {{ $a->description }}</td>
                                        <td class="text-end">{{ $a->formatted_amount }}</td>
                                    </tr>
                                @endforeach

                                @foreach ($item->deductions as $d)
                                    <tr>
                                        <td>Less: {{ $d->description }}</td>
                                        <td class="text-end">{{ $d->formatted_amount }}</td>
                                    </tr>
                                @endforeach
                                
                                <tr>
                                    <td style="border-top:1px solid #cccccc" class="b">Net Pay:</td>
                                    <td style="border-top:1px solid #cccccc" class="b text-end">{{ $item->formatted_net_pay }}</td>
                                </tr>
                            </table>
                        </div>
                    </td>
                @endforeach

                {{-- If odd number, fill empty cell --}}
                @if ($row->count() < 2)
                    <td width="50%"></td>
                @endif
            </tr>
        @endforeach
    </table>
       
</body>
</html>