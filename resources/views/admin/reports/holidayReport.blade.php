@extends('admin.layouts.admin')

@section('content')

<style>
.card {
    border-radius: 8px;
}
.card-header {
    border-radius: 8px 8px 0 0;
}
.info-box {
    border-radius: 6px;
    border: 1px solid #ddd;
}
.badge {
    padding: 6px 12px;
    font-size: 0.9em;
}


</style>


<section class="content-header no-print">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Employee Holiday Report</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="">Home</a></li>
                    <li class="breadcrumb-item active">Holiday Report</li>
                </ol>
            </div>
        </div>
    </div>
</section>

<section class="content no-print">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card card-primary">
                    <div class="card-header bg-gradient-primary">
                        <h3 class="card-title">Search Employee Holiday Records</h3>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-danger d-none errmsg" id="errorMsg"></div>
                        <form action="{{ route('holidayReport.search') }}" method="POST" id="holidaySearchForm">
                            @csrf
                            <div class="row align-items-end">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label for="employee_id">Employee <span class="text-danger">*</span></label>
                                        <select class="form-control select2" id="employee_id" name="employee_id" required>
                                            <option value="">Select Employee</option>
                                            @foreach ($employees as $employee)
                                                <option value="{{ $employee->id }}">{{ $employee->name }}-{{ $employee->branch->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <button type="submit" id="searchBtn" class="btn btn-primary btn-block">
                                            <i class="fas fa-search mr-2"></i> Generate Report
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

@if ($employeeName)
<section class="content mt-4" id="reportContainer" style="background-color: #ffffff;">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-primary">
                    <div class="card-header bg-gradient-primary no-print">
                        <h3 class="card-title">Employee Holiday Report</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-sm btn-light" onclick="window.print()" title="Print Report">
                                <i class="fas fa-print"></i> Print
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table mb-4" style="border: none;">
                            <tr>
                                <th colspan="2" class="bg-light" style="border: none;">Employee Details
                                    <br>

                                    <strong>ID: {{ $employeeName->id ?? 'N/A' }}</strong>
                                    <br>
                                    <strong>Name: {{ $employeeName->name ?? 'N/A' }}</strong>
                                </th>
                                <th colspan="2" class="bg-light text-right" style="border: none;">
                                    <span class="text-primary h5">{{$employeeName->branch->name}}</span><br>
                                    <span>Human Resources System<br>Detailed Employee Holiday Report</span><br>
                                    <span>Date: <strong>{{ date('d/m/Y') }}</strong></span>
                                </th>
                            </tr>
                        </table>

                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h4>Summary</h4>
                                <div class="row">
                                    <div class="col-sm-3">
                                        <div class="info-box bg-light">
                                            <span class="info-box-icon bg-success"><i class="fas fa-umbrella-beach"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Holiday Entitlement</span>
                                                <span class="info-box-number">{{ $employeeName->entitled_holiday }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="info-box bg-light">
                                            <span class="info-box-icon bg-primary"><i class="fas fa-hand-holding-medical"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Taken</span>
                                                <span class="info-box-number">{{ $counts['taken'] ?? 0 }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="info-box bg-light">
                                            <span class="info-box-icon bg-primary"><i class="fas fa-calendar-check"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Booked</span>
                                                <span class="info-box-number">{{ $counts['booked'] ?? 0 }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="info-box bg-light">
                                            <span class="info-box-icon bg-warning"><i class="fas fa-hourglass-half"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Remaining</span>
                                                <span class="info-box-number">{{ $employeeName->entitled_holiday - (($counts['booked'] ?? 0) + ($counts['not_taken'] ?? 0) + ($counts['taken'] ?? 0)) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <h4>Holiday Records</h4>
                        <table class="table table-bordered table-hover">
                            <thead class="bg-light">
                                <tr>
                                    <th> Date</th>
                                    <th>Type</th>
                                    <th>Duration (Days)</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                               @foreach ($holidayData as $key => $item)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($item->date)->format('d-m-Y') }}</td>
                                        <td>{{ $item->holiday->type ?? '' }}</td>
                                        <td>1 day</td>
                                        <td><span class="badge badge-info">{{ $item->holiday_status_label }}</span></td>
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
$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Initialize Select2
    $('.select2').select2({
        width: '100%'
    });

    // Initialize DataTable
    $('#holidayTable').DataTable({
        responsive: true,
        lengthChange: true,
        autoWidth: false,
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
             '<"row"<"col-sm-12"tr>>' +
             '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
        buttons: [
            { extend: 'copy', className: 'btn btn-sm btn-outline-primary' },
            { extend: 'csv', className: 'btn btn-sm btn-outline-primary' },
            { extend: 'excel', className: 'btn btn-sm btn-outline-primary' },
            { extend: 'pdf', className: 'btn btn-sm btn-outline-primary' },
            { extend: 'print', className: 'btn btn-sm btn-outline-primary' }
        ],
        pageLength: 10,
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search records..."
        }
    }).buttons().container().appendTo('#holidayTable_wrapper .col-md-6:eq(0)');

    // Form validation
    $('#holidaySearchForm').on('submit', function(e) {
        if (!$('#employee_id').val()) {
            e.preventDefault();
            $('#errorMsg').text('Please select an employee.').removeClass('d-none');
            setTimeout(() => $('#errorMsg').addClass('d-none'), 3000);
        }
    });
});
</script>


@endsection