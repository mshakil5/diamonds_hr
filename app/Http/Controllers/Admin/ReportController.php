<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Branch;
use App\Models\Employee;
use App\Models\Holiday;
use App\Models\Product;
use App\Models\Stockmaintaince;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PDF;
use App\Models\AssetType;
use App\Models\EmployeePreRota;
use App\Models\StockAssetType;

class ReportController extends Controller
{
    public function employeeReport(Request $request)
    {
        if ($request->isMethod('post')) {
            
        // Format date range
        $fromDate = Carbon::parse($request->from_date)->startOfDay();
        $toDate = Carbon::parse($request->to_date)->endOfDay();

        

        $data = DB::table('attendances')
            ->select('attendances.*', 'employees.name as employee_name')
            ->join('employees', 'attendances.employee_id', '=', 'employees.id')
            ->where('attendances.employee_id', $request->input('employee_id'))
            ->where('attendances.branch_id', Auth::user()->branch_id)
            ->whereBetween('attendances.clock_in', [$fromDate, $toDate])
            ->get();

            // dd($data);
        
        $employeeName = Employee::where('id', $request->input('employee_id'))->where('branch_id', Auth::user()->branch_id)->value('name');
        $employees = Employee::where('is_active', 1)->where('branch_id', Auth::user()->branch_id)->get();
        return view('admin.reports.employeeReport', compact('employees','data','employeeName'));

        } else {
            $employees = Employee::where('is_active', 1)->where('branch_id', Auth::user()->branch_id)->get();
            $data = [];
            $employeeName = null;
            return view('admin.reports.employeeReport', compact('employees','data','employeeName'));
        }
        
    }


    public function holidayReport(Request $request)
    {
        if ($request->isMethod('post')) {
            
            $employeeId=request()->input('employee_id');
            $contractDateBegin = date('Y') . '-04-01';
            $contractDateEnd = date('Y', strtotime('+1 year')) . '-03-31';

            $employee = Employee::find($employeeId);
            $holidayData = EmployeePreRota::with('employee')
                ->whereEmployeeId($employeeId)
                ->where('status', 3)
                ->get();
            $counts = $employee->leave_status_counts;

            
            $holidayDataCount = EmployeePreRota::where('employee_id', $employeeId)
                ->whereBetween('date', [$contractDateBegin, $contractDateEnd])
                ->where('status', '3')
                ->where('branch_id', $employee->branch_id)
                ->count();
            $sickDays = Attendance::whereEmployeeId($employeeId)
                ->whereBetween('clock_in',[$contractDateBegin,Carbon::today()])
                ->where('type','Sick')
                ->where('branch_id', $employee->branch_id)
                ->count();
            $absenceDays=Attendance::whereEmployeeId($employeeId)
                ->whereBetween('clock_in',[$contractDateBegin,Carbon::today()])
                ->where('type','Absence')
                ->where('branch_id', $employee->branch_id)
                ->count();



            $employeeName = Employee::where('id', $request->input('employee_id'))->where('branch_id', Auth::user()->branch_id)->first();
            
            $employees = Employee::where('is_active', 1)->get();
            return view('admin.reports.holidayReport', compact('employees','employeeName','holidayData','holidayDataCount','sickDays','absenceDays','employee','counts'));

        } else {

            $employees = Employee::where('is_active', 1)->get();
            $employeeName = null;
            return view('admin.reports.holidayReport', compact('employees','employeeName'));

        }
        
    }


