@extends('admin.layouts.admin')

@section('content')
@if (auth()->user()->canDo(11))
<section class="content" id="newBtnSection">
    <div class="container-fluid">
        <div class="row">
            <div class="col-2">
                {{-- <button type="button" class="btn btn-secondary my-3" id="newBtn">Add new</button> --}}
            </div>
        </div>
    </div>
</section>
@endif

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
                                <!-- text input -->
                                    <div class="form-group">
                                        <label>From Date <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control" id="from_date" name="from_date" />
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                <!-- text input -->
                                    <div class="form-group">
                                        <label>To Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="to_date" name="to_date"/>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                <!-- text input -->
                                    <div class="form-group">
                                        <label>Employee <span class="text-danger">*</span></label>
                                        <select class="form-control select2" id="employee_id" name="employee_id">
                                            <option value="">Select Employee</option>
                                            @foreach ($employees as $employee)
                                            <option value="{{$employee->id}}">{{$employee->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                <!-- text input -->
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
                                <!-- text input -->
                                    <div class="form-group">
                                        <label>Details <span class="text-danger">*</span></label>
                                        <textarea class="form-control" name="details" id="details" cols="30" rows="1"></textarea>
                                    </div>
                                </div>

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

<script>
    // Function to show error messages in .errmsg div
    // function showError(message) {
    //     $('.errmsg').html('<div class="alert alert-danger">' + message + '</div>');
    // }

    $(document).ready(function() {
        
        
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        var url = "{{URL::to('/admin/holidays')}}";
        var upurl = "{{URL::to('/admin/holidays/update')}}";

        $("#addBtn").click(function() {
            if ($(this).val() == 'Create') {
                var requiredFields = ['#from_date', '#to_date', '#employee_id', '#employee_type', '#details'];
                for (var i = 0; i < requiredFields.length; i++) {
                    if ($(requiredFields[i]).val () === '') {
                        showError('Please fill all required fields.');
                        return;
                    }
                }
                var form_data = new FormData();
                form_data.append("from_date", $("#from_date").val());
                form_data.append("to_date", $("#to_date").val());
                form_data.append("employee_type", $("#employee_type").val());
                form_data.append("employee_id", $("#employee_id").val());
                form_data.append("details", $("#details").val());

                $.ajax({
                    url: url,
                    method: "POST",
                    contentType: false,
                    processData: false,
                    data: form_data,
                    success: function(d) {

                        console.log(d);
                        // if (d.status == 422) {
                        //     $('.errmsg').html('<div class="alert alert-danger">' + d.message + '</div>');
                        // } else {
                        //     showSuccess('Data created successfully.');
                        //     reloadPage(2000);
                        // }
                       
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                        showError('An error occurred. Please try again.');
                    }
                });
            }

            if ($(this).val() == 'Update') {
                var requiredFields = ['#from_date', '#to_date',  '#employee_id', '#employee_type', '#details'];
                for (var i = 0; i < requiredFields.length; i++) {
                    if ($(requiredFields[i]).val() === '') {
                        showError('Please fill all required fields.');
                        return;
                    }
                }

                var form_data = new FormData();
                form_data.append("from_date", $("#from_date").val());
                form_data.append("to_date", $("#to_date").val());
                form_data.append("employee_type", $("#employee_type").val());
                form_data.append("employee_id", $("#employee_id").val());
                form_data.append("details", $("#details").val());
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
                            showSuccess('Data updated successfully.');
                            reloadPage(2000); 
                        }
                        
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                        showError('An error occurred. Please try again.');
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
                    showSuccess('Data deleted successfully.');
                    reloadPage(2000);
                },
                error: function(xhr, status, error) {
                    showError('An error occurred. Please try again.');
                }
            });
        });

        function populateForm(data) {
            $("#from_date").val(data.from_date);
            $("#to_date").val(data.to_date);
            $("#employee_id").val(data.employee_id).trigger('change');
            $("#employee_type").val(data.type);
            $("#details").val(data.details);
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
    });
</script>



@endsection