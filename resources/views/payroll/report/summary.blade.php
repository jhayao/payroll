<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Summary of Dates</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        th, td {
            border: 1px solid #000;
            padding: 2px;
            text-align: center;
            overflow: hidden;
            word-wrap: break-word;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
            font-size: 8px; /* Smaller header font */
        }
        .text-left {
            text-align: left;
            padding-left: 4px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .name-col { width: 120px; }
        .pos-col { width: 80px; }
        .total-col { width: 45px; }
        .date-col { width: auto; font-size: 8px; }
        .vertical-text {
            writing-mode: vertical-rl;
            transform: rotate(180deg);
            white-space: nowrap;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>SUMMARY OF DATES</h2>
        <p>{{ $payroll->department->name }} | {{ \Carbon\Carbon::parse($payroll->date_from)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($payroll->date_to)->format('M d, Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2" class="name-col">NAME</th>
                <th rowspan="2" class="pos-col">POSITION</th>
                <th colspan="{{ $dateCounts }}">SUMMARY OF DATES</th>
                <th rowspan="2" class="total-col">Total<br>Days</th>
                <th rowspan="2" class="total-col">Total<br>Overtime</th>
                <th rowspan="2" class="total-col">Total<br>Undertime</th>
            </tr>
            <tr>
                @foreach($dates as $date)
                    <th class="date-col">{{ $date->format('m/d') }}</th>
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
                            $val = $row['days'][$date->format('Y-m-d')];
                        @endphp
                        <td style="background-color: {{ $val > 0 ? '#e6f3ff' : 'transparent' }}">
                           {{ $val == 0 ? '0' : ($val == 0.5 ? '0.5' : '1') }}
                        </td>
                    @endforeach
                    <td><strong>{{ $row['total_days'] }}</strong></td>
                    <td>{{ $row['total_ot'] }}</td>
                    <td>{{ $row['total_undertime'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
