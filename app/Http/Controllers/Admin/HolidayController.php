<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeePreRota;
use Illuminate\Http\Request;
use App\Models\Holiday;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class HolidayController extends Controller
{
    public function index()
    {
        $data = Holiday::where('branch_id', Auth::user()->branch_id)->with('branch','holidayDetail')->orderby('id','DESC')->get();
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

            $this->preRota($holidayDates, $from, $to, $request, $data);

            DB::commit();

            $holiday = $employee->holidays()->get();
            return response()->json([
                'status' => 200,
                'message' => 'Data created successfully.',
                'counts' => $counts,
                'holiday' => $holiday
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

            // Delete existing holiday_details for this holiday
            DB::table('holiday_details')->where('holiday_id', $data->id)->delete();

            

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

    public function preRota($holidayDates, $from, $to, $request, $data)
    {
        if (!empty($holidayDates)) {
            EmployeePreRota::where('employee_id', $request->employee_id)
                ->whereIn('date', $holidayDates)
                ->update([
                    'status' => 3, // authorised holiday
                    'start_time' => null,
                    'end_time' => null,
                    'updated_by' => auth()->id(),
                ]);

            $existingDates = EmployeePreRota::where('employee_id', $request->employee_id)
                ->whereIn('date', $holidayDates)
                ->pluck('date')
                ->map(function ($date) {
                    return Carbon::parse($date)->format('Y-m-d');
                })->toArray();

            $newDates = array_diff($holidayDates, $existingDates);
            foreach ($newDates as $date) {
                $holidayDate = Carbon::parse($date);
                $newPrerota = new EmployeePreRota();
                $newPrerota->employee_id = $request->employee_id;
                $newPrerota->date = $date;
                $newPrerota->day_name = $holidayDate->format('l');
                $newPrerota->status = 3; // authorised holiday
                $newPrerota->start_time = null;
                $newPrerota->end_time = null;
                $newPrerota->updated_by = auth()->id();
                $newPrerota->save();
            }

            foreach ($holidayDates as $date) {
                DB::table('holiday_details')->insert([
                    'holiday_id' => $data->id,
                    'employee_id' => $request->employee_id,
                    'date' => $date,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        } else {
            $currentDate = $from->copy();
            while ($currentDate <= $to) {
                $dateStr = $currentDate->format('Y-m-d');
                $employeePreRota = EmployeePreRota::where('employee_id', $request->employee_id)
                    ->where('date', $dateStr)
                    ->first();

                if ($employeePreRota) {
                    $employeePreRota->status = 3;
                    $employeePreRota->start_time = null;
                    $employeePreRota->end_time = null;
                    $employeePreRota->updated_by = auth()->id();
                    $employeePreRota->save();
                } else {
                    $newPrerota = new EmployeePreRota();
                    $newPrerota->employee_id = $request->employee_id;
                    $newPrerota->date = $dateStr;
                    $newPrerota->day_name = $currentDate->format('l');
                    $newPrerota->status = 3;
                    $newPrerota->start_time = null;
                    $newPrerota->end_time = null;
                    $newPrerota->updated_by = auth()->id();
                    $newPrerota->save();
                }

                DB::table('holiday_details')->insert([
                    'holiday_id' => $data->id,
                    'employee_id' => $request->employee_id,
                    'date' => $dateStr,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $currentDate->addDay();
            }
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
            'employee_ids' => 'required|array',
            'employee_ids.*' => 'exists:employees,id',
        ]);

        $start_date = $request->start_date;
        $end_date = $request->end_date ?? $start_date; 
        $employee_ids = $request->employee_ids;

        $holidays = Holiday::whereIn('employee_id', $employee_ids)
            ->where(function ($query) use ($start_date, $end_date) {
                $query->whereBetween('from_date', [$start_date, $end_date])
                    ->orWhereBetween('to_date', [$start_date, $end_date])
                    ->orWhere(function ($query) use ($start_date, $end_date) {
                        $query->where('from_date', '<=', $start_date)
                            ->where('to_date', '>=', $end_date);
                    })
                    ->orWhere(function ($query) use ($start_date, $end_date) {
                        $query->where('from_date', '>=', $start_date)
                            ->where('to_date', '<=', $end_date);
                    });
            })
            ->with('employee') 
            ->get();

        if ($holidays->isEmpty()) {
            return response()->json(['success' => false]);
        }

        $html = '<h4>Holiday List</h4><table class="table table-bordered"><thead><tr><th>Employee</th><th>From Date</th><th>To Date</th><th>Type</th><th>Details</th></tr></thead><tbody>';
        foreach ($holidays as $holiday) {
            $html .= "<tr><td>{$holiday->employee->name}</td><td>{$holiday->from_date}</td><td>{$holiday->to_date}</td><td>{$holiday->type}</td><td>{$holiday->details}</td></tr>";
        }
        $html .= '</tbody></table>';

        return response()->json(['success' => true, 'html' => $html]);
    }


}
