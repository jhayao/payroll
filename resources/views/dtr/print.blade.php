<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>DTR</title>

    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }

        table, th, td {
            border: 1px solid #cccccc;
            font-size: 12px;
        }

        th, td {
            padding: 4px;
            text-align: center;
        }
    </style>
</head>
<body>

    <div style="margin-bottom: 20px">
        <strong>{{ $employee->fullname }}</strong>

        @php
            $date_from = \Carbon\Carbon::parse($from);
            $date_to = \Carbon\Carbon::parse($to);
        @endphp

        <div class="mb-3">
            @if ($date_from->equalTo($date_to))
                {{ $date_from->format('F d, Y') }}
            @elseif ($date_from->format('F Y') === $date_to->format('F Y'))
                {{ $date_from->format('F d') }}–{{ $date_to->format('d, Y') }}
            @else
                {{ $date_from->format('F d, Y') }} to {{ $date_to->format('F d, Y') }}
            @endif
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2">DATE</th>
                <th colspan="2">MORNING</th>
                <th colspan="2">AFTERNOON</th>
                <th colspan="2">OVER TIME</th>
                <th colspan="2">TARDINESS</th>
            </tr>
            <tr>
                <th>IN</th>
                <th>OUT</th>
                <th>IN</th>
                <th>OUT</th>
                <th>IN</th>
                <th>OUT</th>
                <th>Hours</th>
                <th>Mins</th>
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
                <tr>
                    <td>{{ $l->log_date }}</td>
                    <td>{{ $l->formatted_am_in }}</td>
                    <td>{{ $l->formatted_am_out }}</td>
                    <td>{{ $l->formatted_pm_in }}</td>
                    <td>{{ $l->formatted_pm_out }}</td>
                    <td>{{ $l->formatted_ot_in }}</td>
                    <td>{{ $l->formatted_ot_out }}</td>
                    <td>{{ $employee->dailyTardiness($l)['hour'] }}</td>
                    <td>{{ $employee->dailyTardiness($l)['minutes'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="text-align:center;">
                        No logs to show.
                    </td>
                </tr>
            @endforelse
            <tr style="font-weight: bold">
                <td colspan="7">TOTAL</td>
                <td>{{ $hours }}</td>
                <td>{{ $minutes }}</td>
            </tr>
        </tbody>
    </table>

</body>
</html>
