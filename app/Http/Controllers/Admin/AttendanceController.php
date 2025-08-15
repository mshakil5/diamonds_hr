<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use League\Csv\Writer;

class AttendanceController extends Controller
{

    public function index(Request $request)
    {
        
        $fromDate = $request->from_date ?? '';
        $toDate = $request->to_date ?? '';
        
        $query = Attendance::where('branch_id', Auth::user()->branch_id)
                ->with(['employee', 'branch'])
                ->orderBy('id', 'DESC');

                $query->when($fromDate && $toDate, function ($q) use ($fromDate, $toDate) {
                    $q->whereBetween('clock_in', [
                        Carbon::parse($fromDate)->startOfDay(),
                        Carbon::parse($toDate)->endOfDay()
                    ]);
                });

            $data = $query->get();


        $employees = Employee::where('is_active', 1)->where('branch_id', Auth::user()->branch_id)->get();
        return view('admin.attendance.index', compact('data','employees', 'fromDate', 'toDate'));
    }



    


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'clock_in' => 'required|date_format:Y-m-d H:i:s',
            'clock_out' => 'nullable|date_format:Y-m-d H:i:s|after:clock_in',
            'employee_id' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'details' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 422, 'message' => $validator->errors()->first()]);
        }

        Attendance::create([
            'employee_id' => $request->input('employee_id'),
            'clock_in' => $request->input('clock_in'),
            'clock_out' => $request->input('clock_out'),
            'details' => $request->input('details'),
            'type' => $request->input('type'),
            'branch_id' => Auth::user()->branch_id,
            'created_by' => Auth::user()->id,
        ]);

        return response()->json(['status' => 200, 'message' => 'Data created successfully.']);
    }

    public function edit(Request $request, $id)
    {
        return response()->json(Attendance::findOrFail($id));
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'clock_in' => 'required|date_format:Y-m-d H:i:s',
            'clock_out' => 'required|date_format:Y-m-d H:i:s|after:clock_in',
            'employee_id' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'details' => 'nullable|string|max:255',
            'codeid' => 'required|exists:attendances,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 422, 'message' => $validator->errors()->first()]);
        }

        $data = Attendance::findOrFail($request->codeid);
        $data->update([
            'employee_id' => $request->employee_id,
            'clock_in' => Carbon::parse($request->clock_in),
            'clock_out' => Carbon::parse($request->clock_out),
            'type' => $request->type,
            'details' => $request->details,
            'branch_id' => Auth::user()->branch_id,
            'updated_by' => Auth::user()->id,
        ]);

        return response()->json(['status' => 200, 'message' => 'Data updated successfully.']);
    }

    public function destroy(Request $request, $id)
    {
        $attendance = Attendance::findOrFail($id);
        $attendance->delete();
        return response()->json([
            'status' => 200,
            'message' => 'Data deleted successfully'
        ]);
    }

    public function export(Request $request)
    {
        $fromDate = $request->query('from_date');
        $toDate = $request->query('to_date');
        
        $attendances = Attendance::whereBetween('clock_in', [$fromDate, Carbon::parse($toDate)->endOfDay()])
            ->with(['employee', 'branch'])
            ->where('branch_id', Auth::user()->branch_id)
            ->get();
        
        $csv = Writer::createFromFileObject(new \SplTempFileObject());
        $csv->insertOne(['ID', 'Employee', 'Branch', 'Type', 'Clock In', 'Clock Out', 'Total Time', 'Details']);
        
        foreach ($attendances as $data) {
            $diff = $data->clock_in && $data->clock_out 
                ? Carbon::parse($data->clock_in)->diff(Carbon::parse($data->clock_out))->format('%H:%I:%S') 
                : '-';
            $csv->insertOne([
                $data->id,
                $data->employee->name,
                $data->branch->name ?? '',
                $data->type,
                $data->clock_in ? Carbon::parse($data->clock_in)->format('d/m/Y H:i:s') : '',
                $data->clock_out ? Carbon::parse($data->clock_out)->format('d/m/Y H:i:s') : '',
                $diff,
                $data->details ?? ''
            ]);
        }
        
        return response($csv->output('attendance.csv'), 200)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="attendance.csv"');
    }
}