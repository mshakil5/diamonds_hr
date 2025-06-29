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

        $todaySick = Attendance::whereDate('clock_in', Carbon::today())->whereType('Sick')->count();
        $todayAbsence = Attendance::whereDate('clock_in', Carbon::today())->whereType('Absence')->count();
        $totalHours = Attendance::whereDate('clock_in', Carbon::today())->count();
        $todayAttendance = Attendance::whereDate('clock_in', Carbon::today())->get();


        return view('admin.dashboard', compact('monthlyHoliday', 'todaySick','todayAbsence','todayAttendance','totalHours'));
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