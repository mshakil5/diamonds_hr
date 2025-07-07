@extends('admin.layouts.admin')

@section('content')

<!-- Main content -->
<section class="content" id="newBtnSection">
    <div class="container-fluid">
        <div class="row">
            <div class="col-2">
                <a href="{{ route('prorota.create') }}" class="btn btn-secondary my-3" id="newBtn">Add new</a>
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
                        <h3 class="card-title">All Prorota</h3>
                    </div>
                    <div class="card-body">
                        <table id="thisTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Sl</th>
                                    <th>Emplpoyee Name</th>
                                    <th>Details</th>
                                    <th>Log</th>
                                    <th>Action</th> 
                                </tr>
                            </thead>
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
    var table = $('#thisTable').DataTable({
        serverSide: true,
        ajax: {
          url: "{{ route('get.prorota') }}",
          error: function(xhr, error, thrown) {
            console.log("XHR Error Response:", xhr.responseText);
            alert("An error occurred. Check console for details.");
          }
        },
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex'},
            {data: 'staff_name', name: 'staff_name' },
            // {data: 'schedule_type', name: 'schedule_type'},
            {
                data: 'id',
                name: 'details',
                render: function(data, type, full, meta) {
                    var viewButtonHtml = '<a href="{{ url('admin/prorota/details') }}/' + data + '" class="btn btn-success"><i class="fa fa-eye"></i></a>';
                    return viewButtonHtml;
                }
            },
            {
                data: 'id',
                name: 'log',
                render: function(data, type, full, meta) {
                    return '<a href="{{ url('admin/prorota-log') }}/' + data + '" class="btn btn-primary"><i class="fa fa-file-alt"></i></a>';
                }
            },
            {
                data: 'id',
                name: 'details',
                render: function(data, type, full, meta) {
                    var editButtonHtml = '<a href="{{ url('admin/prorota/edit') }}/' + data + '" class="btn btn-secondary"><i class="fa fa-edit"></i></a>';
                    var deleteButtonHtml = '<a href="#" class="btn btn-danger delete-prorota" data-prorota-id="' + data + '" style="margin-left: 10px;"><i class="fas fa-trash"></i></a>';
                    return editButtonHtml + deleteButtonHtml;
                }
            }
        ]
    });
  });
</script>


{{-- Delete prorota start --}}
<script>
    $(document).ready(function() {
        $(document).on('click', '.delete-prorota', function(e) {
            e.preventDefault();
            var prorotaId = $(this).data('prorota-id');

            if (confirm("Are you sure you want to delete this data?")) {
                $.ajax({
                    url: '/admin/delete-prorota/' + prorotaId, 
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') 
                    },
                    success: function(response) {
                        if (response.status === 200) {
                            toastr.success("Deleted successfully", 'Success');
                            $('#thisTable').DataTable().ajax.reload();
                        } else {
                        toastr.error("Failed to delete.", "Error!");
                        }
                    },
                    error: function(xhr, status, error) {
                    
                    }
                });
            }
        });
    });
</script>
{{-- Delete prorota start --}}

@endsection