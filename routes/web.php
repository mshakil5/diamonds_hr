<?php
  
use Illuminate\Support\Facades\Route;
  
use App\Http\Controllers\HomeController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\User\ProfileController;
  

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
  
  
Auth::routes();

// Route::fallback(function () {
//     return redirect('/');
// });
  
Route::get('/', [FrontendController::class, 'login'])->name('homepage');
Route::get('/home', [HomeController::class, 'login'])->name('home');
Route::get('about-us', [FrontendController::class, 'about'])->name('about');
Route::get('/blog/{slug}', [FrontendController::class, 'showBlogDetails'])->name('blog.details');



Route::group(['prefix' =>'user/', 'middleware' => ['auth', 'is_user']], function(){
  
    Route::get('/dashboard', [HomeController::class, 'userHome'])->name('user.dashboard');
    Route::get('/profile', [ProfileController::class, 'profile'])->name('user.profile');
    Route::post('/profile', [ProfileController::class, 'profileUpdate'])->name('user.profileUpdate');
});
  

Route::group(['prefix' =>'manager/', 'middleware' => ['auth', 'is_manager']], function(){
  
    Route::get('/dashboard', [HomeController::class, 'managerHome'])->name('manager.dashboard');
});
 