@extends('admin.layouts.admin')

@section('content')

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
            <div class="col-md-8">
                <!-- general form elements disabled -->
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title" id="header-title">Add new data</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <form id="createThisForm">
                            @csrf
                            <input type="hidden" class="form-control" id="codeid" name="codeid">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="name" name="name" placeholder="Enter name">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Surname <span class="text-danger">*</span></label>
                                        <input type="text" id="surname" name="surname" class="form-control" placeholder="Enter surname">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Email <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control" id="email" name="email" placeholder="Enter email">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Phone <span class="text-danger">*</span></label>
                                        <input type="number" id="phone" name="phone" class="form-control" placeholder="Enter phone">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Password</label>
                                        <input type="password" class="form-control" id="password" name="password" placeholder="Enter password">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Confirm Password</label>
                                        <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Enter confirm password">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="feature-img">Image</label>
                                        <input type="file" class="form-control-file" id="image" accept="image/*">
                                        <img id="preview-image" src="#" alt="" style="max-width: 300px; width: 100%; height: auto; margin-top: 20px;">
                                    </div>
                                </div>
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
                                    <th>Email/Phone</th>
                                    <th>Image</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $key => $data)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{$data->name}} {{$data->surname}}</td>
                                    <td>{{$data->email}} <br> {{$data->phone}}</td>
                                    <td>
                                        <img src="{{ asset($data->photo) }}" alt="" style="max-width: 100px; width: 100%; height: auto;">
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
        var url = "{{URL::to('/admin/users')}}";
        var upurl = "{{URL::to('/admin/users-update')}}";
        // console.log(url);
        $("#addBtn").click(function() {
            if ($(this).val() == 'Create') {

                var requiredFields = ['#name', '#email', '#phone', '#surname', '#password', '#confirm_password'];
                for (var i = 0; i < requiredFields.length; i++) {
                    if ($(requiredFields[i]).val() === '') {
                        showError('Please fill all required fields.');
                        return;
                    }
                }

                if ($('#password').val() !== $('#confirm_password').val()) {
                    showError('Passwords do not match.');
                    return;
                }

                var form_data = new FormData();
                form_data.append("name", $("#name").val());
                form_data.append("email", $("#email").val());
                form_data.append("phone", $("#phone").val());
                form_data.append("surname", $("#surname").val());
                form_data.append("password", $("#password").val());
                form_data.append("confirm_password", $("#confirm_password").val());
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

                var requiredFields = ['#name', '#email', '#phone', '#surname'];
                for (var i = 0; i < requiredFields.length; i++) {
                    if ($(requiredFields[i]).val() === '') {
                        showError('Please fill all required fields.');
                        return;
                    }
                }

                if ($('#password').val() !== $('#confirm_password').val()) {
                    showError('Passwords do not match.');
                    return;
                }

                var form_data = new FormData();
                form_data.append("name", $("#name").val());
                form_data.append("email", $("#email").val());
                form_data.append("phone", $("#phone").val());
                form_data.append("surname", $("#surname").val());
                form_data.append("password", $("#password").val());
                form_data.append("confirm_password", $("#confirm_password").val());

                var featureImgInput = document.getElementById('image');
                if(featureImgInput.files && featureImgInput.files[0]) {
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
                        showSuccess('Data updated successfully.');
                        reloadPage(2000);
                    },
                    error: function(xhr, status, error) {
                        error('An error occurred. Please try again.');
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
            $("#name").val(data.name);
            $("#surname").val(data.surname);
            $("#phone").val(data.phone);
            $("#email").val(data.email);
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

@endsection