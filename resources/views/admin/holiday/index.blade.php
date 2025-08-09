@extends('admin.layouts.admin')

@section('content')
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
                                        <label>From Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="from_date" name="from_date" />
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>To Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="to_date" name="to_date"/>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>Employee <span class="text-danger">*</span></label>
                                        <select class="form-control select2" id="employee_id" name="employee_id">
                                            <option value="">Select Employee</option>
                                            @foreach ($employees as $employee)
                                            <option value="{{$employee->id}}">{{$employee->name}} - {{$employee->id}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>Type <span class="text-danger">*</span></label>
                                        <select class="form-control" id="employee_type" name="employee_type">
                                            <option value="">Select Type</option>
                                            <option value="Authorized holiday">Authorized holiday</option>
                                            <option value="Unauthorized holiday">Unauthorized holiday</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label>Note</label>
                                        <textarea class="form-control" name="details" id="details" cols="30" rows="1"></textarea>
                                    </div>
                                </div>
                                <div class="col-sm-12 perrmsg"></div>
                                <div id="prerotaContainer" class="col-sm-12"></div>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer">
                        <button type="submit" id="addBtn" class="btn btn-secondary" value="Create">Create</button>
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
                        <h3 class="card-title">All Holiday Record</h3>
                    </div>
                    <div class="card-body">
                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Sl</th>
                                    <th>Created Date</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Total Holiday</th>
                                    <th>Employee</th>
                                    <th>Type</th>
                                    <th>Branch</th>
                                    <th>Details</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $key => $data)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ \Carbon\Carbon::parse($data->date)->format('d-m-Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($data->from_date)->format('d-m-Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($data->to_date)->format('d-m-Y') }}</td>
                                    <td><span class="btn btn-warning btn-sm">{{ $data->holidayDetail->count() }}</span></td>
                                    <td>{{ $data->employee->name }}</td>
                                    <td>{{ $data->type }}</td>
                                    <td>{{ $data->branch->name ?? '' }}</td>
                                    <td>{{ $data->details }}</td>
                                    <td>
                                        @if (auth()->user()->canDo(12))
                                        <a id="EditBtn" rid="{{ $data->id }}"><i class="fa fa-edit" style="color: #2196f3;font-size:16px;"></i></a>
                                        @endif
                                        @if (auth()->user()->canDo(13))
                                        <a id="deleteBtn" rid="{{ $data->id }}"><i class="fa fa-trash-o" style="color: red;font-size:16px;"></i></a>
                                        @endif
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
<!-- Moment.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>

<!-- Tempus Dominus Bootstrap 4 -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/css/tempusdominus-bootstrap-4.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/js/tempusdominus-bootstrap-4.min.js"></script>

