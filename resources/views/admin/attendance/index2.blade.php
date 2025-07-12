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
                        <h3 class="card-title" id="header-title">Add new Attendance</h3>
                    </div>
                    <div class="card-body">
                        <div class="errmsg"></div>
                        <form id="createThisForm">
                            @csrf
                            <input type="hidden" class="form-control" id="codeid" name="codeid">
                            
                            <div class="row">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>Employee *</label>
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
                                        <label>Type *</label>
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
                                        <label>Clock In Date</label>
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
                                        <label>Clock In Time</label>
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

<section class="content mt-3" id="dateFilterSection">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">Filter Attendance by Date Range</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>From Date</label>
                                    <div class="input-group date" id="fromDate" data-target-input="nearest">
                                        <input type="text" class="form-control datetimepicker-input" data-target="#fromDate" id="from_date" name="from_date" />
                                        <div class="input-group-append" data-target="#fromDate" data-toggle="datetimepicker">
                                            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>To Date</label>
                                    <div class="input-group date" id="toDate" data-target-input="nearest">
                                        <input type="text" class="form-control datetimepicker-input" data-target="#toDate" id="to_date" name="to_date" />
                                        <div class="input-group-append" data-target="#toDate" data-toggle="datetimepicker">
                                            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label> </label>
                                    <button type="button" class="btn btn-secondary" id="filterBtn">Filter</button>
                                    <button type="button" class="btn btn-secondary" id="downloadBtn">Download CSV</button>
                                </div>
                            </div>
                        </div>
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
                        <h3 class="card-title">All Attendance Records</h3>
                    </div>
                    <div class="card-body">
                        <table id="example1" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th style="display: none" class="d-none">SL</th>
                                    <th>Name</th>
                                    <th>Branch</th>
                                    <th>Details</th>
                                    <th>G. Total Time</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
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
    var url = "{{ route('attendance.index') }}";
    var upurl = "{{ route('attendance.update') }}";

    // Initialize DataTable with AJAX
    var table = $("#example1").DataTable({
        "responsive": true,
        "lengthChange": false,
        "autoWidth": false,
        "order": [[0, "desc"]],
        "columnDefs": [
            { "targets": 0, "visible": false }
        ],
        "ajax": {
            "url": url,
            "dataSrc": "data",
            "error": function(xhr, error, thrown) {
                console.error('DataTable AJAX error:', xhr.responseText);
                $('.errmsg').html('<div class="alert alert-danger">Failed to load data. Please try again.</div>');
            }
        },
        "columns": [
            { "data": "id" },
            { "data": "employee.name" },
            { "data": "branch.name", "defaultContent": "" },
            {
                "data": null,
                "render": function(data, type, row) {
                    var diff = '';
                    if (row.clock_in && row.clock_out) {
                        var inTime = moment(row.clock_in);
                        var outTime = moment(row.clock_out);
                        diff = moment.utc(outTime.diff(inTime)).format('HH:mm:ss');
                    }
                    return `
                        <table class="table table-bordered w-100">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Time In</th>
                                    <th>Time Out</th>
                                    <th>Late</th>
                                    <th>Total Time</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>${row.clock_in ? moment(row.clock_in).format('DD/MM/YYYY') : '-'}</td>
                                    <td>${row.type || '-'}</td>
                                    <td>${row.clock_in ? moment(row.clock_in).format('HH:mm:ss') : '-'}</td>
                                    <td>${row.clock_out ? moment(row.clock_out).format('HH:mm:ss') : '-'}</td>
                                    <td></td>
                                    <td>${diff || '-'}</td>
                                    <td>
                                        <a id="DetailsBtn"
                                            rid="${row.id}"
                                            title="Details"
                                            data-id="${row.id}"
                                            data-employee="${row.employee ? row.employee.name : '-'}"
                                            data-type="${row.type || '-'}"
                                            data-clock_in_date="${row.clock_in ? moment(row.clock_in).format('YYYY-MM-DD') : '-'}"
                                            data-clock_in_time="${row.clock_in ? moment(row.clock_in).format('HH:mm:ss') : '-'}"
                                            data-clock_out_date="${row.clock_out ? moment(row.clock_out).format('YYYY-MM-DD') : '-'}"
                                            data-clock_out_time="${row.clock_out ? moment(row.clock_out).format('HH:mm:ss') : '-'}"
                                            data-details="${row.details || ''}"
                                            data-date="${row.created_at ? moment(row.created_at).format('YYYY-MM-DD') : '-'}"
                                            data-total_time="${diff || '-'}"
                                        >
                                            <i class="fa fa-info-circle" style="color: #17a2b8; font-size:16px; margin-right:8px;"></i>
                                        </a>
                                        @if (auth()->user()->canDo(15))
                                        <a id="EditBtn" rid="${row.id}"><i class="fa fa-edit" style="color: #2196f3;font-size:16px;"></i></a>
                                        @endif
                                        @if (auth()->user()->canDo(16))
                                        <a id="deleteBtn" rid="${row.id}"><i class="fa fa-trash-o" style="color: red;font-size:16px;"></i></a>
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    `;
                }
            },
            { "data": null, "defaultContent": "" }
        ],
        "buttons": ["copy", "csv", "excel", "pdf", "print"],
        "initComplete": function(settings, json) {
            if (!json || !json.data) {
                $('.errmsg').html('<div class="alert alert-danger">No data available or invalid response from server.</div>');
            }
        }
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');

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

    // Function to display success message
    function showSuccess(message) {
        $('.errmsg').html(`<div class="alert alert-success">${message}</div>`);
    }

    $("#addBtn").click(function() {
        var requiredFields = [
            {id: '#employee_id', name: 'Employee'},
            {id: '#type', name: 'Type'},
            {id: '#clock_in_date', name: 'Clock In Date'},
            {id: '#clock_in_time', name: 'Clock In Time'}
        ];

        resetFieldStyles(requiredFields.map(field => field.id));

        for (var i = 0; i < requiredFields.length; i++) {
            if ($(requiredFields[i].id).val() === '') {
                showError(`Please fill the ${requiredFields[i].name} field.`, requiredFields[i].id);
                return;
            }
        }

        const dateRegex = /^\d{4}-\d{2}-\d{2}$/;
        if (!dateRegex.test($('#clock_in_date').val())) {
            showError('Please enter a valid Clock In Date (YYYY-MM-DD).', '#clock_in_date');
            return;
        }

        const timeRegex = /^([0-1]?[0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/;
        if (!timeRegex.test($('#clock_in_time').val())) {
            showError('Please enter a valid Clock In Time (HH:mm:ss).', '#clock_in_time');
            return;
        }

        var form_data = new FormData();
        form_data.append("employee_id", $("#employee_id").val());
        form_data.append("type", $("#type").val());
        form_data.append("clock_in", $("#clock_in_date").val() + ' ' + $("#clock_in_time").val());
        form_data.append("clock_out", $("#clock_out_date").val() && $("#clock_out_time").val() ? $("#clock_out_date").val() + ' ' + $("#clock_out_time").val() : '');
        form_data.append("details", $("#details").val());

        if ($(this).val() == 'Create') {
            $.ajax({
                url: url,
                method: "POST",
                contentType: false,
                processData: false,
                data: form_data,
                success: function(d) {
                    if (d.status == 422) {
                        showError(d.message);
                    } else {
                        showSuccess('Data created successfully.');
                        resetFieldStyles(requiredFields.map(field => field.id));
                        table.ajax.reload();
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Create AJAX error:', xhr.responseText);
                    showError('An error occurred while creating the record. Please try again.');
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

            resetFieldStyles(requiredFields.map(field => field.id));

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

            if (!timeRegex.test($('#clock_out_time').val())) {
                showError('Please enter a valid Clock Out Time (HH:mm:ss).', '#clock_out_time');
                return;
            }

            const clockIn = new Date(`${$('#clock_in_date').val()} ${$('#clock_in_time').val()}`);
            const clockOut = new Date(`${$('#clock_out_date').val()} ${$('#clock_out_time').val()}`);
            if (clockOut <= clockIn) {
                showError('Clock Out must be after Clock In.', '#clock_out_time');
                return;
            }

            form_data.append("codeid", $("#codeid").val());

            $.ajax({
                url: upurl,
                type: "POST",
                dataType: 'json',
                contentType: false,
                processData: false,
                data: form_data,
                success: function(d) {
                    if (d.status == 422) {
                        showError(d.message);
                    } else {
                        showSuccess('Data updated successfully.');
                        resetFieldStyles(requiredFields.map(field => field.id));
                        table.ajax.reload();
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Update AJAX error:', xhr.responseText);
                    showError('An error occurred while updating the record. Please try again.');
                }
            });
        }
    });

    $("#contentContainer").on('click', '#EditBtn', function() {
        var codeid = $(this).attr('rid');
        var info_url = url + '/' + codeid + '/edit';
        $.get(info_url, {}, function(d) {
            populateForm(d);
        }, 'json').fail(function(xhr) {
            console.error('Edit AJAX error:', xhr.responseText);
            showError('Failed to load record for editing.');
        });
    });

    $("#contentContainer").on('click', '#deleteBtn', function() {
        if (!confirm('Are you sure you want to delete this record?')) return;
        var codeid = $(this).attr('rid');
        var info_url = url + '/' + codeid;
        $.ajax({
            url: info_url,
            method: "DELETE",
            dataType: 'json',
            success: function(d) {
                if (d.status == 200) {
                    showSuccess(d.message);
                    table.ajax.reload();
                } else {
                    showError('Failed to delete data.');
                }
            },
            error: function(xhr, status, error) {
                console.error('Delete AJAX error:', xhr.responseText);
                showError('An error occurred while deleting the record. Please try again.');
            }
        });
    });

    function populateForm(data) {
        $("#type").val(data.type);
        $("#employee_id").val(data.employee_id).trigger('change');
        $("#clock_in_date").val(data.clock_in ? moment(data.clock_in).format('YYYY-MM-DD') : '');
        $("#clock_in_time").val(data.clock_in ? moment(data.clock_in).format('HH:mm:ss') : '');
        $("#clock_out_date").val(data.clock_out ? moment(data.clock_out).format('YYYY-MM-DD') : '');
        $("#clock_out_time").val(data.clock_out ? moment(data.clock_out).format('HH:mm:ss') : '');
        $("#details").val(data.details || '');
        $("#codeid").val(data.id);
        $("#addBtn").val('Update');
        $("#addBtn").html('Update');
        $("#header-title").html('Update Attendance');
        $("#addThisFormContainer").show(300);
        $("#newBtn").hide(100);
    }

    function clearform() {
        $('#createThisForm')[0].reset();
        $("#addBtn").val('Create');
        $("#addBtn").html('Create');
        $("#header-title").html('Add new Attendance');
        $('.errmsg').html('');
        resetFieldStyles(['#employee_id', '#type', '#clock_in_date', '#clock_in_time', '#clock_out_date', '#clock_out_time']);
    }

    // Date range filter
    $("#filterBtn").click(function() {
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

        if (table && table.ajax) {
            table.ajax.url(url + '?from_date=' + fromDate + '&to_date=' + toDate).load(null, false);
        } else {
            console.error('DataTable AJAX is not initialized');
            showError('Table initialization error. Please refresh the page and try again.');
        }
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
        let modalHtml = `
        <div class="modal fade" id="detailsModal" tabindex="-1" role="dialog" aria-labelledby="detailsModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="detailsModalLabel">Attendance Details</h5>
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
                                        <td>${value || '-'}</td>
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