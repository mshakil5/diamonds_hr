<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CompanyDetails;
use App\Models\Blog;

class FrontendController extends Controller
{

    public function index()
    {
        $blogs = Blog::where('status', 1)->with('category')->select('title', 'created_at', 'slug', 'blog_category_id', 'image')->latest()->paginate(3);
        return view('frontend.index', compact('blogs'));
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
}
