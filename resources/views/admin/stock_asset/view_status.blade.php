@extends('admin.layouts.admin')

@section('content')
<section class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">
        <a href="{{ url()->previous() }}" class="btn btn-secondary my-2">← Back</a>
        <div class="card card-secondary">
          <div class="card-header d-flex justify-content-between align-items-center">
            @php
              $statusLabel = [1 => 'Assigned', 2 => 'In Storage', 3 => 'Under Repair', 4 => 'Damaged'];
            @endphp
            <h3 class="card-title">
              {{ $stock->assetType->name ?? 'N/A' }} - Status: {{ $statusLabel[$status] ?? 'Unknown' }}
            </h3>
          </div>

          <div class="card-body">
            <table id="statusTable" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>SL</th>
                  <th>Product Code</th>
                  @if($status == 1)
                    <th>Location</th>
                  @endif
                </tr>
              </thead>
              <tbody>
                @foreach($assets as $key => $item)
                  <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $item->product_code }}</td>
                    @if($status == 1)
                      <td>{{ $item->location->name ?? 'N/A' }}</td>
                    @endif
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
  $(document).ready(function () {
    $('#statusTable').DataTable({
      responsive: true,
      autoWidth: false,
      lengthChange: false,
      buttons: ["copy", "csv", "excel", "pdf", "print"]
    }).buttons().container().appendTo('#statusTable_wrapper .col-md-6:eq(0)');
  });
</script>
@endsection