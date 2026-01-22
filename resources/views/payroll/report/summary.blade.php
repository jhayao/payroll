<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Summary of Dates</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9px;
            margin: 0;
            padding: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 4px 2px;
            text-align: center;
            word-wrap: break-word;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
            font-size: 8px;
            text-transform: uppercase;
        }

        .text-left {
            text-align: left;
            padding-left: 5px;
        }

        /* Column Widths */
        .name-col {
            width: 120px;
        }

        .pos-col {
            width: 80px;
        }

        .total-col {
            width: 45px;
        }

        .date-col {
            font-size: 8px;
        }

        /* Cell States */
        .active-cell {
            background-color: #e6f3ff;
        }

        .bold {
            font-weight: bold;
        }
    </style>
</head>

<body>

    <table>
        <thead>
            <tr>
                <th rowspan="2" class="name-col">Name</th>
                <th rowspan="2" class="pos-col">Position</th>
                <th colspan="{{ count($dates) * 2 }}">Summary of Dates</th>
                <th rowspan="2" class="total-col">Total<br>Days</th>
                <th rowspan="2" class="total-col">Total<br>OT</th>
                <th rowspan="2" class="total-col">Total<br>UT</th>
            </tr>
            <tr>
                @foreach($dates as $date)
                    <th class="date-col" colspan="2">{{ $date->format('m/d') }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($data as $row)
                <tr>
                    <td class="text-left">{{ strtoupper($row['name']) }}</td>
                    <td>{{ strtoupper($row['position']) }}</td>

                    @foreach($dates as $date)
                        @php 
                            $day = $row['days'][$date->format('Y-m-d')] ?? ['attendance' => 0, 'ot' => 0];
                        @endphp

                        <td class="{{ $day['attendance'] > 0 ? 'active-cell' : '' }}">
                            {{ number_format($day['attendance'], 1) }}
                        </td>
                        <td>
                            {{ $day['ot'] > 0 ? $day['ot'] : '' }}
                        </td>
                    @endforeach
                    <td class="bold">{{ $row['total_days'] }}</td>
                    <td>{{ number_format($row['total_ot'] / 60, 2) }}</td>
                        <td>{{ $row['total_undertime'] }}</td>
                    </tr>
            @endforeach
        </tbody>
    </t
able>

</body>
</html>