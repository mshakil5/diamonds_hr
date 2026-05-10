@extends('admin.layouts.admin')

@section('content')
<div class="container-fluid mt-3">
    <div class="row">
        <!-- Filters -->
        <div class="col-md-12">
            <div class="card card-secondary">
                <div class="card-header">
                    <h3 class="card-title">Multi-Branch Schedule at a Glance</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <label>From Date</label>
                            <input type="date" class="form-control" id="start_date">
                        </div>
                        <div class="col-md-3">
                            <label>To Date</label>
                            <input type="date" class="form-control" id="end_date">
                        </div>
                        <div class="col-md-4">
                            <label>Select Branches</label>
                            <select class="form-control select2" id="branch_ids" multiple="multiple" style="height: 40px;">
                                @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button class="btn btn-primary btn-block" id="generateBtn">Generate</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Report Container -->
        <div class="col-md-12 mt-4" id="reportContainer" style="display: none;">
            <div class="card">
                <div class="card-header text-center bg-white">
                    <h3 class="font-weight-bold">Work Schedule</h3>
                    <h5 id="reportSubtitle" class="text-muted"></h5>
                </div>
                <div class="card-body p-0" style="overflow-x: auto;">
                    <div id="reportTableArea" class="p-3">
                        <!-- Dynamic Table will be injected here -->
                    </div>
                </div>
                <div class="card-footer text-center">
                    <button class="btn btn-success" onclick="window.print()"><i class="fa fa-print"></i> Print Report</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    $(document).ready(function() {
        // Initialize Select2 with multiple selection
        $('.select2').select2({
            placeholder: "Select branches...",
            allowClear: true
        });

        $("#generateBtn").click(function() {
            var start = $("#start_date").val();
            var end   = $("#end_date").val();
            // Select2 multiple returns an array
            var branches = $("#branch_ids").val(); 

            if (!start || !end) {
                alert("Please select dates.");
                return;
            }
            if (!branches || branches.length === 0) {
                alert("Please select at least one branch.");
                return;
            }

            $(this).prop('disabled', true).text('Generating...');
            $("#reportContainer").hide();

            $.get("{{ route('admin.daily-prerotas.multi-report-data') }}", { 
                start_date: start, 
                end_date: end, 
                branch_ids: branches 
            }, function(res) {
                $("#generateBtn").prop('disabled', false).text('Generate');

                if (!res.success) {
                    alert(res.message || "No data found.");
                    return;
                }

                $("#reportSubtitle").text("From " + res.start + " to " + res.end);
                
                var html = '<table class="table table-bordered" style="min-width: 800px; font-size: 13px;">';
                
                // --- ROW 1: Branch Names ---
                html += '<thead><tr class="bg-secondary text-white text-center">';
                html += '<th rowspan="2" class="align-middle" style="width: 120px;">Day / Date</th>';
                
                res.branches.forEach(branch => {
                    // Each branch takes 2 columns (Name + Time)
                    html += '<th colspan="2">' + branch.name + '</th>';
                });
                html += '</tr>';

                // --- ROW 2: Sub-headers ---
                html += '<tr class="bg-gray-light text-dark font-weight-bold text-center">';
                res.branches.forEach(branch => {
                    html += '<th>Employee Name</th>';
                    html += '<th>Time</th>';
                });
                html += '</tr></thead>';

                // --- BODY: Dates and Data ---
                html += '<tbody>';
                res.dates.forEach(dateObj => {
                    html += '<tr>';
                    // First Column: Day Name and Date
                    html += '<td class="font-weight-bold text-center align-middle">' + dateObj.day_name + '<br><small>(' + dateObj.formatted_date + ')</small></td>';
                    
                    // Subsequent Columns: Branch Data
                    res.branches.forEach(branch => {
                        var staffList = res.data[dateObj.full_date]?.[branch.id] || [];
                        
                        var nameCell = '';
                        var timeCell = '';

                        if (staffList.length > 0) {
                            staffList.forEach(staff => {
                                nameCell += staff.name + '<br>';
                                timeCell += staff.time + '<br>';
                            });
                        }

                        html += '<td style="white-space: pre-line; vertical-align: top;">' + nameCell + '</td>';
                        html += '<td style="white-space: pre-line; vertical-align: top;">' + timeCell + '</td>';
                    });
                    
                    html += '</tr>';
                });
                html += '</tbody></table>';
                
                $("#reportTableArea").html(html);
                $("#reportContainer").fadeIn();
            }).fail(function() {
                alert("Error generating report.");
                $("#generateBtn").prop('disabled', false).text('Generate');
            });
        });
    });
</script>

{{-- Print Styles --}}
<style>
    @media print {
        body * { visibility: hidden; }
        #reportContainer, #reportContainer * { visibility: visible; }
        #reportContainer { position: absolute; left: 0; top: 0; width: 100%; }
        .btn { display: none !important; }
        .card-header { border: none !important; box-shadow: none !important; }
        /* Force landscape printing for wide tables */
        @page { size: landscape; }
    }
    .bg-gray-light { background-color: #f4f4f4 !important; }
</style>
@endsection