<script>
    $(document).ready(function() {
        initTimepickers();
        addDayOffToggle();
        addEndTimeValidation();
        
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        var url = "{{URL::to('/admin/holidays')}}";
        var upurl = "{{URL::to('/admin/holidays/update')}}";

        function showError(message) {
            $('.errmsg').html('<div class="alert alert-danger">' + message + '</div>');
        }

        function showSuccess(message) {
            $('.errmsg').html('<div class="alert alert-success">' + message + '</div>');
        }

        function reloadPage(timeout) {
            setTimeout(function() { location.reload(); }, timeout);
        }

        function pagetop() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        $("#addBtn").click(function() {
            pagetop();
            var isUpdate = $(this).val() === 'Update';
            var requiredFields = ['#from_date', '#to_date', '#employee_id', '#employee_type'];
            for (var i = 0; i < requiredFields.length; i++) {
                if ($(requiredFields[i]).val() === '') {
                    showError('Please fill all required fields.');
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

            var form_data = new FormData();
            form_data.append("from_date", $("#from_date").val());
            form_data.append("to_date", $("#to_date").val());
            form_data.append("employee_type", $("#employee_type").val());
            form_data.append("employee_id", $("#employee_id").val());
            form_data.append("details", $("#details").val());
            if (isUpdate) {
                form_data.append("codeid", $("#codeid").val());
            }

            var holidayDates = [];
            $('input[name="make_holiday[]"]:checked').each(function() {
                holidayDates.push($(this).val());
            });
            form_data.append("holiday_dates", JSON.stringify(holidayDates));

            for (let pair of form_data.entries()) {
                console.log(pair[0] + ": " + pair[1]);
            }

            var available_prerota = $('#available_prerota').val();
            if (available_prerota == 1 && !$('#is_prorota').is(':checked')) {
                showError('Please check the prerota checkbox if you want to continue.');
                return;
            }

            $.ajax({
                url: isUpdate ? upurl : url,
                method: "POST",
                contentType: false,
                processData: false,
                data: form_data,
                success: function(d) {
                    if (d.status == 422) {
                        showError(d.message);
                    } else {
                        showSuccess(isUpdate ? 'Data updated successfully.' : 'Data created successfully.');
                        reloadPage(2000);
                    }
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                    showError('An error occurred. Please try again.');
                }
            });
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
            $.ajax({
                url: info_url,
                method: "GET",
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
            $("#from_date").val(data.holiday.from_date);
            $("#to_date").val(data.holiday.to_date);
            $("#employee_id").val(data.holiday.employee_id).trigger('change');
            $("#employee_type").val(data.holiday.type);
            $("#details").val(data.holiday.details);
            $("#codeid").val(data.holiday.id);
            $("#prerotaContainer").html(data.prerota);
            $("#addBtn").val('Update');
            $("#addBtn").html('Update');
            $("#header-title").html('Update data');
            $("#addThisFormContainer").show(300);
            $("#newBtn").hide(100);
            
            initTimepickers();
            addDayOffToggle();
            addEndTimeValidation();
            
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

        $("#employee_id").change(function() {
            var employee_id = $(this).val();
            var from_date = $("#from_date").val();
            var to_date = $("#to_date").val();
            $("#prerotaContainer").html('');

            if (from_date === '' || to_date === '') {
                showError('Please select From Date and To Date first.');
                return;
            }
            console.log(employee_id);

            if (employee_id) {
                $.ajax({
                    url: "{{ route('admin.employee.prorota') }}",
                    type: "GET",
                    data: { employee_id: employee_id, from_date: from_date, to_date: to_date },
                    statusCode: {
                        400: function(response) {
                            handleResponse(response.responseJSON);
                        },
                        404: function(response) {
                            handleResponse(response.responseJSON);
                        }
                    },
                    success: function(data) {
                        console.log(data);
                        handleResponse(data);
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', xhr.responseText);
                        $(".perrmsg").html('<div class="alert alert-danger">An error occurred while fetching pre-rota data. Please try again.</div>');
                    }
                });

                function handleResponse(data) {
                    if (data.status === 400 || data.status === 404) {
                        $(".perrmsg").html(`<div class="alert alert-danger">${data.message}</div>`);
                    } else if (data.status === 200) {
                        $("#prerotaContainer").html(data.prerota);
                        initTimepickers();
                        addDayOffToggle();
                        addEndTimeValidation();
                    } else {
                        $(".perrmsg").html(`<div class="alert alert-danger">${data.message || 'Unexpected response from server.'}</div>`);
                    }
                }
            }
        });

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

        function initTimepickers() {
            $('.timepicker').datetimepicker({
                format: 'HH:mm',
                stepping: 30,
                useCurrent: false,
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

        function addDayOffToggle() {
            document.querySelectorAll('.day-off-btn').forEach(button => {
                button.addEventListener('click', function () {
                    const row = this.closest('.row');
                    const startInput = row.querySelector('[name="start_times[]"]');
                    const endInput = row.querySelector('[name="end_times[]"]');
                    const makeHolidayBtn = row.querySelector('.make-holiday-btn');

                    const isDisabled = startInput.disabled;

                    if (isDisabled) {
                        startInput.disabled = false;
                        endInput.disabled = false;
                        makeHolidayBtn.style.display = 'inline-block';
                        this.classList.remove('btn-warning');
                        this.classList.add('btn-success');
                        this.textContent = 'Working Day';
                    } else {
                        startInput.disabled = true;
                        endInput.disabled = true;
                        startInput.value = '';
                        endInput.value = '';
                        makeHolidayBtn.style.display = 'none';
                        this.classList.remove('btn-success');
                        this.classList.add('btn-warning');
                        this.textContent = 'Day Off';
                    }
                });
            });

            document.querySelectorAll('.make-holiday-btn input[type="checkbox"]').forEach(checkbox => {
                checkbox.addEventListener('change', function () {
                    const row = this.closest('.row');
                    const startInput = row.querySelector('[name="start_times[]"]');
                    const endInput = row.querySelector('[name="end_times[]"]');
                    const otherButton = row.querySelector('.day-off-btn');

                    if (this.checked) {
                        startInput.disabled = true;
                        endInput.disabled = true;
                        startInput.value = '';
                        endInput.value = '';
                        otherButton.style.display = 'none';
                    } else {
                        startInput.disabled = false;
                        endInput.disabled = false;
                        otherButton.style.display = 'inline-block';
                    }
                });
            });
        }
    });
</script>
@endsection