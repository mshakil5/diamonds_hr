@extends('admin.layouts.admin')

@section('content')

<section class="section">
    <div class="row">
        <div class="col-lg-12 px-0 border shadow-sm">
            <div class="row px-3">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="tab-content pt-2" id="myTabjustifiedContent">
                                <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                                    <form id="myForm">
                                        <input type="hidden" name="prorota_id" id="prorota_id" value="{{ $data->id }}">
                                        <div class="row my-4">
                                            <div class="col-lg-8">
                                                <label for="staff_id">Employee</label>
                                                <div class="mt-2">
                                                    <select class="form-control select2 my-2" id="staff_id" name="staff_id[]" multiple disabled>
                                                        <option value="" disabled>Choose Employee</option>
                                                        @foreach($employees as $employee)
                                                            @if($employee->user_id)
                                                                <option value="{{ $employee->user_id }}" {{ $data->staff_id == $employee->user_id ? 'selected' : '' }}>{{ $employee->name }}</option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-4">
                                                <label for="schedule_type">Schedule Type</label>
                                                <div class="mt-2">
                                                    <select class="form-control select2 my-2" id="schedule_type" name="schedule_type">
                                                        <option value="" disabled>Choose type</option>
                                                        <option value="Regular" {{ $data->schedule_type == 'Regular' ? 'selected' : '' }}>Regular</option>
                                                        <option value="Authorized Holiday" {{ $data->schedule_type == 'Authorized Holiday' ? 'selected' : '' }}>Authorized Holiday</option>
                                                        <option value="Unauthorized Holiday" {{ $data->schedule_type == 'Unauthorized Holiday' ? 'selected' : '' }}>Unauthorized Holiday</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Regular Schedule: Day and Time Table -->
                                        <div class="row align-items-center" id="regular_schedule" style="display: {{ $data->schedule_type == 'Regular' ? 'block' : 'none' }};">
                                            <div class="col-lg-8">
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>Day</th>
                                                            <th>Start Time</th>
                                                            <th>End Time</th>
                                                            <th>
                                                                <button class="btn btn-secondary add-new-day" type="button">+</button>
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="scheduleContainer">
                                                        @php
                                                            $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                                                            $existingDetails = $data->prorotaDetail->keyBy('day');
                                                        @endphp
                                                        @foreach ($days as $day)
                                                            @php
                                                                $detail = $existingDetails->get($day);
                                                                $start_time = $detail ? $detail->start_time : ($day == 'Sunday' ? '' : '09:00');
                                                                $end_time = $detail ? $detail->end_time : ($day == 'Sunday' ? '' : '17:00');
                                                                $detail_id = $detail ? $detail->id : '';
                                                            @endphp
                                                            <tr>
                                                                <td>
                                                                    <input type="text" name="day[]" class="form-control" value="{{ $day }}" readonly>
                                                                    <input type="hidden" name="prorotaDetail_id[]" class="form-control" value="{{ $detail_id }}">
                                                                </td>
                                                                <td><input type="time" name="start_time[]" class="form-control" value="{{ $start_time }}"></td>
                                                                <td><input type="time" name="end_time[]" class="form-control" value="{{ $end_time }}"></td>
                                                                <td><button class="btn btn-secondary remove-schedule" style="margin-left: 10px;" type="button">-</button></td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <!-- Holiday Schedule: From and To Date -->
                                        <div class="row align-items-center" id="holiday_schedule" style="display: {{ in_array($data->schedule_type, ['Authorized Holiday', 'Unauthorized Holiday']) ? 'block' : 'none' }};">
                                            <div class="col-lg-4">
                                                <label for="from_date">From Date</label>
                                                <input type="date" name="from_date" id="from_date" class="form-control" value="{{ $data->prorotaDetail->first()->from_date ?? '' }}">
                                            </div>
                                            <div class="col-lg-4">
                                                <label for="to_date">To Date</label>
                                                <input type="date" name="to_date" id="to_date" class="form-control" value="{{ $data->prorotaDetail->first()->to_date ?? '' }}">
                                                <input type="hidden" name="prorotaDetail_id[]" class="form-control" value="{{ $data->prorotaDetail->first()->id ?? '' }}">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-4 mx-auto text-center">
                                                <a href="{{ route('prorota') }}" class="btn btn-primary btn-sm">Cancel</a>
                                                <button id="upButton" class="btn btn-success btn-sm">Update</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@section('script')

