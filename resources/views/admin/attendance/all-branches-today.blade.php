@extends('admin.layouts.admin')

@section('content')

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-calendar-day mr-2"></i>
                            Today's Attendance - All Branches
                            <span class="badge bg-info ml-2">{{ Carbon::today()->format('d-m-Y') }}</span>
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-sm btn-default" onclick="location.reload()">
                                <i class="fas fa-sync-alt mr-1"></i> Refresh
                            </button>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        @if($groupedByBranch->isEmpty())
                            <div class="text-center py-5">
                                <i class="fas fa-inbox fa-3x text-gray"></i>
                                <h4 class="mt-3 text-gray">No attendance records for today</h4>
                            </div>
                        @else
                            <div class="row">
                                @foreach ($groupedByBranch as $branchName => $branchData)
                                    <div class="col-md-6">
                                        <div class="card card-outline card-primary mb-3">
                                            <div class="card-header">
                                                <h3 class="card-title text-sm">
                                                    <i class="fas fa-building mr-1"></i>
                                                    {{ $branchName }}
                                                </h3>
                                                <span class="badge bg-primary">{{ $branchData->count() }}</span>
                                            </div>
                                            <div class="card-body p-0">
                                                <table class="table table-bordered table-sm">
                                                    <thead class="thead-light">
                                                        <tr>
                                                            <th>Employee</th>
                                                            <th>Type</th>
                                                            <th>Time In</th>
                                                            <th>Time Out</th>
                                                            <th>Total</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($branchData as $data)
                                                            @php
                                                                $diff = null;
                                                                if ($data->clock_in && $data->clock_out) {
                                                                    $in = \Carbon\Carbon::parse($data->clock_in);
                                                                    $out = \Carbon\Carbon::parse($data->clock_out);
                                                                    $diff = $in->diff($out);
                                                                }
                                                            @endphp
                                                            <tr>
                                                                <td>{{ $data->employee->name ?? '-' }}</td>
                                                                <td>
                                                                    @if($data->type == 'Regular')
                                                                        <span class="badge badge-success badge-sm">{{ $data->type }}</span>
                                                                    @else
                                                                        <span class="badge badge-warning badge-sm">{{ $data->type }}</span>
                                                                    @endif
                                                                </td>
                                                                <td>{{ $data->clock_in ? \Carbon\Carbon::parse($data->clock_in)->format('h:i A') : '-' }}</td>
                                                                <td>{{ $data->clock_out ? \Carbon\Carbon::parse($data->clock_out)->format('h:i A') : '-' }}</td>
                                                                <td>{{ $diff ? $diff->format('%H:%I') : '-' }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection