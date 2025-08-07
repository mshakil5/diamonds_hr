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
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label for="type">Type <span class="text-danger">*</span></label>
                                        <select class="form-control" id="etype" name="type">
                                            <option value="Regular" @if ($preRota->type == 'Regular') selected @endif>Regular</option>
                                            <option value="Authorized Holiday" @if ($preRota->type == 'Authorized Holiday') selected @endif>Authorized Holiday</option>
                                            <option value="Unauthorized Holiday" @if ($preRota->type == 'Unauthorized Holiday') selected @endif>Unauthorized Holiday</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Employee <span class="text-danger">*</span></label>
                                        <select class="form-control select2" id="employee_id" name="employee_id[]" multiple>
                                            @foreach ($employees as $employee)
                                                <option value="{{$employee->id}}" @if (in_array($employee->id, $preRota->employees->pluck('id')->toArray())) selected @endif>{{$employee->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label>Details</label>
                                        <textarea class="form-control" name="details" id="details" cols="30" rows="2">{{$preRota->details}}</textarea>
                                    </div>
                                </div>

                                <div id="holiday_results" class="col-sm-12"></div>

                                <div id="weekly-schedule" class="col-sm-12 mt-3">
                                    @foreach ($preRota->employees as $index => $schedule)
                                        <div class="row mb-3 align-items-center schedule-row" data-index="{{ $index }}">
                                            <div class="col-md-2">
                                                <input type="text" class="form-control" name="dates[]" value="{{ $schedule->pivot->date }}" readonly>
                                            </div>
                                            <div class="col-md-2">
                                                <input type="text" class="form-control" name="day_names[]" value="{{ $schedule->pivot->day_name }}" readonly>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="input-group date timepicker" id="start_time_{{ $index }}" data-target-input="nearest">
                                                    <input type="text" name="start_times[]" class="form-control datetimepicker-input start-time" 
                                                        data-target="#start_time_{{ $index }}" 
                                                        value="{{ $schedule->pivot->start_time }}" 
                                                        @if (!$schedule->pivot->start_time) disabled @endif />
                                                    <div class="input-group-append" data-target="#start_time_{{ $index }}" data-toggle="datetimepicker">
                                                        <div class="input-group-text"><i class="fa fa-clock-o"></i></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="input-group date timepicker" id="end_time_{{ $index }}" data-target-input="nearest">
                                                    <input type="text" name="end_times[]" class="form-control datetimepicker-input end-time" 
                                                        data-target="#end_time_{{ $index }}" 
                                                        value="{{ $schedule->pivot->end_time }}" 
                                                        @if (!$schedule->pivot->end_time) disabled @endif />
                                                    <div class="input-group-append" data-target="#end_time_{{ $index }}" data-toggle="datetimepicker">
                                                        <div class="input-group-text"><i class="fa fa-clock-o"></i></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                @if ($schedule->pivot->start_time)
                                                    <button type="button" class="btn btn-success btn-sm day-off-btn">Working Day</button>
                                                @else
                                                    <button type="button" class="btn btn-warning btn-sm day-off-btn">Day Off</button>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label>How many days this schedule will continue? <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="to_date" name="to_date" value="{{$preRota->end_date}}"/>
                                    </div>
                                </div>
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
    var upurl = "{{URL::to('/admin/prorota/update')}}";

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

    // Function to clear error on input change
    $(document).on('input change', '.start-time, .end-time', function () {
        $(this).removeClass('is-invalid').css('border-color', '');
        if ($('.is-invalid').length === 0) {
            $('.errmsg').html('');
        }
    });

    // Function to display success message
    function showSuccess(message) {
        pagetop();
        $('.errmsg').html(`<div class="alert alert-success">${message}</div>`);
    }

    // Function to scroll to top
    function pagetop() {
        $('html, body').animate({ scrollTop: 0 }, 'slow');
    }

    $("#addBtn").click(function (e) {
        e.preventDefault();

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

        // Validate time fields
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
                    return false;
                }
                if (!end) {
                    showError(`End time is required for Row ${index + 1}.`, endInput[0]);
                    timeError = true;
                    return false;
                }
                const startDate = new Date(`1970-01-01T${start}:00`);
                const endDate = new Date(`1970-01-01T${end}:00`);
                if (startDate >= endDate) {
                    showError(`End time must be after Start time (Row ${index + 1}).`, endInput[0]);
                    timeError = true;
                    return false;
                }
            }
        });

        if (timeError) return;

        // Prepare FormData
        const form_data = new FormData();
        const employeeIds = $('#employee_id').val() || [];

        // Append basic fields
        employeeIds.forEach(emp => form_data.append('employee_id[]', emp));
        form_data.append("start_date", $("#start_date").val());
        form_data.append("to_date", $("#to_date").val());
        form_data.append("type", $("#etype").val());
        form_data.append("details", $("#details").val());
        form_data.append("codeid", $("#codeid").val());

        // Append schedule data with employee_ids
        $("input[name='dates[]']").each((i, el) => {
            form_data.append("dates[]", el.value);
            form_data.append("day_names[]", $("input[name='day_names[]']").eq(i).val());
            form_data.append("start_times[]", $("input[name='start_times[]']").eq(i).val());
            form_data.append("end_times[]", $("input[name='end_times[]']").eq(i).val());
            // Append the first employee ID for each date (adjust if multiple employees needed)
            form_data.append("employee_ids[]", employeeIds[0]);
        });

        // Debug: Log FormData
        for (let pair of form_data.entries()) {
            console.log(pair[0] + ": " + pair[1]);
        }

        // AJAX Submit
        $.ajax({
            url: upurl,
            type: "POST",
            dataType: 'json',
            contentType: false,
            processData: false,
            data: form_data,
            success: function (d) {
                console.log(d);
                pagetop();
                if (d.status == 422) {
                    $('.errmsg').html('<div class="alert alert-danger">' + d.message + '</div>');
                } else {
                    showSuccess('Data updated successfully.');
                }
            },
            error: function (xhr) {
                console.error('Error:', xhr.responseText);
                showError('An error occurred. Please try again.');
            }
        });
    });

    function formatDate(dateStr) {
        if (!dateStr) return '';
        const date = new Date(dateStr);
        return date.toISOString().split('T')[0];
    }
});
</script>

<script>
$(document).ready(function() {
    // Trigger AJAX on to_date change
    $('#to_date').on('change', function() {
        let start_date = $('#start_date').val();
        let end_date = $('#to_date').val();
        let employee_ids = $('#employee_id').val() || [];

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

<!-- Moment.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>

<!-- Tempus Dominus Bootstrap 4 -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/css/tempusdominus-bootstrap-4.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/js/tempusdominus-bootstrap-4.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize timepickers for existing rows
    initTimepickers();
    addEndTimeValidation();
    addDayOffToggle();

    document.getElementById('start_date').addEventListener('change', function () {
        const startDate = new Date(this.value);
        const scheduleContainer = document.getElementById('weekly-schedule');
        scheduleContainer.innerHTML = '';

        if (isNaN(startDate)) return;

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
});
</script>

@endsection