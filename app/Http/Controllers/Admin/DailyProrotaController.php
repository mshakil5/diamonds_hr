<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;
use App\Models\DailyPreRota;
use App\Models\DailyPreRotaDetail;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DailyProrotaController extends Controller
{
        public function index()
    {
        $data = DailyPreRota::with('branch', 'details')
            ->orderby('id', 'DESC')
            ->get();
            
        $employees = User::where('is_type', 0)->get(); 
        $branches = Branch::all();

        return view('admin.daily-prerota.index', compact('data', 'employees', 'branches'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'from_date'  => 'required|date',
            'to_date'    => 'required|date|after_or_equal:from_date',
            'employee_id'=> 'required|exists:users,id',
            'note'       => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 422, 'message' => $validator->errors()->first()]);
        }

        $employee = User::find($request->employee_id);
        $employeeBranchId = $employee->branch_id ?? Auth::user()->branch_id;

        try {
            DB::beginTransaction();

            $rota = new DailyPreRota();
            $rota->staff_id   = $request->employee_id; 
            $rota->date       = $request->from_date;
            $rota->branch_id  = $employeeBranchId; 
            $rota->note       = $request->note;
            $rota->status     = 1;
            $rota->created_by = Auth::user()->id;
            $rota->save();

            if ($request->has('staff_ids')) {
                foreach ($request->staff_ids as $index => $staffId) {
                    $date       = $request->dates[$index] ?? null;
                    $start      = $request->start_times[$index] ?? null;
                    $end        = $request->end_times[$index] ?? null;
                    $detailNote = $request->detail_notes[$index] ?? null;
                    $detailBranchId = $request->branch_ids[$index] ?? $employeeBranchId; // NEW: Get branch from row
                    $timeRange  = ($start && $end) ? $start . ' - ' . $end : null;

                    $existing = DailyPreRotaDetail::where('staff_id', $staffId)->where('date', $date)->first();

                    if ($existing) {
                        $existing->update([
                            'daily_pre_rota_id' => $rota->id,
                            'branch_id'  => $detailBranchId, // UPDATED: Use row-level branch
                            'time_range' => $timeRange,
                            'note'       => $detailNote,
                            'status'     => 1,
                            'updated_by' => Auth::user()->id,
                        ]);
                    } else {
                        DailyPreRotaDetail::create([
                            'daily_pre_rota_id' => $rota->id,
                            'staff_id'   => $staffId,
                            'branch_id'  => $detailBranchId, // UPDATED: Use row-level branch
                            'date'       => $date,
                            'time_range' => $timeRange,
                            'note'       => $detailNote,
                            'status'     => 1,
                            'created_by' => Auth::user()->id,
                        ]);
                    }
                }
            }

            DB::commit();
            return response()->json(['status' => 200, 'message' => 'Pre-rota created successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 500, 'message' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'codeid'     => 'required|exists:daily_pre_rotas,id',
            'from_date'  => 'required|date',
            'to_date'    => 'required|date|after_or_equal:from_date',
            'employee_id'=> 'required|exists:users,id',
            'note'       => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 422, 'message' => $validator->errors()->first()]);
        }

        $employee = User::find($request->employee_id);
        $employeeBranchId = $employee->branch_id ?? Auth::user()->branch_id;

        try {
            DB::beginTransaction();

            $rota = DailyPreRota::findOrFail($request->codeid);
            $rota->staff_id   = $request->employee_id; 
            $rota->date       = $request->from_date;
            $rota->branch_id  = $employeeBranchId; 
            $rota->note       = $request->note;
            $rota->updated_by = Auth::user()->id;
            $rota->save();

            DailyPreRotaDetail::where('daily_pre_rota_id', $rota->id)->delete();

            if ($request->has('staff_ids')) {
                foreach ($request->staff_ids as $index => $staffId) {
                    $date       = $request->dates[$index] ?? null;
                    $start      = $request->start_times[$index] ?? null;
                    $end        = $request->end_times[$index] ?? null;
                    $detailNote = $request->detail_notes[$index] ?? null;
                    $detailBranchId = $request->branch_ids[$index] ?? $employeeBranchId; // NEW: Get branch from row
                    $timeRange  = ($start && $end) ? $start . ' - ' . $end : null;

                    DailyPreRotaDetail::create([
                        'daily_pre_rota_id' => $rota->id,
                        'staff_id'   => $staffId,
                        'branch_id'  => $detailBranchId, // UPDATED: Use row-level branch
                        'date'       => $date,
                        'time_range' => $timeRange,
                        'note'       => $detailNote,
                        'status'     => 1,
                        'created_by' => Auth::user()->id,
                        'updated_by' => Auth::user()->id,
                    ]);
                }
            }

            DB::commit();
            return response()->json(['status' => 200, 'message' => 'Pre-rota updated successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 500, 'message' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function edit($id)
    {
        $rota    = DailyPreRota::with('details')->findOrFail($id);
        $details = $rota->details;
        $branches = Branch::all(); // NEW: Get branches for dropdown

        $prop = '';
        foreach ($details as $key => $detail) {
            $staff     = User::find($detail->staff_id);
            $staffName = $staff ? $staff->name : 'Unknown';
            $dayName   = $detail->date ? Carbon::parse($detail->date)->format('l') : '';
            $times     = $detail->time_range ? explode(' - ', $detail->time_range) : [null, null];
            $startTime = $times[0] ?? '';
            $endTime   = $times[1] ?? '';

            // NEW: Build branch dropdown options
            $branchOptions = '';
            foreach ($branches as $branch) {
                $selected = ($detail->branch_id == $branch->id) ? 'selected' : '';
                $branchOptions .= '<option value="' . $branch->id . '" ' . $selected . '>' . $branch->name . '</option>';
            }

            $prop .= '<div class="row schedule-row mb-2">';
            $prop .= '<input type="hidden" name="staff_ids[]" value="' . $detail->staff_id . '">';
            
            $prop .= '<div class="col-md-2"><input type="text" class="form-control" value="' . $staffName . '" readonly></div>';
            $prop .= '<div class="col-md-2"><input type="text" class="form-control" name="dates[]" value="' . $detail->date . '" readonly></div>';
            $prop .= '<div class="col-md-1"><input type="text" class="form-control" value="' . $dayName . '" readonly></div>';
            
            $prop .= '<div class="col-md-1"><div class="input-group date timepicker" id="start_time_' . $key . '" data-target-input="nearest">';
            $prop .= '<input type="text" name="start_times[]" class="form-control datetimepicker-input start-time" data-target="#start_time_' . $key . '" value="' . $startTime . '"/>';
            $prop .= '<div class="input-group-append" data-target="#start_time_' . $key . '" data-toggle="datetimepicker"><div class="input-group-text"><i class="fa fa-clock-o"></i></div></div></div></div>';
            
            $prop .= '<div class="col-md-1"><div class="input-group date timepicker" id="end_time_' . $key . '" data-target-input="nearest">';
            $prop .= '<input type="text" name="end_times[]" class="form-control datetimepicker-input end-time" data-target="#end_time_' . $key . '" value="' . $endTime . '"/>';
            $prop .= '<div class="input-group-append" data-target="#end_time_' . $key . '" data-toggle="datetimepicker"><div class="input-group-text"><i class="fa fa-clock-o"></i></div></div></div></div>';
            
            // NEW: Branch dropdown column
            $prop .= '<div class="col-md-2"><select name="branch_ids[]" class="form-control row-branch-select">';
            $prop .= '<option value="">Select</option>';
            $prop .= $branchOptions;
            $prop .= '</select></div>';
            
            $prop .= '<div class="col-md-2"><input type="text" name="detail_notes[]" class="form-control" value="' . $detail->note . '" placeholder="Enter note"></div>';
            
            $prop .= '<div class="col-md-1 d-flex align-items-center"><button type="button" class="btn btn-danger btn-sm remove-row"><i class="fa fa-trash"></i></button></div>';
            $prop .= '</div>';
        }

        return response()->json([
            'branch_id'    => $rota->branch_id, 
            'rota'         => $rota,
            'staff_id'     => $details->first()->staff_id ?? null,
            'details_html' => $prop,
        ]);
    }

    public function delete($id)
    {
        $data = DailyPreRota::findOrFail($id);
        $data->delete();

        return response()->json(['status' => 200, 'message' => 'Data deleted successfully.']);
    }

    public function checkStaffSchedule(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:users,id',
            'start_date'  => 'required|date',
            'end_date'    => 'nullable|date|after_or_equal:start_date',
        ]);

        $start_date = Carbon::parse($request->start_date);
        $end_date   = $request->end_date ? Carbon::parse($request->end_date) : $start_date;
        $staff      = User::find($request->employee_id);
        $branches   = Branch::all(); // NEW: Get branches for dropdown

        $existingDetails = DailyPreRotaDetail::where('staff_id', $request->employee_id)
            ->whereBetween('date', [$start_date, $end_date])
            ->get();

        // NEW: Build branch dropdown options HTML
        $branchOptionsHtml = '';
        foreach ($branches as $branch) {
            $selected = ($staff->branch_id == $branch->id) ? 'selected' : '';
            $branchOptionsHtml .= '<option value="' . $branch->id . '" ' . $selected . '>' . $branch->name . '</option>';
        }

        $prop  = '';
        $index = 0;
        $current_date = $start_date->copy();

        while ($current_date <= $end_date) {
            $date_str = $current_date->toDateString();
            $day_name = $current_date->format('l');

            $existing = $existingDetails->firstWhere('date', $date_str);

            if ($existing) {
                $times     = $existing->time_range ? explode(' - ', $existing->time_range) : [null, null];
                $startTime = $times[0] ?? '';
                $endTime   = $times[1] ?? '';
                $note      = $existing->note ?? '';
                // NEW: Use existing branch if found
                $existingBranchId = $existing->branch_id ?? $staff->branch_id;
                $rowBranchOptions = '';
                foreach ($branches as $branch) {
                    $selected = ($existingBranchId == $branch->id) ? 'selected' : '';
                    $rowBranchOptions .= '<option value="' . $branch->id . '" ' . $selected . '>' . $branch->name . '</option>';
                }
            } else {
                $startTime = '09:00';
                $endTime   = '17:30';
                $note      = '';
                $rowBranchOptions = $branchOptionsHtml; // Use default options
            }

            $prop .= '<div class="row schedule-row mb-2">';
            $prop .= '<input type="hidden" name="staff_ids[]" value="' . $staff->id . '">';
            
            $prop .= '<div class="col-md-2"><input type="text" class="form-control" value="' . $staff->name . '" readonly></div>';
            $prop .= '<div class="col-md-2"><input type="text" class="form-control" name="dates[]" value="' . $date_str . '" readonly></div>';
            $prop .= '<div class="col-md-1"><input type="text" class="form-control" value="' . $day_name . '" readonly></div>';
            
            $prop .= '<div class="col-md-1"><div class="input-group date timepicker" id="start_time_' . $index . '" data-target-input="nearest">';
            $prop .= '<input type="text" name="start_times[]" class="form-control datetimepicker-input start-time" data-target="#start_time_' . $index . '" value="' . $startTime . '"/>';
            $prop .= '<div class="input-group-append" data-target="#start_time_' . $index . '" data-toggle="datetimepicker"><div class="input-group-text"><i class="fa fa-clock-o"></i></div></div></div></div>';
            
            $prop .= '<div class="col-md-1"><div class="input-group date timepicker" id="end_time_' . $index . '" data-target-input="nearest">';
            $prop .= '<input type="text" name="end_times[]" class="form-control datetimepicker-input end-time" data-target="#end_time_' . $index . '" value="' . $endTime . '"/>';
            $prop .= '<div class="input-group-append" data-target="#end_time_' . $index . '" data-toggle="datetimepicker"><div class="input-group-text"><i class="fa fa-clock-o"></i></div></div></div></div>';
            
            // NEW: Branch dropdown column
            $prop .= '<div class="col-md-2"><select name="branch_ids[]" class="form-control row-branch-select">';
            $prop .= '<option value="">Select</option>';
            $prop .= $rowBranchOptions;
            $prop .= '</select></div>';
            
            $prop .= '<div class="col-md-2"><input type="text" name="detail_notes[]" class="form-control" value="' . $note . '" placeholder="Enter note"></div>';
            
            $prop .= '<div class="col-md-1 d-flex align-items-center"><button type="button" class="btn btn-danger btn-sm remove-row"><i class="fa fa-trash"></i></button></div>';
            $prop .= '</div>';

            $current_date->addDay();
            $index++;
        }

        return response()->json(['success' => true, 'html' => $prop]);
    }




    public function reportView()
    {
        $branches = Branch::all();
        return view('admin.daily-prerota.report', compact('branches'));
    }

    public function getReportData(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'branch_id'  => 'required|exists:branches,id',
        ]);

        $startDate = Carbon::parse($request->start_date);
        $endDate   = Carbon::parse($request->end_date);

        // 1. Get all details for this branch and date range
        $details = DailyPreRotaDetail::where('branch_id', $request->branch_id)
            ->whereBetween('date', [$startDate, $endDate])
            ->where('status', 1) // Only "In Rota" staff
            ->with('staff')
            ->get();

        if ($details->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No schedule data found for this criteria.']);
        }

        // 2. Generate Columns (Dates)
        $columns = [];
        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            $columns[] = [
                'full_date' => $currentDate->format('Y-m-d'),
                'day_num'   => $currentDate->format('d'), // e.g., "12"
                'day_name'  => $currentDate->format('l'),  // e.g., "Tuesday"
            ];
            $currentDate->addDay();
        }

        // 3. Generate Rows (Hourly Time Slots)
        // Find the earliest start time and latest end time to create the grid bounds
        $allStarts = $details->map(fn($d) => explode(' - ', $d->time_range)[0] ?? null)->filter()->sort();
        $allEnds   = $details->map(fn($d) => explode(' - ', $d->time_range)[1] ?? null)->filter()->sort();

        $gridStart = $allStarts->first() ? Carbon::parse($allStarts->first()) : Carbon::parse('09:00');
        $gridEnd   = $allEnds->last() ? Carbon::parse($allEnds->last()) : Carbon::parse('17:00');

        $timeSlots = [];
        $slotStart = $gridStart->copy();
        while ($slotStart < $gridEnd) {
            $slotEnd = $slotStart->copy()->addHour();
            $timeSlots[] = $slotStart->format('H:i') . ' - ' . $slotEnd->format('H:i');
            $slotStart = $slotEnd;
        }

        // 4. Build the Matrix Data
        $matrix = [];
        foreach ($timeSlots as $slot) {
            $slotStartTime = Carbon::parse(explode(' - ', $slot)[0]);
            $slotEndTime   = Carbon::parse(explode(' - ', $slot)[1]);

            foreach ($columns as $col) {
                // Find staff working on this date whose shift OVERLAPS with this 1-hour slot
                $staffNames = $details->filter(function ($detail) use ($col, $slotStartTime, $slotEndTime) {
                    if ($detail->date != $col['full_date'] || !$detail->time_range) return false;

                    $shiftStart = Carbon::parse(explode(' - ', $detail->time_range)[0]);
                    $shiftEnd   = Carbon::parse(explode(' - ', $detail->time_range)[1]);

                    // Check overlap: Shift starts before slot ends AND shift ends after slot starts
                    return $shiftStart < $slotEndTime && $shiftEnd > $slotStartTime;
                })->pluck('staff.name')->join(', '); // Join multiple names with comma

                $matrix[$slot][$col['full_date']] = $staffNames;
            }
        }

        return response()->json([
            'success'   => true,
            'branch'    => Branch::find($request->branch_id)->name,
            'start'     => $startDate->format('d M Y'),
            'end'       => $endDate->format('d M Y'),
            'columns'   => $columns,
            'timeSlots' => $timeSlots,
            'matrix'    => $matrix,
        ]);
    }


    public function multiBranchReportView()
    {
        $branches = Branch::orderBy('name')->get();
        return view('admin.daily-prerota.multi-report', compact('branches'));
    }

    public function getMultiBranchReportData(Request $request)
    {
        $request->validate([
            'start_date'  => 'required|date',
            'end_date'    => 'required|date|after_or_equal:start_date',
            'branch_ids'  => 'required|array',
            'branch_ids.*'=> 'exists:branches,id',
        ]);

        $startDate = Carbon::parse($request->start_date);
        $endDate   = Carbon::parse($request->end_date);

        // 1. Get all details for selected branches and date range
        $details = DailyPreRotaDetail::whereIn('branch_id', $request->branch_ids)
            ->whereBetween('date', [$startDate, $endDate])
            ->where('status', 1) // Only "In Rota" staff
            ->with('staff', 'branch')
            ->get();

        // 2. Get selected branches info
        $branches = Branch::whereIn('id', $request->branch_ids)->orderBy('name')->get();

        // 3. Generate Date Rows
        $dates = [];
        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            $dates[] = [
                'full_date'     => $currentDate->format('Y-m-d'),
                'day_name'      => $currentDate->format('D'),   // e.g., "Mon"
                'formatted_date'=> $currentDate->format('d M'),  // e.g., "12 May"
            ];
            $currentDate->addDay();
        }

        // 4. Group Data by Date, then by Branch ID
        // Structure: $groupedData['2026-05-12']['1'] = [ ['name'=>'abc', 'time'=>'9:00 - 13:30'], ... ]
        $groupedData = [];
        foreach ($details as $detail) {
            $groupedData[$detail->date][$detail->branch_id][] = [
                'name' => $detail->staff->name ?? 'Unknown',
                'time' => $detail->time_range ?? '',
                'note' => $detail->note ?? ''
            ];
        }

        return response()->json([
            'success'  => true,
            'start'    => $startDate->format('d M Y'),
            'end'      => $endDate->format('d M Y'),
            'branches' => $branches,
            'dates'    => $dates,
            'data'     => $groupedData,
        ]);
    }




}