    public function stockReport(Request $request)
    {
        $query = DB::table('products as p')
            ->leftJoin('stockmaintainces as sm', 'sm.product_id', '=', 'p.id')
            ->select(
                'p.name',
                'p.id',
                DB::raw("SUM(CASE WHEN sm.cloth_type='Initial Stock' THEN sm.quantity ELSE 0 END) as initial_stock"),
                DB::raw("SUM(CASE WHEN sm.cloth_type='Dirty' THEN sm.quantity ELSE 0 END) as dirty"),
                DB::raw("SUM(CASE WHEN sm.cloth_type='Bed' THEN sm.quantity ELSE 0 END) as bed"),
                DB::raw("SUM(CASE WHEN sm.cloth_type='Arrived' THEN sm.quantity ELSE 0 END) as arrived"),
                DB::raw("SUM(CASE WHEN sm.cloth_type='Lost/Missed' THEN sm.quantity ELSE 0 END) as lost"),
                DB::raw("SUM(sm.marks) as marks")
            )
            ->groupBy('p.id', 'p.name')
            ->where('sm.branch_id', Auth::user()->branch_id);

        if ($request->isMethod('post') && $request->has(['from_date', 'to_date'])) {
            $fromDate = $request->input('from_date');
            $toDate = $request->input('to_date');

            if ($fromDate && $toDate) {
                $query->whereBetween('sm.created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']);
            }
        }

        $products = $query->get();

