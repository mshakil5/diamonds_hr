@extends('admin.layouts.admin')

@section('content')
<div class="container-fluid mt-3">
    <div class="row">
        <!-- Filters -->
        <div class="col-md-12">
            <div class="card card-secondary">
                <div class="card-header">
                    <h3 class="card-title">Generate Weekly Report</h3>
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
                        <div class="col-md-3">
                            <label>Branch</label>
                            <select class="form-control select2" id="branch_id">
                                <option value="">Select Branch</option>
                                @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button class="btn btn-primary btn-block" id="generateBtn">Generate Report</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Report Container -->
        <div class="col-md-12 mt-4" id="reportContainer" style="display: none;">
            <div class="card">
                <div class="card-header text-center">
                    <h3 class="font-weight-bold">Weekly Report</h3>
                    <h5 id="reportSubtitle" class="text-muted"></h5>
                </div>
                <div class="card-body p-0" id="reportTableArea">
                    <!-- Dynamic Table will be injected here -->
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
        $('.select2').select2();

        $("#generateBtn").click(function() {
            var start = $("#start_date").val();
            var end   = $("#end_date").val();
            var branch = $("#branch_id").val();

            if (!start || !end || !branch) {
                alert("Please fill all fields.");
                return;
            }

            $(this).prop('disabled', true).text('Generating...');
            $("#reportContainer").hide();

            $.get("{{ route('admin.daily-prerotas.report-data') }}", { start_date: start, end_date: end, branch_id: branch }, function(res) {
                $("#generateBtn").prop('disabled', false).text('Generate Report');

                if (!res.success) {
                    alert(res.message);
                    return;
                }

                $("#reportSubtitle").text("From " + res.start + " to " + res.end + " | Branch: " + res.branch);
                
                var html = '<table class="table table-bordered table-sm" style="font-size: 12px;">';
                
                // 1. Date Row
                html += '<thead><tr class="bg-secondary text-white"><th style="width: 120px;">Time Range</th>';
                res.columns.forEach(col => {
                    html += '<th class="text-center">' + col.day_num + '</th>';
                });
                html += '</tr>';

                // 2. Day Name Row
                html += '<tr class="bg-gray-light text-dark font-weight-bold"><th>Day</th>';
                res.columns.forEach(col => {
                    html += '<th class="text-center">' + col.day_name + '</th>';
                });
                html += '</tr></thead><tbody>';

                // 3. Time Slots & Matrix Data
                res.timeSlots.forEach(slot => {
                    html += '<tr>';
                    html += '<td class="font-weight-bold text-center">' + slot + '</td>';
                    
                    res.columns.forEach(col => {
                        var staffName = res.matrix[slot][col.full_date] || '';
                        // Highlight cell if someone is working
                        var bgClass = staffName ? 'bg-light-primary' : '';
                        html += '<td class="text-center ' + bgClass + '">' + staffName + '</td>';
                    });
                    
                    html += '</tr>';
                });

                html += '</tbody></table>';
                
                $("#reportTableArea").html(html);
                $("#reportContainer").fadeIn();
            }).fail(function() {
                alert("Error generating report.");
                $("#generateBtn").prop('disabled', false).text('Generate Report');
            });
        });
    });
</script>

{{-- Print Styles to hide everything except the report when printing --}}
<style>
    @media print {
        body * { visibility: hidden; }
        #reportContainer, #reportContainer * { visibility: visible; }
        #reportContainer { position: absolute; left: 0; top: 0; width: 100%; }
        .btn { display: none !important; }
        .card-header { border: none !important; }
    }
    .bg-light-primary { background-color: #e8f4fd !important; }
</style>
@endsection