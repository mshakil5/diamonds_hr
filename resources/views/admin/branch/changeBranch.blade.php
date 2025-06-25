@extends('admin.layouts.admin')

@section('content')

<section class="content" id="newBtnSection">
    <div class="container-fluid">
        <div class="row">
            <div class="col-2">
                
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
                        <h3 class="card-title" id="header-title">Change branch</h3>
                    </div>
                    <div class="card-body">
                        <div class="errmsg"></div>
                        <form id="createThisForm">
                            @csrf
                            <div class="form-group">
                                <label>Branch <span class="text-danger">*</span></label>
                                <select class="form-control" id="branch_id" name="branch_id">

                                    <option value="">Select</option>
                                    @foreach ($branch as $item)

                                    <option value="{{$item->id}}">{{$item->name}}</option>
                                        
                                    @endforeach
                                    
                                </select>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer">
                        <button type="submit" id="addBtn" class="btn btn-secondary" value="Update">Change your branch</button>
                        {{-- <button type="submit" id="FormCloseBtn" class="btn btn-default">Cancel</button> --}}
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
        

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        var upurl = "{{URL::to('/admin/change-branches')}}";

        $("#addBtn").click(function() {
            
            var requiredFields = ['#branch_id'];
            for (var i = 0; i < requiredFields.length; i++) {
                if ($(requiredFields[i]).val() === '') {
                    showError('Please fill all required fields.');
                    return;
                }
            }

            var form_data = new FormData();
            form_data.append("branch_id", $("#branch_id").val());

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
        });

        
    });
</script>

@endsection