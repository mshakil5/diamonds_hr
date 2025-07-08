<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Prorota;
use App\Models\ProrotaDetail;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Spatie\Activitylog\Models\Activity;
use Barryvdh\DomPDF\Facade\Pdf;

class ProrotaController extends Controller
{
    public function prorotaLog($id)
  {
      $prorota = Prorota::find($id);
  
      if (!$prorota) {
          return redirect()->back()->with('error', 'Prorota record not found.');
      }
  
      $prorotaLogs = Activity::with('causer')
          ->where('subject_type', Prorota::class)
          ->where('subject_id', $prorota->id)
          ->latest()
          ->get();
  
      $prorotaDetailLogs = Activity::with('causer')
          ->where('subject_type', ProrotaDetail::class)
          ->whereIn('subject_id', $prorota->prorotaDetail->pluck('id'))
          ->latest()
          ->get();
  
      return view('admin.prorota.prorota_log', compact('prorota', 'prorotaLogs', 'prorotaDetailLogs'));
  }

    public function index()
    {   
        return view('admin.prorota.index');
    }

    public function getprorota(Request $request)
    {
        if ($request->ajax()) {
            $data = Prorota::orderBy('id', 'DESC')->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('staff_name', function($row) {
                  $firstName = $row->staff ? $row->staff->name : '';
                  
                  return trim($firstName);
              })
                ->make(true);
        }
    }

    public function create()
    {
        $employees = Employee::whereNotIn('user_id', function ($query) {
                $query->select('staff_id')->from('prorotas');
            })
            ->orderBy('id', 'DESC')
            ->get();
    
        return view('admin.prorota.create', compact('employees'));
    }    

    public function store(Request $request)
    {

        

        $validator = Validator::make($request->all(), [
            'staff_id' => 'required|array|min:1',
            'staff_id.*' => 'exists:users,id',
            'schedule_type' => 'required|string|max:255|in:Regular,Authorized Holiday,Unauthorized Holiday',
            'start_time.*' => ['required_with:end_time.*', 'nullable'],
            'end_time.*' => ['required_with:start_time.*', 'nullable'],
            'from_date' => ['required_if:schedule_type,Authorized Holiday,Unauthorized Holiday', 'nullable', 'date'],
            'to_date' => ['required_if:schedule_type,Authorized Holiday,Unauthorized Holiday', 'nullable', 'date', 'after_or_equal:from_date'],
        ], [
            'staff_id.required' => 'Please select at least one employee.',
            'staff_id.array' => 'Invalid employee selection format.',
            'staff_id.*.exists' => 'Selected employee does not exist.',
            'schedule_type.required' => 'Please select a schedule type.',
            'start_time.*.required_with' => 'Start time is required when end time is provided.',
            'end_time.*.required_with' => 'End time is required when start time is provided.',
            'from_date.required_if' => 'From date is required for holiday schedules.',
            'to_date.required_if' => 'To date is required for holiday schedules.',
            'to_date.after_or_equal' => 'To date must be on or after the from date.',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 422, 'errors' => $validator->errors()], 422);
        }

        $success = true;
        foreach ($request->staff_id as $staffId) {
            $data = new Prorota;
            $data->staff_id = $staffId;
            $data->schedule_type = $request->schedule_type;

            if ($data->save()) {
                if ($request->schedule_type === 'Regular') {
                    foreach ($request->day as $key => $day) {
                        if (!empty($request->start_time[$key]) && !empty($request->end_time[$key])) {
                            $schedule = new ProrotaDetail();
                            $schedule->staff_id = $staffId;
                            $schedule->prorota_id = $data->id;
                            $schedule->day = $day;
                            $schedule->start_time = $request->start_time[$key];
                            $schedule->end_time = $request->end_time[$key];
                            $schedule->save();
                        }
                    }
                } else {
                    $schedule = new ProrotaDetail();
                    $schedule->staff_id = $staffId;
                    $schedule->prorota_id = $data->id;
                    $schedule->from_date = $request->from_date;
                    $schedule->to_date = $request->to_date;
                    $schedule->save();
                }
            } else {
                $success = false;
            }
        }

