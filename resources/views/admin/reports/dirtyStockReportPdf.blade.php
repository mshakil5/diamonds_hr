<!DOCTYPE html>
<html>
<head>
    <title>Dirty & Rejected Stock Report</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid black; padding: 8px; text-align: center; }
        th { background-color: #f2f2f2; }
        .header { text-align: center; margin-bottom: 20px; }
        .section-title { margin-top: 40px; text-align: center; }
    </style>
</head>
<body>

    {{-- Dirty Section --}}
    <div class="header">
        <h1>Dirty Stock Report</h1>
        <h3>Branch: {{ $branchName }}</h3>
        <h4>Period: {{ $startDate->format('d/m/Y') }} - {{ $endDate->format('d/m/Y') }}</h4>
    </div>

    <table>
        <thead>
            <tr>
                <th>Product</th>
                @foreach($reports['Dirty']['days'] as $day)
                    <th>{{ $day['date']->format('d/m/Y') }}</th>
                @endforeach
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reports['Dirty']['products'] as $product)
                <tr>
                    <td>{{ $product->name }}</td>
                    @foreach($reports['Dirty']['days'] as $day)
                        <td>{{ $day['quantities'][$product->id] ?? '' }}</td>
                    @endforeach
                    <td>{{ $reports['Dirty']['product_totals'][$product->id] ?? 0 }}</td>
                </tr>
            @endforeach
            <tr>
                <td><strong>Total</strong></td>
                @foreach($reports['Dirty']['days'] as $day)
                    <td><strong>{{ $day['total'] }}</strong></td>
                @endforeach
                <td><strong>{{ $reports['Dirty']['total_sum'] }}</strong></td>
            </tr>
        </tbody>
    </table>

    {{-- Rejected Section --}}
    <div class="section-title">
        <h1>Rejected Stock Report</h1>
        <h3>Branch: {{ $branchName }}</h3>
        <h4>Period: {{ $startDate->format('d/m/Y') }} - {{ $endDate->format('d/m/Y') }}</h4>
    </div>

    <table>
        <thead>
            <tr>
                <th>Product</th>
                @foreach($reports['Rejected']['days'] as $day)
                    <th>{{ $day['date']->format('d/m/Y') }}</th>
                @endforeach
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reports['Rejected']['products'] as $product)
                <tr>
                    <td>{{ $product->name }}</td>
                    @foreach($reports['Rejected']['days'] as $day)
                        <td>{{ $day['quantities'][$product->id] ?? '' }}</td>
                    @endforeach
                    <td>{{ $reports['Rejected']['product_totals'][$product->id] ?? 0 }}</td>
                </tr>
            @endforeach
            <tr>
                <td><strong>Total</strong></td>
                @foreach($reports['Rejected']['days'] as $day)
                    <td><strong>{{ $day['total'] }}</strong></td>
                @endforeach
                <td><strong>{{ $reports['Rejected']['total_sum'] }}</strong></td>
            </tr>
        </tbody>
    </table>

</body>
</html>
