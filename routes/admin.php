<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AssetController;
use App\Http\Controllers\Admin\AssetTypeController;
use App\Http\Controllers\Admin\CompanyDetailsController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\BlogCategoryController;
use App\Http\Controllers\Admin\BlogController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\HolidayController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\StockController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\ProrotaController;
use App\Http\Controllers\Admin\AssetStockController;
use App\Http\Controllers\Admin\MaintenanceController;
use App\Http\Controllers\Admin\FloorController;

Route::group(['prefix' =>'admin/', 'middleware' => ['auth']], function(){
  
    Route::get('/dashboard', [HomeController::class, 'adminHome'])->name('admin.dashboard');

    //Admin crud
    Route::get('/new-admin', [AdminController::class, 'getAdmin'])->name('alladmin');
    Route::post('/new-admin', [AdminController::class, 'adminStore']);
    Route::get('/new-admin/{id}/edit', [AdminController::class, 'adminEdit']);
    Route::post('/new-admin-update', [AdminController::class, 'adminUpdate']);
    Route::get('/new-admin/{id}', [AdminController::class, 'adminDelete']);

    //User crud
    Route::get('/users', [UserController::class, 'index'])->name('allUsers');
    Route::post('/users', [UserController::class, 'store']);
    Route::get('/users/{id}/edit', [UserController::class, 'edit']);
    Route::post('/users-update', [UserController::class, 'update']);
    Route::get('/users/{id}', [UserController::class, 'delete']);
    Route::post('/users/{id}/status', [UserController::class, 'updateStatus'])->name('users.updateStatus');

    // company information
    Route::get('/company-details', [CompanyDetailsController::class, 'index'])->name('admin.companyDetail');
    Route::post('/company-details', [CompanyDetailsController::class, 'update'])->name('admin.companyDetails');

    // Blog Categories Routes
    Route::get('/blog-categories', [BlogCategoryController::class, 'index'])->name('allBlogCategories');
    Route::post('/blog-categories', [BlogCategoryController::class, 'store']);
    Route::get('/blog-categories/{id}/edit', [BlogCategoryController::class, 'edit']);
    Route::post('/blog-categories-update', [BlogCategoryController::class, 'update']);
    Route::get('/blog-categories/{id}', [BlogCategoryController::class, 'delete']);
    Route::post('/blog-categories/{id}/status', [BlogCategoryController::class, 'updateStatus'])->name('blogCategories.updateStatus');

    Route::get('/blogs', [BlogController::class, 'index'])->name('allBlogs');
    Route::post('/blogs', [BlogController::class, 'store']);
    Route::get('/blogs/{id}/edit', [BlogController::class, 'edit']);
    Route::post('/blogs-update', [BlogController::class, 'update']);
    Route::get('/blogs/{id}', [BlogController::class, 'delete']);
    Route::post('/blogs/{id}/status', [BlogController::class, 'updateStatus'])->name('blogs.updateStatus');

    // Employee Routes
    Route::prefix('employees')->group(function () {
        Route::get('/', [EmployeeController::class, 'index'])->name('employees.index');
        Route::post('/', [EmployeeController::class, 'store'])->name('employees.store');
        Route::get('/{id}/edit', [EmployeeController::class, 'edit'])->name('employees.edit');
        Route::post('/update', [EmployeeController::class, 'update'])->name('employees.update');
        Route::get('/{id}', [EmployeeController::class, 'delete'])->name('employees.delete');
        Route::post('/change-status', [EmployeeController::class, 'updateStatus'])->name('employees.updateStatus');

        Route::post('/payslip', [EmployeeController::class, 'payslip'])->name('employees.payslip');

        

    });

    Route::get('/pre-rota', [EmployeeController::class, 'checkProrota'])->name('admin.employee.prorota');

    Route::prefix('branches')->group(function () {
        Route::get('/', [BranchController::class, 'index'])->name('branches.index');
        Route::post('/', [BranchController::class, 'store'])->name('branches.store');
        Route::get('/{id}/edit', [BranchController::class, 'edit'])->name('branches.edit');
        Route::post('/update', [BranchController::class, 'update'])->name('branches.update');
        Route::get('/{id}', [BranchController::class, 'delete'])->name('branches.delete');
        Route::post('/change-status', [BranchController::class, 'updateStatus'])->name('branches.updateStatus');
    });

    Route::prefix('holidays')->group(function () {
        Route::get('/', [HolidayController::class, 'index'])->name('holidays.index');
        Route::post('/', [HolidayController::class, 'store'])->name('holidays.store');
        Route::get('/{id}/edit', [HolidayController::class, 'edit'])->name('holidays.edit');
        Route::post('/update', [HolidayController::class, 'update'])->name('holidays.update');
        Route::get('/{id}', [HolidayController::class, 'delete'])->name('holidays.delete');
    });
    Route::post('/holidays/check', [HolidayController::class, 'checkHolidays'])->name('admin.holiday.check');

    Route::prefix('attendance')->group(function () {
        Route::get('/', [AttendanceController::class, 'index'])->name('attendance.index');
        Route::post('/', [AttendanceController::class, 'index'])->name('attendance.search');
        Route::post('/store', [AttendanceController::class, 'store'])->name('attendance.store');
        Route::get('/{id}/edit', [AttendanceController::class, 'edit'])->name('attendance.edit');
        Route::post('/update', [AttendanceController::class, 'update'])->name('attendance.update');
        Route::delete('/{id}', [AttendanceController::class, 'destroy'])->name('attendance.destroy');
        Route::get('/export', [AttendanceController::class, 'export'])->name('attendance.export');
    });


    Route::prefix('products')->group(function () {
        Route::get('/', [ProductController::class, 'index'])->name('products.index');
        Route::post('/', [ProductController::class, 'store'])->name('products.store');
        Route::get('/{id}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::post('/update', [ProductController::class, 'update'])->name('products.update');
        Route::get('/{id}', [ProductController::class, 'delete'])->name('products.delete');
        Route::post('/change-status', [ProductController::class, 'updateStatus'])->name('products.updateStatus');
    });

    Route::prefix('stocks')->group(function () {
        Route::get('/', [StockController::class, 'index'])->name('stocks.index');
        Route::post('/', [StockController::class, 'store'])->name('stocks.store');
        Route::get('/{id}/edit', [StockController::class, 'edit'])->name('stocks.edit');
        Route::post('/update', [StockController::class, 'update'])->name('stocks.update');
        Route::get('/{id}', [StockController::class, 'delete'])->name('stocks.delete');
        Route::post('/change-status', [StockController::class, 'updateStatus'])->name('stocks.updateStatus');

        
    });

    Route::get('/recover-data', [StockController::class, 'addUser']);
    
    // prorota make
    Route::get('/prorota', [ProrotaController::class, 'index'])->name('prorota');
    Route::get('/prorota-list', [ProrotaController::class, 'getprorota'])->name('get.prorota');
    Route::get('/create-prorota', [ProrotaController::class, 'create'])->name('prorota.create');
    Route::post('/prorota', [ProrotaController::class, 'store']);
    Route::post('/prorota/update', [ProrotaController::class, 'update'])->name('prorota.update');
    Route::get('/prorota/details/{id}', [ProrotaController::class,'showDetails'])->name('prorota.details');
    Route::get('/delete-prorota/{id}', [ProrotaController::class, 'destroy'])->name('delete.prorota');
    Route::get('/prorota/{id}/edit', [ProrotaController::class,'edit'])->name('prorota.edit');
    Route::get('/prorota-log/{id}', [ProrotaController::class, 'prorotaLog'])->name('prorota.log');
    Route::get('/prorota/download-pdf', [ProrotaController::class, 'downloadPdf'])->name('prorota.download-pdf');

    

    // employee report
    Route::get('/report/employee', [ReportController::class, 'employeeReport'])->name('employeeReport');
    Route::post('/report/employee', [ReportController::class, 'employeeReport'])->name('employeeReport.search');

    // holiday report
    Route::get('/report/holiday', [ReportController::class, 'holidayReport'])->name('holidayReport');
    Route::post('/report/holiday', [ReportController::class, 'holidayReport'])->name('holidayReport.search');

    // stock report
    Route::get('/report/stock', [ReportController::class, 'stockReport'])->name('stockReport');
    Route::post('/report/stock', [ReportController::class, 'stockReport'])->name('stockReport.search');

    // stock-staff
    Route::get('/report/stock-staff', [ReportController::class, 'stockStaffReport'])->name('stockStaffReport');
    Route::post('/report/stock-staff', [ReportController::class, 'stockStaffReport'])->name('stockStaffReport.search');

    Route::get('/report/dirty-stock', [ReportController::class, 'dirtyStockReport'])->name('dirtyStockReport');
    Route::post('/report/dirty-stock', [ReportController::class, 'dirtyStockReport'])->name('dirtyStockReport.search');

    Route::get('/report/asset-stock', [ReportController::class, 'assetStockReport'])->name('assetStockReport');
    Route::post('/report/asset-stock', [ReportController::class, 'assetStockReport'])->name('assetStockReport.search');

    // roles and permission
    Route::get('settings/role', [RoleController::class, 'index'])->name('admin.role');
    Route::post('role', [RoleController::class, 'store'])->name('admin.rolestore');
    Route::get('settings/role/{id}', [RoleController::class, 'edit'])->name('admin.roleedit');
    Route::post('role-update', [RoleController::class, 'update'])->name('admin.roleupdate');

    
    Route::get('settings/change-branch', [SettingsController::class, 'changeBranch'])->name('changeBranch');
    Route::post('change-branches', [SettingsController::class, 'branchChange']);

    // Asset Types crud
    Route::get('/asset-type', [AssetTypeController::class, 'index'])->name('assetTypes');
    Route::post('/asset-type', [AssetTypeController::class, 'store']);
    Route::get('/asset-type/{id}/edit', [AssetTypeController::class, 'edit']);
    Route::post('/asset-type-update', [AssetTypeController::class, 'update']);
    Route::get('/asset-type/{id}', [AssetTypeController::class, 'delete']);
    Route::post('/asset-type/update-status', [AssetTypeController::class, 'updateStatus'])->name('assetTypes.updateStatus');

    // Locations crud
    Route::get('/location', [LocationController::class, 'index'])->name('locations');
    Route::post('/location', [LocationController::class, 'store']);
    Route::get('/location/{id}/edit', [LocationController::class, 'edit']);
    Route::post('/location-update', [LocationController::class, 'update']);
    Route::get('/location/{id}', [LocationController::class, 'delete']);
    Route::post('/location/update-status', [LocationController::class, 'updateStatus'])->name('locations.updateStatus');

    Route::get('/maintenance', [MaintenanceController::class, 'index'])->name('maintenance.index');
    Route::post('/maintenance', [MaintenanceController::class, 'store']);
    Route::get('/maintenance/{id}/edit', [MaintenanceController::class, 'edit']);
    Route::post('/maintenance-update', [MaintenanceController::class, 'update']);
    Route::get('/maintenance/{id}', [MaintenanceController::class, 'delete']);
    Route::post('/maintenance/status', [MaintenanceController::class, 'updateStatus'])->name('maintenance.status');


    // Stock crud
    Route::get('/stock', [AssetStockController::class, 'index'])->name('stock');
    Route::post('/stock', [AssetStockController::class, 'store']);
    Route::get('/stock/{id}/edit', [AssetStockController::class, 'edit']);
    Route::post('/stock-update', [AssetStockController::class, 'update']);
    Route::get('/stock/{id}', [AssetStockController::class, 'delete']);

    Route::get('/stock-status/{stock?}/{status}', [AssetStockController::class, 'viewByStatus'])->name('stock.view.status');

    Route::get('/get-locations/{branchId}/{floorId}', [LocationController::class, 'getLocationsByBranchAndFloor']);

    Route::get('/get-latest-code/{assetTypeId}', [AssetStockController::class, 'getLatestCode']);

  // Assets crud
  Route::get('/asset', [AssetController::class, 'index'])->name('assets');
  Route::post('/asset', [AssetController::class, 'store']);
  Route::get('/asset/{id}/edit', [AssetController::class, 'edit']);
  Route::post('/asset-update', [AssetController::class, 'update']);
  Route::get('/asset/{id}', [AssetController::class, 'delete']);
  Route::post('/asset/update-status', [AssetController::class, 'updateStatus'])->name('assets.updateStatus');

    // Floors crud
  Route::get('/floor', [FloorController::class, 'index'])->name('floors');
  Route::post('/floor', [FloorController::class, 'store']);
  Route::get('/floor/{id}/edit', [FloorController::class, 'edit']);
  Route::post('/floor-update', [FloorController::class, 'update']);
  Route::get('/floor/{id}', [FloorController::class, 'delete']);
  Route::post('/floor/update-status', [FloorController::class, 'updateStatus'])->name('floors.updateStatus');

  Route::get('/stock/status/codes/{stock}/{status}', [StockController::class, 'printCodes'])->name('stock.codes.print');

  Route::get('/faulty-products', [StockController::class, 'faultyProducts'])->name('faultyProducts');
  Route::post('/update-faulty-status', [StockController::class, 'updateFaultyStatus'])->name('admin.updateFaultyStatus');
});
  