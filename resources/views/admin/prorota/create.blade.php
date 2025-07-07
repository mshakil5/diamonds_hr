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
                                  
                                  <div class="col-lg-4">
                                      <label for="country">Employee</label>
                                      <div class="mt-2">
                                      <select class="form-control select2 my-2" id="staff_id" name="staff_id">
                                          <option value="" selected disabled>Choose Employee</option>
                                          @foreach($employees as $employee)
                                            @if($employee->user_id)
                                                <option value="{{ $employee->user_id }}">{{ $employee->name }}</option>
                                            @endif
                                          @endforeach
                                      </select>
                                      </div>
                                  </div>
                              </div>
                              <div class="row align-items-center">
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
                  console.log(xhr.responseText);
                    console.error("Error occurred: " + error);
                    if (xhr.responseJSON.status == 423) {
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

        $('#clearButton').click(function () {
            event.preventDefault();
            $('#myForm')[0].reset();
        });
    });
</script>
<!-- Staff End -->

@endsection