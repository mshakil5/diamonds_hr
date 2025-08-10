<?php
  
namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Blog;
use App\Models\Holiday;
use App\Models\User;
use Carbon\Carbon;
use App\Models\Employee;
use Illuminate\Support\Facades\Auth;
use App\Models\StockAssetType;
use App\Models\Branch;
use App\Models\EmployeePreRota;
use App\Models\Floor;
use App\Models\Maintenance;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
  
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function index()
    {
        if (auth()->user()->is_type == '1') {
            return redirect()->route('admin.dashboard');
        }else if (auth()->user()->is_type == '0') {
            return redirect()->route('user.profile');
        }else{
            return view('layouts.frontend');
        }
    } 


    public function userHome(): View
    {
        return view('user.dashboard');
    } 
  
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function adminHome(): View
    {
        $blogsCount = Blog::count();
        $usersCount = User::where('is_type', 0)->count();

        $startDate = date('Y') . '-04-01';

        $monthlyHoliday=Holiday::whereYear('date', Carbon::now()->year)
                ->whereMonth('date', Carbon::now()->month)->count();

        $todaySick = Attendance::where('branch_id', Auth::user()->branch_id)->whereDate('clock_in', Carbon::today())->whereType('Sick')->count();
        $todayAbsence = Attendance::where('branch_id', Auth::user()->branch_id)->whereDate('clock_in', Carbon::today())->whereType('Absence')->count();
        $totalHours = Attendance::where('branch_id', Auth::user()->branch_id)->whereDate('clock_in', Carbon::today())->count();
        $todayAttendance = Attendance::where('branch_id', Auth::user()->branch_id)->whereDate('clock_in', Carbon::today())->get();

        $assets = StockAssetType::with(['location.flooor', 'branch', 'maintenance', 'stock.assetType'])
        ->where('asset_status', 5)
        ->get();

        $branches = Branch::where('status', 1)->get();
        $floors = Floor::where('status', 1)->get();
        $maintenances = Maintenance::where('status', 1)->get();

        $statuses = [
            1 => 'Assigned',
            2 => 'In Storage',
            3 => 'Under Repair',
            4 => 'Damaged',
            5 => 'Reported',
        ];

        $status = 5;
        $statusCounts = StockAssetType::selectRaw('asset_status, COUNT(*) as total')
        ->groupBy('asset_status')
        ->pluck('total', 'asset_status');


        $currentDate = Carbon::today();
        $currentWeekStart = $currentDate->copy()->startOfWeek(Carbon::MONDAY); 
        $currentWeekEnd = $currentDate->copy()->endOfWeek(Carbon::SUNDAY);

        $nextWeekStart = $currentWeekStart->copy()->addWeek(); 
        $nextWeekEnd = $currentWeekEnd->copy()->addWeek();


        $currentWeekPreRota = \App\Models\EmployeePreRota::whereBetween('date', [$currentWeekStart->format('Y-m-d'), $currentWeekEnd->format('Y-m-d')])
        ->distinct('employee_id')
        ->count('employee_id');

        $nextWeekPreRota = \App\Models\EmployeePreRota::whereBetween('date', [$nextWeekStart->format('Y-m-d'), $nextWeekEnd->format('Y-m-d')])
            ->distinct('employee_id')
            ->count('employee_id');

        return view('admin.dashboard', compact('monthlyHoliday', 'todaySick','todayAbsence','todayAttendance','totalHours','blogsCount', 'usersCount', 'assets', 'branches', 'floors', 'maintenances', 'statuses', 'status', 'statusCounts','currentWeekPreRota','nextWeekPreRota'));
    }
  
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function managerHome(): View
    {
        return view('manager.dashboard');
    }

    public function clearSession(Request $request)
    {
        $user = Auth::user();

        $employee = $user->employee ?? Employee::where('user_id', $user->id)->first();

        if ($employee) {
            $attendance = Attendance::where('employee_id', $employee->id)
                ->whereDate('clock_in', Carbon::today())
                ->whereNull('clock_out')
                ->latest('clock_in')
                ->first();

            if ($attendance) {
                $attendance->update([
                    'clock_out' => Carbon::now()->format('Y-m-d H:i'),
                    'details' => $request->details,
                ]);
            }
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('message', 'Logged out successfully.');
    }

    public function logoutWithActivity(Request $request)
    {
      return response()->json($request->all());
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
            'details'  => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $employee = $user->employee ?? Employee::where('user_id', $user->id)->first();

            if ($employee) {
                $attendance = Attendance::where('employee_id', $employee->id)
                    ->whereDate('clock_in', Carbon::today())
                    ->whereNull('clock_out')
                    ->latest('clock_in')
                    ->first();

                if ($attendance) {
                    $attendance->update([
                        'clock_out' => now()->format('Y-m-d H:i'),
                        'details'   => $request->details,
                    ]);
                } else {
                    $new = Attendance::create([
                        'employee_id' => $employee->id,
                        'branch_id'   => $employee->branch_id,
                        'clock_in'    => now()->format('Y-m-d H:i'),
                        'type'        => 'Regular',
                    ]);
                    $new->update([
                        'clock_out' => now()->format('Y-m-d H:i'),
                        'details'   => $request->details,
                    ]);
                }
            }

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return response()->json([
                'success' => true,
                'message' => 'Logged out successfully.',
                'redirect' => route('login'),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid credentials. Please try again.',
        ], 401);
    }


}