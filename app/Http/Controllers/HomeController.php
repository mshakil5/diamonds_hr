<?php
  
namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Blog;
use App\Models\User;
  
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
        return view('admin.dashboard', compact('blogsCount', 'usersCount'));
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