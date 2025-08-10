<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Holiday;
use App\Models\PreRota;
use App\Models\EmployeePreRota;
use App\Models\ProrotaDetail;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Spatie\Activitylog\Models\Activity;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ProrotaController extends Controller
{

    public function index()
    {
        $data = PreRota::with('employees')->orderby('id', 'DESC')->get();

        $employees = Employee::all();
        return view('admin.prorota.create', compact('data', 'employees'));
    }
    
    public function create()
    {
        $data = PreRota::with('employees')->orderby('id', 'DESC')->get();

        $employees = Employee::all();
        return view('admin.prorota.create', compact('data', 'employees'));
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required',
            'start_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:start_date',
            'type' => 'required|in:Regular,Authorized Holiday,Unauthorized Holiday',

            'details' => 'nullable|string',

            'dates' => 'nullable|array',
            'dates.*' => 'nullable|date',

            'day_names' => 'nullable|array',
            'day_names.*' => 'nullable|string',

            'start_times' => 'nullable|array',
            'start_times.*' => 'nullable|date_format:H:i',

            'end_times' => 'nullable|array',
            'end_times.*' => 'nullable|date_format:H:i',
        ]);

        try {
            $branchId = Auth::user()->branch_id;
            $createdBy = Auth::id();

            $preRota = PreRota::create([
                'branch_id'   => $branchId,
                'start_date'  => $request->start_date,
                'end_date'    => $request->to_date,
                'type'        => $request->type,
                'details'     => $request->details,
                'start_time'  => null,
                'end_time'    => null,
            ]);

            $scheduleTemplate = [];
            foreach ($request->input('dates', []) as $i => $date) {
                $scheduleTemplate[] = [
                    'date'       => $date,
                    'day_name'   => $request->day_names[$i] ?? null,
                    'start_time' => $request->start_times[$i] ?? null,
                    'end_time'   => $request->end_times[$i] ?? null,
                    'status'   => $request->status[$i] ?? null,
                ];
            }

            $start = Carbon::parse($request->start_date);
            $end = Carbon::parse($request->to_date);

            $allDates = [];
            for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
                $allDates[] = $date->copy();
            }

            foreach ($allDates as $dateObj) {
                $date = $dateObj->format('Y-m-d');
                $dayName = $dateObj->format('l');

                $pattern = collect($scheduleTemplate)->firstWhere('day_name', $dayName);
                $employeBranchId = Employee::find($request->employee_id)->branch_id ?? $branchId;

                $startTime = $pattern['start_time'] ?? null;
                $endTime = $pattern['end_time'] ?? null;
                $status = $pattern['status'] ?? null;

                // 1 = In Rota, 2 = Day off, 3 = Holiday

                EmployeePreRota::where('employee_id', $request->employee_id)
                    ->where('date', $date)
                    ->delete();

                // Create new record
                EmployeePreRota::create([
                    'employee_id' => $request->employee_id,
                    'pre_rota_id' => $preRota->id,
                    'branch_id' => $employeBranchId,
                    'date' => $date,
                    'day_name' => $dayName,
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'status' => $status,
                    'created_by' => $createdBy,
                ]);
            }

            return response()->json([
                'type' => 'success',
                'message' => 'Pre Rota and schedules created successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 422,
                'message' => 'Error creating Pre Rota: ' . $e->getMessage()
            ], 422);
        }
    }

    public function edit($id)
    {
        $preRota = PreRota::with(['employees' => function ($query) {
            $query->withPivot('date', 'day_name', 'start_time', 'end_time', 'status');
        }])->findOrFail($id);

        $employees = Employee::all();

        return view('admin.prorota.edit', compact('preRota', 'employees'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'employee_id' => 'required',
            'start_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:start_date',
            'type' => 'required|in:Regular,Authorized Holiday,Unauthorized Holiday',
            'dates' => 'required|array',
            'day_names' => 'required|array',
            'start_times' => 'required|array',
            'end_times' => 'required|array',
            'status' => 'required|array',
        ]);

        $preRota = PreRota::findOrFail($request->codeid);
        $start = Carbon::parse($request->start_date);
        $end = Carbon::parse($request->to_date);
        $createdBy = auth()->id(); // Assuming authenticated user ID

        // Update PreRota details
        $preRota->update([
            'start_date' => $request->start_date,
            'end_date' => $request->to_date,
            'type' => $request->type,
            'details' => $request->details,
        ]);

        
            // Delete existing EmployeePreRota records for this employee and date range
            EmployeePreRota::where('employee_id', $request->employee_id)
                ->where('pre_rota_id', $preRota->id)
                ->whereBetween('date', [$start, $end])
                ->delete();

            // Create new EmployeePreRota records
            foreach ($request->dates as $index => $date) {
                $employeBranchId = Employee::find($request->employee_id)->branch_id ?? $preRota->branch_id;

                EmployeePreRota::create([
                    'employee_id' => $request->employee_id,
                    'pre_rota_id' => $preRota->id,
                    'branch_id' => $employeBranchId,
                    'date' => $date,
                    'day_name' => $request->day_names[$index],
                    'start_time' => $request->start_times[$index],
                    'end_time' => $request->end_times[$index],
                    'status' => $request->status[$index] ?: null, // Handle empty status
                    'created_by' => $createdBy,
                ]);
            }
            

        return response()->json(['status' => 200, 'message' => 'PreRota updated successfully']);
    }


    public function destroy($id)
    {
        try {
            $preRota = PreRota::findOrFail($id);
            $preRota->employees()->detach(); // Remove all related employees
            $preRota->delete();
            return response()->json([
                'type' => 'success',
                'message' => 'Pre Rota Deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 422,
                'message' => 'Error deleting PreRota: ' . $e->getMessage()
            ], 422);
        }
    }
    
}
