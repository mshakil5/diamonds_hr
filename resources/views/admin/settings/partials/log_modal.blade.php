<div class="modal fade" id="logModal{{ $log->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Log Details</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <strong>Event:</strong> {{ $log->description }} <br><br>
                <strong>Changes:</strong>
                <table class="table table-bordered table-sm">
                    <thead>
                        <tr>
                            <th>Property</th>
                            <th>Old</th>
                            <th>New</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($log->properties['attributes'] ?? [] as $key => $newValue)
                            @php
                                $oldValue = $log->properties['old'][$key] ?? null;
                            @endphp
                            @if ($oldValue !== null && $oldValue != $newValue)
                                <tr>
                                    <td>{{ $key }}</td>
                                    <td>{{ $oldValue }}</td>
                                    <td>{{ $newValue }}</td>
                                </tr>
                            @elseif($oldValue === null)
                                <tr>
                                    <td>{{ $key }}</td>
                                    <td><em>—</em></td>
                                    <td>{{ $newValue }}</td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
