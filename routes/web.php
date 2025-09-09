<?php
  
use Illuminate\Support\Facades\Route;
  
use App\Http\Controllers\HomeController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\User\ProfileController;
use Illuminate\Http\Request;
  

// cache clear
Route::get('/clear', function() {
    Auth::logout();
    session()->flush();
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('config:cache');
    Artisan::call('view:clear');
    return "Cleared!";
 });
//  cache clear
  

// note
Route::get('/_debug-myip', function (Request $request) {
    return response()->json([
        'request_ip'     => $request->ip(),
        'request_ips'    => $request->ips(),   
        'xff_header'     => $request->header('X-Forwarded-For'),
        'remote_addr'    => $_SERVER['REMOTE_ADDR'] ?? null,
        'all_headers'    => $request->headers->all(),
    ]);
});

//note
  
Auth::routes();

Route::get('/', [FrontendController::class, 'login'])->name('homepage')->middleware('ip.whitelist');
Route::get('/home', [FrontendController::class, 'login'])->name('home');
Route::get('about-us', [FrontendController::class, 'about'])->name('about');
Route::get('/blog/{slug}', [FrontendController::class, 'showBlogDetails'])->name('blog.details');

Route::post('/clear-session', [HomeController::class, 'clearSession'])->name('clearSession');
Route::post('/logout-with-activity', [FrontendController::class, 'logoutWithActivity'])->name('logout.with.activity');

Route::get('/login/admin', [FrontendController::class, 'showAdminLogin'])->name('login.admin')->middleware('ip.whitelist');
Route::post('/login/admin', [FrontendController::class, 'adminLogin'])->name('login.admin')->middleware('ip.whitelist');


Route::group(['prefix' =>'user/', 'middleware' => ['auth', 'is_user','ip.whitelist']], function(){
  
    Route::get('/dashboard', [HomeController::class, 'userHome'])->name('user.dashboard');
    Route::get('/profile', [ProfileController::class, 'profile'])->name('user.profile');
    Route::post('/profile', [ProfileController::class, 'profileUpdate'])->name('user.profileUpdate');
});
  

Route::group(['prefix' =>'manager/', 'middleware' => ['auth', 'is_manager','ip.whitelist']], function(){
  
    Route::get('/dashboard', [HomeController::class, 'managerHome'])->name('manager.dashboard');
});
 