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
            <div class="col-md-10">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title" id="header-title">Add new stock</h3>
                    </div>

                    <form id="createThisForm">
                        @csrf
                        <div class="card-body">
                            <div class="errmsg"></div>
                            <input type="hidden" class="form-control" id="codeid" name="codeid">

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="date" name="date" value="{{ date('Y-m-d') }}">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Asset Type <span class="text-danger">*</span></label>
                                        <select class="form-control" id="asset_type_id" name="asset_type_id">
                                            <option value="">Select Asset Type</option>
                                            @foreach($assetTypes as $type)  
                                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Brand</label>
                                        <input type="text" class="form-control" id="brand" name="brand" placeholder="Enter brand">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Model</label>
                                        <input type="text" class="form-control" id="model" name="model" placeholder="Enter model">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Qty <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="quantity" name="quantity" placeholder="Enter quantity" min="1">
                                    </div>
                                </div>
                            </div>

                            <div id="dynamicRows"></div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Note</label>
                                        <textarea class="form-control" id="note" name="note" placeholder="Enter note" rows="3"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer">
                            <button type="button" id="addBtn" class="btn btn-secondary" value="Create">Create</button>
                            <button type="button" id="FormCloseBtn" class="btn btn-default">Cancel</button>
                        </div>
                    </form>

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
                        <h3 class="card-title">Stock</h3>
                    </div>
                    <div class="card-body">
                      <table id="example1" class="table table-bordered table-striped">
                          <thead>
                              <tr>
                                  <th>Sl</th>
                                  <th>Date</th>
                                  <th>Asset Type</th>
                                  <th>Branch</th>
                                  <th>Brand</th>
                                  <th>Model</th>
                                  <th>Qty</th>
                                  <th>Assigned</th>
                                  <th>In storage</th>
                                  <th>Under Repair</th>
                                  <th>Damaged</th>
                                  <th>Action</th>
                              </tr>
                          </thead>
                          <tbody>
                              @foreach ($data as $key => $item)
                              <tr>
                                  <td>{{ $key + 1 }}</td>
                                  <td>{{ \Carbon\Carbon::parse($item->date)->format('d-m-Y') }}</td>
                                  <td>{{ $item->assetType->name ?? 'N/A' }}</td>
                                  <td>{{ $item->branch->name ?? 'N/A' }}</td>
                                  <td>{{ $item->brand }}</td>
                                  <td>{{ $item->model }}</td>
                                  <td>{{ $item->quantity }}</td>
                                  <td><a href="{{ route('stock.view.status', [$item->id, 1]) }}"><span class="badge bg-info">{{ $item->assigned_count }}</span></a></td>
                                  <td><a href="{{ route('stock.view.status', [$item->id, 2]) }}"><span class="badge bg-primary">{{ $item->storage_count }}</span></a></td>
                                  <td><a href="{{ route('stock.view.status', [$item->id, 3]) }}"><span class="badge bg-warning text-dark">{{ $item->repair_count }}</span></a></td>
                                  <td><a href="{{ route('stock.view.status', [$item->id, 4]) }}"><span class="badge bg-danger">{{ $item->damaged_count }}</span></a></td>
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

<script>
    const branchOptions = `@foreach($branches as $branch)
        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
    @endforeach`;

    const floorOptions = `@foreach($floors as $floor)
        <option value="{{ $floor->id }}">{{ $floor->name }}</option>
    @endforeach`;

    const maintenanceOptions = `@foreach($maintainances as $maintenance)
        <option value="{{ $maintenance->id }}">{{ $maintenance->name }}</option>
    @endforeach`;
</script>

