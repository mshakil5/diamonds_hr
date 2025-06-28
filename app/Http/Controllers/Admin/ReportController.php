<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Holiday;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function employeeReport(Request $request)
    {
        if ($request->isMethod('post')) {
            
        // Format date range
        $fromDate = Carbon::parse($request->from_date)->startOfDay();
        $toDate = Carbon::parse($request->to_date)->endOfDay();

        

        $data = DB::table('attendances')
            ->select('attendances.*', 'employees.name as employee_name')
            ->join('employees', 'attendances.employee_id', '=', 'employees.id')
            ->where('attendances.employee_id', $request->input('employee_id'))
            ->where('attendances.branch_id', Auth::user()->branch_id)
            ->whereBetween('attendances.clock_in', [$fromDate, $toDate])
            ->get();

            // dd($data);
        
        $employeeName = Employee::where('id', $request->input('employee_id'))->where('branch_id', Auth::user()->branch_id)->value('name');
        $employees = Employee::where('is_active', 1)->where('branch_id', Auth::user()->branch_id)->get();
        return view('admin.reports.employeeReport', compact('employees','data','employeeName'));

        } else {
            $employees = Employee::where('is_active', 1)->where('branch_id', Auth::user()->branch_id)->get();
            $data = [];
            $employeeName = null;
            return view('admin.reports.employeeReport', compact('employees','data','employeeName'));
        }
        
    }


    public function holidayReport(Request $request)
    {
        if ($request->isMethod('post')) {
            
            $employeeId=request()->input('employee_id');
            $contractDateBegin = date('Y') . '-04-01';
            $contractDateEnd = date('Y', strtotime('+1 year')) . '-03-31';

            $employee= Employee::find($employeeId);
            $holidayData=Holiday::with('employee')
                ->whereEmployeeId($employeeId)
                ->whereBetween('date',[$contractDateBegin,Carbon::today()])
                ->where('type','Authorized holiday')
                ->get();
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


            $employeeName = Employee::where('id', $request->input('employee_id'))->where('branch_id', Auth::user()->branch_id)->first();
            $employees = Employee::where('is_active', 1)->where('branch_id', Auth::user()->branch_id)->get();
            return view('admin.reports.holidayReport', compact('employees','employeeName','holidayData','holidayDataCount','sickDays','absenceDays','employee'));

        } else {

            $employees = Employee::where('is_active', 1)->where('branch_id', Auth::user()->branch_id)->get();
            $employeeName = null;
            return view('admin.reports.holidayReport', compact('employees','employeeName'));

        }
        
    }


}
