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

<section class="content mt-3" id="addThisFormContainer">
    <div class="container-fluid">
        <div class="row justify-content-md-center">
            <div class="col-md-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title" id="header-title">Dirty Product Report</h3>
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
                        <h3 class="card-title">Dirty Product Report</h3>
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
                                        <th>{{ $weekData['days']['Tuesday']['date']->format('d/m/Y') }}</th>
                                        <th>{{ $weekData['days']['Wednesday']['date']->format('d/m/Y') }}</th>
                                        <th>{{ $weekData['days']['Thursday']['date']->format('d/m/Y') }}</th>
                                        <th>{{ $weekData['first_three_days_total'] }}</th>
                                        <th>{{ $weekData['days']['Friday']['date']->format('d/m/Y') }}</th>
                                        <th>{{ $weekData['days']['Saturday']['date']->format('d/m/Y') }}</th>
                                        <th>{{ $weekData['days']['Sunday']['date']->format('d/m/Y') }}</th>
                                        <th>{{ $weekData['days']['Monday']['date']->format('d/m/Y') }}</th>
                                        <th>{{ $weekData['last_four_days_total'] }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($products as $product)
                                        <tr>
                                            <td>{{ $product->name }}</td>
                                            <td>{{ $weekData['days']['Tuesday']['quantities'][$product->id] ?? '' }}</td>
                                            <td>{{ $weekData['days']['Wednesday']['quantities'][$product->id] ?? '' }}</td>
                                            <td>{{ $weekData['days']['Thursday']['quantities'][$product->id] ?? '' }}</td>
                                            <td>
                                                {{ array_sum([
                                                    ($weekData['days']['Tuesday']['quantities'][$product->id] ?? 0),
                                                    ($weekData['days']['Wednesday']['quantities'][$product->id] ?? 0),
                                                    ($weekData['days']['Thursday']['quantities'][$product->id] ?? 0)
                                                ]) }}
                                            </td>
                                            <td>{{ $weekData['days']['Friday']['quantities'][$product->id] ?? '' }}</td>
                                            <td>{{ $weekData['days']['Saturday']['quantities'][$product->id] ?? '' }}</td>
                                            <td>{{ $weekData['days']['Sunday']['quantities'][$product->id] ?? '' }}</td>
                                            <td>{{ $weekData['days']['Monday']['quantities'][$product->id] ?? '' }}</td>
                                            <td>
                                                {{ array_sum([
                                                    ($weekData['days']['Friday']['quantities'][$product->id] ?? 0),
                                                    ($weekData['days']['Saturday']['quantities'][$product->id] ?? 0),
                                                    ($weekData['days']['Sunday']['quantities'][$product->id] ?? 0),
                                                    ($weekData['days']['Monday']['quantities'][$product->id] ?? 0)
                                                ]) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td><strong>Total</strong></td>
                                        <td><strong>{{ $weekData['days']['Tuesday']['total'] }}</strong></td>
                                        <td><strong>{{ $weekData['days']['Wednesday']['total'] }}</strong></td>
                                        <td><strong>{{ $weekData['days']['Thursday']['total'] }}</strong></td>
                                        <td><strong>{{ $weekData['first_three_days_total'] }}</strong></td>
                                        <td><strong>{{ $weekData['days']['Friday']['total'] }}</strong></td>
                                        <td><strong>{{ $weekData['days']['Saturday']['total'] }}</strong></td>
                                        <td><strong>{{ $weekData['days']['Sunday']['total'] }}</strong></td>
                                        <td><strong>{{ $weekData['days']['Monday']['total'] }}</strong></td>
                                        <td><strong>{{ $weekData['last_four_days_total'] }}</strong></td>
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