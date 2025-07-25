@extends('admin.layouts.admin')

@section('content')

<section class="content" id="newBtnSection">
    <div class="container-fluid">
        <div class="row">
            <div class="col-2">
                <button type="button" class="btn btn-secondary my-3" id="newBtn">Add new</button>
            </div>
        </div>
    </div>
</section>

<section class="content mt-3" id="addThisFormContainer">
    <div class="container-fluid">
         <div class="row justify-content-md-center">
            <div class="col-md-8">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title" id="header-title">Add new floor</h3>
                    </div>
                    <div class="card-body">
                        <div class="errmsg"></div>
                        <form id="createThisForm">
                            @csrf
                            <input type="hidden" class="form-control" id="codeid" name="codeid">
                            <div class="form-group">
                                <label>Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Enter floor name">
                            </div>
                            <div class="form-group">
                                <label>Status <span class="text-danger">*</span></label>
                                <select class="form-control" id="status" name="status">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
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
                        <h3 class="card-title">All Floors</h3>
                    </div>
                    <div class="card-body">
                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Sl</th>
                                    <th>Date</th>
                                    <th>Name</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $key => $data)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ \Carbon\Carbon::parse($data->created_at)->format('d-m-Y') }}</td>
                                    <td>{{ $data->name }}</td>
                                    <td>
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input toggle-status" id="customSwitchStatus{{ $data->id }}" data-id="{{ $data->id }}" {{ $data->status == 1 ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="customSwitchStatus{{ $data->id }}"></label>
                                        </div>
                                    </td>
                                    <td>
                                        <a id="EditBtn" rid="{{ $data->id }}"><i class="fa fa-edit" style="color: #2196f3;font-size:16px;"></i></a>
                                        <a id="deleteBtn" rid="{{ $data->id }}"><i class="fa fa-trash-o" style="color: red;font-size:16px;"></i></a>
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
        var url = "{{URL::to('/admin/floor')}}";
        var upurl = "{{URL::to('/admin/floor-update')}}";

        $("#addBtn").click(function() {
            if ($(this).val() == 'Create') {
                var requiredFields = ['#name', '#status'];
                for (var i = 0; i < requiredFields.length; i++) {
                    if ($(requiredFields[i]).val() === '') {
                        $('.errmsg').html('<div class="alert alert-danger">Please fill all required fields.</div>');
                        return;
                    }
                }

                var form_data = new FormData();
                form_data.append("name", $("#name").val());
                form_data.append("status", $("#status").val());

                $.ajax({
                    url: url,
                    method: "POST",
                    contentType: false,
                    processData: false,
                    data: form_data,
                    success: function(d) {
                        if (d.status == 422) {
                            $('.errmsg').html('<div class="alert alert-danger">' + d.message + '</div>');
                        } else {
                            $('.errmsg').html('<div class="alert alert-success">' + d.message + '</div>');
                            reloadPage(2000);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                        $('.errmsg').html('<div class="alert alert-danger">An error occurred. Please try again.</div>');
                    }
                });
            }

            if ($(this).val() == 'Update') {
                var requiredFields = ['#name', '#status'];
                for (var i = 0; i < requiredFields.length; i++) {
                    if ($(requiredFields[i]).val() === '') {
                        $('.errmsg').html('<div class="alert alert-danger">Please fill all required fields.</div>');
                        return;
                    }
                }

                var form_data = new FormData();
                form_data.append("name", $("#name").val());
                form_data.append("status", $("#status").val());
                form_data.append("codeid", $("#codeid").val());

                $.ajax({
                    url: upurl,
                    type: "POST",
                    dataType: 'json',
                    contentType: false,
                    processData: false,
                    data: form_data,
                    success: function(d) {
                        if (d.status == 422) {
                            $('.errmsg').html('<div class="alert alert-danger">' + d.message + '</div>');
                        } else {
                            $('.errmsg').html('<div class="alert alert-success">' + d.message + '</div>');
                            reloadPage(2000); 
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                        $('.errmsg').html('<div class="alert alert-danger">An error occurred. Please try again.</div>');
                    }
                });
            }
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
                    $('.errmsg').html('<div class="alert alert-success">' + d.message + '</div>');
                    reloadPage(2000);
                },
                error: function(xhr, status, error) {
                    $('.errmsg').html('<div class="alert alert-danger">An error occurred. Please try again.</div>');
                }
            });
        });

        function populateForm(data) {
            $("#name").val(data.name);
            $("#status").val(data.status);
            $("#codeid").val(data.id);
            $("#addBtn").val('Update');
            $("#addBtn").html('Update');
            $("#header-title").html('Update data');
            $("#addThisFormContainer").show(300);
            $("#newBtn").hide(100);
        }

        function clearform() {
            $('#createThisForm')[0].reset();
            $("#addBtn").val('Create');
            $("#header-title").html('Add new floor');
        }

        $(document).on('change', '.toggle-status', function() {
            var id = $(this).data('id');
            var status = $(this).prop('checked') ? 1 : 0;

            $.ajax({
                url: '{{ route("floors.updateStatus") }}',
                method: 'POST',
                data: {
                    id: id,
                    status: status,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.status === 200) {
                        $('.errmsg').html('<div class="alert alert-success">' + response.message + '</div>');
                    } else {
                        $('.errmsg').html('<div class="alert alert-danger">Failed to update status.</div>');
                    }
                },
                error: function(xhr, status, error) {
                    $('.errmsg').html('<div class="alert alert-danger">An error occurred. Please try again.</div>');
                }
            });
        });

        function reloadPage(time) {
            setTimeout(function() {
                window.location.reload();
            }, time);
        }

        function pagetop() {
            window.scrollTo(0, 0);
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
    });
</script>
@endsection