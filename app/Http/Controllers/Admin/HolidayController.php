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
        $data = Holiday::where('branch_id', Auth::user()->branch_id)->with('branch')->orderby('id','DESC')->get();
        $employees = Employee::where('is_active', 1)->get();
        return view('admin.holiday.index', compact('data','employees'));
    }

    public function store2(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'employee_id' => 'required|string|max:255',
            'employee_type' => 'required|string|max:255',
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

        $holiday = $employee->holidays()->get();

        $counts = $employee->leave_status_counts;
        $used = ($counts['booked'] ?? 0) + ($counts['taken'] ?? 0);
        $available = $employee->entitled_holiday - $used;

        if ($duration > $available) {
            return response()->json([
                'status' => 422,
                'message' => "Only $available holiday(s) available, but $duration requested."
            ]);
        }

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

        if ($request->available_prerota == 1) {
            $updatePrerota = EmployeePreRota::where('employee_id', $request->employee_id)
                ->whereBetween('date', [$from, $to])
                ->update([
                    'status' => 3, // authorised holiday
                    'updated_by' => auth()->id(),
                ]);
        }

        return response()->json(['status' => 200, 'message' => 'Data created successfully.', 'counts' => $counts, 'holiday' => $holiday]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'employee_id' => 'required|string|max:255',
            'employee_type' => 'required|string|max:255',
            'holiday_dates' => 'nullable|string', // Accept holiday_dates as JSON string
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

        // Check holiday_dates if provided
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

        // Validate holiday dates are within range
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

        

        $holiday = $employee->holidays()->get();
        return response()->json([
            'status' => 200,
            'message' => 'Data created successfully.',
            'counts' => $counts,
            'holiday' => $holiday
        ]);
    }

    public function preRota($holidayDates, $from, $to, $request, $data)
    {
        // Store holiday dates in holiday_details table
        if (!empty($holidayDates)) {
            foreach ($holidayDates as $date) {
                $employeePreRota = EmployeePreRota::where('employee_id', $request->employee_id)->where('date', $date)->first();
                if ($employeePreRota) {
                    $employeePreRota->status = 3;
                    $employeePreRota->start_time = null;
                    $employeePreRota->end_time = null;
                    $employeePreRota->save();

                } else {

                    $newPrerota = new EmployeePreRota();
                    $newPrerota->employee_id = $request->employee_id;
                    $newPrerota->date = $date;
                    $newPrerota->day_name = null;
                    $newPrerota->status = 3;
                    $newPrerota->start_time = null;
                    $newPrerota->end_time = null;
                    $newPrerota->save();
                }
                
                DB::table('holiday_details')->insert([
                    'holiday_id' => $data->id,
                    'employee_id' => $request->employee_id,
                    'date' => $date,
                ]);
            }
        } else {
            // If no specific dates provided, store all dates in the range
            $currentDate = $from->copy();
            while ($currentDate <= $to) {
                DB::table('holiday_details')->insert([
                    'holiday_id' => $data->id,
                    'employee_id' => $request->employee_id,
                    'date' => $currentDate->format('Y-m-d'),
                ]);
                $currentDate->addDay();
            }
        }

    }

    public function edit($id)
    {
        $data = Holiday::findOrFail($id);
        return response()->json($data);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'employee_id' => 'required|string|max:255',
            'employee_type' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 422, 'message' => $validator->errors()->first()]);
        }

        $data = Holiday::findOrFail($request->codeid);

        $employee = Employee::find($request->employee_id);
        if (!$employee) {
            return response()->json(['status' => 404, 'message' => 'Employee not found.']);
        }

        $from = Carbon::parse($request->from_date);
        $to = Carbon::parse($request->to_date);
        $newDuration = $from->diffInDays($to) + 1;

        $counts = $employee->leave_status_counts;
        $currentDuration = Carbon::parse($data->from_date)->diffInDays(Carbon::parse($data->to_date)) + 1;

        $used = ($counts['booked'] ?? 0) + ($counts['taken'] ?? 0) - $currentDuration;

        $available = $employee->entitled_holiday - $used;

        if ($newDuration > $available) {
            return response()->json([
                'status' => 422,
                'message' => "Only $available holiday(s) available, but $newDuration requested."
            ]);
        }

        $data->from_date = $request->from_date;
        $data->to_date = $request->to_date;
        $data->employee_id = $request->employee_id;
        $data->type = $request->employee_type;
        $data->details = $request->details;
        $data->updated_by = auth()->id();
        $data->save();

        return response()->json(['status' => 200, 'message' => 'Data updated successfully.']);
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
