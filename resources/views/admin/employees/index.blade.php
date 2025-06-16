@extends('admin.layouts.admin')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tempusdominus-bootstrap-4@5.39.0/build/css/tempusdominus-bootstrap-4.min.css" />

<!-- Main content -->
<section class="content" id="newBtnSection">
    <div class="container-fluid">
        <div class="row">
            <div class="col-2">
                <button type="button" class="btn btn-secondary my-3" id="newBtn">Add new</button>
            </div>
        </div>
    </div>
</section>
<!-- /.content -->



<!-- Main content -->
<section class="content mt-3" id="addThisFormContainer">
    <div class="container-fluid">
        <div class="row justify-content-md-center">
            <!-- right column -->
            <div class="col-md-12">
                <!-- general form elements disabled -->
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title" id="header-title">Add new data</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <form id="createThisForm">
                            <div class="card mb-2">
                                <div class="card-header">
                                    <h3 class="card-title"> Basic Staff Information</h3>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">
                                    <input type="hidden" id="codeid">
                                    <div class="row">
                                        <div class="col-sm-3">
                                        <!-- text input -->
                                            <div class="form-group">
                                                <label>Staff ID *</label>
                                                <input type="text" class="form-control" id="employee_id" name="employee_id">
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                        <!-- text input -->
                                            <div class="form-group">
                                                <label>Name *</label>
                                                <input type="text" class="form-control" id="name" name="name">
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                        <!-- text input -->
                                            <div class="form-group">
                                                <label>Staff Type *</label>
                                                <select class="form-control" id="employee_type" name="employee_type">
                                                    <option value="">Select Staff Type</option>
                                                    <option value="full time">Full Time</option>
                                                    <option value="part time">Part Time</option>
                                                    <option value="casual">Casual</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                        <!-- text input -->
                                            <div class="form-group">
                                                <label>Phone</label>
                                                <input type="number" class="form-control" id="phone" name="phone">
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                        <!-- text input -->
                                            <div class="form-group">
                                                <label>Email</label>
                                                <input type="email" class="form-control" id="email" name="email">
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                        <!-- text input -->
                                            <div class="form-group">
                                                <label>Em. Contact Number</label>
                                                <input type="number" class="form-control" id="emergency_contact_number" name="emergency_contact_number">
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                        <!-- text input -->
                                            <div class="form-group">
                                                <label>Em. Contact Person</label>
                                                <input type="text" class="form-control" id="emergency_contact_person" name="emergency_contact_person">
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                        <!-- text input -->
                                            <div class="form-group">
                                                <label>Nation Insurance No</label>
                                                <input type="text" class="form-control" id="ni" name="ni">
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                        <!-- text input -->
                                            <div class="form-group">
                                                <label>Nationality</label>
                                                <input type="text" class="form-control" id="nationality" name="nationality">
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                        <!-- text input -->

                                            <div class="form-group">
                                                <label>Join Date</label>
                                                <div class="input-group date" id="reservationdatetime" data-target-input="nearest">
                                                    <input type="text" class="form-control datetimepicker-input" data-target="#reservationdatetime" id="join_date" name="join_date" />
                                                    <div class="input-group-append" data-target="#reservationdatetime" data-toggle="datetimepicker">
                                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                                    </div>
                                                </div>
                                            </div>


                                        </div>
                                        <div class="col-sm-6">
                                        <!-- text input -->
                                            <div class="form-group">
                                                <label>Image</label>
                                                <input type="file" class="form-control" id="image" name="image">
                                                <img id="preview-image" src="#" alt="" style="max-width: 300px; width: 100%; height: auto; margin-top: 20px;">
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                        <!-- text input -->
                                            <div class="form-group">
                                                <label>Address</label>
                                                <textarea class="form-control" name="address" id="address" cols="30" rows="2"></textarea>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <!-- /.card-body -->
                            </div>

                            <div class="card mb-2">
                                <div class="card-header">
                                    <h3 class="card-title">  Payment and holiday info</h3>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">

                                    <div class="row">
                                        <div class="col-sm-4">
                                        <!-- text input -->
                                            <div class="form-group">
                                                <label>Pay Rate *</label>
                                                <input type="number" class="form-control" id="pay_rate" name="pay_rate">
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                        <!-- text input -->
                                            <div class="form-group">
                                                <label>Tax Code *</label>
                                                <input type="text" class="form-control" id="tax_code" name="tax_code">
                                            </div>
                                        </div>
                                        
                                        <div class="col-sm-4">
                                        <!-- text input -->
                                            <div class="form-group">
                                                <label>Entitled Holiday *</label>
                                                <input type="number" class="form-control" id="entitled_holiday" name="entitled_holiday">
                                            </div>
                                        </div>
                                        
                                        
                                        <div class="col-sm-12">
                                        <!-- text input -->
                                            <div class="form-group">
                                                <label>Bank Details</label>
                                                <textarea class="form-control" name="bank_details" id="bank_details" cols="30" rows="2"></textarea>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <!-- /.card-body -->
                            </div>

                            <div class="card mb-2">
                                <div class="card-header">
                                    <h3 class="card-title"> User Login</h3>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">

                                    <div class="row">
                                        
                                        <div class="col-sm-3">
                                        <!-- text input -->
                                            <div class="form-group">
                                                <label>Username*</label>
                                                <input type="text" class="form-control" id="username" name="username">
                                            </div>
                                        </div>
                                        
                                        <div class="col-sm-3">
                                        <!-- text input -->
                                            <div class="form-group">
                                                <label>Password *</label>
                                                <input type="password" class="form-control" id="password" name="password">
                                            </div>
                                        </div>
                                        
                                    </div>

                                </div>
                                <!-- /.card-body -->
                            </div>
                        </form>
                    </div>

                    <!-- /.card-body -->
                    <div class="card-footer">
                        <button type="submit" id="addBtn" class="btn btn-secondary" value="Create">Create</button>
                        <button type="submit" id="FormCloseBtn" class="btn btn-default">Cancel</button>
                    </div>
                    <!-- /.card-footer -->
                    <!-- /.card-body -->
                </div>
            </div>
            <!--/.col (right) -->
        </div>
        <!-- /.row -->
    </div><!-- /.container-fluid -->