<script>
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
</script>

<script>
    $(document).ready(function () {
        // Initialize Select2 for multiple staff selection
        $('#staff_id').select2({
            placeholder: "Choose Employee",
            allowClear: true
        });

        // Initialize Select2 for schedule type
        $('#schedule_type').select2({
            placeholder: "Choose type",
            allowClear: true
        });

        // Function to toggle schedule fields based on schedule type
        function toggleScheduleFields() {
            var scheduleType = $('#schedule_type').val();
            if (scheduleType === 'Regular') {
                $('#regular_schedule').show();
                $('#holiday_schedule').hide();
                $('#from_date, #to_date').val('');
            } else if (scheduleType === 'Authorized Holiday' || scheduleType === 'Unauthorized Holiday') {
                $('#regular_schedule').hide();
                $('#holiday_schedule').show();
                $('input[name="start_time[]"], input[name="end_time[]"]').val('');
            } else {
                $('#regular_schedule').hide();
                $('#holiday_schedule').hide();
                $('#from_date, #to_date').val('');
                $('input[name="start_time[]"], input[name="end_time[]"]').val('');
            }
        }

        // Trigger toggle on schedule type change
        $('#schedule_type').on('change', function () {
            toggleScheduleFields();
        });

        // Initial toggle on page load
        toggleScheduleFields();

        // Add new day row
        $(document).on('click', '.add-new-day', function () {
            var inputField = '<tr><td><select name="day[]" class="form-control"><option value="Monday">Monday</option><option value="Tuesday">Tuesday</option><option value="Wednesday">Wednesday</option><option value="Thursday">Thursday</option><option value="Friday">Friday</option><option value="Saturday">Saturday</option><option value="Sunday">Sunday</option></select><input type="hidden" name="prorotaDetail_id[]" class="form-control" value=""></td><td><input type="time" name="start_time[]" class="form-control" value="09:00"></td><td><input type="time" name="end_time[]" class="form-control" value="17:00"></td><td><button class="btn btn-secondary remove-schedule" style="margin-left: 10px;" type="button">-</button></td></tr>';
            $('#scheduleContainer').append(inputField);
        });

        // Remove schedule row
        $(document).on('click', '.remove-schedule', function () {
            $(this).closest('tr').remove();
        });

        // Update form submission
        $('#upButton').click(function (event) {
            event.preventDefault();

            var formData = new FormData($('#myForm')[0]);

            $.ajax({
                url: "{{ route('prorota.update') }}",
                type: 'POST',
                data: formData,
                async: false,
                success: function (response) {
                    toastr.success("Prorota updated successfully", "Success");
                    setTimeout(function() {
                        window.location.href = "{{ route('prorota') }}";
                    }, 2000);
                },
                error: function (xhr, status, error) {
                    console.error("Error occurred: " + error);
                    if (xhr.responseJSON.status == 422) {
                        toastr.error(JSON.stringify(xhr.responseJSON.errors), 'Error');
                    } else {
                        var errorMessage = "";
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            $.each(xhr.responseJSON.errors, function (key, value) {
                                errorMessage += value.join(", ") + "<br>";
                            });
                            toastr.error(errorMessage, 'Error');
                        } else {
                            errorMessage = "An error occurred. Please try again later.";
                            toastr.error(errorMessage, 'Error');
                        }
                    }
                },
                cache: false,
                contentType: false,
                processData: false
            });
            return false;
        });
    });
</script>

@endsection