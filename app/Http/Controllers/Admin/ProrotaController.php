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
            'employee_id' => 'required|array',
            'employee_id.*' => 'exists:employees,id',

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
                ];
            }

            $start = Carbon::parse($request->start_date);
            $end   = Carbon::parse($request->to_date);

            $allDates = [];
            for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
                $allDates[] = $date->copy();
            }

            foreach ($request->employee_id as $employeeId) {
                foreach ($allDates as $dateObj) {
                    $date     = $dateObj->format('Y-m-d');
                    $dayName  = $dateObj->format('l');

                    $pattern = collect($scheduleTemplate)->firstWhere('day_name', $dayName);
                    $employeBranchId = Employee::find($employeeId)->branch_id ?? $branchId;

                    $startTime = $pattern['start_time'] ?? null;
                    $endTime = $pattern['end_time'] ?? null;
                    $status = $startTime ? 1 : 2;


                    EmployeePreRota::create([
                        'employee_id' => $employeeId,
                        'pre_rota_id' => $preRota->id,
                        'branch_id'   => $employeBranchId,
                        'date'        => $date,
                        'day_name'    => $dayName,
                        'start_time'  => $startTime,
                        'end_time'    => $endTime,
                        'status'      => $status,
                        'created_by'  => $createdBy,
                    ]);
                }
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
        $preRota = PreRota::with('employees')->findOrFail($id);

        $employees = Employee::all();
        
        return view('admin.prorota.edit', compact('preRota','employees'));
        
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|array',
            'employee_id.*' => 'exists:employees,id',
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
            'employee_ids' => 'nullable|array',
            'employee_ids.*' => 'nullable|exists:employees,id',
            'codeid' => 'required|exists:pre_rotas,id',
        ]);

        try {
            $branchId = Auth::user()->branch_id;
            $createdBy = Auth::id();

            // Validate array lengths
            $datesCount = count($request->input('dates', []));
            if ($datesCount > 0 && (
                $datesCount !== count($request->input('employee_ids', [])) ||
                $datesCount !== count($request->input('day_names', [])) ||
                $datesCount !== count($request->input('start_times', [])) ||
                $datesCount !== count($request->input('end_times', []))
            )) {
                throw new \Exception('Mismatch in array lengths for dates, employee_ids, day_names, start_times, or end_times.');
            }

            // Step 1: Find and update the PreRota entry
            $preRota = PreRota::findOrFail($request->codeid);
            $preRota->update([
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
                if (isset($request->employee_ids[$i])) {
                    $scheduleTemplate[] = [
                        'date'       => $date,
                        'day_name'   => $request->day_names[$i] ?? null,
                        'start_time' => $request->start_times[$i] ?? null,
                        'end_time'   => $request->end_times[$i] ?? null,
                        'employee_id' => $request->employee_ids[$i],
                    ];
                }
            }


            $start = Carbon::parse($request->start_date);
            $end = Carbon::parse($request->to_date);
            $allDates = [];
            for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
                $allDates[] = $date->copy();
            }

            // Step 4: Delete existing EmployeePreRota records for this PreRota
            EmployeePreRota::where('pre_rota_id', $preRota->id)->delete();

            // Step 5: Loop through employees and dates to insert new schedule rows
            foreach ($request->employee_id as $employeeId) {
                foreach ($allDates as $dateObj) {
                    $date = $dateObj->format('Y-m-d');
                    $dayName = $dateObj->format('l');

                    $pattern = collect($scheduleTemplate)->firstWhere(function ($item) use ($dayName, $employeeId) {
                        return $item['day_name'] === $dayName && $item['employee_id'] == $employeeId;
                    });

                    $employee = Employee::find($employeeId);
                    if (!$employee) {
                        throw new \Exception("Employee ID $employeeId not found.");
                    }
                    $employeBranchId = $employee->branch_id ?? $branchId;

                    $startTime = $pattern['start_time'] ?? null;
                    $endTime = $pattern['end_time'] ?? null;
                    $status = $startTime ? 1 : 2;


                    EmployeePreRota::create([
                        'employee_id' => $employeeId,
                        'pre_rota_id' => $preRota->id,
                        'branch_id'   => $employeBranchId,
                        'date'        => $date,
                        'day_name'    => $dayName,
                        'start_time'  => $startTime,
                        'end_time'    => $endTime,
                        'status'      => $status,
                        'created_by'  => $createdBy,
                    ]);
                }
            }

            return response()->json([
                'type' => 'success',
                'data' => $request->all(),
                'message' => 'Pre Rota and schedules updated successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 422,
                'message' => 'Error updating Pre Rota: ' . $e->getMessage()
            ], 422);
        }
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
