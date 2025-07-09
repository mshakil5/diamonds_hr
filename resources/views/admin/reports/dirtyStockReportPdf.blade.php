
<!DOCTYPE html>
<html>
<head>
    <title>Dirty Stock Report</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid black; padding: 8px; text-align: center; }
        th { background-color: #f2f2f2; }
        .header { text-align: center; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Dirty Stock Report</h1>
        <h3>Branch: {{ $branchName }}</h3>
        <h4>Period: {{ $startDate->format('d/m/Y') }} - {{ $endDate->format('d/m/Y') }}</h4>
    </div>

    <table>
        <thead>
            <tr>
                <th>Product</th>
                @foreach($reportData['days'] as $date => $dayData)
                    <th>{{ $dayData['date']->format('d/m/Y') }}</th>
                @endforeach
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
                <tr>
                    <td>{{ $product->name }}</td>
                    @foreach($reportData['days'] as $date => $dayData)
                        <td>{{ $dayData['quantities'][$product->id] ?? '' }}</td>
                    @endforeach
                    <td>{{ $reportData['product_totals'][$product->id] ?? 0 }}</td>
                </tr>
            @endforeach
            <tr>
                <td><strong>Total</strong></td>
                @foreach($reportData['days'] as $date => $dayData)
                    <td><strong>{{ $dayData['total'] }}</strong></td>
                @endforeach
                <td><strong>{{ $reportData['total_sum'] }}</strong></td>
            </tr>
        </tbody>
    </table>
</body>
</html>
