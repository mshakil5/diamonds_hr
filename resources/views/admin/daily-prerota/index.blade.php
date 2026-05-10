@extends('admin.layouts.admin')

@section('content')
<section class="content mt-3" id="addThisFormContainer">
    <div class="container-fluid">
        <div class="row justify-content-md-center">
            <div class="col-md-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title" id="header-title">Add New Pre-Rota</h3>
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
                                        <input type="date" class="form-control" id="to_date" name="to_date" />
                                    </div>
                                </div>
                                
                                <!-- NEW: Branch Dropdown -->
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>Branch <span class="text-danger">*</span></label>
                                        <select class="form-control select2" id="branch_id" name="branch_id">
                                            <option value="">Select Branch</option>
                                            @foreach ($branches as $branch)
                                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- UPDATED: Employee Dropdown (Empty initially) -->
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>Employee <span class="text-danger">*</span></label>
                                        <select class="form-control select2" id="employee_id" name="employee_id">
                                            <option value="">Select Branch First</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label>General Note</label>
                                        <input type="text" class="form-control" name="note" id="note" placeholder="Optional main note">
                                    </div>
                                </div>
                                
                                <div class="col-sm-12 perrmsg"></div>
                                
                                <!-- Dynamic Table Area -->
                                <div class="col-sm-12">
                                    <div class="row font-weight-bold mb-2" style="padding-left:15px;">
                                        <div class="col-md-2">Employee Name</div>
                                        <div class="col-md-2">Date</div>
                                        <div class="col-md-1">Day</div>
                                        <div class="col-md-1">Start</div>
                                        <div class="col-md-1">End</div>
                                        <div class="col-md-3">Note</div>
                                        <div class="col-md-2">Action</div>
                                    </div>
                                </div>
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
    <!-- Keep your existing table exactly as it was -->
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">All Pre-Rota Records</h3>
                    </div>
                    <div class="card-body">
                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Sl</th>
                                    <th>Created Date</th>
                                    <th>Date Range</th>
                                    <th>Employee</th>
                                    <th>Total Days</th>
                                    <th>Branch</th>
                                    <th>Note</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $key => $item)
                                @php
                                    $startDate = \Carbon\Carbon::parse($item->date)->format('d-m-Y');
                                    $endDate = $item->details->max('date') ? \Carbon\Carbon::parse($item->details->max('date'))->format('d-m-Y') : $startDate;
                                    $totalEntries = $item->details->count();
                                    $employeeName = $item->details->first()->staff->name ?? 'N/A';
                                @endphp
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ \Carbon\Carbon::parse($item->created_at)->format('d-m-Y') }}</td>
                                    <td>{{ $startDate }} <b>to</b> {{ $endDate }}</td>
                                    <td>{{ $employeeName }}</td>
                                    <td><span class="btn btn-warning btn-sm">{{ $totalEntries }}</span></td>
                                    <td>{{ $item->branch->name ?? '' }}</td>
                                    <td>{{ Str::limit($item->note, 50) }}</td>
                                    <td>
                                        <a id="EditBtn" rid="{{ $item->id }}"><i class="fa fa-edit" style="color: #2196f3;font-size:16px;"></i></a>
                                        <a id="deleteBtn" rid="{{ $item->id }}"><i class="fa fa-trash-o" style="color: red;font-size:16px;"></i></a>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/css/tempusdominus-bootstrap-4.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/js/tempusdominus-bootstrap-4.min.js"></script>

