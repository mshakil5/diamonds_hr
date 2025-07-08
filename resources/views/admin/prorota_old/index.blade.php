@extends('admin.layouts.admin')

@section('content')

<!-- Main content -->
<section class="content" id="newBtnSection">
    <div class="container-fluid">
        <div class="row">
            <div class="col-2">
                <a href="{{ route('prorota.create') }}" class="btn btn-secondary my-3" id="newBtn">Add new</a>
            </div>
            <div class="col-2">
                <button class="btn btn-primary my-3" id="downloadPdfBtn">Download PDF</button>
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
                                    <th>Employee Name</th>
                                    <th>Schedule Type</th>
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

<!-- Details Modal -->
<div class="modal fade" id="detailsModal" tabindex="-1" role="dialog" aria-labelledby="detailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailsModalLabel">Schedule Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="detailsContent">
                    <!-- Details will be loaded here via AJAX -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

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
            {data: 'staff_name', name: 'staff_name'},
            {data: 'schedule_type', name: 'schedule_type'}, 
            {
                data: 'id',
                name: 'details',
                render: function(data, type, full, meta) {
                    return '<button class="btn btn-success view-details" data-prorota-id="' + data + '"><i class="fa fa-eye"></i></button>';
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
                name: 'action',
                render: function(data, type, full, meta) {
                    var editButtonHtml = '<a href="{{ url('admin/prorota/edit') }}/' + data + '" class="btn btn-secondary"><i class="fa fa-edit"></i></a>';
                    var deleteButtonHtml = '<a href="#" class="btn btn-danger delete-prorota" data-prorota-id="' + data + '" style="margin-left: 10px;"><i class="fas fa-trash"></i></a>';
                    return editButtonHtml + deleteButtonHtml;
                }
            }
        ]
    });

    // View Details in Modal
    $(document).on('click', '.view-details', function(e) {
        e.preventDefault();
        var prorotaId = $(this).data('prorota-id');

        $.ajax({
            url: '{{ url("admin/prorota/details") }}/' + prorotaId,
            type: 'GET',
            success: function(response) {
                if (response.status === 200) {
                    $('#detailsContent').html(response.data);
                    $('#detailsModal').modal('show');
                } else {
                    toastr.error('Failed to load details.', 'Error');
                }
            },
            error: function(xhr, status, error) {
                console.error("Error occurred: " + error);
                toastr.error('An error occurred while loading details.', 'Error');
            }
        });
    });

    // Download PDF
    $(document).on('click', '#downloadPdfBtn', function(e) {
        e.preventDefault();
        window.location.href = '{{ route("prorota.download-pdf") }}';
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
                        toastr.error("An error occurred while deleting.", "Error!");
                    }
                });
            }
        });
    });
</script>
{{-- Delete prorota end --}}

@endsection