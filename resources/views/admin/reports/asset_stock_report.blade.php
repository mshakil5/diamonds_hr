@extends('admin.layouts.admin')
@section('content')

<section class="content pt-3">
    <div class="container-fluid">
        <form method="GET" action="{{ route('assetStockReport') }}">
            <div class="row">
                <div class="col-sm-2">
                    <label>From Date</label>
                    <input type="date" name="from_date" class="form-control" value="{{ $request->from_date }}">
                </div>
                <div class="col-sm-2">
                    <label>To Date</label>
                    <input type="date" name="to_date" class="form-control" value="{{ $request->to_date }}">
                </div>
                <div class="col-sm-2">
                    <label>Asset Type</label>
                    <select name="asset_type_id" class="form-control">
                        <option value="">All</option>
                        @foreach($assetTypes as $type)
                            <option value="{{ $type->id }}" {{ $request->asset_type_id == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-2">
                    <label>Status</label>
                    <select name="status" class="form-control">
                        <option value="">All</option>
                        @foreach($statuses as $key => $label)
                            <option value="{{ $key }}" {{ $request->status == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-2">
                    <label>Branch</label>
                    <select name="branch_id" class="form-control">
                        <option value="">All</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ $request->branch_id == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-2 mt-4">
                    <button type="submit" class="btn btn-secondary btn-block mt-2"><i class="fa fa-search"></i> Filter</button>
                </div>
            </div>
        </form>
    </div>
</section>

<section class="content mt-3">
    <div class="container-fluid">
        <div class="card card-secondary">
            <div class="card-header"><h3 class="card-title">Asset Stock Report</h3></div>
            <div class="card-body table-responsive">
                <table class="table table-bordered table-hover" id="reportTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Asset Type</th>
                            <th>Product Code</th>
                            <th>Status</th>
                            <th>Branch</th>
                            <th>Floor</th>
                            <th>Room</th>
                            <th>Maintenance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($results as $key => $row)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ \Carbon\Carbon::parse($row->stock->date)->format('d-m-Y') ?? '' }}</td>
                                <td>{{ $row->assetType->name ?? '' }}</td>
                                <td>{{ $row->product_code }}</td>
                                <td>{{ $statuses[$row->asset_status] ?? 'N/A' }}</td>
                                <td>{{ $row->branch->name ?? '' }}</td>
                                <td>{{ $row->location->flooor->name ?? '' }}</td>
                                <td>{{ $row->location->room ?? '' }}</td>
                                <td>{{ $row->maintenance->name ?? '' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
@endsection

@section('script')
<script>
$(function () {
    $('#reportTable').DataTable({
        pageLength: 25,
        responsive: true,
        dom: 'Bfrtip',
        buttons: ['copy', 'csv', 'excel', 'pdf', 'print']
    });
});
</script>
@endsection