<script>
    $(document).ready(function() {
        initTimepickers();
        addRemoveRow();
        addEndTimeValidation();

        // Init Select2
        $('.select2').select2();

        // PASS ALL EMPLOYEES TO JAVASCRIPT ARRAY
        const allEmployees = @json($employees);

        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        var url   = "{{ URL::to('/admin/daily-pre-rotas') }}";
        var upurl = "{{ URL::to('/admin/daily-pre-rotas/update') }}";

        function showError(message) { $('.errmsg').html('<div class="alert alert-danger">' + message + '</div>'); }
        function showSuccess(message) { $('.errmsg').html('<div class="alert alert-success">' + message + '</div>'); }
        function reloadPage(timeout) { setTimeout(function() { location.reload(); }, timeout); }
        function pagetop() { window.scrollTo({ top: 0, behavior: 'smooth' }); }

        // NEW: FILTER EMPLOYEES WHEN BRANCH CHANGES
        $("#branch_id").change(function() {
            var branchId = $(this).val();
            var $empSelect = $("#employee_id");
            
            // Reset employee dropdown
            $empSelect.empty().trigger('change');
            $empSelect.append('<option value="">Select Employee</option>');
            
            // Clear schedule if branch changes
            $("#prerotaContainer").html('');

            if (!branchId) {
                $empSelect.trigger('change');
                return;
            }

            // Filter employees from JS array
            var filteredEmployees = allEmployees.filter(emp => emp.branch_id == branchId);
            
            filteredEmployees.forEach(emp => {
                $empSelect.append(`<option value="${emp.id}">${emp.name}</option>`);
            });
            
            $empSelect.trigger('change'); // Refresh Select2
        });

        // Submit
        $("#addBtn").click(function() {
            pagetop();
            var isUpdate = $(this).val() === 'Update';

            if ($('#from_date').val() === '' || $('#to_date').val() === '') {
                showError('Please select From Date and To Date.');
                return;
            }
            if ($('#branch_id').val() === '') {
                showError('Please select a Branch.');
                return;
            }
            if ($('#employee_id').val() === '') {
                showError('Please select an Employee.');
                return;
            }
            if ($("input[name='staff_ids[]']").length === 0) {
                showError('No schedule rows found.');
                return;
            }

            var timeError = false;
            $(".schedule-row").each(function(index) {
                var startInput = $(this).find(".start-time");
                var endInput   = $(this).find(".end-time");

                if (!startInput.val() || !endInput.val()) {
                    showError('Start and End time are required for all rows.');
                    timeError = true;
                    return false;
                }
                var startDate = new Date('2025-01-01T' + startInput.val() + ':00');
                var endDate   = new Date('2025-01-01T' + endInput.val() + ':00');
                if (startDate >= endDate) {
                    showError('End time must be after Start time in row ' + (index + 1) + '.');
                    timeError = true;
                    return false;
                }
            });
            if (timeError) return;

            var form_data = new FormData();
            form_data.append("from_date", $("#from_date").val());
            form_data.append("to_date",   $("#to_date").val());
            form_data.append("note",      $("#note").val());
            form_data.append("employee_id", $("#employee_id").val()); // Always send
            
            if (isUpdate) {
                form_data.append("codeid", $("#codeid").val());
            }

            $("input[name='staff_ids[]']").each((i, el)   => form_data.append("staff_ids[]",   el.value));
            $("input[name='dates[]']").each((i, el)        => form_data.append("dates[]",       el.value));
            $("input[name='start_times[]']").each((i, el)  => form_data.append("start_times[]", el.value));
            $("input[name='end_times[]']").each((i, el)    => form_data.append("end_times[]",   el.value));
            $("input[name='detail_notes[]']").each((i, el) => form_data.append("detail_notes[]",el.value));

            $.ajax({
                url: isUpdate ? upurl : url,
                method: "POST",
                contentType: false,
                processData: false,
                data: form_data,
                success: function(d) {
                    if (d.status == 422) showError(d.message);
                    else { showSuccess(isUpdate ? 'Data updated.' : 'Data created.'); reloadPage(2000); }
                },
                error: function() { showError('An error occurred.'); }
            });
        });

        // Edit
        $("#contentContainer").on('click', '#EditBtn', function() {
            var codeid  = $(this).attr('rid');
            $.get(url + '/' + codeid + '/edit', {}, function(d) {
                $("#from_date").val(d.rota.date);
                var dates = d.rota.details.map(dd => dd.date).sort();
                if (dates.length > 0) $("#to_date").val(dates[dates.length - 1]);
                
                // 1. Set Branch First
                $("#branch_id").val(d.branch_id).trigger('change');
                
                // 2. Wait a tiny bit for JS to populate the employee dropdown, THEN set employee
                setTimeout(function() {
                    $("#employee_id").val(d.staff_id).trigger('change');
                }, 150);

                $("#note").val(d.rota.note);
                $("#codeid").val(d.rota.id);
                $("#prerotaContainer").html(d.details_html);

                $("#addBtn").val('Update').html('Update');
                $("#header-title").html('Update Pre-Rota');
                $("#addThisFormContainer").show(300);

                initTimepickers();
                addRemoveRow();
                addEndTimeValidation();
                pagetop();
            });
        });

        // Delete
        $("#contentContainer").on('click', '#deleteBtn', function() {
            if (!confirm('Sure?')) return;
            $.ajax({
                url: url + '/' + $(this).attr('rid'), method: "GET", type: "DELETE",
                success: function(d) { showSuccess('Deleted.'); reloadPage(2000); },
                error: function() { showError('Error.'); }
            });
        });

        // Load schedule ONLY when Employee changes
        $("#employee_id").change(function() {
            loadStaffSchedule();
        });

        // If dates change while employee is selected, reload
        $("#from_date, #to_date").change(function() {
            if($("#employee_id").val() !== "") {
                loadStaffSchedule();
            }
        });

        function loadStaffSchedule() {
            var start_date = $("#from_date").val();
            var end_date   = $("#to_date").val();
            var employee_id = $("#employee_id").val();
            
            $("#prerotaContainer").html('');
            $(".perrmsg").html('');

            if (start_date === '' || end_date === '') {
                showError('Please select both dates first.');
                return;
            }
            if (employee_id === '') {
                return; 
            }

            $.ajax({
                url: "{{ route('admin.daily-prerotas.check-staff-schedule') }}",
                type: "GET",
                data: { employee_id: employee_id, start_date: start_date, end_date: end_date },
                success: function(data) {
                    if (data.success) {
                        $("#prerotaContainer").html(data.html);
                        initTimepickers();
                        addRemoveRow();
                        addEndTimeValidation();
                    } else {
                        $(".perrmsg").html('<div class="alert alert-danger">' + (data.message || 'Error.') + '</div>');
                    }
                },
                error: function(xhr) {
                    var msg = xhr.responseJSON?.message || 'Error loading schedule.';
                    $(".perrmsg").html('<div class="alert alert-danger">' + msg + '</div>');
                }
            });
        }

        function addRemoveRow() {
            $(document).off('click', '.remove-row').on('click', '.remove-row', function() {
                $(this).closest('.schedule-row').remove();
            });
        }

        $("#example1").DataTable({
            "responsive": true, "lengthChange": false, "autoWidth": false,
            "buttons": ["copy", "csv", "excel", "pdf", "print"]
        }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');

        function initTimepickers() {
            $('.timepicker').datetimepicker({
                format: 'HH:mm', stepping: 30, useCurrent: false,
                icons: { time: 'fa fa-clock', up: 'fa fa-chevron-up', down: 'fa fa-chevron-down', close: 'fa fa-times' }
            });
        }

        function addEndTimeValidation() {
            $(document).off('change', '.start-time').on('change', '.start-time', function() {
                var endInput = $(this).closest('.schedule-row').find('.end-time');
                endInput.attr('required', $(this).val() ? 'required' : false);
            });
        }
    });
</script>
@endsection