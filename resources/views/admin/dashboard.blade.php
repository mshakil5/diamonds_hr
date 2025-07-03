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
                                                            if ($data->clock_in && $data->clock_out) {
                                                                $in = \Carbon\Carbon::parse($data->clock_in);
                                                                $out = \Carbon\Carbon::parse($data->clock_out);
                                                                $diff = $in->diff($out);
                                                                
                                                            } else {
                                                                
                                                            }
                                                        @endphp
                                                        <tr>
                                                            <td>{{ \Carbon\Carbon::parse($data->created_at)->format('d-m-Y') }}</td>
                                                            <td>{{ $data->type }}</td>
                                                            <td>{{ $data->clock_in }}</td>
                                                            <td>{{ $data->clock_out }}</td>
                                                            <td></td>
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
     @endif
@endsection

@section('script')


<script>
    
</script>

@endsection
