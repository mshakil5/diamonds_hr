@extends('admin.layouts.admin')

@section('content')

<section class="content pt-3" id="searchFormContainer">
    <div class="container-fluid">
        <form method="GET" action="{{ route('faultyProducts') }}">
            <div class="row">
                <div class="col-sm-4">
                    <label>Product Code <span class="text-danger">*</span></label>
                    <input type="text" name="product_code" class="form-control" value="{{ $request->product_code }}" autofocus>
                </div>
                <div class="col-sm-2 mt-4">
                    <button type="submit" class="btn btn-secondary mt-2"><i class="fa fa-search"></i> Filter</button>
                </div>
            </div>
        </form>
    </div>
</section>

@if($results->count())
<section class="content mt-3" id="tableContainer">
    <div class="container-fluid">
        <div class="card card-secondary">
            <div class="card-body table-responsive">
                <table class="table table-bordered table-hover" id="reportTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Asset Type</th>
                            <th>Product Code</th>
                            {{-- <th>Asset No.</th> --}}
                            <th>Status</th>
                            <th>Branch</th>
                            <th>Floor</th>
                            <th>Room</th>
                            <th>Maintenance</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($results as $key => $row)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ \Carbon\Carbon::parse($row->stock->date)->format('d-m-Y') ?? '' }}</td>
                                <td>{{ $row->assetType->name ?? '' }}</td>
                                <td>{{ $row->product_code }}</td>
                                {{-- <td>{{ $row->code ?? '' }}</td> --}}
                                <td>{{ $statuses[$row->asset_status] ?? 'N/A' }}</td>
                                <td>{{ $row->branch->name ?? '' }}</td>
                                <td>{{ $row->location->flooor->name ?? '' }}</td>
                                <td>{{ $row->location->room ?? '' }}</td>
                                <td>{{ $row->maintenance->name ?? '' }}</td>
                                <td>
                                    <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#statusModal{{ $row->id }}">
                                        Update
                                    </button>
                                </td>
                            </tr>
                            
                            <div class="modal fade" id="statusModal{{ $row->id }}" tabindex="-1" role="dialog" aria-labelledby="statusModalLabel{{ $row->id }}" aria-hidden="true">
                              <div class="modal-dialog" role="document">
                                <form class="status-update-form" data-id="{{ $row->id }}">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $row->id }}">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="statusModalLabel{{ $row->id }}">Update Status - {{ $row->product_code }}</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                              <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label>Status <span class="text-danger">*</span></label>
                                                <select class="form-control asset-status-select" name="asset_status" required>
                                                    {{-- @foreach($statuses as $k => $v)
                                                        <option value="{{ $k }}" {{ $row->asset_status == $k ? 'selected' : '' }}>{{ $v }}</option>
                                                    @endforeach --}}
                                                      <option value="4" selected>Damaged</option>
                                                      <option value="5" selected>Reported</option>
                                                </select>
                                            </div>
                                            
                                            {{-- <div class="branch-fields" style="display: {{ in_array($row->asset_status, [1,2]) ? 'block' : 'none' }};">
                                                <div class="form-group">
                                                    <label>Branch <span class="text-danger">*</span></label>
                                                    <select class="form-control branch-select" name="branch_id">
                                                        <option value="">Select Branch</option>
                                                        @foreach($branches as $branch)
                                                            <option value="{{ $branch->id }}" {{ $row->branch_id == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                
                                                <div class="form-group">
                                                    <label>Floor <span class="text-danger">*</span></label>
                                                    <select class="form-control floor-select" name="floor_id">
                                                        <option value="">Select Floor</option>
                                                        @foreach($floors as $floor)
                                                            <option value="{{ $floor->id }}" {{ $row->floor_id == $floor->id ? 'selected' : '' }}>{{ $floor->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                
                                                <div class="form-group">
                                                    <label>Room</label>
                                                    <select class="form-control location-select" name="location_id">
                                                        <option value="">Select Room</option>
                                                        @if($row->location)
                                                            <option value="{{ $row->location->id }}" selected>{{ $row->location->room }}</option>
                                                        @endif
                                                    </select>
                                                </div>
                                            </div> --}}
                                            
                                            {{-- <div class="maintenance-field" style="display: {{ $row->asset_status == 3 ? 'block' : 'none' }};">
                                                <div class="form-group">
                                                    <label>Maintenance <span class="text-danger">*</span></label>
                                                    <select class="form-control" name="maintenance_id">
                                                        <option value="">Select Maintenance</option>
                                                        @foreach($maintenances as $maintenance)
                                                            <option value="{{ $maintenance->id }}" {{ $row->maintenance_id == $maintenance->id ? 'selected' : '' }}>{{ $maintenance->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div> --}}
                                            
                                            <div class="form-group">
                                                <label>Note</label>
                                                <textarea class="form-control" name="note" rows="3" placeholder="Optional note...">{{ $row->note }}</textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-success">Update</button>
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                        </div>
                                    </div>
                                </form>
                              </div>
                            </div>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
@endif

<section class="content mt-3" id="tableContainer">
    <div class="container-fluid">
        <div class="card card-secondary">
            <div class="card-header">
                <h3 class="card-title">Faulty Asset Reports</h3>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-bordered table-striped" id="faultyReportsTable">
                    <thead class="">
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Asset Type</th>
                            <th>Asset No</th>
                            <th>Status</th>
                            <th>Branch</th>
                            <th>Room</th>
                            <th>Maintenance</th>
                            <th>Note</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reports as $key => $report)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ \Carbon\Carbon::parse($report->date)->format('d-m-Y') }}</td>
                            <td>{{ $report->assetType->name ?? '' }}</td>
                            <td>{{ $report->stockAssetType->code ?? '' }}</td>
                            <td>
                                @php 
                                  $statuses = [
                                    1=>'Assigned', 
                                    2=>'In Storage', 
                                    3=>'Under Repair', 
                                    4=>'Damaged', 
                                    5=>'Reported']; 
                                @endphp
                                <span class="badge badge-info">{{ $statuses[$report->status] ?? 'N/A' }}</span>
                            </td>
                            <td>{{ $report->branch->name ?? '-' }}</td>
                            <td>{{ $report->location->room ?? '-' }}</td>
                            <td>{{ $report->maintenance->name ?? '-' }}</td>
                            <td>{{ $report->note }}</td>
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
$(document).ready(function () {
    $('#reportTable').DataTable({
        pageLength: 10,
        responsive: true,
        lengthChange: false,
        buttons: ["copy", "csv", "excel", "pdf", "print"]
    }).buttons().container().appendTo('#reportTable_wrapper .col-md-6:eq(0)');

    $('#faultyReportsTable').DataTable({
        responsive: true,
        pageLength: 10,
        lengthChange: false,
        autoWidth: false,
        ordering: true,
        buttons: ['copy', 'csv', 'excel', 'pdf', 'print']
    }).buttons().container().appendTo('#faultyReportsTable_wrapper .col-md-6:eq(0)');

    $(document).on('change', '.asset-status-select', function() {
        const selectedStatus = $(this).val();
        const $modal = $(this).closest('.modal-content');
        
        if (selectedStatus == 1 || selectedStatus == 2) {
            $modal.find('.branch-fields').show();
            $modal.find('.maintenance-field').hide();
        } else if (selectedStatus == 3) {
            $modal.find('.branch-fields').hide();
            $modal.find('.maintenance-field').show();
        } else {
            $modal.find('.branch-fields').hide();
            $modal.find('.maintenance-field').hide();
        }
    });

    $(document).on('change', '.branch-select, .floor-select', function() {
        const $form = $(this).closest('form');
        const $modal = $(this).closest('.modal');
        const branchId = $modal.find('.branch-select').val();
        const floorId = $modal.find('.floor-select').val();
        const $locationSelect = $modal.find('.location-select');
        
        $locationSelect.empty().append('<option value="">Select Room</option>');
        
        if (branchId && floorId) {
            $.get('/admin/get-locations/' + branchId + '/' + floorId, function(locations) {
                if (locations.length > 0) {
                    locations.forEach(loc => {
                        $locationSelect.append(`<option value="${loc.id}">${loc.room}</option>`);
                    });
                }
            });
        }
    });

    $(document).on('submit', '.status-update-form', function(e) {
        e.preventDefault();
        const form = $(this);
        const assetId = form.data('id');
        
        $.ajax({
            url: '/admin/update-faulty-status',
            method: 'POST',
            data: form.serialize(),
            success: function(response) {
                if (response.status === 200) {
                    toastr.success(response.message);
                    $('#statusModal' + assetId).modal('hide');
                    setTimeout(function() {
                        window.location.href = "{{ route('faultyProducts') }}";
                    }, 1000);
                } else {
                    toastr.error('Failed to update status');
                }
            },
            error: function(xhr) {
                toastr.error('An error occurred. Please try again.');
                console.error(xhr.responseText);
            }
        });
    });
});
</script>
@endsection