<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CompanyDetails;
use App\Models\Blog;
use Illuminate\Support\Facades\Validator;
use App\Models\Employee;
use App\Models\Attendance;
use Illuminate\Support\Carbon;

class FrontendController extends Controller
{

    public function index()
    {
        $blogs = Blog::where('status', 1)->with('category')->select('title', 'created_at', 'slug', 'blog_category_id', 'image')->latest()->paginate(3);
        return view('frontend.index', compact('blogs'));
    }

    public function login()
    {
        if (Auth::check()) {
            if (auth()->user()->is_type == '1') {
                return redirect()->route('admin.dashboard');
            } elseif (auth()->user()->is_type == '2') {
                return redirect()->route('manager.dashboard');
            } elseif (auth()->user()->is_type == '0') {
                return redirect()->route('user.profile');
            }
        }
        return view('auth.login');
    }

    public function about()
    {
        $aboutUs = CompanyDetails::select('about_us')->first();
        return view('frontend.about', compact('aboutUs'));
    }

    public function showBlogDetails($slug)
    {
        $blog = Blog::with('category')->select('title', 'description', 'image', 'created_at', 'created_by', 'blog_category_id')->where('slug', $slug)->firstOrFail();
        $recentBlogs = Blog::select('title', 'created_at', 'slug')
                        ->where('slug', '!=', $slug)
                        ->where('status', 1)
                        ->latest()
                        ->take(5)
                        ->get();
        return view('frontend.blog_details', compact('blog', 'recentBlogs'));
    }

    // public function index()
    // {
    //     if (Auth::check()) {
    //         $user = auth()->user();

    //         if ($user->is_type == '1') {
    //             return redirect()->route('admin.dashboard');
    //         } elseif ($user->is_type == '2') {
    //             return redirect()->route('manager.dashboard');
    //         } else {
    //             return redirect()->route('user.dashboard');
    //         }
    //     } else {
    //         return redirect()->route('login');
    //     }
    // }

    public function logoutWithActivity(Request $request)
    {
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
                'message' => 'Details recorded successfully',
                'redirect' => route('login'),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid credentials. Please try again.',
        ], 401);
    }
}
