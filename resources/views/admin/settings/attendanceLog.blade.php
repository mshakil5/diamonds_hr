@extends('admin.layouts.admin')

@section('content')
<section class="content" id="contentContainer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-secondary">
                    <div class="card-header p-2">
                        <ul class="nav nav-pills">
                            <li class="nav-item"><a class="nav-link active" href="#created" data-toggle="tab">Created</a></li>
                            <li class="nav-item"><a class="nav-link" href="#updated" data-toggle="tab">Updated</a></li>
                            <li class="nav-item"><a class="nav-link" href="#deleted" data-toggle="tab">Deleted</a></li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            {{-- Created Tab --}}
                            <div class="tab-pane active" id="created">
                                @include('admin.settings.partials.log_table', ['logs' => $createdLogs])
                            </div>

                            {{-- Updated Tab --}}
                            <div class="tab-pane" id="updated">
                                @include('admin.settings.partials.log_table', ['logs' => $updatedLogs])
                            </div>

                            {{-- Deleted Tab --}}
                            <div class="tab-pane" id="deleted">
                                @include('admin.settings.partials.log_table', ['logs' => $deletedLogs])
                            </div>
                        </div>
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
        $(".table").DataTable({
            responsive: true,
            lengthChange: false,
            autoWidth: false,
        }).buttons().container().appendTo('.dataTables_wrapper .col-md-6:eq(0)');
    });
</script>
@endsection
