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
            'employee_id' => 'required|exists:employees,id',
            'start_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:start_date',
            'type' => 'required|in:Regular,Authorized Holiday,Unauthorized Holiday',
            'details' => 'nullable|string',
            'dates' => 'required|array',
            'dates.*' => 'required|date',
            'day_names' => 'required|array',
            'day_names.*' => 'required|string',
            'start_times' => 'required|array',
            'start_times.*' => 'nullable|date_format:H:i',
            'end_times' => 'required|array',
            'end_times.*' => 'nullable|date_format:H:i',
            'status' => 'required|array',
            'status.*' => 'nullable|in:1,2,3',
        ]);

        try {
            $branchId = Auth::user()->branch_id;
            $createdBy = Auth::id();

            $holidayCount = count(array_filter($request->status, fn($status) => $status === '3'));

            $employee = Employee::find($request->employee_id);
            if (!$employee) {
                return response()->json(['status' => 422, 'message' => 'Employee not found.'], 422);
            }

            $counts = $employee->leave_status_counts;
            $used = ($counts['booked'] ?? 0) + ($counts['taken'] ?? 0);
            $available = $employee->entitled_holiday - $used;

            if ($holidayCount > $available) {
                return response()->json([
                    'status' => 422,
                    'message' => "Only $available holiday(s) available, but $holidayCount requested."
                ], 422);
            }

            $preRota = PreRota::create([
                'branch_id' => $branchId,
                'start_date' => $request->start_date,
                'end_date' => $request->to_date,
                'type' => $request->type,
                'details' => $request->details,
                'start_time' => null,
                'end_time' => null,
            ]);

            $start = Carbon::parse($request->start_date);
            $end = Carbon::parse($request->to_date);
            $employeBranchId = $employee->branch_id ?? $branchId;

            foreach ($request->dates as $index => $date) {
                $dateObj = Carbon::parse($date);
                if ($dateObj->lt($start) || $dateObj->gt($end)) {
                    continue; // Skip dates outside the range
                }

                // Check for existing EmployeePreRota record for this employee and date
                $existingRecord = EmployeePreRota::where('employee_id', $request->employee_id)
                    ->where('date', $date)
                    ->first();

                if ($existingRecord) {
                    // Update existing record
                    $existingRecord->update([
                        'pre_rota_id' => $preRota->id,
                        'branch_id' => $employeBranchId,
                        'day_name' => $request->day_names[$index],
                        'start_time' => $request->start_times[$index] ?? null,
                        'end_time' => $request->end_times[$index] ?? null,
                        'status' => $request->status[$index] ?? null,
                        'created_by' => $createdBy,
                    ]);
                } else {
                    // Create new record
                    EmployeePreRota::create([
                        'employee_id' => $request->employee_id,
                        'pre_rota_id' => $preRota->id,
                        'branch_id' => $employeBranchId,
                        'date' => $date,
                        'day_name' => $request->day_names[$index],
                        'start_time' => $request->start_times[$index] ?? null,
                        'end_time' => $request->end_times[$index] ?? null,
                        'status' => $request->status[$index] ?? null,
                        'created_by' => $createdBy,
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
        $preRota = PreRota::with(['employees' => function ($query) {
            $query->withPivot('date', 'day_name', 'start_time', 'end_time', 'status');
        }])->findOrFail($id);

        $employees = Employee::all();

        return view('admin.prorota.edit', compact('preRota', 'employees'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required',
            'start_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:start_date',
            'type' => 'required|in:Regular,Authorized Holiday,Unauthorized Holiday',
            'details' => 'nullable|string',
            'dates' => 'required|array',
            'dates.*' => 'required|date',
            'day_names' => 'required|array',
            'day_names.*' => 'required|string',
            'start_times' => 'required|array',
            'start_times.*' => 'nullable|date_format:H:i',
            'end_times' => 'required|array',
            'end_times.*' => 'nullable|date_format:H:i',
            'status' => 'required|array',
            'status.*' => 'nullable|in:1,2,3',
            'codeid' => 'required|exists:pre_rotas,id',
        ]);

        try {
            $start = Carbon::parse($request->start_date);
            $end = Carbon::parse($request->to_date);
            $createdBy = auth()->id();
            $preRota = PreRota::findOrFail($request->codeid);

            $preRota->update([
                'start_date' => $request->start_date,
                'end_date' => $request->to_date,
                'type' => $request->type,
                'details' => $request->details,
            ]);

                $holidayCount = count(array_filter($request->status, fn($status) => $status === '3'));

                $employee = Employee::find($request->employee_id);
                if (!$employee) {
                    return response()->json(['status' => 422, 'message' => "Employee ID $request->employee_id not found."], 422);
                }

                $counts = $employee->leave_status_counts;
                $used = ($counts['booked'] ?? 0) + ($counts['taken'] ?? 0);
                $available = $employee->entitled_holiday - $used;

                if ($holidayCount > $available) {
                    return response()->json([
                        'status' => 422,
                        'message' => "Only $available holiday(s) available for this employee, but $holidayCount requested."
                    ], 422);
                }

                $employeBranchId = $employee->branch_id ?? $preRota->branch_id;
                foreach ($request->dates as $index => $date) {
                    $dateObj = Carbon::parse($date);
                    if ($dateObj->lt($start) || $dateObj->gt($end)) {
                        continue; 
                    }

                    $existingRecord = EmployeePreRota::where('employee_id', $request->employee_id)
                        ->where('date', $date)
                        ->first();

                    if ($existingRecord) {
                        $existingRecord->update([
                            'pre_rota_id' => $preRota->id,
                            'branch_id' => $employeBranchId,
                            'day_name' => $request->day_names[$index],
                            'start_time' => $request->start_times[$index] ?? null,
                            'end_time' => $request->end_times[$index] ?? null,
                            'status' => $request->status[$index] ?? null,
                            'created_by' => $createdBy,
                        ]);
                    } else {
                        EmployeePreRota::create([
                            'employee_id' => $request->employee_id,
                            'pre_rota_id' => $preRota->id,
                            'branch_id' => $employeBranchId,
                            'date' => $date,
                            'day_name' => $request->day_names[$index],
                            'start_time' => $request->start_times[$index] ?? null,
                            'end_time' => $request->end_times[$index] ?? null,
                            'status' => $request->status[$index] ?? null,
                            'created_by' => $createdBy,
                        ]);
                    }
                }

            return response()->json(['status' => 200, 'message' => 'PreRota updated successfully']);
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
