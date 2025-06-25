<?php
  
namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Blog;
use App\Models\Holiday;
use App\Models\User;
use Carbon\Carbon;

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
}