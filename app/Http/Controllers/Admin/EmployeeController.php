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
    public function index2(Request $request)
    {
        if (Auth::user()->is_type == '1') {
          $query = Employee::with('user.branch')->orderby('id','DESC')->get();
        } else{
          $query = Employee::with('user.branch')->where('branch_id', Auth::user()->branch_id)->orderby('id','DESC')->get();
        }
        $roles = Role::latest()->get();
        $branches = Branch::where('status', 1)->get();
        return view('admin.employees.index', compact('query','roles','branches'));
    }

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
            'branch_id' => Auth::user()->branch_id,
            'created_by' => Auth::user()->id,
            'role_id' => is_numeric($request->role_id) ? (int)$request->role_id : null
        ]);
        $request->merge(['user_id'=>$user->id]);
        $request->merge(['branch_id' => Auth::user()->branch_id]);
        
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

    public function update(Request $request)
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
            $request->merge(['branch_id' => Auth::user()->branch_id]);
            
            $user = User::whereId($employee->user_id)->first()->update([
                'name'=>$request->name,
                'email'=>$request->email,
                'password'=>$request->password,
                'photo'=>$userphoto,
                'username'=>$request->username,
                'branch_id' => Auth::user()->branch_id,
                'role_id' => is_numeric($request->role_id) ? (int)$request->role_id : null
            ]);

        }else {
            $request->merge(['branch_id' => Auth::user()->branch_id]);
            $user = User::whereId($employee->user_id)->first()->update([
                'name'=>$request->name,
                'email'=>$request->email,
                'photo'=>$userphoto ?? '',
                'username'=>$request->username,
                'branch_id' => Auth::user()->branch_id,
                'role_id' => is_numeric($request->role_id) ? (int)$request->role_id : null
            ]);
        }

        
        $employee->update($request->all());
        return response()->json([
            'type'=>'success',
            'message'=>'Staff updated successfully'
        ]);
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


}
