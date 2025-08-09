@extends('admin.layouts.admin')

@section('content')
    @if (auth()->user()->canDo(1))
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Dashboard</h1>
          </div><!-- /.col -->
          <div class="col-sm-6 d-none">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Dashboard</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>

    <section class="content">
      <div class="container-fluid">
        <!-- Small boxes (Stat box) -->
        <div class="row">
          <div class="col-lg-3 col-md-4 col-6 mb-3">
            <div class="small-box bg-info rounded shadow-sm">
              <div class="inner">
                <h3 class="text-white font-weight-bold">{{ $monthlyHoliday }}</h3>
                <p class="text-white m-0">This month holiday</p>
              </div>
              <div class="icon">
                <i class="fas fa-blog"></i>
              </div>
            </div>
          </div> 

          <div class="col-lg-3 col-md-4 col-6 mb-3">
            <div class="small-box bg-info rounded shadow-sm">
              <div class="inner">
                <h3 class="text-white font-weight-bold">{{ $todaySick }}</h3>
                <p class="text-white m-0">Today sick</p>
              </div>
              <div class="icon">
                <i class="fas fa-users"></i>
              </div>
            </div>
          </div>

          <div class="col-lg-3 col-md-4 col-6 mb-3">
            <div class="small-box bg-info rounded shadow-sm">
              <div class="inner">
                <h3 class="text-white font-weight-bold">{{ $todayAbsence }}</h3>
                <p class="text-white m-0">Today absence</p>
              </div>
              <div class="icon">
                <i class="fas fa-users"></i>
              </div>
            </div>
          </div>

          <div class="col-lg-3 col-md-4 col-6 mb-3">
            <div class="small-box bg-info rounded shadow-sm">
              <div class="inner">
                <h3 class="text-white font-weight-bold">{{ $totalHours }}</h3>
                <p class="text-white m-0">Total Today Hour</p>
              </div>
              <div class="icon">
                <i class="fas fa-users"></i>
              </div>
            </div>
          </div>

          @php
            $statusLabels = [
                1 => 'Assigned',
                2 => 'In Storage',
                3 => 'Under Repair',
                4 => 'Damaged',
                5 => 'Reported',
            ];

            $statusColors = [
                1 => 'bg-success',
                2 => 'bg-primary',
                3 => 'bg-warning',
                4 => 'bg-danger',
                5 => 'bg-secondary',
            ];
          @endphp

          @foreach ($statusLabels as $key => $label)
            <div class="col-lg-3 col-md-4 col-6 mb-3">
              @php
                $params = ['status' => $key];
                if (!empty($stock?->id)) {
                    $params['stock'] = $stock->id;
                }
              @endphp
              <a href="{{ route('stocks.view.status', $params) }}" class="text-decoration-none">
                <div class="small-box {{ $statusColors[$key] }} rounded shadow-sm">
                  <div class="inner">
                    <h3 class="text-white font-weight-bold">{{ $statusCounts[$key] ?? 0 }}</h3>
                    <p class="text-white m-0">{{ $label }}</p>
                  </div>
                  <div class="icon">
                    <i class="fas fa-box"></i>
                  </div>
                </div>
              </a>
            </div>
          @endforeach

          <!-- ./col -->
          
        </div>
        <!-- /.row (main row) -->
      </div><!-- /.container-fluid -->
    </section>

    <section class="content" id="contentContainer">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">Today Attendane Record</h3>
                        </div>
                        <div class="card-body">
                            <table id="example1" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th style="text-align:center">Name</th>
                                        <th> </th>
                                        <th style="text-align:center">G. Total Time</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach ($todayAttendance as $data)
                                        <tr>
                                            <td>{{ $data->employee->name }}</td>
                                            <td>
                                                <table class="table table-bordered w-100">
                                                    <thead>
                                                        <tr>
                                                            <th>Date</th>
                                                            <th>Type</th>
                                                            <th>Time In</th>
                                                            <th>Time Out</th>
                                                            <th>Late</th>
                                                            <th>Total Time</th>
                                                            <th class="d-none">Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php
                                                          $date = \Carbon\Carbon::parse($data->created_at)->format('Y-m-d');

                                                          $checkPrerota = \App\Models\EmployeePreRota::where('employee_id', $data->employee_id)->where('date', $date)->first();
                                                          if ($data->clock_in && $data->clock_out) {
                                                              $in = \Carbon\Carbon::parse($data->clock_in);
                                                              $out = \Carbon\Carbon::parse($data->clock_out);
                                                              $diff = $in->diff($out);
                                                          }
                                                          $lateTime = null; 
                                                          if ($checkPrerota && $data->clock_in) {
                                                              $scheduledStart = \Carbon\Carbon::parse($checkPrerota->start_time); 
                                                              $actualClockIn = \Carbon\Carbon::parse($data->clock_in);
                                                              if ($actualClockIn->gt($scheduledStart)) {
                                                                  $lateTime = $scheduledStart->diff($actualClockIn);
                                                              } else {
                                                                  $lateTime = null;
                                                              }
                                                          }
                                                          @endphp
                                                          <tr>
                                                              <td>{{ \Carbon\Carbon::parse($data->created_at)->format('d-m-Y') }}</td>
                                                              <td>{{ $data->type }}</td>
                                                              <td>{{ \Carbon\Carbon::parse($data->clock_in)->format('h:i') }}</td>
                                                              <td>{{ \Carbon\Carbon::parse($data->clock_out)->format('h:i') }}</td>
                                                              <td>
                                                                  @if($lateTime)
                                                                      {{ $lateTime->format('%H:%I:%S') }}
                                                                  @else
                                                                      -
                                                                  @endif
                                                              </td>
                                                              @if(isset($diff))
                                                                  <td>{{ $diff->format('%H:%I:%S') }}</td>
                                                              @else
                                                                  <td>-</td>
                                                              @endif
                                                              <td class="d-none">
                                                                  <a id="DetailsBtn"
                                                                    rid="{{$data->id}}"
                                                                    title="Details"
                                                                    data-id="{{ $data->id }}"
                                                                    data-employee="{{ $data->employee->name }}"
                                                                    data-type="{{ $data->type }}"
                                                                    data-clock_in="{{ $data->clock_in }}"
                                                                    data-clock_out="{{ $data->clock_out }}"
                                                                    data-details="{{ $data->details }}"
                                                                    data-date="{{ \Carbon\Carbon::parse($data->created_at)->format('Y-m-d') }}"
                                                                    data-total_time="{{ isset($diff) ? $diff->format('%H:%I:%S') : '-' }}"
                                                                  >
                                                                      <i class="fa fa-info-circle" style="color: #17a2b8; font-size:16px; margin-right:8px;"></i>
                                                                  </a>
                                                                  <a id="EditBtn" rid="{{$data->id}}"><i class="fa fa-edit" style="color: #2196f3;font-size:16px;"></i></a>
                                                                  <a id="deleteBtn" rid="{{$data->id}}"><i class="fa fa-trash-o" style="color: red;font-size:16px;"></i></a>
                                                              </td>
                                                          </tr>
                                                    </tbody>
                                                </table>
                                            </td>
                                            <td> </td>
                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card card-secondary">
              <div class="card-header d-flex justify-content-between align-items-center">
                @php
                  $statusLabel = [1 => 'Assigned', 2 => 'In Storage', 3 => 'Under Repair', 4 => 'Damaged', 5 => 'Reported'];
                @endphp
                <h3 class="card-title">
                  Reported Assets
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
     @endif
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
