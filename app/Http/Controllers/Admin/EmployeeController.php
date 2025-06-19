<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Stockmaintaince;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Holiday;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = Employee::all();
        return view('admin.employees.index', compact('query'));
    }

    public function store(Request $request)
    {
        $request->merge(['password'=>Hash::make($request->password)]);
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/employees'), $imageName);
            $userphoto = '/images/employees/' . $imageName;
        }
        $user = User::create([
            'name'=>$request->name,
            'email'=>$request->username.'@diamondsgroup.com',
            'password'=>$request->password,
            'role'=>'staff',
            'photo'=>$userphoto,
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
        return Employee::find($id);
    }

    public function update(Request $request)
    {

        $employee= Employee::find($request->codeid);

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
                'email'=>$request->username.'@diamondsgroup.com',
                'password'=>$request->password,
                'photo'=>$userphoto,
            ]);

        }else {
            $request->merge(['branch_id' => Auth::user()->branch_id]);
            $user = User::whereId($employee->user_id)->first()->update([
                'name'=>$request->name,
                'email'=>$request->username.'@diamondsgroup.com',
                'photo'=>$userphoto,
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

            $employee=Employee::find($id);
            $user=User::whereEmail($employee->username.'@diamondsgroup.com')->first();
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
}
