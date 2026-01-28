<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Stockmaintaince;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Branch;
use App\Models\Employee;
use App\Models\Holiday;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Models\Role;
use Carbon\Carbon;

class EmployeeController extends Controller
{


    public function index(Request $request)
    {
        if (Auth::user()->is_type == '1') {
            $query = Employee::with('user.branch')->orderBy('id', 'DESC')->get();
        } else {
            $query = Employee::with('user.branch')->where('branch_id', Auth::user()->branch_id)->orderBy('id', 'DESC')->get();
        }
        $roles = Role::latest()->get();
        $branches = Branch::where('status', 1)->get();

        $thisMonthStart = Carbon::now()->startOfMonth()->format('Y-m-d');
        $thisMonthEnd = Carbon::now()->endOfMonth()->format('Y-m-d');

        $paySlip = null;
        return view('admin.employees.index', compact('query', 'roles', 'branches','thisMonthStart','thisMonthEnd','paySlip'));
    }

    public function payslip(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
        ]);

        $formDate=$request->input('from_date').' 00:00:00';
        $toDate=$request->input('to_date').' 23:59:59';
        $employee=Employee::find($request->employee_id);
        $payslip=Attendance::whereType('Regular')
            ->whereEmployeeId($request->employee_id)
            ->whereBetween('clock_in',[$formDate,$toDate])
            ->get();

        $contractDateBegin = date('Y') . '-04-01';
        $workingHours = Attendance::whereEmployeeId($request->employee_id)
            ->whereBetween('clock_in',[$contractDateBegin,Carbon::today()])
            ->where('type','Regular')
            ->sum(DB::raw('TIMESTAMPDIFF(HOUR, clock_in, clock_out)'));

        $holidayController = $this->getHolidayReport($request);
        return response()->json([
            'payslip'=>$payslip,
            'employee'=>$employee,
            'holiday' =>$holidayController,
            'workingHours' => $workingHours

        ]);
    }

    public function store(Request $request)
    {

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email'),
                Rule::unique('employees', 'email'),
            ],
            'username' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users', 'username')->whereNull('deleted_at'),
                Rule::unique('employees', 'username')->whereNull('deleted_at'),
            ],
            'password' => 'required|string|min:4',
        ]);

        $request->merge(['password'=>Hash::make($request->password)]);
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/employees'), $imageName);
            $userphoto = '/images/employees/' . $imageName;
        }
        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => $request->password,
            'is_type' => '0',
            'photo' => $userphoto ?? '',
            'branch_id' =>$request->branch_id ?? Auth::user()->branch_id,
            'created_by' => Auth::user()->id,
            'role_id' => is_numeric($request->role_id) ? (int)$request->role_id : null
        ]);
        $request->merge(['user_id'=>$user->id]);
        $request->merge(['branch_id' =>$request->branch_id ?? Auth::user()->branch_id]);
        
        Employee::create($request->all());

        return response()->json([
           'type'=>'success',
           'message'=>'Staff create successfully'
        ]);
    }

    public function edit(Request $request, $id)
    {
        return Employee::with('user')->find($id);
    }

    public function update2(Request $request)
    {

        $employee = Employee::find($request->codeid);
        $user = User::find($employee->user_id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
                Rule::unique('employees', 'email')->ignore($employee->id),
            ],
            'username' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users', 'username')->whereNull('deleted_at')->ignore($user->id),
                Rule::unique('employees', 'username')->whereNull('deleted_at')->ignore($employee->id),
            ],
            'password' => 'nullable|string|min:4',
            'image' => 'nullable|image|mimes:jpg,jpeg,png',
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/employees'), $imageName);
            $userphoto = '/images/employees/' . $imageName;
        }

        if($request->password){
            $request->merge(['password'=>Hash::make($request->password)]);
            $request->merge(['branch_id' =>$request->branch_id ?? Auth::user()->branch_id]);
            
            $user = User::whereId($employee->user_id)->first()->update([
                'name'=>$request->name,
                'email'=>$request->email,
                'password'=>$request->password,
                'photo'=>$userphoto,
                'username'=>$request->username,
                'branch_id' => $request->branch_id ?? Auth::user()->branch_id,
                'role_id' => is_numeric($request->role_id) ? (int)$request->role_id : null
            ]);

        }else {
            $request->merge(['branch_id' =>$request->branch_id ?? Auth::user()->branch_id]);
            $user = User::whereId($employee->user_id)->first()->update([
                'name'=>$request->name,
                'email'=>$request->email,
                'photo'=>$userphoto ?? '',
                'username'=>$request->username,
                'branch_id' => $request->branch_id ?? Auth::user()->branch_id,
                'role_id' => is_numeric($request->role_id) ? (int)$request->role_id : null
            ]);
        }

        
        $employee->update($request->all());
        return response()->json([
            'type'=>'success',
            'message'=>'Staff updated successfully'
        ]);
    }

    public function update(Request $request)
    {
        // 1. Find records or fail early
        $employee = Employee::findOrFail($request->codeid);
        $user = User::findOrFail($employee->user_id);

        // 2. Validation (Matches JS 'photo' name)
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'username' => ['required', 'string', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:4',
            'photo'    => 'nullable|image|mimes:jpg,jpeg,png', // Changed from 'image' to 'photo'
        ]);

        try {
            DB::beginTransaction();

            // 3. Handle File Upload
            $photoPath = $user->photo; // Default to existing photo
            if ($request->hasFile('photo')) {
                $image = $request->file('photo');
                $imageName = uniqid() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('images/employees'), $imageName);
                $photoPath = '/images/employees/' . $imageName;
            }

            // 4. Update User Model
            $userData = [
                'name'      => $request->name,
                'email'     => $request->email,
                'username'  => $request->username,
                'photo'     => $photoPath,
                'branch_id' => $request->branch_id ?? auth()->user()->branch_id,
                'role_id'   => is_numeric($request->role_id) ? (int)$request->role_id : $user->role_id,
            ];

            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            $user->update($userData);

            // 5. Update Employee Model 
            // Use except() to avoid sending User-specific data into the Employee table
            $employee->update($request->except(['password', 'photo', 'codeid']));

            DB::commit();

            return response()->json([
                'type'    => 'success',
                'message' => 'Staff updated successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'type'    => 'error',
                'message' => 'Update failed: ' . $e->getMessage()
            ], 500);
        }
    }


    public function delete(Request $request, $id)
    {
        $employee = Employee::find($id);
        $attendace = Attendance::whereEmployeeId($id)->count();
        $holiday = Holiday::whereEmployeeId($id)->count();
        $stock = Stockmaintaince::whereEmployeeId($id)->count();
        $preRota = DB::table('employee_pre_rota')
            ->where('employee_id',$id)->count();
        if($attendace+$preRota+$holiday+$stock>0){
            return response()->json([
                'type'=>'error',
                'message'=>'Staff exist with Prorota, Attendance, Holiday, Stock Maintain!'
            ]);
        }else{
            $user = User::find($employee->user_id);
            $user->delete();
            $employee->delete();
            return response()->json([
                'type'=>'success',
                'message'=>'Staff Deleted successfully'
            ]);
        }
    }

    public function getEmployeeList(){
        return Employee::whereIsActive(1)->get();
    }

    public function getHolidayCount($id){
        $contractDateBegin = date('Y') . '-04-01';
        $contractDateEnd = date('Y', strtotime('+1 year')) . '-03-31';

        $holidayDataCount=Holiday::whereEmployeeId($id)
            ->whereBetween('date',[$contractDateBegin,$contractDateEnd])
            ->where('type','Authorized holiday')
            ->count();

        return response()->json([
            'holidayDataCount'=>$holidayDataCount,

        ]);
    }

    public function updateStatus(Request $request)
    {
        $employee = Employee::find($request->userId);
        if (!$employee) {
            return response()->json([
                'type' => 'error',
                'message' => 'Employee not found'
            ], 404);
        }

        $employee->is_active = $request->status;
        $employee->save();

        return response()->json([
            'status' => 200,
            'type' => 'success',
            'message' => 'Employee status updated successfully',
            'employee' => $employee
        ]);
    }


    public function getHolidayReport(Request $request)
    {
        $employeeId=request()->input('employee_id');
        $contractDateBegin = date('Y') . '-04-01';
        $contractDateEnd = date('Y', strtotime('+1 year')) . '-03-31';

        
        $holidayDataCount=Holiday::whereEmployeeId($employeeId)
            ->whereBetween('date',[$contractDateBegin,$contractDateEnd])
            ->where('type','Authorized holiday')
            ->count();
        $sickDays = Attendance::whereEmployeeId($employeeId)
            ->whereBetween('clock_in',[$contractDateBegin,Carbon::today()])
            ->where('type','Sick')
            ->count();
        $absenceDays=Attendance::whereEmployeeId($employeeId)
            ->whereBetween('clock_in',[$contractDateBegin,Carbon::today()])
            ->where('type','Absence')
            ->count();

        

        return response()->json([
            'sickDays'=>$sickDays,
            'absenceDays'=>$absenceDays,
            'holidayDataCount'=>$holidayDataCount
        ]);
    }


    public function checkProrota(Request $request)
    {
        // Validate inputs
        $employeeId = $request->employee_id;
        $fromDate = $request->from_date;
        $toDate = $request->to_date;

        if (!$employeeId || !$fromDate || !$toDate) {
            return response()->json([
                'status' => 400,
                'message' => 'Employee ID, from date, and to date are required.'
            ], 400);
        }

        try {
            $fromDate = Carbon::parse($fromDate)->startOfDay();
            $toDate = Carbon::parse($toDate)->endOfDay();
        } catch (\Exception $e) {
            return response()->json([
                'status' => 400,
                'message' => 'Invalid date format for from_date or to_date.'
            ], 400);
        }

        try {
            $prorotaData = DB::table('employee_pre_rotas')
                ->where('employee_id', $employeeId)
                ->whereBetween('date', [$fromDate, $toDate])
                ->get()
                ->keyBy('date'); // Key by date for easy lookup

            // Build full schedule data for all dates in the range
            $scheduleData = collect();
            $currentDate = $fromDate->copy();
            while ($currentDate <= $toDate) {
                $dateStr = $currentDate->format('Y-m-d');
                $dayName = $currentDate->format('l'); // e.g., Monday

                if (isset($prorotaData[$dateStr])) {
                    $entry = $prorotaData[$dateStr];
                } else {
                    $entry = (object) [
                        'employee_id' => $employeeId,
                        'date' => $dateStr,
                        'day_name' => $dayName,
                        'start_time' => '',
                        'end_time' => '',
                        'status' => 4, // Default to Day Off
                    ];
                }

                $scheduleData->push($entry);
                $currentDate->addDay();
            }

            if ($scheduleData->isEmpty()) {
                return response()->json([
                    'status' => 404,
                    'message' => 'No dates in the specified range.'
                ], 404);
            }

            // Calculate sum of effective hours
            $totalHours = 0;
            foreach ($scheduleData as $entry) {
                if ($entry->status == '1' && $entry->start_time && $entry->end_time) {
                    $start = Carbon::parse($entry->date . ' ' . $entry->start_time);
                    $end = Carbon::parse($entry->date . ' ' . $entry->end_time);
                    if ($end < $start) {
                        $end->addDay();
                    }
                    $totalHours += $end->diffInMinutes($start) / 60;
                }
            }

            $prop = '';
            foreach ($scheduleData as $key => $prorota) {
                $prop .= '<div class="row schedule-row"><div class="col-md-2">
                            <input type="text" class="form-control" name="dates[]" value="' . $prorota->date . '" readonly>
                        </div>
                        <div class="col-md-2">
                            <input type="text" class="form-control" name="day_names[]" value="' . $prorota->day_name . '" readonly>
                        </div>
                        <div class="col-md-2">
                            <div class="input-group date timepicker" id="start_time_' . $key . '" data-target-input="nearest" >
                                <input type="text" name="start_times[]" readonly class="form-control datetimepicker-input start-time" data-target="#start_time_' . $key . '" value="' . $prorota->start_time . '"' . ($prorota->status == '2' ? ' disabled="disabled"' : '') . '/>
                                <div class="input-group-append" data-target="#start_time_' . $key . '" data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="fa fa-clock-o"></i></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="input-group date timepicker" id="end_time_' . $key . '" data-target-input="nearest">
                                <input type="text" name="end_times[]" readonly class="form-control datetimepicker-input end-time" data-target="#end_time_' . $key . '"  value="' . $prorota->end_time . '"' . ($prorota->status == '2' ? ' disabled="disabled"' : '') . '/>
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
                            <input type="checkbox" name="make_holiday[]" value="' . $prorota->date . '" class="mr-1">Make Holiday
                        </button>';
                } elseif ($prorota->status == '2') {
                    $prop .= '<button type="button" class="btn btn-warning btn-sm day-off-btn">Day Off</button>';

                    
                } else {
                    $prop .= '<button type="button" class="btn btn-warning btn-sm day-off-btn">Day Off</button><button type="button" class="btn btn-primary btn-sm make-holiday-btn ml-1">
                            <input type="checkbox" name="make_holiday[]" value="' . $prorota->date . '" class="mr-1">Make Holiday
                        </button>';
                }

                $prop .= '</div></div>';
            }

            return response()->json([
                'status' => 200,
                'message' => 'Pre-rota schedules found.',
                'prerota' => $prop,
                'data' => $scheduleData,
                'total_hours' => $totalHours
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'An error occurred while fetching pre-rota data.'
            ], 500);
        }
    }

}
