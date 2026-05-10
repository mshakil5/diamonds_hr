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



    public function allBranches(Request $request)
    {
        $fromDate = $request->from_date ?? '';
        $toDate = $request->to_date ?? '';
        $branchId = $request->branch_id ?? '';
        
        // Default to today if no dates provided
        if (!$fromDate && !$toDate) {
            $fromDate = Carbon::today()->format('Y-m-d');
            $toDate = Carbon::today()->format('Y-m-d');
        }
        
        $query = Attendance::with(['employee', 'branch'])
                ->orderBy('id', 'DESC');
        
        // Date filter
        $query->when($fromDate && $toDate, function ($q) use ($fromDate, $toDate) {
            $q->whereBetween('clock_in', [
                Carbon::parse($fromDate)->startOfDay(),
                Carbon::parse($toDate)->endOfDay()
            ]);
        });
        
        // Branch filter
        $query->when($branchId, function ($q) use ($branchId) {
            $q->where('branch_id', $branchId);
        });
        
        $data = $query->get();
        
        // Group data by branch
        $groupedByBranch = $data->groupBy(function ($item) {
            return $item->branch->name ?? 'Unknown Branch';
        });
        
        // Get all branches for dropdown
        $branches = \App\Models\Branch::where('status', 1)->orderBy('name', 'ASC')->get();
        
        return view('admin.attendance.all-branches', compact('groupedByBranch', 'branches', 'fromDate', 'toDate', 'branchId'));
    }

    // For Today's All Branches Attendance (if needed)
    public function allBranchesToday()
    {
        $todayAttendance = Attendance::with(['employee', 'branch'])
                ->whereDate('clock_in', Carbon::today())
                ->orderBy('branch_id', 'ASC')
                ->get();
        
        // Group by branch
        $groupedByBranch = $todayAttendance->groupBy(function ($item) {
            return $item->branch->name ?? 'Unknown Branch';
        });
        
        return view('admin.attendance.all-branches-today', compact('groupedByBranch'));
    }

    // Export for all branches
    public function allBranchesExport(Request $request)
    {
        $fromDate = $request->from_date;
        $toDate = $request->to_date;
        $branchId = $request->branch_id;
        
        $query = Attendance::with(['employee', 'branch']);
        
        if ($fromDate && $toDate) {
            $query->whereBetween('clock_in', [
                Carbon::parse($fromDate)->startOfDay(),
                Carbon::parse($toDate)->endOfDay()
            ]);
        }
        
        if ($branchId) {
            $query->where('branch_id', $branchId);
        }
        
        $data = $query->orderBy('id', 'DESC')->get();
        
        $filename = 'all_branches_attendance_' . date('Y-m-d') . '.csv';
        
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];
        
        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Employee Name', 'Branch', 'Date', 'Type', 'Time In', 'Time Out', 'Late', 'Early Leave', 'Total Time']);
            
            foreach ($data as $item) {
                $totalTime = '-';
                if ($item->clock_in && $item->clock_out) {
                    $in = Carbon::parse($item->clock_in);
                    $out = Carbon::parse($item->clock_out);
                    $diff = $in->diff($out);
                    $totalTime = $diff->format('%H:%I:%S');
                }
                
                fputcsv($file, [
                    $item->employee->name ?? '',
                    $item->branch->name ?? '',
                    $item->clock_in ? Carbon::parse($item->clock_in)->format('d/m/Y') : '',
                    $item->type ?? '',
                    $item->clock_in ? Carbon::parse($item->clock_in)->format('H:i:s') : '',
                    $item->clock_out ? Carbon::parse($item->clock_out)->format('H:i:s') : '',
                    $item->late ?? '',
                    $item->early_leave ?? '',
                    $totalTime
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }







}