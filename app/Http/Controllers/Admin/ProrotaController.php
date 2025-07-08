<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Holiday;
use App\Models\PreRota;
use App\Models\Prorota;
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
        // Log incoming request data for debugging
        \Log::info('Store Request Data:', $request->all());

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

        try {
            // Add branch_id to the request
            $request->merge(['branch_id' => Auth::user()->branch_id]);

            // Create PreRota record
            $preRota = PreRota::create($request->except('employee_id'));

            // Log created PreRota ID
            \Log::info('Created PreRota ID:', ['id' => $preRota->id]);

            // Filter out employees who are on holiday for start_date
            $employeeIds = [];
            foreach ($request->employee_id as $employeeId) {
                $holiday = Holiday::where('employee_id', $employeeId)
                    ->whereDate('date', $request->start_date)
                    ->count();

                \Log::info('Holiday Check for Employee ID:', [
                    'employee_id' => $employeeId,
                    'date' => $request->start_date,
                    'holiday_count' => $holiday
                ]);

                if (!$holiday) {
                    $employeeIds[] = $employeeId;
                }
            }

            // Log filtered employee IDs
            \Log::info('Filtered Employee IDs:', $employeeIds);

            // Attach employees to the pivot table
            if (!empty($employeeIds)) {
                $preRota->employees()->attach($employeeIds);
                \Log::info('Employees Attached:', $employeeIds);
            } else {
                \Log::warning('No employees attached after holiday filter.');
            }

            return response()->json([
                'type' => 'success',
                'message' => 'Pre Rota Created successfully'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error creating PreRota:', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 422,
                'message' => 'Error creating PreRota: ' . $e->getMessage()
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
