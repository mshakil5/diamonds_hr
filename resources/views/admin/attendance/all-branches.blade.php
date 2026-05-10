
@extends('admin.layouts.admin')

@section('content')

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-building mr-2"></i>
                            All Branches Attendance
                            @if($fromDate == $toDate)
                                <span class="badge bg-info ml-2">
                                    {{ \Carbon\Carbon::parse($fromDate)->format('d-m-Y') }}
                                </span>
                            @else
                                <span class="badge bg-info ml-2">
                                    {{ \Carbon\Carbon::parse($fromDate)->format('d-m-Y') }} to {{ \Carbon\Carbon::parse($toDate)->format('d-m-Y') }}
                                </span>
                            @endif
                        </h3>
                        <div class="card-tools">
                            <a href="{{ route('attendance.index') }}" class="btn btn-sm btn-default">
                                <i class="fas fa-arrow-left mr-1"></i> Back to My Branch
                            </a>
                        </div>
                    </div>
                    
                    <!-- Filter Section -->
                    <div class="card-body">
                        <form action="{{ route('attendance.all-branches.search') }}" method="POST" id="filterForm">
                            @csrf
                            <div class="row">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>Branch</label>
                                        <select class="form-control select2" id="branch_id" name="branch_id">
                                            <option value="">All Branches</option>
                                            @foreach ($branches as $branch)
                                            <option value="{{ $branch->id }}" {{ $branchId == $branch->id ? 'selected' : '' }}>
                                                {{ $branch->name }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label>From Date</label>
                                        <input type="date" class="form-control" id="from_date" name="from_date" value="{{ $fromDate }}"/>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label>To Date</label>
                                        <input type="date" class="form-control" id="to_date" name="to_date" value="{{ $toDate }}"/>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label>&nbsp;</label><br>
                                        <button type="submit" class="btn btn-secondary">
                                            <i class="fas fa-filter mr-1"></i> Filter
                                        </button>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label>&nbsp;</label><br>
                                        <button type="button" class="btn btn-success" id="downloadBtn">
                                            <i class="fas fa-download mr-1"></i> Export CSV
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Summary Cards -->
                    <div class="card-body pb-0">
                        <div class="row">
                            <div class="col-md-3 col-sm-6 col-12">
                                <div class="info-box">
                                    <span class="info-box-icon bg-info"><i class="fas fa-building"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Total Branches</span>
                                        <span class="info-box-number">{{ $groupedByBranch->count() }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 col-12">
                                <div class="info-box">
                                    <span class="info-box-icon bg-success"><i class="fas fa-users"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Total Employees</span>
                                        <span class="info-box-number">{{ $groupedByBranch->pluck('employee_id')->unique()->count() }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 col-12">
                                <div class="info-box">
                                    <span class="info-box-icon bg-warning"><i class="fas fa-clock"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Total Records</span>
                                        <span class="info-box-number">{{ $groupedByBranch->flatten()->count() }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 col-12">
                                <div class="info-box">
                                    <span class="info-box-icon bg-danger"><i class="fas fa-user-times"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Absence</span>
                                        <span class="info-box-number">{{ $groupedByBranch->flatten()->where('type', 'Absence')->count() }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Attendance Tables by Branch -->
                    <div class="card-body">
                        @if($groupedByBranch->isEmpty())
                            <div class="text-center py-5">
                                <i class="fas fa-inbox fa-3x text-gray"></i>
                                <h4 class="mt-3 text-gray">No attendance records found</h4>
                            </div>
                        @else
                            @foreach ($groupedByBranch as $branchName => $branchData)
                                <div class="card card-outline card-{{ $loop->index % 2 == 0 ? 'primary' : 'success' }} mb-4">
                                    <div class="card-header">
                                        <h3 class="card-title">
                                            <i class="fas fa-building mr-2"></i>
                                            {{ $branchName }}
                                        </h3>
                                        <div class="card-tools">
                                            <span class="badge bg-{{ $loop->index % 2 == 0 ? 'primary' : 'success' }}">
                                                {{ $branchData->count() }} Records
                                            </span>
                                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body p-0">
                                        <table class="table table-bordered table-striped">
                                            <thead class="thead-dark">
                                                <tr>
                                                    <th style="text-align:center">Name</th>
                                                    <th colspan="6"></th>
                                                    <th style="text-align:center">G. Total Time</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $employeeData = $branchData->groupBy('employee_id');
                                                @endphp
                                                @foreach ($employeeData as $employeeId => $records)
                                                    @php
                                                        $grandTotalSeconds = 0;
                                                        foreach ($records as $record) {
                                                            if ($record->clock_in && $record->clock_out) {
                                                                $in = \Carbon\Carbon::parse($record->clock_in);
                                                                $out = \Carbon\Carbon::parse($record->clock_out);
                                                                $grandTotalSeconds += $out->diffInSeconds($in);
                                                            }
                                                        }
                                                        $grandTotalHours = intdiv($grandTotalSeconds, 3600);
                                                        $grandTotalMinutes = intdiv($grandTotalSeconds % 3600, 60);
                                                        $grandTotalSecs = $grandTotalSeconds % 60;
                                                        $grandTotalFormatted = sprintf('%02d:%02d:%02d', $grandTotalHours, $grandTotalMinutes, $grandTotalSecs);
                                                    @endphp
                                                    <tr>
                                                        <td style="text-align:center; font-weight: 600;">
                                                            {{ $records->first()->employee->name ?? 'Unknown' }}
                                                        </td>
                                                        <td colspan="6" style="padding: 0;">
                                                            <table class="table table-bordered w-100 mb-0">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Date</th>
                                                                        <th>Type</th>
                                                                        <th>Time In</th>
                                                                        <th>Time Out</th>
                                                                        <th>Late</th>
                                                                        <th>Early Leave</th>
                                                                        <th>Total Time</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach ($records as $data)
                                                                        @php
                                                                            $diff = null;
                                                                            if ($data->clock_in && $data->clock_out) {
                                                                                $in = \Carbon\Carbon::parse($data->clock_in);
                                                                                $out = \Carbon\Carbon::parse($data->clock_out);
                                                                                $diff = $in->diff($out);
                                                                            }
                                                                        @endphp
                                                                        <tr>
                                                                            <td>{{ \Carbon\Carbon::parse($data->clock_in)->format('d/m/Y') }}</td>
                                                                            <td>
                                                                                @if($data->type == 'Regular')
                                                                                    <span class="badge badge-success">{{ $data->type }}</span>
                                                                                @elseif($data->type == 'Sick')
                                                                                    <span class="badge badge-warning">{{ $data->type }}</span>
                                                                                @else
                                                                                    <span class="badge badge-danger">{{ $data->type }}</span>
                                                                                @endif
                                                                            </td>
                                                                            <td>{{ \Carbon\Carbon::parse($data->clock_in)->format('H:i:s') }}</td>
                                                                            <td>{{ $data->clock_out ? \Carbon\Carbon::parse($data->clock_out)->format('H:i:s') : '-' }}</td>
                                                                            <td>{{ $data->late ?? '-' }}</td>
                                                                            <td>{{ $data->early_leave ?? '-' }}</td>
                                                                            <td>{{ $diff ? $diff->format('%H:%I:%S') : '-' }}</td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </td>
                                                        <td style="text-align:center; font-weight: 600; background-color: #e9ecef;">
                                                            {{ $grandTotalFormatted }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endforeach
                        @endif
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
    
    // Initialize Select2
    $('.select2').select2();
    
    // Download CSV
    $("#downloadBtn").click(function(e) {
        e.preventDefault();
        
        var fromDate = $("#from_date").val();
        var toDate = $("#to_date").val();
        var branchId = $("#branch_id").val();
        
        var url = "{{ URL::to('/admin/attendance/all-branches-export') }}";
        url += '?from_date=' + (fromDate || '');
        url += '&to_date=' + (toDate || '');
        url += '&branch_id=' + (branchId || '');
        
        window.location.href = url;
    });
    
    // Clear filter
    $("#clearFilter").click(function(e) {
        e.preventDefault();
        $("#branch_id").val('').trigger('change');
        $("#from_date").val('');
        $("#to_date").val('');
        $("#filterForm").submit();
    });
});
</script>

@endsection