</section>
<!-- /.content -->


<!-- Main content -->
<section class="content" id="contentContainer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <!-- /.card -->

                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">All Data</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Sl</th>
                                    <th>Name</th>
                                    <th>Username</th>
                                    <th>Type</th>
                                    <th>Email/Phone</th>
                                    <th>Image</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($query as $key => $data)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{$data->name}}</td>
                                    <td>{{$data->username}}</td>
                                    <td>{{$data->employee_type}}</td>
                                    <td>{{$data->user->email}} <br> {{$data->phone}}</td>
                                    <td>
                                        @if ($data->user->photo)
                                        <a href="{{ asset('public'.$data->user->photo) }}" target="_blank">
                                            <img src="{{ asset('public'.$data->user->photo) }}" alt="" style="max-width: 100px; width: 100%; height: auto;">
                                        </a>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input toggle-status" id="customSwitchStatus{{ $data->id }}" data-id="{{ $data->id }}" {{ $data->status == 1 ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="customSwitchStatus{{ $data->id }}"></label>
                                        </div>
                                    </td>
                                    <td>
                                        <a id="EditBtn" rid="{{$data->id}}"><i class="fa fa-edit" style="color: #2196f3;font-size:16px;"></i></a>
                                        <a id="deleteBtn" rid="{{$data->id}}"><i class="fa fa-trash-o" style="color: red;font-size:16px;"></i></a>
                                    </td>
                                </tr>
                                @endforeach

                            </tbody>
                        </table>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </div>
    <!-- /.container-fluid -->
