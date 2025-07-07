<!DOCTYPE html>
<html>
<head>
    <title>Staff Holiday Records</title>
    <style>
        body { font-family: Arial, sans-serif; }
        h1 { text-align: center; }
        h3 { margin-top: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Staff Holiday Records</h1>
    @forelse($holidays as $staffId => $prorotas)
        @php $staff = \App\Models\User::find($staffId); @endphp
        <h3>Employee: {{ $staff ? $staff->name : 'Unknown' }}</h3>
        <table>
            <thead>
                <tr>
                    <th>Schedule Type</th>
                    <th>From Date</th>
                    <th>To Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($prorotas as $prorota)
                    @foreach($prorota->prorotaDetail as $detail)
                        @if($detail->from_date && $detail->to_date)
                            <tr>
                                <td>{{ $prorota->schedule_type }}</td>
                                <td>{{ $detail->from_date }}</td>
                                <td>{{ $detail->to_date }}</td>
                            </tr>
                        @endif
                    @endforeach
                @endforeach
            </tbody>
        </table>
    @empty
        <p>No holiday records found for Authorized Holiday or Unauthorized Holiday.</p>
    @endforelse
</body>
</html>