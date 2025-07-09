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
                        <h3 class="card-title" id="header-title">Add new asset</h3>
                    </div>
                    <div class="card-body">
                        <div class="errmsg"></div>
                        <form id="createThisForm">
                            @csrf
                            <input type="hidden" class="form-control" id="codeid" name="codeid">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                      <label>Asset Type <span class="text-danger">*</span></label>
                                      <select class="form-control" id="asset_type_id" name="asset_type_id" required>
                                          <option value="">Select Asset Type</option>
                                          @foreach($assetTypes as $type)
                                              <option value="{{ $type->id }}">{{ $type->name }}</option>
                                          @endforeach
                                      </select>
                                </div>

                                <div class="col-md-6">
                                    <label>Location <span class="text-danger">*</span></label>
                                    <select class="form-control" id="location_id" name="location_id" required>
                                        <option value="">Select Location</option>
                                        @foreach($locations as $location)
                                            <option value="{{ $location->id }}">{{ $location->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label>Assigned To</label>
                                <input type="text" class="form-control" id="assigned_to" name="assigned_to" placeholder="Enter person name">
                            </div>
                            
                            <div class="form-group">
                                <label>Purchase Date</label>
                                <input type="date" class="form-control" id="purchase_date" name="purchase_date">
                            </div>
                            
                            <div class="form-group">
                                <label>Warranty Expiry</label>
                                <input type="date" class="form-control" id="warranty_expiry" name="warranty_expiry">
                            </div>
                            
                            <div class="form-group">
                                <label>Status <span class="text-danger">*</span></label>
                                <select class="form-control" id="status" name="status" required>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label>Notes</label>
                                <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Enter any notes"></textarea>
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
                        <h3 class="card-title">All Assets</h3>
                    </div>
                    <div class="card-body">
                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Sl</th>
                                    <th>Asset Type</th>
                                    <th>Location</th>
                                    <th>Assigned To</th>
                                    <th>Purchase Date</th>
                                    <th>Warranty Expiry</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $key => $asset)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $asset->assetType->name ?? 'N/A' }}</td>
                                    <td>{{ $asset->location->name ?? 'N/A' }}</td>
                                    <td>{{ $asset->assigned_to ?? 'N/A' }}</td>
                                    <td>{{ $asset->purchase_date ? \Carbon\Carbon::parse($asset->purchase_date)->format('d-m-Y') : 'N/A' }}</td>
                                    <td>{{ $asset->warranty_expiry ? \Carbon\Carbon::parse($asset->warranty_expiry)->format('d-m-Y') : 'N/A' }}</td>
                                    <td>
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input toggle-status" id="customSwitchStatus{{ $asset->id }}" data-id="{{ $asset->id }}" {{ $asset->status == 1 ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="customSwitchStatus{{ $asset->id }}"></label>
                                        </div>
                                    </td>
                                    <td>
                                        <a id="EditBtn" rid="{{ $asset->id }}"><i class="fa fa-edit" style="color: #2196f3;font-size:16px;"></i></a>
                                        <a id="deleteBtn" rid="{{ $asset->id }}"><i class="fa fa-trash-o" style="color: red;font-size:16px;"></i></a>
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
        
        var url = "{{URL::to('/admin/asset')}}";
        var upurl = "{{URL::to('/admin/asset-update')}}";

        $("#addBtn").click(function() {
            if ($(this).val() == 'Create') {
                var requiredFields = ['#asset_type_id', '#location_id', '#status'];
                for (var i = 0; i < requiredFields.length; i++) {
                    if ($(requiredFields[i]).val() === '') {
                        $('.errmsg').html('<div class="alert alert-danger">Please fill all required fields.</div>');
                        return;
                    }
                }

                var form_data = new FormData();
                form_data.append("asset_type_id", $("#asset_type_id").val());
                form_data.append("location_id", $("#location_id").val());
                form_data.append("assigned_to", $("#assigned_to").val());
                form_data.append("purchase_date", $("#purchase_date").val());
                form_data.append("warranty_expiry", $("#warranty_expiry").val());
                form_data.append("status", $("#status").val());
                form_data.append("notes", $("#notes").val());

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
                var requiredFields = ['#asset_type_id', '#location_id', '#status'];
                for (var i = 0; i < requiredFields.length; i++) {
                    if ($(requiredFields[i]).val() === '') {
                        $('.errmsg').html('<div class="alert alert-danger">Please fill all required fields.</div>');
                        return;
                    }
                }

                var form_data = new FormData();
                form_data.append("asset_type_id", $("#asset_type_id").val());
                form_data.append("location_id", $("#location_id").val());
                form_data.append("assigned_to", $("#assigned_to").val());
                form_data.append("purchase_date", $("#purchase_date").val());
                form_data.append("warranty_expiry", $("#warranty_expiry").val());
                form_data.append("status", $("#status").val());
                form_data.append("notes", $("#notes").val());
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
            $("#asset_type_id").val(data.asset_type_id);
            $("#location_id").val(data.location_id);
            $("#assigned_to").val(data.assigned_to);
            $("#purchase_date").val(data.purchase_date);
            $("#warranty_expiry").val(data.warranty_expiry);
            $("#status").val(data.status);
            $("#notes").val(data.notes);
            $("#codeid").val(data.id);
            $("#addBtn").val('Update');
            $("#addBtn").html('Update');
            $("#header-title").html('Update asset');
            $("#addThisFormContainer").show(300);
            $("#newBtn").hide(100);
        }

        function clearform() {
            $('#createThisForm')[0].reset();
            $("#addBtn").val('Create');
            $("#header-title").html('Add new asset');
        }

        $(document).on('change', '.toggle-status', function() {
            var id = $(this).data('id');
            var status = $(this).prop('checked') ? 1 : 0;

            $.ajax({
                url: '{{ route("assets.updateStatus") }}',
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