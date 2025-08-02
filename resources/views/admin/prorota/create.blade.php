@extends('admin.layouts.admin')

@section('content')

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tempusdominus-bootstrap-4@5.39.0/build/css/tempusdominus-bootstrap-4.min.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
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
                        <h3 class="card-title" id="header-title">PreRota Create</h3>
                    </div>
                    <div class="card-body">
                        <div class="errmsg"></div>
                        <form id="createThisForm">
                            @csrf
                            <input type="hidden" class="form-control" id="codeid" name="codeid">
                            
                            <div class="row">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>From Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="start_date" name="start_date" />
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>To Date</label>
                                        <input type="date" class="form-control" id="end_date" name="end_date" />
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label for="type">Type <span class="text-danger">*</span></label>
                                        <select class="form-control select2" id="type" name="type">
                                            <option value="" disabled>Choose type</option>
                                            <option value="Regular">Regular</option>
                                            <option value="Authorized Holiday">Authorized Holiday</option>
                                            <option value="Unauthorized Holiday">Unauthorized Holiday</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>Employee <span class="text-danger">*</span></label>
                                        <select class="form-control select2" id="employee_id" name="employee_id[]" multiple>
                                            <option value="">Select Employee</option>
                                            @foreach ($employees as $employee)
                                                <option value="{{$employee->id}}">{{$employee->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>Start Time</label>
                                        <input type="time" class="form-control" id="start_time" name="start_time" />
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>End Time</label>
                                        <input type="time" class="form-control" id="end_time" name="end_time" />
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Details</label>
                                        <textarea class="form-control" name="details" id="details" cols="30" rows="2"></textarea>
                                    </div>
                                </div>

                                <div id="holiday_results" class="col-sm-12"></div>


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
                        <table id="example1" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th style="display: none" class="d-none">SL</th>
                                    <th>Created date</th>
                                    <th>From date</th>
                                    <th>To date</th>
                                    <th>Type</th>
                                    <th>Start Time</th>
                                    <th>End Time</th>
                                    <th>Employees</th>
                                    <th>Details</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $data)
                                    <tr>
                                        <td></td>
                                        <td>{{$data->created_at}}</td>
                                        <td>{{$data->start_date}}</td>
                                        <td>{{$data->end_date}}</td>
                                        <td>{{$data->type}}</td>
                                        <td>{{$data->start_time ?? '-'}}</td>
                                        <td>{{$data->end_time ?? '-'}}</td>
                                        <td>
                                            @foreach ($data->employees as $employee)
                                                {{$employee->name}}{{ !$loop->last ? ', ' : ''}}
                                            @endforeach
                                        </td>
                                        <td>{{$data->details}}</td>
                                        <td>
                                            <button id="EditBtn" rid="{{$data->id}}" class="btn btn-sm btn-primary">Edit</button>
                                            <button id="deleteBtn" rid="{{$data->id}}" class="btn btn-sm btn-danger">Delete</button>
                                        </td>
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

<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/datatables.net@1.11.5/js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize Select2 for employee_id with multiple selection
    $('#employee_id').select2({
        placeholder: 'Select Employees',
        allowClear: true,
        width: '100%'
    });

    // Initialize Select2 for type
    $('#type').select2({
        placeholder: 'Choose type',
        allowClear: true,
        width: '100%'
    });

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    var url = "{{URL::to('/admin/prorota')}}";
    var upurl = "{{URL::to('/admin/prorota/update')}}";
    var dlturl = "{{URL::to('/admin/delete-prorota')}}";

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

    // Function to reload page
    function reloadPage(timeout) {
        setTimeout(function() {
            location.reload();
        }, timeout);
    }

    // Function to scroll to top
    function pagetop() {
        $('html, body').animate({ scrollTop: 0 }, 'slow');
    }

    $("#addBtn").click(function() {
        // Define required fields with user-friendly names
        var requiredFields = [
            {id: '#employee_id', name: 'Employee'},
            {id: '#type', name: 'Type'},
            {id: '#start_date', name: 'From Date'}
        ];

        // Reset previous validation styles
        resetFieldStyles(requiredFields.map(field => field.id));

        // Validate required fields
        for (var i = 0; i < requiredFields.length; i++) {
            if (requiredFields[i].id === '#employee_id') {
                if ($('#employee_id').val().length === 0) {
                    showError(`Please select at least one ${requiredFields[i].name}.`, requiredFields[i].id);
                    return;
                }
            } else if ($(requiredFields[i].id).val() === '') {
                showError(`Please fill the ${requiredFields[i].name} field.`, requiredFields[i].id);
                return;
            }
        }

        // Additional validation for date format (YYYY-MM-DD)
        const dateRegex = /^\d{4}-\d{2}-\d{2}$/;
        if (!dateRegex.test($('#start_date').val())) {
            showError('Please enter a valid From Date (YYYY-MM-DD).', '#start_date');
            return;
        }

        var form_data = new FormData();
        // Debug: Log selected employee IDs
        var employeeIds = $('#employee_id').val();
        console.log('Selected Employee IDs:', employeeIds);

        // Append each employee_id individually to FormData
        employeeIds.forEach(function(employeeId) {
            form_data.append('employee_id[]', employeeId);
        });
        form_data.append("start_date", $("#start_date").val());
        form_data.append("end_date", $("#end_date").val());
        form_data.append("type", $("#type").val());
        form_data.append("start_time", $("#start_time").val());
        form_data.append("end_time", $("#end_time").val());
        form_data.append("details", $("#details").val());

        // Debug: Log FormData contents
        for (var pair of form_data.entries()) {
            console.log('FormData:', pair[0] + ': ' + pair[1]);
        }

        if ($(this).val() == 'Create') {
            $.ajax({
                url: url,
                method: "POST",
                contentType: false,
                processData: false,
                data: form_data,
                success: function(d) {
                    console.log('Create Response:', d);
                    if (d.status == 422) {
                        $('.errmsg').html('<div class="alert alert-danger">' + d.message + '</div>');
                    } else {
                        showSuccess('Data created successfully.');
                        resetFieldStyles(requiredFields.map(field => field.id));
                        reloadPage(2000);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Create Error:', xhr.responseText);
                    showError('An error occurred. Please try again.');
                }
            });
        }

        if ($(this).val() == 'Update') {
            var requiredFields = [
                {id: '#employee_id', name: 'Employee'},
                {id: '#type', name: 'Type'},
                {id: '#start_date', name: 'From Date'}
            ];

            // Reset previous validation styles
            resetFieldStyles(requiredFields.map(field => field.id));

            // Validate required fields
            for (var i = 0; i < requiredFields.length; i++) {
                if (requiredFields[i].id === '#employee_id') {
                    if ($('#employee_id').val().length === 0) {
                        showError(`Please select at least one ${requiredFields[i].name}.`, requiredFields[i].id);
                        return;
                    }
                } else if ($(requiredFields[i].id).val() === '') {
                    showError(`Please fill the ${requiredFields[i].name} field.`, requiredFields[i].id);
                    return;
                }
            }

            if (!dateRegex.test($('#end_date').val()) && $('#end_date').val() !== '') {
                showError('Please enter a valid To Date (YYYY-MM-DD).', '#end_date');
                return;
            }

            // Debug: Log FormData for update
            for (var pair of form_data.entries()) {
                console.log('Update FormData:', pair[0] + ': ' + pair[1]);
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
                    console.log('Update Response:', d);
                    if (d.status == 422) {
                        $('.errmsg').html('<div class="alert alert-danger">' + d.message + '</div>');
                    } else {
                        showSuccess('Data updated successfully.');
                        resetFieldStyles(requiredFields.map(field => field.id));
                        reloadPage(2000);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Update Error:', xhr.responseText);
                    showError('An error occurred. Please try again.');
                }
            });
        }
    });

    $("#contentContainer").on('click', '#EditBtn', function() {
        var codeid = $(this).attr('rid');
        var info_url = url + '/' + codeid + '/edit';
        $.get(info_url, {}, function(d) {
            console.log('Edit Response:', d);
            populateForm(d);
            pagetop();
        });
    });

    $("#contentContainer").on('click', '#deleteBtn', function() {
        if (!confirm('Sure?')) return;
        var codeid = $(this).attr('rid');
        var info_url = dlturl + '/' + codeid;
        $.ajax({
            url: info_url,
            method: "GET",
            type: "DELETE",
            data: {},
            success: function(d) {
                console.log('Delete Response:', d);
                showSuccess('Data deleted successfully.');
                reloadPage(2000);
            },
            error: function(xhr, status, error) {
                console.error('Delete Error:', xhr.responseText);
                showError('An error occurred. Please try again.');
            }
        });
    });

    function formatDate(dateStr) {
        if (!dateStr) return '';
        const date = new Date(dateStr);
        return date.toISOString().split('T')[0]; // Returns YYYY-MM-DD
    }

    function populateForm(data) {
        $("#type").val(data.type).trigger('change');
        $("#employee_id").val(data.employees.map(emp => emp.id)).trigger('change');
        $("#start_date").val(formatDate(data.start_date));
        $("#start_time").val(data.start_time || '');
        $("#end_date").val(formatDate(data.end_date));
        $("#end_time").val(data.end_time || '');
        $("#details").val(data.details);
        $("#codeid").val(data.id);
        $("#addBtn").val('Update');
        $("#addBtn").html('Update');
        $("#header-title").html('Update PreRota');
        $("#addThisFormContainer").show(300);
        $("#newBtn").hide(100);
    }

    function clearform() {
        $('#createThisForm')[0].reset();
        $('#employee_id').val(null).trigger('change');
        $('#type').val(null).trigger('change');
        $("#addBtn").val('Create');
        $("#header-title").html('Add new PreRota');
    }

    $("#FormCloseBtn").click(function() {
        clearform();
        $("#addThisFormContainer").hide(300);
        $("#newBtn").show(100);
    });

    $("#newBtn").click(function() {
        clearform();
        $("#addThisFormContainer").show(300);
        $("#newBtn").hide(100);
    });

    $(function() {
        $("#example1").DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
            "order": [[0, "desc"]],
            "columnDefs": [
                { "targets": 0, "visible": false }
            ],
            "buttons": ["copy", "csv", "excel", "pdf", "print"]
        }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    });

    $("#contentContainer").on('click', '#DetailsBtn', function() {
        var attrs = {};
        $.each(this.attributes, function() {
            if(this.specified && this.name.startsWith('data-')) {
                var key = this.name.replace('data-', '');
                attrs[key] = this.value;
            }
        });
        console.log('Details Attributes:', attrs);
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

<script>
$(document).ready(function() {

    // Trigger AJAX on employee selection change
    $('#employee_id').on('change', function() {
        let start_date = $('#start_date').val();
        let end_date = $('#end_date').val();
        let employee_ids = $(this).val() || []; // Get selected employee IDs

        // Validate inputs
        if (!start_date || employee_ids.length === 0) {
            $('#holiday_results').html('<div class="alert alert-danger">Please select a start date and at least one employee.</div>');
            return;
        }

        $.ajax({
            url: "{{ route('admin.holiday.check') }}", 
            method: 'POST',
            data: {
                start_date: start_date,
                end_date: end_date,
                employee_ids: employee_ids,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    $('#holiday_results').html(response.html);
                } else {
                    $('#holiday_results').html('<div class="alert alert-warning">No holidays found for the selected criteria.</div>');
                }
            },
            error: function() {
                $('#holiday_results').html('<div class="alert alert-danger">An error occurred. Please try again.</div>');
            }
        });
    });
});
</script>

@endsection