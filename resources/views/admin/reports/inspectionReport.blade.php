@extends('admin.layouts.admin')

@section('content')
<section class="content mt-3">
    <div class="container-fluid">
        <div class="card card-secondary">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-search mr-1"></i> Filter Inspections</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('inspectionReport.search') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>From Date</label>
                                <input type="date" class="form-control" name="from_date" value="{{ request('from_date') }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>To Date</label>
                                <input type="date" class="form-control" name="to_date" value="{{ request('to_date') }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Branch</label>
                                <select class="form-control select2" name="branch_id">
                                    <option value="">All Branches</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Floor</label>
                                <select class="form-control select2" name="floor_id">
                                    <option value="">All Floors</option>
                                    @foreach($floors as $floor)
                                        <option value="{{ $floor->id }}" {{ request('floor_id') == $floor->id ? 'selected' : '' }}>{{ $floor->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Inspector</label>
                                <select class="form-control select2" name="employee_id">
                                    <option value="">All Employees</option>
                                    @foreach($employees as $emp)
                                        <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>{{ $emp->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label>&nbsp;</label><br>
                            <button type="submit" class="btn btn-secondary btn-block">
                                <i class="fa fa-search"></i> Search
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card card-outline card-info">
            <div class="card-header">
                <div id="report-header" style="text-align: center;">
                    <h2 class="mb-0">Room Inspection Report</h2>
                    <p class="text-muted">Generated on: {{ now()->format('d M Y') }}</p>
                </div>
            </div>
            <div class="card-body">
                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Inspector</th>
                            <th>Branch</th>
                            <th>Floor</th>
                            <th>Room</th>
                            <th>Items Checked</th>
                            <th>Notes</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($inspections as $inspection)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($inspection->date)->format('d-m-Y') }}</td>
                                <td>{{ $inspection->user->name ?? 'System' }}</td>
                                <td>{{ $inspection->branch->name ?? 'N/A' }}</td>
                                <td>{{ $inspection->floor->name ?? 'N/A' }}</td>
                                <td><span class="badge badge-info">{{ $inspection->room }}</span></td>
                                <td>
                                    <span class="badge badge-success">{{ $inspection->items->count() }} Items</span>
                                </td>
                                <td><small>{{ Str::limit($inspection->note, 50) }}</small></td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-primary view-details" data-id="{{ $inspection->id }}">
                                        <i class="fas fa-eye"></i> View
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>


<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<div class="modal fade" id="inspectionDetailsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title">Inspection Report Detail</h5>
                <div>
                    <button type="button" class="btn btn-sm btn-success mr-2" id="downloadPDF">
                        <i class="fas fa-download"></i> Download PDF
                    </button>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
            </div>
            
            <div class="modal-body" id="pdfContent" style="padding: 30px; background: #fff;">
                <div class="text-center mb-4 border-bottom pb-3">
                    <h2 class="mb-0 text-uppercase" style="letter-spacing: 2px;">Room Inspection Report</h2>
                    <p class="text-muted" id="modalBranchName" style="font-size: 1.2rem;"></p>
                </div>

                <div class="row mb-4 bg-light p-3 rounded">
                    <div class="col-6 mb-2">
                        <small class="text-muted d-block">ROOM / SUITE</small>
                        <strong id="modalRoom" class="text-primary" style="font-size: 1.1rem;"></strong>
                    </div>
                    <div class="col-6 mb-2 text-right">
                        <small class="text-muted d-block">INSPECTION DATE</small>
                        <strong id="modalDate"></strong>
                    </div>
                    <div class="col-4">
                        <small class="text-muted d-block">FLOOR</small>
                        <span id="modalFloor"></span>
                    </div>
                    <div class="col-4 text-center">
                        <small class="text-muted d-block">INSPECTED BY</small>
                        <span id="modalInspector"></span>
                    </div>
                    <div class="col-4 text-right">
                        <small class="text-muted d-block">STATUS</small>
                        <span class="badge badge-success">COMPLETED</span>
                    </div>
                </div>

                <div id="checklistResult">
                    </div>

                <div class="mt-4 p-3 border-left border-info" style="background: #f8f9fa;">
                    <h6><strong>Manager/Inspector Notes:</strong></h6>
                    <p id="modalNote" class="mb-0 italic text-muted"></p>
                </div>

                <div class="mt-5 text-center border-top pt-3" style="font-size: 0.8rem; color: #aaa;">
                    Verified on <span class="currentDate"></span>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection

@section('script')
    <script>
        $(document).on('click', '.view-details', function() {
            let id = $(this).data('id');
            $('#inspectionDetailsModal').modal('show');
            $('.currentDate').text(new Date().toLocaleDateString());

            $.get("/admin/inspection-details/" + id, function(data) {
                // Fill Header & Meta Info
                $('#modalBranchName').text(data.inspection.branch.name);
                $('#modalRoom').text("ROOM " + data.inspection.room);
                $('#modalDate').text(data.inspection.date);
                $('#modalFloor').text(data.inspection.floor.name);
                $('#modalInspector').text(data.inspection.employee ? data.inspection.employee.name : 'System Admin');
                $('#modalNote').text(data.inspection.note || 'No specific observations recorded.');

                let html = '';
                data.categories.forEach(function(cat) {
                    html += `
                        <div class="mt-4 mb-2 border-bottom">
                            <h6 class="text-secondary font-weight-bold text-uppercase" style="font-size: 0.9rem;">${cat.name}</h6>
                        </div>
                        <div class="row">`;
                    
                    cat.item.forEach(function(item) {
                        let isChecked = data.checked_ids.includes(item.id);
                        let icon = isChecked 
                            ? '<i class="fas fa-check-square text-success"></i>' 
                            : '<i class="far fa-square text-muted"></i>';
                        let style = isChecked ? 'color: #28a745; font-weight: 500;' : 'color: #adb5bd;';

                        html += `
                            <div class="col-md-4 mb-2" style="font-size: 0.9rem;">
                                ${icon} <span style="${style}">${item.name}</span>
                            </div>`;
                    });
                    html += `</div>`;
                });

                $('#checklistResult').html(html);
            });
        });

        // Download PDF Logic
        $('#downloadPDF').click(function() {
            const element = document.getElementById('pdfContent');
            const room = $('#modalRoom').text();
            
            const opt = {
                margin:       0.5,
                filename:     `Inspection_Report_${room}.pdf`,
                image:        { type: 'jpeg', quality: 0.98 },
                html2canvas:  { scale: 2 },
                jsPDF:        { unit: 'in', format: 'letter', orientation: 'portrait' }
            };

            // New API for html2pdf to handle professional styling
            html2pdf().set(opt).from(element).save();
        });
    </script>
@endsection