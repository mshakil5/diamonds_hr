@extends('admin.layouts.admin')

@section('content')

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tempusdominus-bootstrap-4@5.39.0/build/css/tempusdominus-bootstrap-4.min.css" />
@if (auth()->user()->canDo(14))
<section class="content" id="newBtnSection">
    <div class="container-fluid">
        <div class="row">
            <div class="col-2">
                <button type="button" class="btn btn-secondary my-3" id="newBtn">Add new</button>
            </div>
        </div>
    </div>
</section>
@endif

<section class="content mt-3" id="addThisFormContainer">
    <div class="container-fluid">
         <div class="row justify-content-md-center">
            <div class="col-md-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title" id="header-title">Add new Holiday</h3>
                    </div>
                    <div class="card-body">
                        <div class="errmsg"></div>
                        <form id="createThisForm">
                            @csrf
                            <input type="hidden" class="form-control" id="codeid" name="codeid">
                            
                            <div class="row">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>Employee <span class="text-danger">*</span></label>
                                        <select class="form-control select2" id="employee_id" name="employee_id">
                                            <option value="">Select Employee</option>
                                            @foreach ($employees as $employee)
                                            <option value="{{$employee->id}}">{{$employee->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>Type <span class="text-danger">*</span></label>
                                        <select class="form-control" id="type" name="type">
                                            <option value="">Select Type</option>
                                            <option value="Regular">Regular</option>
                                            <option value="Sick">Sick</option>
                                            <option value="Absence">Absence</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>Clock In Date <span class="text-danger">*</span></label>
                                        <div class="input-group date" id="clockInDate" data-target-input="nearest">
                                            <input type="text" class="form-control datetimepicker-input" data-target="#clockInDate" id="clock_in_date" name="clock_in_date" />
                                            <div class="input-group-append" data-target="#clockInDate" data-toggle="datetimepicker">
                                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>Clock In Time <span class="text-danger">*</span></label>
                                        <div class="input-group date" id="clockInTime" data-target-input="nearest">
                                            <input type="text" class="form-control datetimepicker-input" data-target="#clockInTime" id="clock_in_time" name="clock_in_time" />
                                            <div class="input-group-append" data-target="#clockInTime" data-toggle="datetimepicker">
                                                <div class="input-group-text"><i class="fa fa-clock"></i></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>Clock Out Date</label>
                                        <div class="input-group date" id="clockOutDate" data-target-input="nearest">
                                            <input type="text" class="form-control datetimepicker-input" data-target="#clockOutDate" id="clock_out_date" name="clock_out_date" />
                                            <div class="input-group-append" data-target="#clockOutDate" data-toggle="datetimepicker">
                                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>Clock Out Time</label>
                                        <div class="input-group date" id="clockOutTime" data-target-input="nearest">
                                            <input type="text" class="form-control datetimepicker-input" data-target="#clockOutTime" id="clock_out_time" name="clock_out_time" />
                                            <div class="input-group-append" data-target="#clockOutTime" data-toggle="datetimepicker">
                                                <div class="input-group-text"><i class="fa fa-clock"></i></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label>Details</label>
                                        <textarea class="form-control" name="details" id="details" cols="30" rows="2"></textarea>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer">
                        <button type="submit" id="addBtn" class="btn btn-secondary" value="Create">Create</button>
                        <button type="submit" id="FormCloseBtn" class="btn btn-default">Cancel</button>
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
                        <h3 class="card-title">All Attendance Record</h3>
                    </div>
                    <div class="card-body">

                        <form action="{{route('attendance.search')}}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>From Date</label>
                                            <input type="date" class="form-control" id="from_date" name="from_date" value="{{$fromDate}}" required/>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>To Date</label>
                                        <input type="date" class="form-control" id="to_date" name="to_date" value="{{$toDate}}" required/>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>Action</label> <br>
                                        <button type="submit" class="btn btn-secondary" >Filter</button>
                                        <button type="button" class="btn btn-secondary" id="downloadBtn">Download CSV</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                        
                    </div>



                        <table id="example1" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th style="display: none" class="d-none">SL</th>
                                    <th>Name</th>
                                    <th>Branch</th>
                                    <th> </th>
                                    <th>G. Total Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $data)
                                    <tr>
                                        <td style="display: none" class="d-none">{{ $data->id }}</td>
                                        <td>{{ $data->employee->name }}</td>
                                        <td>{{ $data->branch->name ?? '' }}</td>
                                        <td>
                                            <table class="table table-bordered w-100">
                                                <thead>
                                                    <tr>
                                                        <th>Date</th>
                                                        <th>Type</th>
                                                        <th>Time In</th>
                                                        <th>Time Out</th>
                                                        <th>Late</th>
                                                        <th>Early Leave</th>
                                                        <th>Total Time</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php
                                                        $diff = '';
                                                        if ($data->clock_in && $data->clock_out) {
                                                            $in = \Carbon\Carbon::parse($data->clock_in);
                                                            $out = \Carbon\Carbon::parse($data->clock_out);
                                                            $diff = $in->diff($out);
                                                        }
                                                    @endphp
                                                    <tr>
                                                        <td>{{ \Carbon\Carbon::parse($data->clock_in)->format('d/m/Y') }}</td>
                                                        <td>{{ $data->type }}</td>
                                                        <td>{{ \Carbon\Carbon::parse($data->clock_in)->format('H:i:s') }}</td>
                                                        <td>{{ $data->clock_out ? \Carbon\Carbon::parse($data->clock_out)->format('H:i:s') : Null }}</td>
                                                        <td>{{ $data->late }}</td>
                                                        <td>{{ $data->early_leave }}</td>
                                                        <td>{{ $diff ? $diff->format('%H:%I:%S') : '-' }} </td>
                                                        <td>
                                                            <a id="DetailsBtn"
                                                                rid="{{$data->id}}"
                                                                title="Details"
                                                                data-id="{{ $data->id }}"
                                                                data-employee="{{ $data->employee->name }}"
                                                                data-type="{{ $data->type }}"
                                                                data-clock_in_date="{{ \Carbon\Carbon::parse($data->clock_in)->format('Y-m-d') }}"
                                                                data-clock_in_time="{{ \Carbon\Carbon::parse($data->clock_in)->format('H:i:s') }}"
                                                                data-clock_out_date="{{ \Carbon\Carbon::parse($data->clock_out)->format('Y-m-d') }}"
                                                                data-clock_out_time="{{ \Carbon\Carbon::parse($data->clock_out)->format('H:i:s') }}"
                                                                data-details="{{ $data->details }}"
                                                                data-date="{{ \Carbon\Carbon::parse($data->created_at)->format('Y-m-d') }}"
                                                                data-total_time="{{ $diff ? $diff->format('%H:%I:%S') : '-' }}"
                                                            >
                                                                <i class="fa fa-info-circle" style="color: #17a2b8; font-size:16px; margin-right:8px;"></i>
                                                            </a>
                                                            @if (auth()->user()->canDo(15))
                                                            <a id="EditBtn" rid="{{$data->id}}"><i class="fa fa-edit" style="color: #2196f3;font-size:16px;"></i></a>
                                                            @endif
                                                            @if (auth()->user()->canDo(16))
                                                            <a id="deleteBtn" rid="{{$data->id}}"><i class="fa fa-trash-o" style="color: red;font-size:16px;"></i></a>
                                                            @endif
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

@endsection

@section('script')

<script>
$(document).ready(function() {
    $("#addThisFormContainer").hide();
    $("#newBtn").click(function() {
        clearform();
        $("#newBtn").hide(100);
        $("#addThisFormContainer").show(300);
    });
    $("#FormCloseBtn").click(function() {
        $("#addThisFormContainer").hide(200);
        $("#newBtn").show(100);
        clearform();
    });
    
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    var url = "{{URL::to('/admin/attendance')}}";
    var upurl = "{{URL::to('/admin/attendance/update')}}";


    
    // Function to reset field styles
    function resetFieldStyles(fields) {
        fields.forEach(function(field) {
            $(field).css('border-color', '');
            $(field).removeClass('is-invalid');
        });
    }

    // Function to display error message
    function showError(message, field = null) {
        $('.errmsg').html(`<div class="alert alert-danger">${message}</div>`);
        if (field) {
            $(field).css('border-color', 'red');
            $(field).addClass('is-invalid');
            $(field).focus();
        }
    }

    $("#addBtn").click(function() {
        // Define required fields with user-friendly names
        var requiredFields = [
            {id: '#employee_id', name: 'Employee'},
            {id: '#type', name: 'Type'},
            {id: '#clock_in_date', name: 'Clock In Date'},
            {id: '#clock_in_time', name: 'Clock In Time'}
        ];

        // Reset previous validation styles
        resetFieldStyles(requiredFields.map(field => field.id));

        // Validate required fields
        for (var i = 0; i < requiredFields.length; i++) {
            if ($(requiredFields[i].id).val() === '') {
                showError(`Please fill the ${requiredFields[i].name} field.`, requiredFields[i].id);
                return;
            }
        }

        // Additional validation for date format (YYYY-MM-DD)
        const dateRegex = /^\d{4}-\d{2}-\d{2}$/;
        if (!dateRegex.test($('#clock_in_date').val())) {
            showError('Please enter a valid Clock In Date (YYYY-MM-DD).', '#clock_in_date');
            return;
        }

        // Additional validation for time format (HH:mm:ss)
        const timeRegex = /^([0-1]?[0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/;
        if (!timeRegex.test($('#clock_in_time').val())) {
            showError('Please enter a valid Clock In Time (HH:mm:ss).', '#clock_in_time');
            return;
        }
        

        

        var form_data = new FormData();
        form_data.append("employee_id", $("#employee_id").val());
        form_data.append("type", $("#type").val());
        form_data.append("clock_in", $("#clock_in_date").val() + ' ' + $("#clock_in_time").val());
        form_data.append("clock_out", $("#clock_out_date").val() + ' ' + $("#clock_out_time").val());

        if ($(this).val() == 'Create') {
            $.ajax({
                url: "{{ route('attendance.store') }}",
                method: "POST",
                contentType: false,
                processData: false,
                data: form_data,
                success: function(d) {
                    console.log(d);
                    if (d.status == 422) {
                        $('.errmsg').html('<div class="alert alert-danger">' + d.message + '</div>');
                    } else {
                        showSuccess('Data created successfully.');
                        resetFieldStyles(requiredFields.map(field => field.id));
                        reloadPage(2000);
                    }
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                    showError('An error occurred. Please try again.');
                }
            });
        }

        if ($(this).val() == 'Update') {

            var requiredFields = [
                {id: '#employee_id', name: 'Employee'},
                {id: '#type', name: 'Type'},
                {id: '#clock_in_date', name: 'Clock In Date'},
                {id: '#clock_in_time', name: 'Clock In Time'},
                {id: '#clock_out_date', name: 'Clock Out Date'},
                {id: '#clock_out_time', name: 'Clock Out Time'}
            ];


            // Reset previous validation styles
            resetFieldStyles(requiredFields.map(field => field.id));

            // Validate required fields
            for (var i = 0; i < requiredFields.length; i++) {
                if ($(requiredFields[i].id).val() === '') {
                    showError(`Please fill the ${requiredFields[i].name} field.`, requiredFields[i].id);
                    return;
                }
            }
            

            
            if (!dateRegex.test($('#clock_out_date').val())) {
                showError('Please enter a valid Clock Out Date (YYYY-MM-DD).', '#clock_out_date');
                return;
            }

            // Additional validation for time format (HH:mm:ss)
            const timeRegex = /^([0-1]?[0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/;
            if (!timeRegex.test($('#clock_out_time').val())) {
                showError('Please enter a valid Clock In Time (HH:mm:ss).', '#clock_out_time');
                return;
            }

            // Validate that clock out is after clock in
            const clockIn = new Date(`${$('#clock_in_date').val()} ${$('#clock_in_time').val()}`);
            const clockOut = new Date(`${$('#clock_out_date').val()} ${$('#clock_out_time').val()}`);
            if (clockOut <= clockIn) {
                showError('Clock Out must be after Clock In.', '#clock_out_time');
                return;
            }

            

            form_data.append("clock_out", $("#clock_out_date").val() + ' ' + $("#clock_out_time").val());
            form_data.append("details", $("#details").val());

            form_data.append("codeid", $("#codeid").val());
            $.ajax({
                url: upurl,
                type: "POST",
                dataType: 'json',
                contentType: false,
                processData: false,
                data: form_data,
                success: function(d) {
                    console.log(d);
                    if (d.status == 422) {
                        $('.errmsg').html('<div class="alert alert-danger">' + d.message + '</div>');
                    } else {
                        showSuccess('Data updated successfully.');
                        resetFieldStyles(requiredFields.map(field => field.id));
                        reloadPage(2000); 
                    }
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                    showError('An error occurred. Please try again.');
                }
            });
        }
    });
    
    





    $("#contentContainer").on('click', '#EditBtn', function() {
        var codeid = $(this).attr('rid');
        var info_url = url + '/' + codeid + '/edit';
        $.get(info_url, {}, function(d) {
            populateForm(d);
            pagetop();
        });
    });

    $("#contentContainer").on('click', '#deleteBtn', function() {
        if (!confirm('Sure?')) return;
        var codeid = $(this).attr('rid');
        var info_url = url + '/' + codeid;
        console.log(info_url);
        $.ajax({
            url: info_url,
            method: "DELETE",
            type: "DELETE",
            data: {},
            success: function(d) {
                showSuccess('Data deleted successfully.');
                reloadPage(2000);
            },
            error: function(xhr, status, error) {
                showError('An error occurred. Please try again.');
            }
        });
    });

    function populateForm(data) {
        $("#type").val(data.type);
        $("#employee_id").val(data.employee_id).trigger('change');
        $("#clock_in_date").val(moment(data.clock_in).format('YYYY-MM-DD'));
        $("#clock_in_time").val(moment(data.clock_in).format('HH:mm:ss'));
        $("#clock_out_date").val(moment(data.clock_out).format('YYYY-MM-DD'));
        $("#clock_out_time").val(moment(data.clock_out).format('HH:mm:ss'));
        $("#details").val(data.details);
        $("#codeid").val(data.id);
        $("#addBtn").val('Update');
        $("#addBtn").html('Update');
        $("#header-title").html('Update data');
        $("#addThisFormContainer").show(300);
        $("#newBtn").hide(100);
    }

    function clearform() {
        $('#createThisForm')[0].reset();
        $("#addBtn").val('Create');
        $("#header-title").html('Add new data');
    }

    $(function() {

        $("#example1").DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
            "order": [[0, "desc"]], // Order by first (hidden) column (id) descending
            "columnDefs": [
            { "targets": 0, "visible": false } // Hide the first column (id)
            ],
            "buttons": ["copy", "csv", "excel", "pdf", "print"]
        }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');

    });

    // Download CSV
    $("#downloadBtn").click(function() {
        var fromDate = $("#from_date").val();
        var toDate = $("#to_date").val();

        const dateRegex = /^\d{4}-\d{2}-\d{2}$/;
        if (!fromDate || !dateRegex.test(fromDate)) {
            showError('Please enter a valid From Date (YYYY-MM-DD).', '#from_date');
            return;
        }
        if (!toDate || !dateRegex.test(toDate)) {
            showError('Please enter a valid To Date (YYYY-MM-DD).', '#to_date');
            return;
        }

        const start = new Date(fromDate);
        const end = new Date(toDate);
        if (end < start) {
            showError('To Date must be after From Date.', '#to_date');
            return;
        }

        window.location.href = url + '/export?from_date=' + fromDate + '&to_date=' + toDate;
    });

    $("#contentContainer").on('click', '#DetailsBtn', function() {
        var attrs = {};
        $.each(this.attributes, function() {
            if(this.specified && this.name.startsWith('data-')) {
                var key = this.name.replace('data-', '');
                attrs[key] = this.value;
            }
        });
        console.log(attrs);
        let modalHtml = `
        <div class="modal fade" id="detailsModal" tabindex="-1" role="dialog" aria-labelledby="detailsModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="detailsModalLabel">Employee Details</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <table class="table table-bordered">
                            <tbody>
                                ${Object.entries(attrs).map(([key, value]) => `
                                    <tr>
                                        <th>${key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())}</th>
                                        <td>${value}</td>
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        `;

        $('#detailsModal').remove();
        $('body').append(modalHtml);
        $('#detailsModal').modal('show');
    });
});
</script>

<script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/tempusdominus-bootstrap-4@5.39.0/build/js/tempusdominus-bootstrap-4.min.js"></script>

<script type="text/javascript">
$(function () {
    $('#clockInDate').datetimepicker({
        format: 'YYYY-MM-DD'
    });
    $('#clockInTime').datetimepicker({
        format: 'HH:mm:ss'
    });
    $('#clockOutDate').datetimepicker({
        format: 'YYYY-MM-DD'
    });
    $('#clockOutTime').datetimepicker({
        format: 'HH:mm:ss'
    });
    $('#fromDate').datetimepicker({
        format: 'YYYY-MM-DD'
    });
    $('#toDate').datetimepicker({
        format: 'YYYY-MM-DD'
    });
});
</script>

@endsection