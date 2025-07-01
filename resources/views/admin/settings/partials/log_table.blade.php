<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>Sl</th>
            <th>Employee</th>
            <th>Performed By</th>
            <th>Time</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($logs as $key => $log)
        <tr>
            <td>{{ $key + 1 }}</td>
            <td>{{ optional(optional($log->subject)->employee)->name ?? 'Deleted Employee' }}</td>
            <td>{{ optional($log->causer)->name ?? 'System' }}</td>
            <td>{{ $log->created_at->diffForHumans() }}</td>
            <td>
                <button class="btn btn-info btn-sm" data-toggle="modal" data-target="#logModal{{ $log->id }}">
                    View Changes
                </button>
                @include('admin.settings.partials.log_modal', ['log' => $log])
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
