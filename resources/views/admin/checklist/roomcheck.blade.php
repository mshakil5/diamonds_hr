@extends('admin.layouts.admin')

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Room Checklist Management</h1>
            </div>
        </div>
    </div>
</section>

<section class="content" id="contentContainer">
    <div class="container-fluid">
        {{-- Alert Messages --}}
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h5><i class="icon fas fa-ban"></i> Validation Error!</h5>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h5><i class="icon fas fa-check"></i> Success!</h5>
                {{ session('success') }}
            </div>
        @endif

        <div class="row">
            {{-- Left Side: History/List --}}
            <div class="col-md-5">
                <div class="card card-outline card-info">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-list mr-1"></i> Recent Checklists</h3>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Branch</th>
                                    <th>Room</th>
                                    <th class="text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $row)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($row->date)->format('d M, Y') }}</td>
                                        <td>{{ $row->branch->name ?? 'N/A' }}</td>
                                        <td><span class="badge badge-secondary">{{ $row->room }}</span></td>
                                        <td class="text-right">
                                            <button type="button" class="btn btn-sm btn-info edit-btn" data-id="{{ $row->id }}" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">No inspections found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Right Side: Form --}}
            <div class="col-md-7">
                <div class="card card-outline card-success">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-plus-circle mr-1"></i> New Room Inspection</h3>
                    </div>
                    <form id="inspectionForm"> 
                        @csrf
                        <input type="hidden" name="inspection_id" id="inspection_id">
                        <div class="card-body">
                            <div class="form-group">
                                <label for="branch_id">Branch <span class="text-danger">*</span></label>
                                <select class="form-control select2" id="branch_id" name="branch_id" style="width: 100%;" required>
                                    <option value="">Select Branch</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="floor_id">Floor <span class="text-danger">*</span></label>
                                        <select class="form-control select2" id="floor_id" name="floor_id" style="width: 100%;" required>
                                            <option value="">Select Floor</option>
                                            @foreach($floors as $floor)
                                                <option value="{{ $floor->id }}">{{ $floor->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="room">Room <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="room" name="room" placeholder="e.g. 101 or Suite A">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="note">General Notes</label>
                                <textarea name="note" id="note" class="form-control" rows="2" placeholder="Enter any observation..."></textarea>
                            </div>

                            <hr>
                            <label class="text-muted text-uppercase" style="font-size: 0.8rem;">Checklist Items</label>
                            
                            <div class="checklist-container mt-2" style="max-height: 400px; overflow-y: auto;">
                                @foreach ($categories as $cat)
                                    <div class="mb-3">
                                        <h6 class="font-weight-bold text-primary border-bottom pb-1">{{ $cat->name }}</h6>
                                        <div class="row">
                                            @foreach ($cat->item as $item)
                                                <div class="col-sm-6">
                                                    <div class="custom-control custom-checkbox">
                                                        <input class="custom-control-input" type="checkbox" id="checkitemid{{$item->id}}" name="checkitem[]" value="{{$item->id}}">
                                                        <label class="custom-control-label font-weight-normal" for="checkitemid{{$item->id}}">
                                                            {{$item->name}}
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="card-footer bg-white">
                            <button class="btn btn-success btn-block" id="submitBtn" type="submit">
                                <i class="fa fa-save mr-1"></i> <span id="btnText">Save Inspection Report</span>
                            </button>
                            <button class="btn btn-secondary btn-block mt-2" id="resetBtn" type="button" style="display:none;">
                                Cancel Edit
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('script')
<script>
    $(document).ready(function() {
        // Professional dropdowns
        $('.select2').select2({
            placeholder: "Select an option"
        });
    });


    $(document).ready(function() {
        const form = $('#inspectionForm');
        const submitBtn = $('#submitBtn');
        const btnText = $('#btnText');
        const resetBtn = $('#resetBtn');

        // 1. Handle Submit (Create or Update)
        form.on('submit', function(e) {
            e.preventDefault();
            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');

            $.ajax({
                url: "{{ route('inspectionStore') }}",
                method: "POST",
                data: new FormData(this),
                processData: false,
                contentType: false,
                success: function(response) {
                    if(response.success) {
                        toastr.success(response.message); // Or use standard alert
                        location.reload(); // Reload to update the list on the left
                    }
                },
                error: function(xhr) {
                    submitBtn.prop('disabled', false).html('<i class="fa fa-save mr-1"></i> Save Inspection');
                    
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        // This will show you exactly which field failed in the browser console
                        console.log(errors); 
                        alert("Validation Failed: " + Object.values(errors).flat().join('\n'));
                    } else {
                        alert("Server Error: " + xhr.statusText);
                    }
                }
            });
        });

        // 2. Handle Edit Click
        $(document).on('click', '.edit-btn', function() {
            let id = $(this).data('id');
            btnText.text('Update Inspection Report');
            submitBtn.removeClass('btn-success').addClass('btn-primary');
            resetBtn.show();

            $.get("/admin/inspection-edit/" + id, function(data) {
                $('#inspection_id').val(data.id);
                $('#branch_id').val(data.branch_id).trigger('change');
                $('#floor_id').val(data.floor_id).trigger('change');
                $('#room').val(data.room);
                $('#note').val(data.note);

                // Uncheck all items first
                $('.custom-control-input').prop('checked', false);
                // Check the items returned from DB
                data.items.forEach(function(item) {
                    $(`#checkitemid${item.checklist_item_id}`).prop('checked', true);
                });
                
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        });

        // 3. Reset Form
        resetBtn.on('click', function() {
            form[0].reset();
            $('#inspection_id').val('');
            $('.select2').val('').trigger('change');
            btnText.text('Save Inspection Report');
            submitBtn.removeClass('btn-primary').addClass('btn-success');
            $(this).hide();
        });
    });


</script>
@endsection