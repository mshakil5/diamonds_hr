<!DOCTYPE html>
<html>
<head>
    <title>Dirty Stock Report</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div style="padding: 20px;">
        <div class="header">
            <h1>Dirty Stock Report</h1>
            <h3>Branch: {{ $branchName }}</h3>
            {{-- <h4>Period: {{ $startDate->format('d/m/Y') }} - {{ $endDate->format('d/m/Y') }}</h4> --}}
        </div>

        <!-- Report Table -->
        @foreach($reportData as $index => $weekData)
            <h3>Week {{ $index + 1 }} ({{ $weekData['days']['Tuesday']['date']->format('d/m/Y') }} - {{ $weekData['days']['Monday']['date']->format('d/m/Y') }})</h3>
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Tuesday</th>
                        <th>Wednesday</th>
                        <th>Thursday</th>
                        <th>Total Outgoing (Tue-Thu)</th>
                        <th>Friday</th>
                        <th>Saturday</th>
                        <th>Sunday</th>
                        <th>Monday</th>
                        <th>Total Outgoing (Fri-Mon)</th>
                    </tr>
                    <tr>
                        <th>Date</th>
                        @foreach($weekData['days'] as $dayName => $dayData)
                            <th>{{ $dayData['date']->format('d/m/Y') }}</th>
                            @if($dayName == 'Thursday')
                                <th>{{ $weekData['first_three_days_total'] }}</th>
                            @endif
                            @if($dayName == 'Monday')
                                <th>{{ $weekData['last_four_days_total'] }}</th>
                            @endif
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $product)
                        <tr>
                            <td>{{ $product->name }}</td>
                            @foreach($weekData['days'] as $dayName => $dayData)
                                <td>{{ $dayData['quantities'][$product->id] ?? 0 }}</td>
                                @if($dayName == 'Thursday')
                                    <td>
                                        {{ array_sum([
                                            ($weekData['days']['Tuesday']['quantities'][$product->id] ?? 0),
                                            ($weekData['days']['Wednesday']['quantities'][$product->id] ?? 0),
                                            ($weekData['days']['Thursday']['quantities'][$product->id] ?? 0)
                                        ]) }}
                                    </td>
                                @endif
                                @if($dayName == 'Monday')
                                    <td>
                                        {{ array_sum([
                                            ($weekData['days']['Friday']['quantities'][$product->id] ?? 0),
                                            ($weekData['days']['Saturday']['quantities'][$product->id] ?? 0),
                                            ($weekData['days']['Sunday']['quantities'][$product->id] ?? 0),
                                            ($weekData['days']['Monday']['quantities'][$product->id] ?? 0)
                                        ]) }}
                                    </td>
                                @endif
                            @endforeach
                        </tr>
                    @endforeach
                    <tr>
                        <td><strong>Total</strong></td>
                        @foreach($weekData['days'] as $dayName => $dayData)
                            <td><strong>{{ $dayData['total'] }}</strong></td>
                            @if($dayName == 'Thursday')
                                <td><strong>{{ $weekData['first_three_days_total'] }}</strong></td>
                            @endif
                            @if($dayName == 'Monday')
                                <td><strong>{{ $weekData['last_four_days_total'] }}</strong></td>
                            @endif
                        @endforeach
                    </tr>
                </tbody>
            </table>
        @endforeach
    </div>
</body>
</html>