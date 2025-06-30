<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $data = Attendance::where('branch_id', Auth::user()->branch_id)->orderby('id','DESC')->get();
        $employees = Employee::where('is_active', 1)->where('branch_id', Auth::user()->branch_id)->get();
        return view('admin.attendance.index', compact('data','employees'));
    }

    public function store(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'clock_in' => 'required|date_format:Y-m-d H:i:s',
            'clock_out' => 'required|date_format:Y-m-d H:i:s|after:clock_in',
            'employee_id' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'details' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 422, 'message' => $validator->errors()->first()]);
        }
        
        $request->merge([
            'clock_in'=>Carbon::parse($request->clock_in),
            'clock_out'=>Carbon::parse($request->clock_out),
            ]);

        
        Attendance::create([
            'employee_id'=>$request->input('employee_id'),
            'clock_in'=>$request->input('clock_in'),
            'clock_out'=>$request->input('clock_out'),
            'details'=>$request->input('details'),
            'type'=>$request->input('type'),
            'branch_id'=>Auth::user()->branch_id,
            'created_by'=>Auth::user()->id,
        ]);
        return response()->json(['status' => 200, 'message' => 'Data created successfully.']);
    }

    public function edit(Request $request, $id)
    {
        return Attendance::find($id);
    }

    public function update(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'clock_in' => 'required|date_format:Y-m-d H:i:s',
            'clock_out' => 'required|date_format:Y-m-d H:i:s|after:clock_in',
            'employee_id' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'details' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 422, 'message' => $validator->errors()->first()]);
        }
        
        $request->merge([
            'clock_in'=>Carbon::parse($request->clock_in),
            'clock_out'=>Carbon::parse($request->clock_out),
            ]);

        $data = Attendance::find($request->codeid);
        $request->merge(['branch_id' => Auth::user()->branch_id]);
        $request->merge(['updated_by' => Auth::user()->id]);
        $data->update($request->all());

        return response()->json(['status' => 200, 'message' => 'Data updated successfully.']);
    }


    public function delete(Request $request, $id)
    {
        
        $employee=Attendance::find($id);
        $employee->delete();
        return response()->json([
            'type'=>'success',
            'message'=>'Data Deleted successfully'
        ]);
    }
}
