<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payslips</title>
    <style>
        body { font-family: sans-serif; }
        .wrapper { width: 100%; margin-bottom: 20px; page-break-inside: avoid; }
        .payslip-container { width: 100%; max-width: 400px; margin: 0 auto; border: 1px solid #000; font-size: 12px; }
        .header { color: #4a8c3d; font-weight: bold; font-size: 24px; border-bottom: 3px solid #4a8c3d; display: inline-block; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 3px 5px; }
        .no-border-top { border-top: none; }
        .no-border-bottom { border-bottom: none; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-red { color: red; font-weight: bold; }
        .bg-gray { background-color: #f0f0f0; }
        .total-row td { font-weight: bold; }
        .net-pay-row td { font-weight: bold; font-size: 14px; background-color: #e6f2e6; }
        .net-label { color: purple; }
        .footer { margin-top: 20px; font-size: 11px; }
        .signatory-box { margin-bottom: 30px; }
        .sign-name { text-decoration: underline; font-weight: bold; }
    </style>
</head>
<body>
    <table width="100%" cellspacing="0" cellpadding="0" style="border: none;">
        @foreach ($payroll->items->chunk(2) as $row)
            <tr style="border: none;">
                @foreach ($row as $item)
                    <td valign="top" width="50%" style="border: none; padding: 10px;">
                        <div class="wrapper">
                            <div class="header">PAYSLIP</div>
                            
                            <table class="payslip-table">
                                <!-- Info Section -->
                                <tr>
                                    <td colspan="2">DATE: {{ $payroll->date_from->format('M. d') }}-{{ $payroll->date_to->format('d, Y') }}</td>
                                    <td width="30%"></td>
                                </tr>
                                <tr>
                                    <td colspan="2">NAME: {{ strtoupper($item->employee->lastname) }}, {{ strtoupper($item->employee->firstname) }}</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>POSITION:</td>
                                    <td class="text-center b">{{ strtoupper($item->employee->position->description ?? '') }}</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>BASIC RATE:</td>
                                    <td></td>
                                    <td class="text-right">{{ number_format($item->daily_rate, 2) }}</td>
                                </tr>
                                
                                <!-- Earnings -->
                                <tr><td colspan="3" style="border-left:1px solid #000; border-right:1px solid #000; height: 10px;"></td></tr> <!-- Spacer -->
                                <tr>
                                    <td>REG. NO. DAYS</td>
                                    <td></td>
                                    <td class="text-right">{{ $item->num_of_days }}</td>
                                </tr>
                                <tr>
                                    <td>OVERTIME:</td>
                                    <td class="text-center">{{ round($item->overtime / 60, 2) }} HRS</td>
                                    <td class="text-right">{{ number_format($item->overtime_pay, 2) }}</td>
                                </tr>
                                @foreach($item->allowances as $allowance)
                                <tr>
                                    <td>ALLOWANCE:</td>
                                    <td class="text-center">{{ $allowance->description }}</td>
                                    <td class="text-right">{{ number_format($allowance->amount, 2) }}</td>
                                </tr>
                                @endforeach

                                <tr class="total-row">
                                    <td colspan="2" class="text-right text-red">TOTAL:</td>
                                    <td class="text-right">{{ number_format($item->gross_pay + $item->totalAllowance(), 2) }}</td>
                                </tr>

                                <!-- Deductions -->
                                <tr><td colspan="3" style="border:1px solid #000; height: 10px;"></td></tr> <!-- Spacer -->
                                <tr>
                                    <td colspan="3" class="text-red">DEDUCTIONS</td>
                                </tr>
                                
                                @foreach($item->deductions as $deduction)
                                <tr>
                                    <td>{{ strtoupper($deduction->description) }}:</td>
                                    <td></td>
                                    <td class="text-right">{{ number_format($deduction->amount, 2) }}</td>
                                </tr>
                                @endforeach

                                <!-- Undertime -->
                                @if($item->undertime_amount > 0)
                                <tr>
                                    <td>TARDINESS:</td>
                                    <td class="text-center">
                                        {{ floor($item->undertime_minutes / 60) }}H, {{ $item->undertime_minutes % 60 }}M
                                    </td>
                                    <td class="text-right">{{ number_format($item->undertime_amount, 2) }}</td>
                                </tr>
                                @endif

                                <tr class="total-row">
                                    <td colspan="2" class="text-right text-red">TOTAL:</td>
                                    <td class="text-right">{{ number_format($item->totalDeduction() + $item->undertime_amount, 2) }}</td>
                                </tr>

                                <!-- Net Pay -->
                                <tr><td colspan="3" style="border:1px solid #000; height: 10px;"></td></tr> <!-- Spacer -->
                                <tr class="net-pay-row">
                                    <td></td>
                                    <td class="text-center net-label">NET PAY:</td>
                                    <td class="text-right">P{{ number_format($item->net_pay, 2) }}</td>
                                </tr>
                            </table>

                            <div class="footer">
                                <div class="signatory-box">
                                    PREPARED BY:<br>
                                    <div style="text-align: center; margin-top: 20px;">
                                        <span class="sign-name">{{ strtoupper($preparedBy->name) }}</span><br>
                                        {{ strtoupper($preparedBy->position ?? 'Administrator') }}
                                    </div>
                                </div>
                                <div class="signatory-box">
                                    RECEIVED BY:<br>
                                    <div style="text-align: center; margin-top: 20px;">
                                        <span class="sign-name">{{ strtoupper($item->employee->lastname) }}, {{ strtoupper($item->employee->firstname) }}</span><br>
                                        EMPLOYEE
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                @endforeach
                @if ($row->count() < 2) <td width="50%" style="border: none;"></td> @endif
            </tr>
        @endforeach
    </table>
</body>
</html>