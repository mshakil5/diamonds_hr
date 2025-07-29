@extends('admin.layouts.admin')

@section('content')
<section class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">
        <a href="{{ url()->previous() }}" class="btn btn-secondary my-2">← Back</a>
        <div class="card card-secondary">
          <div class="card-header d-flex justify-content-between align-items-center">
            @php
              $statusLabel = [1 => 'Assigned', 2 => 'In Storage', 3 => 'Under Repair', 4 => 'Damaged', 5 => 'Reported'];
            @endphp
            <h3 class="card-title">
              {{ $stock->assetType->name ?? 'N/A' }} - Status: {{ $statusLabel[$status] ?? 'Unknown' }}
            </h3>
          </div>

          <div class="card-body">
            <table id="statusTable" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>SL</th>
                  <th>Product Code</th>
                  {{-- <th>Asset No.</th> --}}
                  @if(in_array($status, [1, 2]))
                    <th>Branch</th>
                    <th>Floor</th>
                    <th>Room</th>
                  @endif
                  @if($status == 3)
                    <th>Maintenance</th>
                  @endif
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                @foreach($assets as $key => $item)
                  <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $item->product_code ?? '' }}</td>
                    {{-- <td>{{ $item->code ?? '' }}</td> --}}
                    @if(in_array($status, [1, 2]))
                      <td>{{ $item->branch->name ?? 'N/A' }}</td>
                      <td>{{ $item->location->flooor->name ?? 'N/A' }}</td>
                      <td>{{ $item->location->room ?? 'N/A' }}</td>
                    @endif
                    @if($status == 3)
                      <td>{{ $item->maintenance->name ?? 'N/A' }}</td>
                    @endif
                     <td>
                        <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#statusModal{{ $item->id }}">
                            Update
                        </button>
                    </td>
                  </tr>
                  <div class="modal fade" id="statusModal{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="statusModalLabel{{ $item->id }}" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                      <form class="status-update-form" data-id="{{ $item->id }}">
                          @csrf
                          <input type="hidden" name="id" value="{{ $item->id }}">
                          <div class="modal-content">
                              <div class="modal-header">
                                  <h5 class="modal-title" id="statusModalLabel{{ $item->id }}">Update Status - {{ $item->product_code }}</h5>
                                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                  </button>
                              </div>
                              <div class="modal-body">
                                  <div class="form-group">
                                      <label>Status <span class="text-danger">*</span></label>
                                      <select class="form-control asset-status-select" name="asset_status" required>
                                          @foreach($statuses as $k => $v)
                                              <option value="{{ $k }}" {{ $item->asset_status == $k ? 'selected' : '' }}>{{ $v }}</option>
                                          @endforeach
                                      </select>
                                  </div>
                                  
                                  <div class="branch-fields" style="display: {{ in_array($item->asset_status, [1,2]) ? 'block' : 'none' }};">
                                      <div class="form-group">
                                          <label>Branch <span class="text-danger">*</span></label>
                                          <select class="form-control branch-select" name="branch_id">
                                              <option value="">Select Branch</option>
                                              @foreach($branches as $branch)
                                                  <option value="{{ $branch->id }}" {{ $item->branch_id == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                                              @endforeach
                                          </select>
                                      </div>
                                      
                                      <div class="form-group">
                                          <label>Floor <span class="text-danger">*</span></label>
                                          <select class="form-control floor-select" name="floor_id">
                                              <option value="">Select Floor</option>
                                              @foreach($floors as $floor)
                                                  <option value="{{ $floor->id }}" {{ $item->floor_id == $floor->id ? 'selected' : '' }}>{{ $floor->name }}</option>
                                              @endforeach
                                          </select>
                                      </div>
                                      
                                      <div class="form-group">
                                          <label>Room</label>
                                          <select class="form-control location-select" name="location_id">
                                              <option value="">Select Room</option>
                                              @if($item->location)
                                                  <option value="{{ $item->location->id }}" selected>{{ $item->location->room }}</option>
                                              @endif
                                          </select>
                                      </div>
                                  </div>
                                  
                                  <div class="maintenance-field" style="display: {{ $item->asset_status == 3 ? 'block' : 'none' }};">
                                      <div class="form-group">
                                          <label>Maintenance <span class="text-danger">*</span></label>
                                          <select class="form-control" name="maintenance_id">
                                              <option value="">Select Maintenance</option>
                                              @foreach($maintenances as $maintenance)
                                                  <option value="{{ $maintenance->id }}" {{ $item->maintenance_id == $maintenance->id ? 'selected' : '' }}>{{ $maintenance->name }}</option>
                                              @endforeach
                                          </select>
                                      </div>
                                  </div>
                                  
                                  <div class="form-group">
                                      <label>Note</label>
                                      <textarea class="form-control" name="note" rows="3" placeholder="Optional note...">{{ $item->note }}</textarea>
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
    </div>
  </div>
</section>
@endsection

@section('script')
<script>
  $(document).ready(function () {
    $('#statusTable').DataTable({
      responsive: true,
      autoWidth: false,
      lengthChange: false,
      buttons: ["copy", "csv", "excel", "pdf", "print"]
    }).buttons().container().appendTo('#statusTable_wrapper .col-md-6:eq(0)');

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
                        location.reload();
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