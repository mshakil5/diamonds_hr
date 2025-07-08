@extends('admin.layouts.admin')

@section('content')

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

<section class="content" id="newBtnSection">
    <div class="container-fluid">
        <div class="row">
            <div class="col-2">
                {{-- <button type="button" class="btn btn-secondary my-3" id="newBtn">Add new</button> --}}
            </div>
        </div>
    </div>
</section>

<section class="content mt-3" id="addThisFormContainer">
    <div class="container-fluid">
         <div class="row justify-content-md-center">
            <div class="col-md-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title" id="header-title">Dirty product report</h3>
                    </div>
                    <div class="card-body">
                        <div class="errmsg"></div>
                                <!-- Date Range Form -->
                        <div class="no-print">
                            <form method="GET" action="{{ route('dirtyStockReport') }}">
                                <div class="form-group">
                                    <label for="start_date">Start Date:</label>
                                    <input type="date" name="start_date" id="start_date" value="{{ $startDate->format('Y-m-d') }}">
                                    
                                    <label for="end_date">End Date:</label>
                                    <input type="date" name="end_date" id="end_date" value="{{ $endDate->format('Y-m-d') }}">
                                    
                                    <button type="submit">Generate Report</button>
                                    <button type="submit" name="download" value="pdf">Download PDF</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>




 <section class="content" id="contentContainer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">Dirty product report</h3>
                    </div>
                    <div class="card-body">

                        <div class="header">
                            <h1>Dirty Stock Report</h1>
                            <h3>Branch: {{ $branchName }}</h3>
                            <h4>Period: {{ $startDate->format('d/m/Y') }} - {{ $endDate->format('d/m/Y') }}</h4>
                        </div>
                        
                         <!-- Report Table -->
                        @foreach($reportData as $index => $weekData)
                            <h3>Week {{ $index + 1 }} ({{ $weekData['days']['Tuesday']['date']->format('d/m/Y') }} - {{ $weekData['days']['Monday']['date']->format('d/m/Y') }})</h3>
                            <table class="table table-bordered">
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
                                                <td>{{ $dayData['quantities'][$product->id] ?? ''}}</td>
                                                @if($dayName == 'Thursday')
                                                    <td>
                                                        {{ array_sum([
                                                            ($weekData['days']['Tuesday']['quantities'][$product->id] ?? ''),
                                                            ($weekData['days']['Wednesday']['quantities'][$product->id] ?? ''),
                                                            ($weekData['days']['Thursday']['quantities'][$product->id] ?? '')
                                                        ]) }}
                                                    </td>
                                                @endif
                                                @if($dayName == 'Monday')
                                                    <td>
                                                        {{ array_sum([
                                                            ($weekData['days']['Friday']['quantities'][$product->id] ?? ''),
                                                            ($weekData['days']['Saturday']['quantities'][$product->id] ?? ''),
                                                            ($weekData['days']['Sunday']['quantities'][$product->id] ?? ''),
                                                            ($weekData['days']['Monday']['quantities'][$product->id] ?? '')
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
                </div>
            </div>
        </div>
    </div>
</section>   



@endsection

@section('script')





@endsection
