<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeePreRota;
use Illuminate\Http\Request;
use App\Models\Holiday;
use App\Models\HolidayDetail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class HolidayController extends Controller
{
    public function index()
    {
        $data = Holiday::where('branch_id', Auth::user()->branch_id)->with('branch','employeePreRota')->orderby('id','DESC')->get();
        $employees = Employee::where('is_active', 1)->get();
        return view('admin.holiday.index', compact('data','employees'));
    }



    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'employee_id' => 'required|string|max:255',
            'employee_type' => 'required|string|max:255',
            'holiday_dates' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 422, 'message' => $validator->errors()->first()]);
        }

        $employee = Employee::find($request->employee_id);
        if (!$employee) {
            return response()->json(['status' => 422, 'message' => 'Employee not found.']);
        }

        $from = Carbon::parse($request->from_date);
        $to = Carbon::parse($request->to_date);
        $duration = $from->diffInDays($to) + 1;

        $holidayDates = $request->holiday_dates ? json_decode($request->holiday_dates, true) : [];
        $holidayCount = !empty($holidayDates) ? count($holidayDates) : $duration;

        $counts = $employee->leave_status_counts;
        $used = ($counts['booked'] ?? 0) + ($counts['taken'] ?? 0);
        $available = $employee->entitled_holiday - $used;

        if ($holidayCount > $available) {
            return response()->json([
                'status' => 422,
                'message' => "Only $available holiday(s) available, but $holidayCount requested."
            ]);
        }

        if (!empty($holidayDates)) {
            foreach ($holidayDates as $date) {
                $holidayDate = Carbon::parse($date);
                if ($holidayDate->lt($from) || $holidayDate->gt($to)) {
                    return response()->json([
                        'status' => 422,
                        'message' => 'Selected holiday dates must be within the specified date range.'
                    ]);
                }
            }
        }

        try {
            DB::beginTransaction();

            $data = new Holiday();
            $data->date = date('Y-m-d');
            $data->from_date = $request->from_date;
            $data->to_date = $request->to_date;
            $data->employee_id = $request->employee_id;
            $data->type = $request->employee_type;
            $data->details = $request->details;
            $data->branch_id = Auth::user()->branch_id;
            $data->created_by = auth()->id();
            $data->save();


            $start = Carbon::parse($request->from_date);
            $end = Carbon::parse($request->to_date);
            $employeBranchId = $employee->branch_id ?? Auth::user()->branch_id;
            
            foreach ($request->dates as $index => $date) {
                $dateObj = Carbon::parse($date);
                if ($dateObj->lt($start) || $dateObj->gt($end)) {
                    continue; // Skip dates outside the range
                }

                // Check for existing EmployeePreRota record for this employee and date
                $existingRecord = EmployeePreRota::where('employee_id', $request->employee_id)
                    ->where('date', $date)
                    ->first();

                if ($existingRecord) {
                    // Update existing record
                    $existingRecord->update([
                        'holiday_id' => $data->id,
                        'branch_id' => $employeBranchId,
                        'day_name' => $request->day_names[$index],
                        'start_time' => $request->start_times[$index] ?? null,
                        'end_time' => $request->end_times[$index] ?? null,
                        'status' => $request->status[$index] ?? null,
                    ]);
                } else {
                    // Create new record
                    EmployeePreRota::create([
                        'employee_id' => $request->employee_id,
                        'holiday_id' => $data->id,
                        'branch_id' => $employeBranchId,
                        'date' => $date,
                        'day_name' => $request->day_names[$index],
                        'start_time' => $request->start_times[$index] ?? null,
                        'end_time' => $request->end_times[$index] ?? null,
                        'status' => $request->status[$index] ?? null,
                        'created_by' => auth()->id,
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'Data created successfully.',
                'counts' => $counts
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'message' => 'An error occurred while creating the holiday: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'codeid' => 'required|exists:holidays,id',
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'employee_id' => 'required|string|max:255',
            'employee_type' => 'required|string|max:255',
            'holiday_dates' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 422, 'message' => $validator->errors()->first()]);
        }

        $data = Holiday::findOrFail($request->codeid);

        $employee = Employee::find($request->employee_id);
        if (!$employee) {
            return response()->json(['status' => 422, 'message' => 'Employee not found.']);
        }

        $from = Carbon::parse($request->from_date);
        $to = Carbon::parse($request->to_date);
        $duration = $from->diffInDays($to) + 1;

        $holidayDates = $request->holiday_dates ? json_decode($request->holiday_dates, true) : [];
        $holidayCount = !empty($holidayDates) ? count($holidayDates) : $duration;

        $counts = $employee->leave_status_counts;
        $currentDuration = Carbon::parse($data->from_date)->diffInDays(Carbon::parse($data->to_date)) + 1;
        $used = ($counts['booked'] ?? 0) + ($counts['taken'] ?? 0) - $currentDuration;
        $available = $employee->entitled_holiday - $used;

        if ($holidayCount > $available) {
            return response()->json([
                'status' => 422,
                'message' => "Only $available holiday(s) available, but $holidayCount requested."
            ]);
        }

        if (!empty($holidayDates)) {
            foreach ($holidayDates as $date) {
                $holidayDate = Carbon::parse($date);
                if ($holidayDate->lt($from) || $holidayDate->gt($to)) {
                    return response()->json([
                        'status' => 422,
                        'message' => 'Selected holiday dates must be within the specified date range.'
                    ]);
                }
            }
        }

        try {
            DB::beginTransaction();

            $data->from_date = $request->from_date;
            $data->to_date = $request->to_date;
            $data->employee_id = $request->employee_id;
            $data->type = $request->employee_type;
            $data->details = $request->details;
            $data->updated_by = auth()->id();
            $data->save();

            $this->preRota($holidayDates, $from, $to, $request, $data);

            DB::commit();

            $holiday = $employee->holidays()->get();
            return response()->json([
                'status' => 200,
                'message' => 'Data updated successfully.',
                'counts' => $counts,
                'holiday' => $holiday
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'message' => 'An error occurred while updating the holiday: ' . $e->getMessage()
            ], 500);
        }
    }

    

    // public function edit($id)
    // {
    //     $data = Holiday::with('holidayDetail')->findOrFail($id);
    //     return response()->json($data);
    // }

    public function edit($id)
    {
        $holiday = Holiday::with('holidayDetail')->findOrFail($id);
        $preRota = EmployeePreRota::where('employee_id', $holiday->employee_id)
            ->whereBetween('date', [$holiday->from_date, $holiday->to_date])
            ->get();



            $prop = '';
            foreach ($preRota as $key => $prorota) {
                $prop .= '<div class="row schedule-row"><div class="col-md-2">
                            <input type="text" class="form-control" name="dates[]" value="' . $prorota->date . '" readonly>
                        </div>
                        <div class="col-md-2">
                            <input type="text" class="form-control" name="day_names[]" value="' . $prorota->day_name . '" readonly>
                        </div>
                        <div class="col-md-2">
                            <div class="input-group date timepicker" id="start_time_' . $key . '" data-target-input="nearest" >
                                <input type="text" name="start_times[]" class="form-control datetimepicker-input start-time" data-target="#start_time_' . $key . '" value="' . $prorota->start_time . '"' . ($prorota->status == '2' ? ' disabled="disabled"' : '') . '/>
                                <div class="input-group-append" data-target="#start_time_' . $key . '" data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="fa fa-clock-o"></i></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="input-group date timepicker" id="end_time_' . $key . '" data-target-input="nearest">
                                <input type="text" name="end_times[]" class="form-control datetimepicker-input end-time" data-target="#end_time_' . $key . '"  value="' . $prorota->end_time . '"' . ($prorota->status == '2' ? ' disabled="disabled"' : '') . '/>
                                <div class="input-group-append" data-target="#end_time_' . $key . '" data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="fa fa-clock-o"></i></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">';

                if ($prorota->status == '1') {
                    $prop .= '<button type="button" class="btn btn-success btn-sm day-off-btn">In Rota</button><button type="button" class="btn btn-primary btn-sm make-holiday-btn ml-1">
                            <input type="checkbox" name="make_holiday[]" value="' . $prorota->date . '" class="mr-1">Make Holiday
                        </button>';
                } elseif ($prorota->status == '3') {
                    $prop .= '<button type="button" class="btn btn-primary btn-sm make-holiday-btn ml-1">
                            <input type="checkbox" checked name="make_holiday[]" value="' . $prorota->date . '" class="mr-1">Make Holiday
                        </button>';
                } else {
                    $prop .= '<button type="button" class="btn btn-warning btn-sm day-off-btn">Day Off</button>';
                }

                $prop .= '</div></div>';
            }

        return response()->json([
            'holiday' => $holiday,
            'prerota' => $prop,
            'preRotaDetails' => $preRota,
        ]);
    }



    public function delete($id)
    {
        $data = Holiday::findOrFail($id);
        $data->delete();

        return response()->json(['status' => 200, 'message' => 'Data deleted successfully.']);
    }



public function checkHolidays(Request $request)
{
    $request->validate([
        'start_date' => 'required|date',
        'end_date' => 'nullable|date|after_or_equal:start_date',
    ]);

    $start_date = Carbon::parse($request->start_date);
    $end_date = $request->end_date ? Carbon::parse($request->end_date) : $start_date;
    $employee_ids = $request->employee_ids;

    $holidays = HolidayDetail::where('employee_id', $employee_ids)
        ->whereBetween('date', [$start_date, $end_date])
        ->with('employee')
        ->get();

    $chkPrerota = EmployeePreRota::where('employee_id', $employee_ids)
        ->whereBetween('date', [$start_date, $end_date])
        ->get();

    // Generate HTML for each date in the range
    $prop = '';
    $current_date = $start_date->copy();
    $index = 0;

    while ($current_date <= $end_date) {
        $date_str = $current_date->toDateString();
        $day_name = $current_date->format('l');

        // Find matching pre-rota entry for the current date
        $prorota = $chkPrerota->firstWhere('date', $date_str);

        // If no pre-rota entry exists, create a default one with empty fields
        $prorota = $prorota ?: (object)[
            'date' => $date_str,
            'day_name' => $day_name,
            'start_time' => '',
            'end_time' => '',
            'status' => '', // Default to empty for "Please Select"
        ];

        $prop .= '<div class="row schedule-row"><div class="col-md-2">
                    <input type="text" class="form-control" name="dates[]" value="' . $prorota->date . '" readonly>
                </div>
                <div class="col-md-2">
                    <input type="text" class="form-control" name="day_names[]" value="' . $prorota->day_name . '" readonly>
                </div>
                <div class="col-md-2">
                    <div class="input-group date timepicker" id="start_time_' . $index . '" data-target-input="nearest" >
                        <input type="text" name="start_times[]" class="form-control datetimepicker-input start-time" data-target="#start_time_' . $index . '" value="' . $prorota->start_time . '"' . ($prorota->status == '2' ? ' disabled="disabled"' : '') . '/>
                        <div class="input-group-append" data-target="#start_time_' . $index . '" data-toggle="datetimepicker">
                            <div class="input-group-text"><i class="fa fa-clock-o"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="input-group date timepicker" id="end_time_' . $index . '" data-target-input="nearest">
                        <input type="text" name="end_times[]" class="form-control datetimepicker-input end-time" data-target="#end_time_' . $index . '" value="' . $prorota->end_time . '"' . ($prorota->status == '2' ? ' disabled="disabled"' : '') . '/>
                        <div class="input-group-append" data-target="#end_time_' . $index . '" data-toggle="datetimepicker">
                            <div class="input-group-text"><i class="fa fa-clock-o"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <select name="status[]" class="form-control status-select  ' . ($prorota->status == '1' ? 'bg-success' : '').($prorota->status == '2' ? 'bg-warning' : '').($prorota->status == '3' ? 'bg-primary' : '') . '">
                        <option value="" ' . ($prorota->status == '' ? 'selected' : '') . '>Please Select</option>
                        <option class="bg-success" value="1" ' . ($prorota->status == '1' ? 'selected' : '') . '> Rota</option>
                        <option class="bg-warning" value="2" ' . ($prorota->status == '2' ? 'selected' : '') . '>Day Off</option>
                        <option class="bg-primary" value="3" ' . ($prorota->status == '3' ? 'selected' : '') . '>Holiday</option>
                    </select>
                </div>
                </div>';

        $current_date->addDay();
        $index++;
    }

    return response()->json(['success' => true, 'html' => $prop, 'holidays' => $holidays]);
}


}
