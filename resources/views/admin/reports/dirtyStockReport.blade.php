
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
                                <div classform-group">
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
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    @foreach($reportData['days'] as $date => $dayData)
                                        <th>{{ $dayData['date']->format('d/m/Y') }}</th>
                                    @endforeach
                                    <th>Sum</th>
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
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
