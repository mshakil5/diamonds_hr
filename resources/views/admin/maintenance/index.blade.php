@extends('admin.layouts.admin')

@section('content')
<section id="newBtnSection" class="content">
  <div class="container-fluid">
    <button id="newBtn" class="btn btn-secondary my-3">Add new</button>
            <div class="errmsg"></div>
  </div>
</section>

<section id="addThisFormContainer" class="content mt-3" style="display:none;">
  <div class="container-fluid">
    <div class="card card-secondary">
      <div class="card-header"><h3 id="header-title" class="card-title">Add new maintenance</h3></div>
      <div class="card-body">
        <form id="createThisForm">
          @csrf
          <input type="hidden" id="codeid" name="codeid">
          <div class="row">
            <div class="col-6">
                <div class="form-group"><label>Name <span class="text-danger">*</span></label><input type="text" id="name" name="name" class="form-control"></div>
            </div>
            <div class="col-6">
                <div class="form-group"><label>Phone</label><input type="text" id="phone" name="phone" class="form-control"></div>
            </div>
            <div class="col-6">
                <div class="form-group"><label>Email</label><input type="email" id="email" name="email" class="form-control"></div>
            </div>
            <div class="col-6">  
                <div class="form-group"><label>Business Name</label><input type="text" id="business_name" name="business_name" class="form-control"></div>
            </div>
          </div>  
          <div class="form-group"><label>Description</label><textarea id="description" name="description" class="form-control"></textarea></div>
        </form>
      </div>
      <div class="card-footer">
        <button id="addBtn" class="btn btn-secondary" value="Create">Create</button>
        <button id="FormCloseBtn" class="btn btn-default">Cancel</button>
      </div>
    </div>
  </div>
</section>

<section id="contentContainer" class="content">
  <div class="container-fluid">
    <div class="card card-secondary">
      <div class="card-header"><h3 class="card-title">All Maintenance</h3></div>
      <div class="card-body">
        <table id="example1" class="table table-bordered table-striped">
          <thead>
            <tr>
              <th>Sl</th><th>Name</th><th>Phone</th><th>Email</th><th>Business</th><th>Status</th><th>Action</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($data as $i => $m)
            <tr>
              <td>{{$i+1}}</td>
              <td>{{$m->name}}</td>
              <td>{{$m->phone}}</td>
              <td>{{$m->email}}</td>
              <td>{{$m->business_name}}</td>
              <td>
                <div class="custom-control custom-switch">
                  <input type="checkbox" class="custom-control-input toggle-status" id="sw{{$m->id}}" data-id="{{$m->id}}"
                    {{$m->status ? 'checked' : ''}}>
                  <label class="custom-control-label" for="sw{{$m->id}}"></label>
                </div>
              </td>
              <td>
                <a id="EditBtn" rid="{{$m->id}}"><i class="fa fa-edit text-primary"></i></a>
                <a id="deleteBtn" rid="{{$m->id}}"><i class="fa fa-trash-o text-danger ml-2"></i></a>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</section>
@endsection

@section('script')
<script>
$(document).ready(function() {
  // New button click handler
  $("#newBtn").click(() => {
    $("#createThisForm")[0].reset();
    $("#codeid").val("");
    $("#header-title").text("Add new maintenance");
    $("#addBtn").val("Create").text("Create");
    $("#addThisFormContainer").show();
    $("#newBtn").hide();
  });

  // Form close button handler
  $("#FormCloseBtn").click(() => {
    $("#addThisFormContainer").hide();
    $("#newBtn").show();
  });

  const url = "{{ url('/admin/maintenance') }}";
  const upurl = "{{ url('/admin/maintenance-update') }}";

  // Add/Update button handler
  $("#addBtn").click(function() {
    let req = ['#name'], e = false;
    req.forEach(r => {
      if (!$(r).val()) {
        e = true;
        $('.errmsg').html('<div class="alert alert-danger">Please fill all required fields.</div>');
      }
    });
    if (e) return;

    let form = new FormData($("#createThisForm")[0]);
    let sendTo = $(this).val() == "Create" ? url : upurl;
    
    $.ajax({
      url: sendTo,
      type: 'POST',
      data: form,
      processData: false,
      contentType: false,
      success(d) {
        $('.errmsg').html(
          d.status == 422 
            ? `<div class="alert alert-danger">${d.message}</div>`
            : `<div class="alert alert-success">${d.message}</div>`
        );
        if (d.status == 200) setTimeout(() => location.reload(), 1500);
      },
      error: function(xhr, status, error) {
          console.error(xhr.responseText);
          showError('An error occurred. Please try again.');
      }
    });
  });

  // Edit button handler
  $("#contentContainer").on('click', '#EditBtn', function() {
    $.get(`${url}/${$(this).attr('rid')}/edit`, d => {
      Object.keys(d).forEach(k => $('#' + k).val(d[k] || ''));
      $("#codeid").val(d.id);
      $("#addBtn").val("Update").text("Update");
      $("#header-title").text("Update maintenance");
      $("#addThisFormContainer").show();
      $("#newBtn").hide();
    });
  });

  // Delete button handler
  $("#contentContainer").on('click', '#deleteBtn', function() {
    if (!confirm('Sure?')) return;
    $.get(`${url}/${$(this).attr('rid')}`, () => {
      $('.errmsg').html('<div class="alert alert-success">Deleted.</div>');
      setTimeout(() => location.reload(), 1500);
    });
  });

  // Toggle status handler
  $(document).on('change', '.toggle-status', function() {
    let id = $(this).data('id'),
        st = $(this).prop('checked') ? 1 : 0;
    $.post("{{ route('maintenance.status') }}", {
      id,
      status: st,
      _token: "{{csrf_token()}}"
    }, d => {
      $('.errmsg').html(
        `<div class="alert alert-${d.status == 200 ? 'success' : 'danger'}">${d.message}</div>`
      );
    });
  });

  // DataTable initialization
  $("#example1").DataTable({
    responsive: true,
    lengthChange: false,
    autoWidth: false,
    buttons: ["copy", "csv", "excel", "pdf", "print"]
  }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
});
</script>
@endsection