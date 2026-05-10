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
            
        // Pass staff to the view for the dropdown
        $employees = User::where('is_type', 0)->get(); 
        $branches = Branch::all();

        return view('admin.daily-prerota.index', compact('data', 'employees','branches'));
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

        // GET EMPLOYEE'S BRANCH ID
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
                    $timeRange  = ($start && $end) ? $start . ' - ' . $end : null;

                    $existing = DailyPreRotaDetail::where('staff_id', $staffId)->where('date', $date)->first();

                    if ($existing) {
                        $existing->update([
                            'daily_pre_rota_id' => $rota->id,
                            'branch_id'  => $employeeBranchId, 
                            'time_range' => $timeRange,
                            'note'       => $detailNote,
                            'status'     => 1,
                            'updated_by' => Auth::user()->id,
                        ]);
                    } else {
                        DailyPreRotaDetail::create([
                            'daily_pre_rota_id' => $rota->id,
                            'staff_id'   => $staffId,
                            'branch_id'  => $employeeBranchId, 
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
            'employee_id'=> 'required|exists:users,id', // Make sure you pass this from JS
            'note'       => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 422, 'message' => $validator->errors()->first()]);
        }

        // GET EMPLOYEE'S BRANCH ID
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

            // Delete old details and recreate
            DailyPreRotaDetail::where('daily_pre_rota_id', $rota->id)->delete();

            if ($request->has('staff_ids')) {
                foreach ($request->staff_ids as $index => $staffId) {
                    $date       = $request->dates[$index] ?? null;
                    $start      = $request->start_times[$index] ?? null;
                    $end        = $request->end_times[$index] ?? null;
                    $detailNote = $request->detail_notes[$index] ?? null;
                    $timeRange  = ($start && $end) ? $start . ' - ' . $end : null;

                    DailyPreRotaDetail::create([
                        'daily_pre_rota_id' => $rota->id,
                        'staff_id'   => $staffId,
                        'branch_id'  => $employeeBranchId,
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

        $prop = '';
        foreach ($details as $key => $detail) {
            $staff     = User::find($detail->staff_id);
            $staffName = $staff ? $staff->name : 'Unknown';
            $dayName   = $detail->date ? Carbon::parse($detail->date)->format('l') : '';
            $times     = $detail->time_range ? explode(' - ', $detail->time_range) : [null, null];
            $startTime = $times[0] ?? '';
            $endTime   = $times[1] ?? '';

            $prop .= '<div class="row schedule-row mb-2">';
            $prop .= '<input type="hidden" name="staff_ids[]" value="' . $detail->staff_id . '">';
            
            // 1. Employee Name
            $prop .= '<div class="col-md-2"><input type="text" class="form-control" value="' . $staffName . '" readonly></div>';
            // 2. Date
            $prop .= '<div class="col-md-2"><input type="text" class="form-control" name="dates[]" value="' . $detail->date . '" readonly></div>';
            // 3. Day Name
            $prop .= '<div class="col-md-1"><input type="text" class="form-control" value="' . $dayName . '" readonly></div>';
            
            // 4. Start Time
            $prop .= '<div class="col-md-1"><div class="input-group date timepicker" id="start_time_' . $key . '" data-target-input="nearest">';
            $prop .= '<input type="text" name="start_times[]" class="form-control datetimepicker-input start-time" data-target="#start_time_' . $key . '" value="' . $startTime . '"/>';
            $prop .= '<div class="input-group-append" data-target="#start_time_' . $key . '" data-toggle="datetimepicker"><div class="input-group-text"><i class="fa fa-clock-o"></i></div></div></div></div>';
            
            // 5. End Time
            $prop .= '<div class="col-md-1"><div class="input-group date timepicker" id="end_time_' . $key . '" data-target-input="nearest">';
            $prop .= '<input type="text" name="end_times[]" class="form-control datetimepicker-input end-time" data-target="#end_time_' . $key . '" value="' . $endTime . '"/>';
            $prop .= '<div class="input-group-append" data-target="#end_time_' . $key . '" data-toggle="datetimepicker"><div class="input-group-text"><i class="fa fa-clock-o"></i></div></div></div></div>';
            
            // 6. Note
            $prop .= '<div class="col-md-3"><input type="text" name="detail_notes[]" class="form-control" value="' . $detail->note . '" placeholder="Enter note"></div>';
            
            // 7. Delete Button
            $prop .= '<div class="col-md-2 d-flex align-items-center"><button type="button" class="btn btn-danger btn-sm remove-row"><i class="fa fa-trash"></i></button></div>';
            $prop .= '</div>';
        }

        return response()->json([
            'branch_id'    => $rota->branch_id, 
            'rota'         => $rota,
            'staff_id'     => $details->first()->staff_id ?? null, // ADDED THIS
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

        $existingDetails = DailyPreRotaDetail::where('staff_id', $request->employee_id)
            ->whereBetween('date', [$start_date, $end_date])
            ->get();

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
            } else {
                $startTime = '09:00';
                $endTime   = '17:30';
                $note      = '';
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
            
            $prop .= '<div class="col-md-3"><input type="text" name="detail_notes[]" class="form-control" value="' . $note . '" placeholder="Enter note"></div>';
            
            $prop .= '<div class="col-md-2 d-flex align-items-center"><button type="button" class="btn btn-danger btn-sm remove-row"><i class="fa fa-trash"></i></button></div>';
            $prop .= '</div>';

            $current_date->addDay();
            $index++;
        }

        return response()->json(['success' => true, 'html' => $prop]);
    }


}
