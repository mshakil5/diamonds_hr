@extends('admin.layouts.admin')

@section('content')

<section class="content">
  <div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">

          <div class="card">
              <div class="card-body">
                  <div class="tab-content pt-2" id="myTabjustifiedContent">

                      <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">

                          <form id="myForm">
                              <div class="row my-4">
                                  <div class="col-lg-8">
                                      <label for="staff_id">Employee</label>
                                      <div class="mt-2">
                                      <select class="form-control select2 my-2" id="staff_id" name="staff_id[]" multiple>
                                          <option value="" disabled>Choose Employee</option>
                                          @foreach($employees as $employee)
                                            @if($employee->user_id)
                                                <option value="{{ $employee->user_id }}">{{ $employee->name }}</option>
                                            @endif
                                          @endforeach
                                      </select>
                                      </div>
                                  </div>

                                  <div class="col-lg-4">
                                      <label for="schedule_type">Schedule type</label>
                                      <div class="mt-2">
                                      <select class="form-control select2 my-2" id="schedule_type" name="schedule_type">
                                          <option value="" disabled>Choose type</option>
                                          <option value="Regular">Regular</option>
                                          <option value="Authorized Holiday">Authorized Holiday</option>
                                          <option value="Unauthorized Holiday">Unauthorized Holiday</option>
                                      </select>
                                      </div>
                                  </div>
                              </div>

                              <!-- Regular Schedule: Day and Time Table -->
                              <div class="row align-items-center" id="regular_schedule" style="display: none;">
                                  <div class="col-lg-8">
                                      <table class="table table-bordered">
                                          <thead>
                                              <tr>
                                                  <th>Day</th>
                                                  <th>Start time</th>
                                                  <th>End Time</th>
                                              </tr>
                                          </thead>
                                          <tbody>
                                              <tr>
                                                  <td><input type="text" name="day[]" class="form-control" value="Monday" readonly></td>
                                                  <td><input type="time" name="start_time[]" class="form-control" value="09:00"></td>
                                                  <td><input type="time" name="end_time[]" class="form-control" value="17:00"></td>
                                              </tr>
                                              <tr>
                                                  <td><input type="text" name="day[]" class="form-control" value="Tuesday" readonly></td>
                                                  <td><input type="time" name="start_time[]" class="form-control" value="09:00"></td>
                                                  <td><input type="time" name="end_time[]" class="form-control" value="17:00"></td>
                                              </tr>
                                              <tr>
                                                  <td><input type="text" name="day[]" class="form-control" value="Wednesday" readonly></td>
                                                  <td><input type="time" name="start_time[]" class="form-control" value="09:00"></td>
                                                  <td><input type="time" name="end_time[]" class="form-control" value="17:00"></td>
                                              </tr>
                                              <tr>
                                                  <td><input type="text" name="day[]" class="form-control" value="Thursday" readonly></td>
                                                  <td><input type="time" name="start_time[]" class="form-control" value="09:00"></td>
                                                  <td><input type="time" name="end_time[]" class="form-control" value="17:00"></td>
                                              </tr>
                                              <tr>
                                                  <td><input type="text" name="day[]" class="form-control" value="Friday" readonly></td>
                                                  <td><input type="time" name="start_time[]" class="form-control" value="09:00"></td>
                                                  <td><input type="time" name="end_time[]" class="form-control" value="17:00"></td>
                                              </tr>
                                              <tr>
                                                  <td><input type="text" name="day[]" class="form-control" value="Saturday" readonly></td>
                                                  <td><input type="time" name="start_time[]" class="form-control" value="09:00"></td>
                                                  <td><input type="time" name="end_time[]" class="form-control" value="17:00"></td>
                                              </tr>
                                              <tr>
                                                  <td><input type="text" name="day[]" class="form-control" value="Sunday" readonly></td>
                                                  <td><input type="time" name="start_time[]" class="form-control"></td>
                                                  <td><input type="time" name="end_time[]" class="form-control"></td>
                                              </tr>
                                          </tbody>
                                      </table>
                                  </div>
                              </div>

                              <!-- Holiday Schedule: From and To Date -->
                              <div class="row align-items-center" id="holiday_schedule" style="display: none;">
                                  <div class="col-lg-4">
                                      <label for="from_date">From Date</label>
                                      <input type="date" name="from_date" id="from_date" class="form-control">
                                  </div>
                                  <div class="col-lg-4">
                                      <label for="to_date">To Date</label>
                                      <input type="date" name="to_date" id="to_date" class="form-control">
                                  </div>
                              </div>

                              <div class="row">
                                  <div class="col-lg-4 mx-auto text-center">
                                      <a href="{{ route('prorota') }}" class="btn btn-warning btn-sm">Cancel</a>
                                      <button id="clearButton" class="btn btn-primary btn-sm">Clear</button>
                                      <button id="saveButton" class="btn btn-success btn-sm">Save</button>
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
</section>

@endsection

@section('script')

<script>
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
</script>

<!-- Staff Start -->
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
                $('#from_date, #to_date').val(''); // Clear date fields
            } else if (scheduleType === 'Authorized Holiday' || scheduleType === 'Unauthorized Holiday') {
                $('#regular_schedule').hide();
                $('#holiday_schedule').show();
                $('input[name="start_time[]"], input[name="end_time[]"]').val(''); // Clear time fields
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

        $('#saveButton').click(function (event) {
            event.preventDefault();

            var formData = new FormData($('#myForm')[0]);

            $.ajax({
                url: "{{URL::to('/admin/prorota')}}",
                type: 'POST',
                data: formData,
                async: false,
                success: function (response) {
                    toastr.success("Prorota created successfully", "Success");
                    setTimeout(function() {
                        window.location.href = "{{ route('prorota') }}";
                    }, 2000);
                },
                error: function (xhr, status, error) {
                    console.error("Error occurred: " + error);
                    if (xhr.responseJSON.status == 422) {
                        console.log(xhr.responseJSON.errors);
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

        $('#clearButton').click(function (event) {
            event.preventDefault();
            $('#myForm')[0].reset();
            $('#staff_id').val(null).trigger('change'); // Clear staff_id Select2 selection
            $('#schedule_type').val(null).trigger('change'); // Clear schedule_type Select2 selection
            toggleScheduleFields(); // Reset visibility of schedule fields
        });
    });
</script>
<!-- Staff End -->

@endsection