</section>
<!-- /.content -->

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
        var url = "{{URL::to('/admin/employees')}}";
        var upurl = "{{URL::to('/admin/employees/update')}}";
        // console.log(url);
        $("#addBtn").click(function() {
            if ($(this).val() == 'Create') {

                var requiredFields = [
                    '#employee_id',
                    '#name',
                    '#employee_type',
                    '#pay_rate',
                    '#tax_code',
                    '#entitled_holiday',
                    '#username',
                    '#password'
                ];
                for (var i = 0; i < requiredFields.length; i++) {
                    if ($(requiredFields[i]).val() === '') {
                        showError('Please fill all required fields.');
                        return;
                    }
                }


                var form_data = new FormData($('#createThisForm')[0]);

                var featureImgInput = document.getElementById('image');
                if (featureImgInput.files && featureImgInput.files[0]) {
                    form_data.append("photo", featureImgInput.files[0]);
                }
                $.ajax({
                    url: url,
                    method: "POST",
                    contentType: false,
                    processData: false,
                    data: form_data,
                    success: function(d) {
                        console.log(d);
                        showSuccess('Data created successfully.');
                        reloadPage(2000);
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                        showError('An error occurred. Please try again.');
                        
                    }
                });
            }
            //create  end
            //Update
            if ($(this).val() == 'Update') {

                var requiredFields = [
                    '#employee_id',
                    '#name',
                    '#employee_type',
                    '#pay_rate',
                    '#tax_code',
                    '#entitled_holiday',
                    '#username'
                ];
                for (var i = 0; i < requiredFields.length; i++) {
                    if ($(requiredFields[i]).val() === '') {
                        showError('Please fill all required fields.');
                        return;
                    }
                }


                var form_data = new FormData($('#createThisForm')[0]);

                var featureImgInput = document.getElementById('image');
                if (featureImgInput.files && featureImgInput.files[0]) {
                    form_data.append("photo", featureImgInput.files[0]);
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
                        console.log(d);
                        showSuccess('Data updated successfully.');
                        reloadPage(2000);
                    },
                    error: function(xhr, status, error) {
                        showError('An error occurred. Please try again.');
                        console.error(xhr.responseText);
                    }
                });
            }
            //Update
        });
        //Edit
        $("#contentContainer").on('click', '#EditBtn', function() {
            //alert("btn work");
            codeid = $(this).attr('rid');
            //console.log($codeid);
            info_url = url + '/' + codeid + '/edit';
            //console.log($info_url);
            $.get(info_url, {}, function(d) {
                populateForm(d);
                pagetop();
            });
        });
        //Edit  end
        //Delete
        $("#contentContainer").on('click', '#deleteBtn', function() {
            if (!confirm('Sure?')) return;
            codeid = $(this).attr('rid');
            info_url = url + '/' + codeid;
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
                    console.error(error);
                }
            });
        });
        //Delete  
        function populateForm(data) {
            $("#employee_id").val(data.employee_id);
            $("#name").val(data.name);
            $("#username").val(data.username);
            $("#phone").val(data.phone);
            $("#email").val(data.email);
            $("#employee_type").val(data.employee_type);
            $("#emergency_contact_number").val(data.emergency_contact_number);
            $("#emergency_contact_person").val(data.emergency_contact_person);
            $("#ni").val(data.ni);
            $("#nationality").val(data.nationality);
            $("#join_date").val(data.join_date);
            $("#address").val(data.address);
            $("#pay_rate").val(data.pay_rate);
            $("#tax_code").val(data.tax_code);
            $("#entitled_holiday").val(data.entitled_holiday);
            $("#bank_details").val(data.bank_details);
            var image = document.getElementById('preview-image');
            if (data.photo) { 
                image.src = data.photo;
            } else {
                image.src = "#";
            }
            $("#codeid").val(data.id);
            $("#addBtn").val('Update');
            $("#addBtn").html('Update');
            $("#header-title").html('Update new data');
            $("#addThisFormContainer").show(300);
            $("#newBtn").hide(100);
        }

        function clearform() {
            $('#createThisForm')[0].reset();
            $("#addBtn").val('Create');
            $('#preview-image').attr('src', '#');
            $("#header-title").html('Add new data');
        }

        $("#image").change(function(e){
            var reader = new FileReader();
            reader.onload = function(e){
                $("#preview-image").attr("src", e.target.result);
            };
            reader.readAsDataURL(this.files[0]);
        });

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

        $(document).on('change', '.toggle-status', function() {
            var userId = $(this).data('id');
            var status = $(this).prop('checked') ? 1 : 0;

            $.ajax({
                url: '/admin/users/' + userId + '/status',
                method: 'POST',
                data: {
                    status: status,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.status === 200) {
                        showSuccess(response.message);
                    } else {
                        showError('Failed to update status.');
                    }
                },
                error: function(xhr, status, error) {
                    showError('An error occurred. Please try again.');
                }
            });
        });



    });
</script>
<!-- JS to initialize picker -->
<script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/tempusdominus-bootstrap-4@5.39.0/build/js/tempusdominus-bootstrap-4.min.js"></script>

<!-- Initialize picker with DD-MM-YYYY HH:mm format -->
<script type="text/javascript">
    $(function () {
        $('#reservationdatetime').datetimepicker({
            format: 'YYYY-MM-DD HH:mm'
        });
    });
</script>

@endsection