@extends('admin.layouts.admin')

@section('content')

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tempusdominus-bootstrap-4@5.39.0/build/css/tempusdominus-bootstrap-4.min.css" />
@if (auth()->user()->canDo(20))
<section class="content" id="newBtnSection">
    <div class="container-fluid">
        <div class="row">
            <div class="col-2">
                <button type="button" class="btn btn-secondary my-3" id="newBtn">Add new</button>
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
                        <h3 class="card-title" id="header-title">Add new Stock</h3>
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
                                        <label>Date</label>
                                        <input type="date" class="form-control" id="date" name="date" value="{{ date('Y-m-d') }}">
                                    </div>
                                </div>
                                
                                <div class="col-sm-3">
                                <!-- text input -->
                                    <div class="form-group">
                                        <label>Product *</label>
                                        <select class="form-control select2" id="product_id" name="product_id">
                                            <option value="">Select Product</option>
                                            @foreach ($products as $product)
                                            <option value="{{$product->id}}">{{$product->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                <!-- text input -->
                                    <div class="form-group">
                                        <label>Type *</label>
                                        <select class="form-control" id="cloth_type" name="cloth_type">
                                            <option value="">Select Type</option>
                                            <option value="Dirty">Dirty</option>
                                            <option value="Bed">Bed</option>
                                            <option value="Arrived">Arrived</option>
                                            <option value="Initial Stock">Initial Stock</option>
                                            <option value="Lost/Missed">Missed</option>
                                            <option value="Rejected">Rejected</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                <!-- text input -->
                                    <div class="form-group">
                                        <label>Quantity</label>
                                        <input type="number" class="form-control" id="quantity" name="quantity" >
                                    </div>
                                </div>
                                

                                <div class="col-sm-3 d-none">
                                <!-- text input -->
                                    <div class="form-group">
                                        <label>Marks</label>
                                        <input type="number" class="form-control" name="marks" id="marks">
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
                        <h3 class="card-title">Stock Maintenance List</h3>
                    </div>
                    <div class="card-body">
                        <table id="example1" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Staff</th>
                                    <th>Date</th>
                                    <th>Branch</th>
                                    <th>Product</th>
                                    <th>Type</th>
                                    <th>Quantity</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $key => $data)
                                <tr>
                                    <td>{{ $data->user->name ?? "" }}</td>
                                    <td>{{ \Carbon\Carbon::parse($data->date)->format('d-m-Y') }}</td>
                                    <td>{{ $data->branch->name ?? '' }}</td>
                                    <td>{{ $data->product->name }}</td>
                                    <td>{{ $data->cloth_type }}</td>
                                    <td>{{ $data->quantity }}</td>
                                    <td>
                                        <a id="DetailsBtn"
                                            data-id="{{ $data->id }}"
                                            data-product="{{ $data->product->name }}"
                                            data-date="{{ $data->date }}"
                                            data-product="{{ $data->product->name }}"
                                            data-type="{{ $data->cloth_type }}"
                                            data-quantity="{{ $data->quantity }}"
                                            data-marks="{{ $data->marks }}"
                                            data-details="{{ $data->details }}"
                                            style="cursor:pointer;">
                                            <i class="fa fa-info-circle" style="color: #17a2b8;font-size:16px;"></i>
                                        </a>
                                        @if (auth()->user()->canDo(21))
                                        <a id="EditBtn" rid="{{ $data->id }}"><i class="fa fa-edit" style="color: #2196f3;font-size:16px;"></i></a>
                                        @endif
                                        @if (auth()->user()->canDo(22))
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
        var url = "{{URL::to('/admin/stocks')}}";
        var upurl = "{{URL::to('/admin/stocks/update')}}";

        $("#addBtn").click(function() {
            if ($(this).val() == 'Create') {
                var requiredFields = ['#product_id','#cloth_type', '#quantity', '#marks', '#details'];
                for (var i = 0; i < requiredFields.length; i++) {
                    if ($(requiredFields[i]).val () === '') {
                        showError('Please fill all required fields.');
                        return;
                    }
                }
                var form_data = new FormData();
                form_data.append("date", $("#date").val());
                form_data.append("product_id", $("#product_id").val());
                form_data.append("cloth_type", $("#cloth_type").val());
                form_data.append("quantity", $("#quantity").val());
                form_data.append("marks", $("#marks").val());
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
                var requiredFields = ['#product_id','#cloth_type', '#quantity', '#marks', '#details'];
                for (var i = 0; i < requiredFields.length; i++) {
                    if ($(requiredFields[i]).val () === '') {
                        showError('Please fill all required fields.');
                        return;
                    }
                }
                var form_data = new FormData();
                form_data.append("date", $("#date").val());
                form_data.append("product_id", $("#product_id").val());
                form_data.append("cloth_type", $("#cloth_type").val());
                form_data.append("quantity", $("#quantity").val());
                form_data.append("marks", $("#marks").val());
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
            $("#cloth_type").val(data.cloth_type);
            $("#product_id").val(data.product_id).trigger('change');
            $("#quantity").val(data.quantity);
            $("#marks").val(data.marks);
            $("#details").val(data.details);
            $("#date").val(data.date);
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
                            <h5 class="modal-title" id="detailsModalLabel">Stock Details</h5>
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