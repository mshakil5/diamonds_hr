@extends('admin.layouts.admin')

@section('content')
<style>
    select.time-select option {
        max-height: 20px;
        overflow-y: auto;
    }
</style>

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
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label for="type">Type <span class="text-danger">*</span></label>
                                        <select class="form-control" id="etype" name="type">
                                            <option value="Regular" selected>Regular</option>
                                            <option value="Authorized Holiday">Authorized Holiday</option>
                                            <option value="Unauthorized Holiday">Unauthorized Holiday</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Employee <span class="text-danger">*</span></label>
                                        <select class="form-control select2" id="employee_id" name="employee_id[]" multiple>
                                            @foreach ($employees as $employee)
                                                <option value="{{$employee->id}}">{{$employee->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label>Note</label>
                                        <textarea class="form-control" name="details" id="details" cols="30" rows="2"></textarea>
                                    </div>
                                </div>


                                <div id="weekly-schedule" class="col-sm-12 mt-3"></div>

                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label>How many days this schedule will continue? <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="to_date" name="to_date" />
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
                                        <td>
                                            @foreach ($data->employees->unique('id') as $employee)
                                                
                                                <span class="btn btn-info btn-sm">{{ $employee->name }} </span>
                                            @endforeach
                                        </td>
                                        <td>{{$data->details}}</td>
                                        <td>

                                            <button
                                                class="btn btn-sm btn-info"
                                                id="DetailsBtn"
                                                data-id="{{ $data->id }}"
                                                data-type="{{ $data->type }}"
                                                data-start_date="{{ $data->start_date }}"
                                                data-end_date="{{ $data->end_date }}"
                                                data-details="{{ $data->details }}"
                                                data-created_at="{{ $data->created_at }}"
                                                data-employees='@json($data->employees)'
                                            >
                                                Details
                                            </button>

                                            <a href="{{route('prorota.edit', $data->id)}}" class="btn btn-sm btn-primary">Edit</a>
                                            {{-- <button id="EditBtn" rid="{{$data->id}}" class="btn btn-sm btn-primary">Edit</button> --}}
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

<script>
$(document).ready(function() {
    // Initialize Select2 for employee_id with multiple selection
    $('#employee_id').select2({
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

    // Function to display error message with close button
    function showError(message, field = null) {
        $('.errmsg').html(`
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        `);

        if (field) {
            $(field).css('border-color', 'red');
            $(field).addClass('is-invalid');
            $(field).focus();
        }

        pagetop();
    }

    // When user selects or types a time, remove error message and styles
    $(document).on('input change', '.start-time, .end-time', function () {
        $(this).removeClass('is-invalid').css('border-color', '');
        
        // Clear the main error message only if there are no other visible errors
        if ($('.is-invalid').length === 0) {
            $('.errmsg').html('');
        }
    });



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

    $("#addBtn").click(function (e) {
        e.preventDefault();

        // add a loader when clicked and disable the button
        $(this).prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Processing...');

        const isCreate = $(this).val() === 'Create';
        const isUpdate = $(this).val() === 'Update';

        // Required fields
        const requiredFields = [
            { id: '#employee_id', name: 'Employee', type: 'multi' },
            { id: '#etype', name: 'Type', type: 'single' },
            { id: '#start_date', name: 'From Date', type: 'date' },
            { id: '#to_date', name: 'To Date', type: 'date' }
        ];

        resetFieldStyles(requiredFields.map(field => field.id));
        $('.errmsg').html('');

        // Validate basic fields
        for (let field of requiredFields) {
            const value = $(field.id).val();

            if (field.type === 'multi' && (!value || value.length === 0)) {
                showError(`Please select at least one ${field.name}.`, field.id);
                return;
            }

            if ((field.type === 'single' || field.type === 'date') && (!value || value === '')) {
                showError(`Please fill the ${field.name} field.`, field.id);
                return;
            }

            if (field.type === 'date' && !/^\d{4}-\d{2}-\d{2}$/.test(value)) {
                showError(`Please enter a valid ${field.name} (YYYY-MM-DD).`, field.id);
                return;
            }
        }

        const startTimes = $("input[name='start_times[]']");
        const endTimes = $("input[name='end_times[]']");
        

        let timeError = false;

        $(".schedule-row").each(function (index) {
            const row = $(this);
            const startInput = row.find(".start-time");
            const endInput = row.find(".end-time");

            const start = startInput.val();
            const end = endInput.val();
            const isDayOff = startInput.prop('disabled') && endInput.prop('disabled');

            if (!isDayOff) {
                if (!start) {
                    showError(`Start time is required for Row ${index + 1}.`, startInput[0]);
                    timeError = true;
                    return false; // breaks the .each loop
                }

                if (!end) {
                    showError(`End time is required for Row ${index + 1}.`, endInput[0]);
                    timeError = true;
                    return false;
                }

                const startDate = new Date(`2025-01-01T${start}:00`);
                const endDate = new Date(`2025-01-01T${end}:00`);
                if (startDate >= endDate) {
                    showError(`End time must be after Start time (Row ${index + 1}).`, endInput[0]);
                    timeError = true;
                    return false;
                }
            }
        });

        if (timeError) return;



        const form_data = new FormData();

        $('#employee_id').val().forEach(emp => form_data.append('employee_id[]', emp));
        form_data.append("start_date", $("#start_date").val());
        form_data.append("to_date", $("#to_date").val());
        form_data.append("type", $("#etype").val());
        form_data.append("details", $("#details").val());

        $("input[name='dates[]']").each((i, el) => form_data.append("dates[]", el.value));
        $("input[name='day_names[]']").each((i, el) => form_data.append("day_names[]", el.value));
        $("input[name='start_times[]']").each((i, el) => form_data.append("start_times[]", el.value));
        $("input[name='end_times[]']").each((i, el) => form_data.append("end_times[]", el.value));

        if (isUpdate) {
            form_data.append("codeid", $("#codeid").val());
        }

        for (let pair of form_data.entries()) {
            console.log(pair[0] + ": " + pair[1]);
        }

        $.ajax({
            url: isCreate ? url : upurl,
            type: "POST",
            dataType: 'json',
            contentType: false,
            processData: false,
            data: form_data,
            success: function (d) {
                if (d.status == 422) {
                    $('.errmsg').html('<div class="alert alert-danger">' + d.message + '</div>');
                } else {
                    pagetop();
                    showSuccess(isCreate ? 'Data created successfully.' : 'Data updated successfully.');
                    resetFieldStyles(requiredFields.map(f => f.id));
                    reloadPage(2000);
                }
            },
            error: function (xhr) {
                console.error('Error:', xhr.responseText);
                showError('An error occurred. Please try again.');
            }
        });
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
        $("#etype").val(data.type).trigger('change');
        $("#employee_id").val(data.employees.map(emp => emp.id)).trigger('change');
        $("#start_date").val(formatDate(data.start_date));
        $("#to_date").val(formatDate(data.end_date));
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

    $("#addThisFormContainer").hide(300);
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
        }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    });

    // dtls
    $("#contentContainer").on('click', '#DetailsBtn', function () {
        var attrs = {};
        $.each(this.attributes, function () {
            if (this.specified && this.name.startsWith('data-')) {
                var key = this.name.replace('data-', '');
                if (key === 'employees') {
                    try {
                        attrs[key] = JSON.parse(this.value);
                    } catch (e) {
                        attrs[key] = [];
                    }
                } else {
                    attrs[key] = this.value;
                }
            }
        });

        // Build general info rows
        let generalRows = Object.entries(attrs).filter(([k]) => k !== 'employees').map(([key, value]) => `
            <tr>
                <th>${key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())}</th>
                <td>${value}</td>
            </tr>
        `).join('');

        // Build schedule rows if employees present
        let scheduleRows = '';
        if (Array.isArray(attrs.employees)) {
            scheduleRows = attrs.employees.map(emp => {
                return `
                    <tr>
                        <td>${emp.name}</td>
                        <td>${emp.pivot?.date ?? '-'}</td>
                        <td>${emp.pivot?.day_name ?? '-'}</td>
                        <td>${emp.pivot?.start_time ?? '-'}</td>
                        <td>${emp.pivot?.end_time ?? '-'}</td>
                    </tr>`;
            }).join('');
        }

        // Full modal HTML
        let modalHtml = `
            <div class="modal fade" id="detailsModal" tabindex="-1" role="dialog" aria-labelledby="detailsModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="detailsModalLabel">Pre-Rota Details</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <h5>General Info</h5>
                            <table class="table table-sm table-bordered mb-4">
                                <tbody>
                                    ${generalRows}
                                </tbody>
                            </table>
                            <h5>Employee Schedules</h5>
                            <table class="table table-sm table-bordered">
                                <thead>
                                    <tr>
                                        <th>Employee</th>
                                        <th>Date</th>
                                        <th>Day</th>
                                        <th>Start Time</th>
                                        <th>End Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${scheduleRows}
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

        $('#detailsModal').remove(); // Remove any existing modals
        $('body').append(modalHtml); // Append new modal
        $('#detailsModal').modal('show'); // Show modal
    });



});
</script>

<script>
$(document).ready(function() {

    // Trigger AJAX on employee selection change
    $('#to_date').on('change', function() {
        let start_date = $('#start_date').val();
        let end_date = $('#to_date').val();
        let employee_ids = $('#employee_id').val() || []; // Get selected employee IDs

        // Validate inputs
        if (!start_date || employee_ids.length === 0) {
            $('#holiday_results').html('<div class="alert alert-danger">Please select a start date and at least one employee.</div>');
            $('#to_date').val('');
            return;
        }

        console.log(start_date, end_date, employee_ids);

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
                console.log(response );
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


<!-- Moment.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>

<!-- Tempus Dominus Bootstrap 4 -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/css/tempusdominus-bootstrap-4.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/js/tempusdominus-bootstrap-4.min.js"></script>


<script>
    document.getElementById('start_date').addEventListener('change', function () {
        const startDate = new Date(this.value);
        const scheduleContainer = document.getElementById('weekly-schedule');
        scheduleContainer.innerHTML = ''; // Clear previous content

        if (isNaN(startDate)) return; // Invalid date

        for (let i = 0; i < 7; i++) {
            const currentDate = new Date(startDate);
            currentDate.setDate(startDate.getDate() + i);

            const dayName = currentDate.toLocaleDateString('en-US', { weekday: 'long' });
            const formattedDate = currentDate.toISOString().split('T')[0];

            const row = document.createElement('div');
            row.className = 'row mb-3 align-items-center schedule-row';
            row.dataset.index = i;

            row.innerHTML = `
                <div class="col-md-2">
                    <input type="text" class="form-control" name="dates[]" value="${formattedDate}" readonly>
                </div>
                <div class="col-md-2">
                    <input type="text" class="form-control" name="day_names[]" value="${dayName}" readonly>
                </div>
                <div class="col-md-2">
                    <div class="input-group date timepicker" id="start_time_${i}" data-target-input="nearest">
                        <input type="text" name="start_times[]" class="form-control datetimepicker-input start-time" data-target="#start_time_${i}" />
                        <div class="input-group-append" data-target="#start_time_${i}" data-toggle="datetimepicker">
                            <div class="input-group-text"><i class="fa fa-clock-o"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="input-group date timepicker" id="end_time_${i}" data-target-input="nearest">
                        <input type="text" name="end_times[]" class="form-control datetimepicker-input end-time" data-target="#end_time_${i}" />
                        <div class="input-group-append" data-target="#end_time_${i}" data-toggle="datetimepicker">
                            <div class="input-group-text"><i class="fa fa-clock-o"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-success btn-sm day-off-btn">Working Day</button>
                </div>
            `;

            scheduleContainer.appendChild(row);
        }

        initTimepickers();
        addEndTimeValidation();
        addDayOffToggle();
    });

    function initTimepickers() {
        $('.timepicker').datetimepicker({
            format: 'HH:mm', // 24-hour format, e.g., 14:30
            stepping: 30,
            icons: {
                time: 'fa fa-clock',
                date: 'fa fa-calendar',
                up: 'fa fa-chevron-up',
                down: 'fa fa-chevron-down',
                previous: 'fa fa-chevron-left',
                next: 'fa fa-chevron-right',
                today: 'fa fa-calendar-check',
                clear: 'fa fa-trash',
                close: 'fa fa-times'
            }
        });

    }

    function addEndTimeValidation() {
        const startInputs = document.querySelectorAll('[name="start_times[]"]');
        const endInputs = document.querySelectorAll('[name="end_times[]"]');

        startInputs.forEach((startInput, index) => {
            startInput.addEventListener('change', function () {
                const endInput = endInputs[index];
                if (this.value) {
                    endInput.setAttribute('required', 'required');
                } else {
                    endInput.removeAttribute('required');
                    endInput.value = '';
                }
            });
        });
    }

    function addDayOffToggle() {
        document.querySelectorAll('.day-off-btn').forEach(button => {
            button.addEventListener('click', function () {
                const row = this.closest('.schedule-row');
                const startInput = row.querySelector('[name="start_times[]"]');
                const endInput = row.querySelector('[name="end_times[]"]');

                const isDisabled = startInput.disabled;

                if (isDisabled) {
                    startInput.disabled = false;
                    endInput.disabled = false;
                    this.classList.remove('btn-warning');
                    this.classList.add('btn-success');
                    this.textContent = 'Working Day';
                } else {
                    startInput.disabled = true;
                    endInput.disabled = true;
                    startInput.value = '';
                    endInput.value = '';
                    this.classList.remove('btn-success');
                    this.classList.add('btn-warning');
                    this.textContent = 'Day Off';
                }
            });
        });
    }
</script>






@endsection