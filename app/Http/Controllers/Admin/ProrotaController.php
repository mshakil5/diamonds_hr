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

            // Step 1: Create PreRota entry
            $preRota = PreRota::create([
                'branch_id'   => $branchId,
                'start_date'  => $request->start_date,
                'end_date'    => $request->to_date,
                'type'        => $request->type,
                'details'     => $request->details,
                'start_time'  => null,
                'end_time'    => null,
            ]);

            // Step 2: Build a 7-day schedule template
            $scheduleTemplate = [];
            foreach ($request->input('dates', []) as $i => $date) {
                $scheduleTemplate[] = [
                    'date'       => $date,
                    'day_name'   => $request->day_names[$i] ?? null,
                    'start_time' => $request->start_times[$i] ?? null,
                    'end_time'   => $request->end_times[$i] ?? null,
                ];
            }

            // Step 3: Generate date range (from start_date to to_date)
            $start = Carbon::parse($request->start_date);
            $end   = Carbon::parse($request->to_date);

            $allDates = [];
            for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
                $allDates[] = $date->copy();
            }

            // Step 4: Loop through employees and dates to insert schedule rows
            foreach ($request->employee_id as $employeeId) {
                foreach ($allDates as $dateObj) {
                    $date     = $dateObj->format('Y-m-d');
                    $dayName  = $dateObj->format('l');

                    // Match with the 7-day pattern
                    $pattern = collect($scheduleTemplate)->firstWhere('day_name', $dayName);

                    EmployeePreRota::create([
                        'employee_id' => $employeeId,
                        'pre_rota_id' => $preRota->id,
                        'branch_id'   => $branchId,
                        'date'        => $date,
                        'day_name'    => $dayName,
                        'start_time'  => $pattern['start_time'] ?? null,
                        'end_time'    => $pattern['end_time'] ?? null,
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
        try {
            $preRota = PreRota::with('employees')->findOrFail($id);
            return response()->json($preRota);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 422,
                'message' => 'Error retrieving PreRota: ' . $e->getMessage()
            ], 422);
        }
    }

    public function update(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'employee_id' => 'required|array',
            'employee_id.*' => 'exists:employees,id',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'type' => 'required|in:Regular,Authorized Holiday,Unauthorized Holiday',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'details' => 'nullable|string',
        ]);
        
        $id = $request->codeid;


        try {
            $preRota = PreRota::findOrFail($id);

            // Update PreRota record
            $preRota->update($request->except('employee_id'));

            // Filter out employees who are on holiday today
            $employeeIds = [];
            foreach ($request->employee_id as $employeeId) {
                $holiday = Holiday::where('employee_id', $employeeId)
                    ->whereDate('date', Carbon::today())
                    ->count();

                if (!$holiday) {
                    $employeeIds[] = $employeeId;
                }
            }

            // Sync employees in the pivot table
            $preRota->employees()->sync($employeeIds);

            return response()->json([
                'type' => 'success',
                'message' => 'Pre Rota Updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 422,
                'message' => 'Error updating PreRota: ' . $e->getMessage()
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
