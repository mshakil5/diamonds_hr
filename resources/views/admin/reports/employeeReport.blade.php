@extends('admin.layouts.admin')

@section('content')



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
                        <h3 class="card-title" id="header-title">Employee report</h3>
                    </div>
                    <div class="card-body">
                        <div class="errmsg"></div>
                        <form action="{{ route('employeeReport.search')}}" method="POST">
                            @csrf
                            
                            <div class="row">
                                
                                <div class="col-sm-3">
                                <!-- text input -->
                                    <div class="form-group">
                                        <label>Employee <span class="text-danger">*</span></label>
                                        <select class="form-control select2" id="employee_id" name="employee_id">
                                            <option value="">Select Employee</option>
                                            @foreach ($employees as $employee)
                                            <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                                                {{ $employee->name }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>


                                <div class="col-sm-3">
                                <!-- text input -->
                                    <div class="form-group">
                                        <label>From Date</label>
                                        <input type="date" class="form-control" id="from_date" name="from_date" value="{{ request('from_date') }}">
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                <!-- text input -->
                                    <div class="form-group">
                                        <label>To Date</label>
                                        <input type="date" class="form-control" id="to_date" name="to_date" value="{{ request('to_date') }}">
                                    </div>
                                </div>

                                
                                <div class="col-sm-3">
                                <!-- text input -->
                                    <div class="form-group">
                                        <label>Action</label> <br>
                                        <button type="submit" id="searchBtn" class="btn btn-secondary">
                                            <i class="fa fa-search"></i> Search
                                        </button>
                                    </div>
                                </div>
                                
                                
                                

                            </div>
                            
                        </form>
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
                        <h3 class="card-title">Name: {{$employeeName ?? ''}}</h3>
                    </div>
                    <div class="card-body">
                        <table id="example1" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Date</th>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>Late</th>
                                    <th>Details</th>
                                    <th>Total Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $totalSeconds = 0; @endphp
                                @foreach ($data as $key => $data)

                                @php
                                    $date = \Carbon\Carbon::parse($data->created_at)->format('Y-m-d');
                                    $checkPrerota = \App\Models\EmployeePreRota::where('employee_id', $data->employee_id)->where('date', $date)->first();
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
                                    <td>{{$key + 1}}</td>
                                    <td>{{ \Carbon\Carbon::parse($data->clock_in)->format('d/m/Y') }}</td>
                                    <td>{{ $data->employee_name }}</td>
                                    <td>{{ $data->type }}</td>
                                    <td>
                                        @if($lateTime)
                                            {{ $lateTime->format('%H:%I:%S') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ $data->details }}</td>
                                    <td>
                                        @if($data->clock_in && $data->clock_out)
                                            @php
                                                $in = \Carbon\Carbon::parse($data->clock_in);
                                                $out = \Carbon\Carbon::parse($data->clock_out);
                                                $diff = $out->diff($in);
                                                $totalSeconds += $out->diffInSeconds($in);
                                            @endphp

                                            <span style="background-color: #f0f8ff; padding: 6px 12px; border-radius: 20px; display: inline-block; color: #333;">
                                                {{ $diff->h }} Hours {{ $diff->i }} Minutes {{ $diff->s }} Seconds
                                            </span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                                @endforeach

                                @php
                                    $h = floor($totalSeconds / 3600);
                                    $m = floor(($totalSeconds % 3600) / 60);
                                    $s = $totalSeconds % 60;
                                @endphp

                                <tr>
                                    <td colspan="5" class="text-right"><strong>Total Time</strong></td>
                                    <td><strong>{{ $h }}h {{ $m }}m {{ $s }}s</strong></td>
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

@section('script')


<script>
 
    $(document).ready(function() {
        

        
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(function() {
            $("#example1").DataTable({
                "responsive": true,
                "lengthChange": false,
                "autoWidth": false,
                "buttons": ["copy", "csv", "excel", "pdf", "print"]
            }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
            $('#example2').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": false,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
            });
        });


    });
</script>



@endsection