@extends('admin.layouts.admin')

@section('content')

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tempusdominus-bootstrap-4@5.39.0/build/css/tempusdominus-bootstrap-4.min.css" />

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
                                        <label>Employee *</label>
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
                                        <label>Type *</label>
                                        <select class="form-control" id="type" name="type">
                                            <option value="">Select Type</option>
                                            <option value="Regular">Regular</option>
                                            <option value="Sick">Sick</option>
                                            <option value="Absence">Absence</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                <!-- text input -->
                                    <div class="form-group">
                                        <label>Clock In</label>
                                        <div class="input-group date" id="clockIndatetime" data-target-input="nearest">
                                            <input type="text" class="form-control datetimepicker-input" data-target="#clockIndatetime" id="clock_in" name="clock_in" />
                                            <div class="input-group-append" data-target="#clockIndatetime" data-toggle="datetimepicker">
                                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                <!-- text input -->
                                    <div class="form-group">
                                        <label>Clock Out</label>
                                        <div class="input-group date" id="clockOutdatetime" data-target-input="nearest">
                                            <input type="text" class="form-control datetimepicker-input" data-target="#clockOutdatetime" id="clock_out" name="clock_out" />
                                            <div class="input-group-append" data-target="#clockOutdatetime" data-toggle="datetimepicker">
                                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-12">
                                <!-- text input -->
                                    <div class="form-group">
                                        <label>Details</label>
                                        <textarea class="form-control" name="details" id="details" cols="30" rows="2"></textarea>
                                    </div>
                                </div>

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
                        <h3 class="card-title">All Attendance Record</h3>
                    </div>
                    <div class="card-body">
                        <table id="example1" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th> </th>
                                    <th>G. Total Time</th>
                                </tr>
                            </thead>
                            <tbody>

                                @foreach ($data as $data)
                                    <tr>
                                        <td>{{ $data->employee->name }}</td>
                                        <td>
                                            <table class="table table-bordered w-100">
                                                <thead>
                                                    <tr>
                                                        <th>Date</th>
                                                        <th>Type</th>
                                                        <th>Time In</th>
                                                        <th>Time Out</th>
                                                        <th>Late</th>
                                                        <th>Total Time</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php
                                                        $diff = '';
                                                        if ($data->clock_in && $data->clock_out) {
                                                            $in = \Carbon\Carbon::parse($data->clock_in);
                                                            $out = \Carbon\Carbon::parse($data->clock_out);
                                                            $diff = $in->diff($out);
                                                            
                                                        } else {
                                                            
                                                        }
                                                    @endphp
                                                    <tr>
                                                        <td>{{ \Carbon\Carbon::parse($data->created_at)->format('Y-m-d') }}</td>
                                                        <td>{{ $data->type }}</td>
                                                        <td>{{ $data->clock_in }}</td>
                                                        <td>{{ $data->clock_out }}</td>
                                                        <td></td>
                                                        <td>{{ $diff ? $diff->format('%H:%I:%S') : '-' }} </td>
                                                        <td>
                                                            <a id="DetailsBtn"
                                                                rid="{{$data->id}}"
                                                                title="Details"
                                                                data-id="{{ $data->id }}"
                                                                data-employee="{{ $data->employee->name }}"
                                                                data-type="{{ $data->type }}"
                                                                data-clock_in="{{ $data->clock_in }}"
                                                                data-clock_out="{{ $data->clock_out }}"
                                                                data-details="{{ $data->details }}"
                                                                data-date="{{ \Carbon\Carbon::parse($data->created_at)->format('Y-m-d') }}"
                                                                data-total_time="{{ $diff ? $diff->format('%H:%I:%S') : '-' }}"
                                                            >
                                                                <i class="fa fa-info-circle" style="color: #17a2b8; font-size:16px; margin-right:8px;"></i>
                                                            </a>
                                                            <a id="EditBtn" rid="{{$data->id}}"><i class="fa fa-edit" style="color: #2196f3;font-size:16px;"></i></a>
                                                            <a id="deleteBtn" rid="{{$data->id}}"><i class="fa fa-trash-o" style="color: red;font-size:16px;"></i></a>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                        <td> </td>
                                    </tr>
                                @endforeach


                                {{-- @foreach ($data as $key => $data)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $data->date }}</td>
                                    <td>{{ $data->employee->name }}</td>
                                    <td>{{ $data->type }}</td>
                                    <td>{{ $data->details }}</td>
                                    <td>
                                        <a id="EditBtn" rid="{{ $data->id }}"><i class="fa fa-edit" style="color: #2196f3;font-size:16px;"></i></a>
                                        <a id="deleteBtn" rid="{{ $data->id }}"><i class="fa fa-trash-o" style="color: red;font-size:16px;"></i></a>
                                    </td>
                                </tr>
                                @endforeach --}}
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
        var url = "{{URL::to('/admin/attendance')}}";
        var upurl = "{{URL::to('/admin/attendance/update')}}";

        $("#addBtn").click(function() {
            if ($(this).val() == 'Create') {
                var requiredFields = ['#employee_id','#type', '#clock_in', '#clock_out', '#details'];
                for (var i = 0; i < requiredFields.length; i++) {
                    if ($(requiredFields[i]).val () === '') {
                        showError('Please fill all required fields.');
                        return;
                    }
                }
                var form_data = new FormData();
                form_data.append("employee_id", $("#employee_id").val());
                form_data.append("type", $("#type").val());
                form_data.append("clock_in", $("#clock_in").val());
                form_data.append("clock_out", $("#clock_out").val());
                form_data.append("details", $("#details").val());

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
                            showSuccess('Data created successfully.');
                            reloadPage(2000);
                        }
                       
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                        showError('An error occurred. Please try again.');
                    }
                });
            }

            if ($(this).val() == 'Update') {
                var requiredFields = ['#employee_id','#type', '#clock_in', '#clock_out', '#details'];
                for (var i = 0; i < requiredFields.length; i++) {
                    if ($(requiredFields[i]).val () === '') {
                        showError('Please fill all required fields.');
                        return;
                    }
                }
                var form_data = new FormData();
                form_data.append("employee_id", $("#employee_id").val());
                form_data.append("type", $("#type").val());
                form_data.append("clock_in", $("#clock_in").val());
                form_data.append("clock_out", $("#clock_out").val());
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
            $("#type").val(data.type);
            $("#employee_id").val(data.employee_id).trigger('change');
            $("#clock_in").val(data.clock_in);
            $("#clock_out").val(data.clock_out);
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


        $("#contentContainer").on('click', '#DetailsBtn', function() {
            var attrs = {};
            $.each(this.attributes, function() {
                if(this.specified && this.name.startsWith('data-')) {
                    var key = this.name.replace('data-', '');
                    attrs[key] = this.value;
                }
            });
            console.log(attrs);
            // You can use attrs object as needed, e.g., show in a modal
            let modalHtml = `
            <div class="modal fade" id="detailsModal" tabindex="-1" role="dialog" aria-labelledby="detailsModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="detailsModalLabel">Employee Details</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <table class="table table-bordered">
                                <tbody>
                                    ${Object.entries(attrs).map(([key, value]) => `
                                        <tr>
                                            <th>${key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())}</th>
                                            <td>${value}</td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
            `;

            // Remove any existing modal to avoid duplicates
            $('#detailsModal').remove();
            $('body').append(modalHtml);
            $('#detailsModal').modal('show');
        });



    });
</script>

<!-- JS to initialize picker -->
<script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/tempusdominus-bootstrap-4@5.39.0/build/js/tempusdominus-bootstrap-4.min.js"></script>

<!-- Initialize picker with DD-MM-YYYY HH:mm format -->
<script type="text/javascript">
    $(function () {
        $('#clockIndatetime').datetimepicker({
            format: 'YYYY-MM-DD HH:mm:ss'
        });

        $('#clockOutdatetime').datetimepicker({
            format: 'YYYY-MM-DD HH:mm:ss'
        });
    });
</script>

@endsection