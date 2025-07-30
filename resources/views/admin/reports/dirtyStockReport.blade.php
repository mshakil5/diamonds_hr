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
                        <h3 class="card-title" id="header-title">Dirty & Rejected Product Report</h3>
                    </div>
                    <div class="card-body">
                        <div class="no-print">
                            <form method="GET" action="{{ route('dirtyStockReport') }}">
                                <div class="form-group">
                                    <label for="start_date">Start Date:</label>
                                    <input type="date" name="start_date" id="start_date" value="{{ $startDate->format('Y-m-d') }}">

                                    <label for="end_date">End Date:</label>
                                    <input type="date" name="end_date" id="end_date" value="{{ $endDate->format('Y-m-d') }}">

                                    <button type="submit" class="btn btn-secondary btn-sm">Generate Report</button>
                                    <button type="submit" name="download" class="btn btn-secondary btn-sm" value="pdf">Download PDF</button>
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

                        <table class="table table-bordered">
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
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-5">
            <div class="col-12">
                <div class="card card-danger">
                    <div class="card-header">
                        <h3 class="card-title">Rejected Product Report</h3>
                    </div>
                    <div class="card-body">
                        <div class="header">
                            <h1>Rejected Stock Report</h1>
                            <h3>Branch: {{ $branchName }}</h3>
                            <h4>Period: {{ $startDate->format('d/m/Y') }} - {{ $endDate->format('d/m/Y') }}</h4>
                        </div>

                        <table class="table table-bordered">
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
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

@endsection
