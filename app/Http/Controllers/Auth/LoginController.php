<?php
  
namespace App\Http\Controllers\Auth;
  
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Carbon;
use App\Models\Employee;
use App\Models\Attendance;
  
class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */
  
    use AuthenticatesUsers;
  
    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;
  
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
    
    
    public function login2(Request $request)
    {   
        $input = $request->all();
     
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $chksts = User::where('email', $input['email'])->first();
        if ($chksts) {
            if ($chksts->status == 1) {


                if ($chksts->is_type == '1') {
                    
                    if(auth()->attempt(array('email' => $input['email'], 'password' => $input['password'])))
                    {

                        return redirect()->route('admin.dashboard');
                        
                        
                    }else{
                        return view('auth.login')
                            ->with('message','Wrong Password.');
                    }


                }else if ($chksts->is_type == '0') {

                    $employee = Employee::where('user_id', $chksts->id)->first();

                    if ($employee) {
                        Attendance::create([
                            'employee_id' => $employee->id,
                            'branch_id'   => $employee->branch_id,
                            'clock_in' => Carbon::now()->format('Y-m-d H:i'),
                            'type'        => 'Regular',
                        ]);
                    }

                    return redirect()->route('user.profile');
                    
                }else {
                    return view('auth.login')
                        ->with('message','You are not authenticate user.');
                }
                

            }else{
                return view('auth.login')
                ->with('message','Your ID is Deactive.');
            }
        }else {
            return view('auth.login')
                ->with('message','Credential Error. You are not authenticate user.');
        }
          
    }


    public function login3(Request $request)
    {
        $input = $request->all();

        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $input['email'])->first();
        if ($user && $user->is_type == '0' && $user->status == 1) {
            if (auth()->attempt(['email' => $input['email'], 'password' => $input['password']])) {
                $employee = Employee::where('user_id', $user->id)->first();
                if ($employee) {
                    // Check for existing clock-in
                    $existingAttendance = Attendance::where('employee_id', $employee->id)
                        ->whereDate('clock_in', Carbon::today())
                        ->first();
                    if ($existingAttendance) {
                        auth()->logout();
                        return view('auth.login')->with('message', 'You have already clocked in today.');
                    }

                    // Create attendance record
                    Attendance::create([
                        'employee_id' => $employee->id,
                        'branch_id' => $employee->branch_id,
                        'clock_in' => Carbon::now()->format('Y-m-d H:i'),
                        'type' => 'Regular',
                    ]);

                    auth()->logout();
                    return view('auth.login')->with('message', 'Attendance recorded successfully.');
                }
                auth()->logout();
                return view('auth.login')->with('message', 'Employee record not found.');
            }
            return view('auth.login')->with('message', 'Wrong Password.');
        }elseif ($user && $user->is_type == '1' && $user->status == 1) {
            if(auth()->attempt(array('email' => $input['email'], 'password' => $input['password'])))
                {
                    return redirect()->route('admin.dashboard');
                    
                }else{
                    return view('auth.login')
                        ->with('message','Wrong Password.');
                }
        }
        return view('auth.login')->with('message', 'Credential Error or not an employee.');
    }

    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->login)
            ->orWhere('username', $request->login)
            ->first();

        if ($user && $user->is_type == '0' && $user->status == 1) {
            if (auth()->attempt(['email' => $user->email, 'password' => $request->password])) {
                $employee = Employee::where('user_id', $user->id)->first();
                if (!$employee) {
                    auth()->logout();
                    return back()->with('message', 'Employee record not found.');
                }

                $alreadyClockedIn = Attendance::where('employee_id', $employee->id)
                    ->whereDate('clock_in', now())->exists();

                if ($alreadyClockedIn) {
                    auth()->logout();
                    return back()->with('message', 'Already clocked in today.');
                }

                Attendance::create([
                    'employee_id' => $employee->id,
                    'branch_id' => $employee->branch_id,
                    'clock_in' => now(),
                    'type' => 'Regular',
                ]);

                auth()->logout();
                return back()->with('message', 'Attendance recorded successfully.');
            }

            return back()->with('message', 'Wrong Password.');
        }

        return back()->with('message', 'Invalid employee credentials.');
    }

}