        return view('admin.reports.stockReport', compact('products'));
    }

    public function stockStaffReport(Request $request){

        $query = DB::table('users as u')
            ->leftJoin('stockmaintainces as sm', 'sm.user_id', '=', 'u.id')
            ->leftJoin('products as p', 'p.id', '=', 'sm.product_id')
            ->select(
                'u.id',
                'u.name',
                'p.name as product_name',
                'p.id as product_id',
                DB::raw("SUM(CASE WHEN sm.cloth_type = 'Initial Stock' THEN sm.quantity ELSE NULL END) as initial_stock"),
                DB::raw("SUM(CASE WHEN sm.cloth_type = 'Dirty' THEN sm.quantity ELSE NULL END) as dirty"),
                DB::raw("SUM(CASE WHEN sm.cloth_type = 'Bed' THEN sm.quantity ELSE NULL END) as bed"),
                DB::raw("SUM(CASE WHEN sm.cloth_type = 'Arrived' THEN sm.quantity ELSE NULL END) as arrived"),
                DB::raw("SUM(CASE WHEN sm.cloth_type = 'Lost/Missed' THEN sm.quantity ELSE NULL END) as lost"),
                DB::raw("SUM(sm.marks) as marks")
            )
            ->groupBy('u.id', 'u.name', 'p.id', 'p.name') // Add all non-aggregated columns
            ->where('u.branch_id', Auth::user()->branch_id)
            ->get();
            
            return view('admin.reports.staffStockReport', compact('query'));
    }




    // public function dirtyStockReport(Request $request)
    // {
    //     // Initialize variables
    //     $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::today()->endOfDay();
    //     $startDate = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : $endDate->copy()->subDays(9)->startOfDay();

    //     // Validate date range
    //     $request->validate([
    //         'start_date' => 'nullable|date',
    //         'end_date' => 'nullable|date|after_or_equal:start_date',
    //     ]);

    //     // Ensure the date range is exactly 10 days
    //     if ($endDate->diffInDays($startDate) > 9) {
    //         $startDate = $endDate->copy()->subDays(9)->startOfDay();
    //     }

    //     // Get branch name
    //     $branch = Branch::find(Auth::user()->branch_id);
    //     $branchName = $branch ? $branch->name : 'Unknown Branch';

    //     // Get all products with names
    //     $products = Stockmaintaince::with('product')
    //         ->select('product_id')
    //         ->distinct()
    //         ->where('branch_id', Auth::user()->branch_id)
    //         ->where('cloth_type', 'Dirty')
    //         ->get()
    //         ->pluck('product');

    //     // Initialize report data
    //     $reportData = [
    //         'days' => [],
    //         'total_sum' => 0,
    //     ];

    //     // Create a period for the last 10 days
    //     $period = CarbonPeriod::create($startDate, '1 day', $endDate);

    //     // Initialize days array
    //     $days = [];
    //     foreach ($period as $date) {
    //         $dateStr = $date->format('Y-m-d');
    //         $days[$dateStr] = [
    //             'date' => $date->copy(),
    //             'quantities' => [],
    //             'total' => 0,
    //         ];
    //     }

    //     // Get stock data for the date range
    //     $stockData = Stockmaintaince::where('branch_id', Auth::user()->branch_id)
    //         ->where('cloth_type', 'Dirty')
    //         ->whereBetween('date', [$startDate, $endDate])
    //         ->selectRaw('product_id, DATE(date) as stock_date, SUM(quantity) as total_quantity')
    //         ->groupBy('product_id', 'stock_date')
    //         ->get();

    //     // Process stock data
    //     $productTotals = [];
    //     foreach ($stockData as $stock) {
    //         $stockDate = Carbon::parse($stock->stock_date)->format('Y-m-d');
    //         if (isset($days[$stockDate])) {
    //             $quantity = (int) $stock->total_quantity; // Cast to integer to avoid type errors
    //             $days[$stockDate]['quantities'][$stock->product_id] = $quantity;
    //             $days[$stockDate]['total'] += $quantity;
    //             $reportData['total_sum'] += $quantity;

    //             // Track total per product
    //             if (!isset($productTotals[$stock->product_id])) {
    //                 $productTotals[$stock->product_id] = 0;
    //             }
    //             $productTotals[$stock->product_id] += $quantity;
    //         }
    //     }

    //     $reportData['days'] = $days;
    //     $reportData['product_totals'] = $productTotals;

    //     // Handle PDF download
    //     if ($request->has('download')) {
    //         $pdf = PDF::loadView('admin.reports.dirtyStockReportPdf', compact(
    //             'reportData',
    //             'products',
    //             'startDate',
    //             'endDate',
    //             'branchName'
    //         ));
    //         return $pdf->download('Dirty_Stock_Report_' . $branchName . '_' . $startDate->format('Y-m-d') . '_to_' . $endDate->format('Y-m-d') . '.pdf');
    //     }

    //     return view('admin.reports.dirtyStockReport', compact(
    //         'reportData',
    //         'products',
    //         'startDate',
    //         'endDate',
    //         'branchName'
    //     ));
    // }

    public function dirtyStockReport(Request $request)
    {
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::today()->endOfDay();
        $startDate = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : $endDate->copy()->subDays(9)->startOfDay();

        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        if ($endDate->diffInDays($startDate) > 9) {
            $startDate = $endDate->copy()->subDays(9)->startOfDay();
        }

        $branch = Branch::find(Auth::user()->branch_id);
        $branchName = $branch ? $branch->name : 'Unknown Branch';

        $types = ['Dirty', 'Rejected'];
        $reports = [];

        foreach ($types as $type) {
            $products = Stockmaintaince::with('product')
                ->select('product_id')
                ->distinct()
                ->where('branch_id', Auth::user()->branch_id)
                ->where('cloth_type', $type)
                ->whereBetween('date', [$startDate, $endDate])
                ->get()
                ->pluck('product');

            $period = CarbonPeriod::create($startDate, '1 day', $endDate);
            $days = [];
            foreach ($period as $date) {
                $key = $date->format('Y-m-d');
                $days[$key] = [
                    'date' => $date->copy(),
                    'quantities' => [],
                    'total' => 0,
                ];
            }

            $stockData = Stockmaintaince::where('branch_id', Auth::user()->branch_id)
                ->where('cloth_type', $type)
                ->whereBetween('date', [$startDate, $endDate])
                ->selectRaw('product_id, DATE(date) as stock_date, SUM(quantity) as total_quantity')
                ->groupBy('product_id', 'stock_date')
                ->get();

            $productTotals = [];
            $totalSum = 0;

            foreach ($stockData as $stock) {
                $dateKey = Carbon::parse($stock->stock_date)->format('Y-m-d');
                if (isset($days[$dateKey])) {
                    $qty = (int) $stock->total_quantity;
                    $days[$dateKey]['quantities'][$stock->product_id] = $qty;
                    $days[$dateKey]['total'] += $qty;
                    $totalSum += $qty;

                    if (!isset($productTotals[$stock->product_id])) {
                        $productTotals[$stock->product_id] = 0;
                    }
                    $productTotals[$stock->product_id] += $qty;
                }
            }

            $reports[$type] = [
                'products' => $products,
                'days' => $days,
                'product_totals' => $productTotals,
                'total_sum' => $totalSum,
            ];
        }

        if ($request->has('download')) {
            $pdf = PDF::loadView('admin.reports.dirtyStockReportPdf', compact(
                'reports', 'startDate', 'endDate', 'branchName'
            ));
            return $pdf->download('Dirty_Rejected_Stock_Report_' . $branchName . '_' . $startDate->format('Y-m-d') . '_to_' . $endDate->format('Y-m-d') . '.pdf');
        }

        return view('admin.reports.dirtyStockReport', compact(
            'reports', 'startDate', 'endDate', 'branchName'
        ));
    }

    public function assetStockReport(Request $request)
    {
        $assetTypes = AssetType::where('status', 1)->get();
        $branches = Branch::where('status', 1)->get();
        $statuses = [
            1 => 'Assigned',
            2 => 'In Storage',
            3 => 'Under Repair',
            4 => 'Damaged',
        ];

        $query = StockAssetType::with(['stock', 'assetType', 'branch', 'location.flooor', 'maintenance'])
            ->whereHas('stock', function ($q) {
                $q->whereNotNull('id');
            });

        if ($request->from_date && $request->to_date) {
            $query->whereHas('stock', function ($q) use ($request) {
                $q->whereBetween('date', [$request->from_date, $request->to_date]);
            });
        }

        if ($request->asset_type_id) {
            $query->where('asset_type_id', $request->asset_type_id);
        }

        if ($request->status !== null) {
            $query->where('asset_status', $request->status);
        }

        if ($request->branch_id) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->product_code) {
            $query->where('product_code', 'like', '%' . $request->product_code . '%');
        }

        $results = $query->get();
        // dd($results);

        return view('admin.reports.asset_stock_report', compact('results', 'assetTypes', 'branches', 'statuses', 'request'));
    }

    public function weeklyPrerotaReport(Request $request)
    {
        
        $currentDate = Carbon::today();
        $currentWeekStart = $currentDate->copy()->startOfWeek(Carbon::MONDAY); 
        $currentWeekEnd = $currentDate->copy()->endOfWeek(Carbon::SUNDAY);

        $nextWeekStart = $currentWeekStart->copy()->addWeek(); 
        $nextWeekEnd = $currentWeekEnd->copy()->addWeek();

        $currentWeekPreRota = EmployeePreRota::whereBetween('date', [$currentWeekStart->format('Y-m-d'), $currentWeekEnd->format('Y-m-d')])
            ->get();

        $nextWeekPreRota = EmployeePreRota::whereBetween('date', [$nextWeekStart->format('Y-m-d'), $nextWeekEnd->format('Y-m-d')])
            ->get();
        return view('admin.reports.weeklyPrerotaReport', compact('currentWeekPreRota'));
       
        
    }

    public function nextweekPrerotaReport(Request $request)
    {
        
        $currentDate = Carbon::today();
        $currentWeekStart = $currentDate->copy()->startOfWeek(Carbon::MONDAY); 
        $currentWeekEnd = $currentDate->copy()->endOfWeek(Carbon::SUNDAY);

        $nextWeekStart = $currentWeekStart->copy()->addWeek(); 
        $nextWeekEnd = $currentWeekEnd->copy()->addWeek();

        $currentWeekPreRota = EmployeePreRota::whereBetween('date', [$nextWeekStart->format('Y-m-d'), $nextWeekEnd->format('Y-m-d')])
            ->get();
        return view('admin.reports.weeklyPrerotaReport', compact('currentWeekPreRota'));
       
        
    }

}