<script>
    $(document).ready(function() {

        $('#quantity').on('input', function () {
            let qty = +$(this).val() || 0;
            let html = '';

            for (let i = 0; i < qty; i++) {
                html += `
                <div class="row mb-2 align-items-end">
                    <div class="col-md-6">
                        <input type="text" name="product_code[]" class="form-control" placeholder="Product Code">
                    </div>
                    <div class="col-md-6 status-wrap">
                        <select name="asset_status[]" class="form-control asset-status">
                            <option value="">Select Status</option>
                            <option value="1">Assigned</option>
                            <option value="2">In storage</option>
                            <option value="3">Under Repair</option>
                            <option value="4">Damaged</option>
                        </select>
                    </div>
                </div>`;
            }

            $('#dynamicRows').html(html);
        });

        $(document).on('change', '.asset-status', function () {
            const selected = $(this).val();
            const $wrap = $(this).closest('.status-wrap');
            const $row = $wrap.closest('.row');

            $row.find('.branch-col, .floor-col, .location-col, .maintenance-col').remove();
            $wrap.removeClass('col-md-3').addClass('col-md-6');

            if (selected === '1' || selected === '2') {
                $wrap.removeClass('col-md-6').addClass('col-md-3');
                
                $row.append(`
                    <div class="col-md-3 branch-col">
                        <select name="branch_id[]" class="form-control branch-select">
                            <option value="">Select Branch</option>
                            ${branchOptions}
                        </select>
                    </div>
                `);

                $row.append(`
                    <div class="col-md-3 floor-col mt-2">
                        <select name="floor_id[]" class="form-control floor-select">
                            <option value="">Select Floor</option>
                            ${floorOptions}
                        </select>
                    </div>
                `);

                $row.append(`
                    <div class="col-md-3 location-col mt-2" style="display:none;">
                        <select name="location_id[]" class="form-control location-select">
                            <option value="">Select Room</option>
                        </select>
                    </div>
                `);
            } 
            else if (selected === '3') {
                $wrap.removeClass('col-md-6').addClass('col-md-3');
                $row.append(`
                    <div class="col-md-3 maintenance-col">
                        <select name="maintenance_id[]" class="form-control">
                            <option value="">Select Maintenance</option>
                            ${maintenanceOptions}
                        </select>
                    </div>
                `);
            } 
            else {
                $wrap.removeClass('col-md-3').addClass('col-md-6');
            }
        });

        $(document).on('change', '.branch-select, .floor-select', function() {
            const $row = $(this).closest('.row');
            const branchId = $row.find('.branch-select').val();
            const floorId = $row.find('.floor-select').val();
            const $locationSelect = $row.find('.location-select');
            const $locationCol = $row.find('.location-col');

            $locationSelect.empty().append('<option value="">Select Room</option>');
            
            if (branchId && floorId) {
                $.get(`/admin/get-locations/${branchId}/${floorId}`, function(locations) {
                  console.log(locations);
                    if (locations.length > 0) {
                        locations.forEach(loc => {
                            $locationSelect.append(`<option value="${loc.id}">${loc.room}</option>`);
                        });
                        $locationCol.show();
                    } else {
                        $locationCol.hide();
                    }
                });
            } else {
                $locationCol.hide();
            }
        });

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
        var url = "{{URL::to('/admin/stock')}}";
        var upurl = "{{URL::to('/admin/stock-update')}}";

        $("#addBtn").click(function() {
            if ($(this).val() == 'Create') {
                var requiredFields = ['#date', '#asset_type_id', '#quantity'];
                for (var i = 0; i < requiredFields.length; i++) {
                    if ($(requiredFields[i]).val() === '') {
                        $('.errmsg').html('<div class="alert alert-danger">Please fill all required fields.</div>');
                        return;
                    }
                }

                var form_data = new FormData();
                form_data.append("date", $("#date").val());
                form_data.append("asset_type_id", $("#asset_type_id").val());
                form_data.append("brand", $("#brand").val());
                form_data.append("model", $("#model").val());
                form_data.append("quantity", $("#quantity").val());
                form_data.append("note", $("#note").val());

                $('#dynamicRows .row').each(function () {
                    form_data.append('product_code[]', $(this).find('input[name="product_code[]"]').val());
                    form_data.append('asset_status[]', $(this).find('select[name="asset_status[]"]').val());
                    
                    const branchVal = $(this).find('select[name="branch_id[]"]').val();
                    const locationVal = $(this).find('select[name="location_id[]"]').val();
                    const maintenanceVal = $(this).find('select[name="maintenance_id[]"]').val();
                    const floorVal = $(this).find('select[name="floor_id[]"]').val();

                    form_data.append('branch_id[]', branchVal ? branchVal : '');
                    form_data.append('location_id[]', locationVal ? locationVal : '');
                    form_data.append('maintenance_id[]', maintenanceVal ? maintenanceVal : '');
                    form_data.append('floor_id[]', floorVal ? floorVal : '');
                });

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
                var requiredFields = ['#date', '#asset_type_id', '#quantity'];
                for (var i = 0; i < requiredFields.length; i++) {
                    if ($(requiredFields[i]).val() === '') {
                        $('.errmsg').html('<div class="alert alert-danger">Please fill all required fields.</div>');
                        return;
                    }
                }

                var qty = +$('#quantity').val();
                var productCodes = $('input[name="product_code[]"]').filter(function() {
                    return $(this).val().trim() === '';
                }).length;

                if (productCodes > 0) {
                    $('.errmsg').html('<div class="alert alert-danger">Please fill all product codes.</div>');
                    return;
                }

                var form_data = new FormData();
                form_data.append("date", $("#date").val());
                form_data.append("asset_type_id", $("#asset_type_id").val());
                form_data.append("brand", $("#brand").val());
                form_data.append("model", $("#model").val());
                form_data.append("quantity", $("#quantity").val());
                form_data.append("note", $("#note").val());
                form_data.append("codeid", $("#codeid").val());

                $('#dynamicRows .row').each(function () {
                    form_data.append('product_code[]', $(this).find('input[name="product_code[]"]').val());
                    form_data.append('asset_status[]', $(this).find('select[name="asset_status[]"]').val());

                    const branchVal = $(this).find('select[name="branch_id[]"]').val();
                    const locationVal = $(this).find('select[name="location_id[]"]').val();
                    const maintenanceVal = $(this).find('select[name="maintenance_id[]"]').val();
                    const floorVal = $(this).find('select[name="floor_id[]"]').val();

                    form_data.append('branch_id[]', branchVal ? branchVal : '');
                    form_data.append('location_id[]', locationVal ? locationVal : '');
                    form_data.append('maintenance_id[]', maintenanceVal ? maintenanceVal : '');
                    form_data.append('floor_id[]', floorVal ? floorVal : '');
                });

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
                if (d.status == 200) {
                    populateForm(d.data);
                    pagetop();
                } else {
                    $('.errmsg').html('<div class="alert alert-danger">' + d.message + '</div>');
                }
            }).fail(function() {
                $('.errmsg').html('<div class="alert alert-danger">Failed to fetch data.</div>');
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
            clearform();
            
            $("#date").val(data.date);
            $("#asset_type_id").val(data.asset_type_id);
            $("#brand").val(data.brand);
            $("#model").val(data.model);
            $("#quantity").val(data.quantity);
            $("#note").val(data.note);
            $("#codeid").val(data.id);
            
            $('#dynamicRows').empty();
            
            if (data.stock_asset_types && data.stock_asset_types.length > 0) {
                data.stock_asset_types.forEach(function(asset) {
                    let rowHtml = `
                    <div class="row mb-2 align-items-end">
                        <div class="col-md-6">
                            <input type="text" name="product_code[]" class="form-control" 
                                  placeholder="Product Code" value="${asset.product_code || ''}">
                        </div>
                        <div class="col-md-${[1,2,3].includes(asset.asset_status) ? '3' : '6'} status-wrap">
                            <select name="asset_status[]" class="form-control asset-status">
                                <option value="">Select Status</option>
                                <option value="1" ${asset.asset_status == 1 ? 'selected' : ''}>Assigned</option>
                                <option value="2" ${asset.asset_status == 2 ? 'selected' : ''}>In storage</option>
                                <option value="3" ${asset.asset_status == 3 ? 'selected' : ''}>Under Repair</option>
                                <option value="4" ${asset.asset_status == 4 ? 'selected' : ''}>Damaged</option>
                            </select>
                        </div>`;
                    
                    $('#dynamicRows').append(rowHtml);
                    
                    const $row = $('#dynamicRows .row:last');
                    
                    if (asset.asset_status == 1 || asset.asset_status == 2) {
                        $row.find('.status-wrap').removeClass('col-md-6').addClass('col-md-3');
                        
                        $row.append(`
                            <div class="col-md-3 branch-col">
                                <select name="branch_id[]" class="form-control branch-select">
                                    <option value="">Select Branch</option>
                                    ${branchOptions}
                                </select>
                            </div>
                            <div class="col-md-3 floor-col mt-2">
                                <select name="floor_id[]" class="form-control floor-select">
                                    <option value="">Select Floor</option>
                                    ${floorOptions}
                                </select>
                            </div>
                            <div class="col-md-3 location-col mt-2" style="display:none;">
                                <select name="location_id[]" class="form-control location-select">
                                    <option value="">Select Room</option>
                                </select>
                            </div>
                        `);
                        
                        if (asset.branch_id && asset.floor_id) {
                            const $branchSelect = $row.find('.branch-select');
                            const $floorSelect = $row.find('.floor-select');
                            const $locationSelect = $row.find('.location-select');
                            const $locationCol = $row.find('.location-col');

                            $branchSelect.val(asset.branch_id);
                            $floorSelect.val(asset.floor_id);

                            $.get(`/admin/get-locations/${asset.branch_id}/${asset.floor_id}`, function(locations) {
                                $locationSelect.empty().append('<option value="">Select Room</option>');
                                
                                if (locations.length > 0) {
                                    locations.forEach(loc => {
                                        $locationSelect.append(`<option value="${loc.id}">${loc.room}</option>`);
                                    });
                                    $locationSelect.val(asset.location_id);
                                    $locationCol.show();
                                }
                            });
                        }
                    }
                    else if (asset.asset_status == 3) {
                        $row.find('.status-wrap').removeClass('col-md-6').addClass('col-md-3');
                        
                        $row.append(`
                            <div class="col-md-3 maintenance-col">
                                <select name="maintenance_id[]" class="form-control">
                                    <option value="">Select Maintenance</option>
                                    ${maintenanceOptions}
                                </select>
                            </div>
                        `);

                        if (asset.maintenance_id) {
                            $row.find('.maintenance-col select').val(asset.maintenance_id);
                        }
                    }
                });
            }
            
            $("#addBtn").val('Update');
            $("#addBtn").html('Update');
            $("#header-title").html('Update Stock');
            $("#addThisFormContainer").show(300);
            $("#newBtn").hide(100);
        }

        function clearform() {
            $('#createThisForm')[0].reset();
            $('#dynamicRows').empty();
            $("#addBtn").val('Create');
            $("#header-title").html('Add new stock');
        }

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