        if ($success) {
            return response()->json(['status' => 200, 'message' => 'Staff schedules created successfully']);
        } else {
            return response()->json(['status' => 500, 'message' => 'Server Error']);
        }

    }

    // public function showDetails($id)
    // {
    //     $data = Prorota::with('prorotaDetail')->findOrFail($id);
    //     return view('admin.prorota.details', compact('data'));
    // }

    public function showDetails($id)
    {
        try {

            $prorota = Prorota::with('prorotaDetail')->findOrFail($id);
            $staff = User::findOrFail($prorota->staff_id); // Adjust based on your User/Employee model

            $html = '<p><strong>Employee:</strong> ' . htmlspecialchars($staff->name) . '</p>';
            $html .= '<p><strong>Schedule Type:</strong> ' . htmlspecialchars($prorota->schedule_type) . '</p>';

            if ($prorota->schedule_type === 'Regular') {
                $html .= '<h5>Schedule Details:</h5>';
                $html .= '<table class="table table-bordered">';
                $html .= '<thead><tr><th>Day</th><th>Start Time</th><th>End Time</th></tr></thead>';
                $html .= '<tbody>';
                foreach ($prorota->prorotaDetail as $detail) {
                    if ($detail->day && $detail->start_time && $detail->end_time) {
                        $html .= '<tr>';
                        $html .= '<td>' . htmlspecialchars($detail->day) . '</td>';
                        $html .= '<td>' . htmlspecialchars($detail->start_time) . '</td>';
                        $html .= '<td>' . htmlspecialchars($detail->end_time) . '</td>';
                        $html .= '</tr>';
                    }
                }
                $html .= '</tbody></table>';
            } else {
                $html .= '<h5>Holiday Details:</h5>';
                foreach ($prorota->prorotaDetail as $detail) {
                    if ($detail->from_date && $detail->to_date) {
                        $html .= '<p><strong>From Date:</strong> ' . htmlspecialchars($detail->from_date) . '</p>';
                        $html .= '<p><strong>To Date:</strong> ' . htmlspecialchars($detail->to_date) . '</p>';
                    }
                }
            }

            return response()->json(['status' => 200, 'data' => $html]);
            
        } catch (\Exception $e) {
            return response()->json(['status' => 500, 'message' => 'Error fetching details'], 500);
        }
    }

    public function deleteData($id)
    {
            $user = Prorota::findOrFail($id);
            $user->delete();
            return response()->json([
                'status' => 200,
                'message' => 'Data deleted successfully.'
            ]);
    }

    public function edit($id)
    {
        $employees = Employee::orderby('id','DESC')->get();
        $data = Prorota::with('prorotaDetail')->findOrFail($id);
        return view('admin.prorota.edit', compact('data','employees'));
    }

    public function update(Request $request)
    {

        

        $validator = Validator::make($request->all(), [
            'staff_id' => 'required|max:255',
            // 'schedule_type' => '|string|max:255',
            'start_time.*' => ['required_with:end_time.*'],
            'end_time.*' => ['required_with:start_time.*'],
            'day.*' => ['distinct'],
        ],[
            'staff_id' => 'Please select an employee.',
            'start_time.*.required_with' => 'Start time is required when end time is provided.',
            'end_time.*.required_with' => 'End time is required when start time is provided.',
            'day.*.distinct' => 'Duplicate values are not allowed for the day field.',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 422, 'errors' => $validator->errors()], 422);
        }

        $data = Prorota::find($request->prorota_id);
        $data->staff_id = $request->staff_id;
        $data->schedule_type = $request->schedule_type;
        if ($data->save()) {

            $missingIds = ProrotaDetail::where('prorota_id', $data->id)
                ->whereNotIn('id', $request->prorotaDetail_id)
                ->pluck('id');

            ProrotaDetail::whereIn('id', $missingIds)->delete();


            foreach ($request->day as $key => $day) {
                if (!empty($request->get('start_time')[$key])) {
                    if ($request->get('prorotaDetail_id')[$key]) {
                        $schedule = ProrotaDetail::find($request->get('prorotaDetail_id')[$key]);
                        $schedule->staff_id = $request->staff_id;
                        $schedule->prorota_id = $data->id;
                        $schedule->day =  $request->get('day')[$key];
                        $schedule->start_time = $request->get('start_time')[$key];
                        $schedule->end_time = $request->get('end_time')[$key];
                        $schedule->save();
                    } else {
                        
                        $schedule = new ProrotaDetail();
                        $schedule->staff_id = $request->staff_id;
                        $schedule->prorota_id = $data->id;
                        $schedule->day =  $request->get('day')[$key];
                        $schedule->start_time = $request->get('start_time')[$key];
                        $schedule->end_time = $request->get('end_time')[$key];
                        $schedule->save();
                    }
                    
                }
            }
            return response()->json(['status' => 200, 'message' => 'Staff schedule created successfully', 'dids' => $missingIds]);
        } else {
            return response()->json(['status' => 500, 'message' => 'Server Error']);
        }

    }

    public function downloadPdf()
    {
        $year = now()->year;
        $branchId = Auth::user()->branch_id;

        $holidays = Prorota::with(['prorotaDetail', 'staff'])
            ->whereIn('schedule_type', ['Authorized Holiday', 'Unauthorized Holiday'])
            ->whereHas('staff', function ($query) use ($branchId) {
            $query->where('branch_id', $branchId);
            })
            ->whereYear('created_at', $year)
            ->get()
        ->groupBy('staff_id');

        $pdf = Pdf::loadView('admin.prorota.download-pdf', compact('holidays'));
        return $pdf->download('holiday_records.pdf');
    }
    
}
