@extends('admin.layouts.admin')

@section('content')
<style>
    select.time-select option {
        max-height: 20px;
        overflow-y: auto;
    }
</style>

<section class="content mt-3" id="addThisFormContainer">
    <div class="container-fluid">
        <div class="row justify-content-md-center">
            <div class="col-md-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title" id="header-title">PreRota Update</h3>
                    </div>
                    <div class="card-body">
                        <div class="errmsg"></div>
                        <form id="createThisForm">
                            @csrf
                            <input type="hidden" class="form-control" id="codeid" name="codeid" value="{{$preRota->id}}">

                            <div class="row">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>From Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="start_date" name="start_date" value="{{$preRota->start_date}}"/>
                                    </div>
                                </div>
                                <div class="col-lg-3 d-none">
                                    <div class="form-group">
                                        <label for="type">Type <span class="text-danger">*</span></label>
                                        <select class="form-control" id="etype" name="type">
                                            <option value="Regular" @if ($preRota->type == 'Regular') selected @endif>Regular</option>
                                            <option value="Authorized Holiday" @if ($preRota->type == 'Authorized Holiday') selected @endif>Authorized Holiday</option>
                                            <option value="Unauthorized Holiday" @if ($preRota->type == 'Unauthorized Holiday') selected @endif>Unauthorized Holiday</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label>Employee <span class="text-danger">*</span></label>
                                        <select class="form-control select2" id="employee_id" name="employee_id[]">
                                            @foreach ($employees as $employee)
                                                <option value="{{$employee->id}}" @if (in_array($employee->id, $preRota->employees->pluck('id')->toArray())) selected @endif>{{$employee->name}} - {{$employee->branch->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-5">
                                    <div class="form-group">
                                        <label>How many days this schedule will continue? <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="to_date" name="to_date" value="{{$preRota->end_date}}"/>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label>Details</label>
                                        <textarea class="form-control" name="details" id="details" cols="30" rows="2">{{$preRota->details}}</textarea>
                                    </div>
                                </div>

                                <div id="holiday_results" class="col-sm-12"></div>

                                <div id="weekly-schedule" class="col-sm-12 mt-3"></div>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer">
                        <button type="submit" id="addBtn" class="btn btn-secondary" value="Update">Update</button>
                        <a href="{{route('prorota')}}" class="btn btn-default">Cancel</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
@section('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/css/tempusdominus-bootstrap-4.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/js/tempusdominus-bootstrap-4.min.js"></script>

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
    var upurl = "{{ URL::to('/admin/prorota/update') }}";

    // Trigger initial holiday check to populate schedule
    triggerHolidayCheck();

    // Function to reset field styles
    function resetFieldStyles(fields) {
        fields.forEach(function(field) {
            $(field).css('border-color', '');
            $(field).removeClass('is-invalid');
        });
    }

    // Function to display error message
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

    // Function to display success message
    function showSuccess(message) {
        pagetop();
        $('.errmsg').html(`<div class="alert alert-success">${message}</div>`);
    }

    // Function to scroll to top
    function pagetop() {
        $('html, body').animate({ scrollTop: 0 }, 'slow');
    }

    // Initialize timepickers
    function initTimepickers() {
        $('.timepicker').datetimepicker({
            format: 'HH:mm',
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

    // Add end time validation
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

    // Add day off toggle for dropdown
    function addDayOffToggle() {
        document.querySelectorAll('.status-select').forEach(select => {
            select.addEventListener('change', function () {
                const row = this.closest('.schedule-row');
                const startInput = row.querySelector('[name="start_times[]"]');
                const endInput = row.querySelector('[name="end_times[]"]');

                if (this.value === '2') {
                    startInput.disabled = true;
                    endInput.disabled = true;
                    startInput.value = '';
                    endInput.value = '';
                } else {
                    startInput.disabled = false;
                    endInput.disabled = false;
                }
            });
        });
    }

    // Validate schedule
    function validateSchedule() {
        let timeError = false;
        let hasHoliday = false;

        // Check for Holiday status
        $(".schedule-row").each(function () {
            const statusSelect = $(this).find(".status-select");
            if (statusSelect.val() === '3') {
                hasHoliday = true;
            }
        });

        // Show single alert for Holiday
        if (hasHoliday && !confirm('One or more rows are marked as Holiday. Continue?')) {
            return false;
        }

        $(".schedule-row").each(function (index) {
            const row = $(this);
            const startInput = row.find(".start-time");
            const endInput = row.find(".end-time");
            const statusSelect = row.find(".status-select");
            const status = statusSelect.val();

            if (status === '1' || status === '') {
                const start = startInput.val();
                const end = endInput.val();

                if (!start) {
                    showError(`Start time is required for Row ${index + 1}.`, startInput[0]);
                    timeError = true;
                    return false;
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

        return !timeError;
    }

    // Trigger holiday check on start_date, to_date, or employee_id change
    function triggerHolidayCheck() {
        let start_date = $('#start_date').val();
        let end_date = $('#to_date').val();
        let employee_ids = $('#employee_id').val() || [];

        if (!start_date || !end_date || employee_ids.length === 0) {
            $('#holiday_results').html('<div class="alert alert-danger">Please select a start date, end date, and at least one employee.</div>');
            $('#weekly-schedule').html('');
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
                    initTimepickers();
                    addEndTimeValidation();
                    addDayOffToggle();
                } else {
                    $('#holiday_results').html('<div class="alert alert-warning">No holidays found for the selected criteria.</div>');
                    $('#weekly-schedule').html('');
                }
            },
            error: function() {
                $('#holiday_results').html('<div class="alert alert-danger">An error occurred. Please try again.</div>');
                $('#weekly-schedule').html('');
            }
        });
    }

    $('#start_date, #to_date, #employee_id').on('change', triggerHolidayCheck);

    // Form submission
    $("#addBtn").click(function (e) {
        e.preventDefault();

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

        // Validate schedule rows
        if (!validateSchedule()) {
            return;
        }

        // Prepare FormData
        const form_data = new FormData();

        form_data.append("employee_id", $("#employee_id").val());
        form_data.append("start_date", $("#start_date").val());
        form_data.append("to_date", $("#to_date").val());
        form_data.append("type", $("#etype").val());
        form_data.append("details", $("#details").val());
        form_data.append("codeid", $("#codeid").val());

        $("input[name='dates[]']").each((i, el) => {
            form_data.append("dates[]", el.value);
            form_data.append("day_names[]", $("input[name='day_names[]']").eq(i).val());
            form_data.append("start_times[]", $("input[name='start_times[]']").eq(i).val());
            form_data.append("end_times[]", $("input[name='end_times[]']").eq(i).val());
            form_data.append("status[]", $("select[name='status[]']").eq(i).val());
        });

        for (let pair of form_data.entries()) {
            console.log(pair[0] + ": " + pair[1]);
        }

        $(this).prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Processing...');

        $.ajax({
            url: upurl,
            type: "POST",
            dataType: 'json',
            contentType: false,
            processData: false,
            data: form_data,
            success: function (d) {
                $('#addBtn').prop('disabled', false).html('Update');
                console.log('Response:', d);
                
                if (d.status === 422) {
                    $('.errmsg').html(`
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            ${d.message}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    `);
                    pagetop();
                } else {
                    pagetop();
                    showSuccess('Data updated successfully.');
                    resetFieldStyles(requiredFields.map(f => f.id));
                    reloadPage(2000);
                }
            },
            error: function (xhr) {
                $('#addBtn').prop('disabled', false).html('Update');
                console.error('AJAX Error:', xhr.responseText);
                
                let errorMessage = 'An error occurred. Please try again.';
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.status === 422 && response.message) {
                        errorMessage = response.message;
                    }
                } catch (e) {
                    // If response is not JSON, use default error message
                }
                
                $('.errmsg').html(`
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        ${errorMessage}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                `);
                pagetop();
            }



            
        });
    });
});
</script>
@endsection