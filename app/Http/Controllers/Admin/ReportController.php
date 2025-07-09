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

            $employee= Employee::find($employeeId);
            $holidayData=Holiday::with('employee')
                ->whereEmployeeId($employeeId)
                ->whereBetween('date',[$contractDateBegin,Carbon::today()])
                ->where('type','Authorized holiday')
                ->get();
            $holidayDataCount=Holiday::whereEmployeeId($employeeId)
                ->whereBetween('date',[$contractDateBegin,$contractDateEnd])
                ->where('type','Authorized holiday')
                ->count();
            $sickDays = Attendance::whereEmployeeId($employeeId)
                ->whereBetween('clock_in',[$contractDateBegin,Carbon::today()])
                ->where('type','Sick')
                ->count();
            $absenceDays=Attendance::whereEmployeeId($employeeId)
                ->whereBetween('clock_in',[$contractDateBegin,Carbon::today()])
                ->where('type','Absence')
                ->count();



            $employeeName = Employee::where('id', $request->input('employee_id'))->where('branch_id', Auth::user()->branch_id)->first();
            // dd($holidayData);
            $employees = Employee::where('is_active', 1)->get();
            return view('admin.reports.holidayReport', compact('employees','employeeName','holidayData','holidayDataCount','sickDays','absenceDays','employee'));

        } else {

            $employees = Employee::where('is_active', 1)->get();
            $employeeName = null;
            return view('admin.reports.holidayReport', compact('employees','employeeName'));

        }
        
    }


    public function stockReport(Request $request)
    {
        if ($request->isMethod('post')) {
            
            $products = DB::table('products as p')
                ->leftJoin('stockmaintainces as sm', 'sm.product_id', '=', 'p.id')
                ->select(
                    'p.name',
                    'p.id',
                    DB::raw("sum(CASE when sm.cloth_type='Initial Stock' THEN sm.quantity ELSE NULL END) as initial_stock"),
                    DB::raw("sum(CASE when sm.cloth_type='Dirty' THEN sm.quantity ELSE NULL END) as dirty"),
                    DB::raw("sum(CASE when sm.cloth_type='Bed' THEN sm.quantity ELSE NULL END) as bed"),
                    DB::raw("sum(CASE when sm.cloth_type='Arrived' THEN sm.quantity ELSE NULL END) as arrived"),
                    DB::raw("sum(CASE when sm.cloth_type='Lost/Missed' THEN sm.quantity ELSE NULL END) as lost"),
                    DB::raw("sum(sm.marks) as marks")
                )
                ->groupBy('p.id', 'p.name')  // ✅ add p.name here
                ->where('sm.branch_id', Auth::user()->branch_id)
                ->get();



            return view('admin.reports.stockReport', compact('products'));

        } else {


            $products = DB::table('products as p')
                ->leftJoin('stockmaintainces as sm', 'sm.product_id', '=', 'p.id')
                ->select(
                    'p.name',
                    'p.id',
                    DB::raw("sum(CASE when sm.cloth_type='Initial Stock' THEN sm.quantity ELSE NULL END) as initial_stock"),
                    DB::raw("sum(CASE when sm.cloth_type='Dirty' THEN sm.quantity ELSE NULL END) as dirty"),
                    DB::raw("sum(CASE when sm.cloth_type='Bed' THEN sm.quantity ELSE NULL END) as bed"),
                    DB::raw("sum(CASE when sm.cloth_type='Arrived' THEN sm.quantity ELSE NULL END) as arrived"),
                    DB::raw("sum(CASE when sm.cloth_type='Lost/Missed' THEN sm.quantity ELSE NULL END) as lost"),
                    DB::raw("sum(sm.marks) as marks")
                )
                ->groupBy('p.id', 'p.name')  // ✅ add p.name here
                ->where('sm.branch_id', Auth::user()->branch_id)
                ->get();

            
            return view('admin.reports.stockReport', compact('products'));

        }
        
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


    // public function dirtyStockReport(Request $request){

    //     $data = Stockmaintaince::where('branch_id', Auth::user()->branch_id)->where('cloth_type', 'Dirty')->get();

    //     dd($data);
        
    //     return view('admin.reports.dirtyStockReport', compact('data'));
    // }

    public function dirtyStockReport(Request $request)
    {
        // Initialize variables
        $startDate = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : Carbon::today()->startOfWeek(Carbon::TUESDAY);
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::today()->endOfWeek(Carbon::MONDAY);
        
        // Validate date range
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        // Get branch name
        $branch = Branch::find(Auth::user()->branch_id);
        $branchName = $branch ? $branch->name : 'Unknown Branch';

        // Get all products with names for grouping
        $products = Stockmaintaince::with('product')
            ->select('product_id')
            ->distinct()
            ->where('branch_id', Auth::user()->branch_id)
            ->where('cloth_type', 'Dirty')
            ->get()
            ->pluck('product');

        // Initialize report data array
        $reportData = [];
        $weeklyTotals = [];

        // Adjust startDate to the previous Tuesday if it's not already a Tuesday
        $startDate = $startDate->startOfWeek(Carbon::TUESDAY);
        $endDate = $endDate->endOfWeek(Carbon::MONDAY);

        // Get week periods
        $period = CarbonPeriod::create($startDate, '1 week', $endDate);

        foreach ($period as $weekStart) {
            $weekEnd = $weekStart->copy()->endOfWeek(Carbon::MONDAY);
            $weekData = [
                'days' => [],
                'first_three_days_total' => 0,
                'last_four_days_total' => 0,
                'total_outgoing' => 0,
            ];

            // Initialize days array (Tuesday to Monday)
            $days = [
                'Tuesday' => ['date' => $weekStart->copy(), 'quantities' => [], 'total' => 0],
                'Wednesday' => ['date' => $weekStart->copy()->addDay(), 'quantities' => [], 'total' => 0],
                'Thursday' => ['date' => $weekStart->copy()->addDays(2), 'quantities' => [], 'total' => 0],
                'Friday' => ['date' => $weekStart->copy()->addDays(3), 'quantities' => [], 'total' => 0],
                'Saturday' => ['date' => $weekStart->copy()->addDays(4), 'quantities' => [], 'total' => 0],
                'Sunday' => ['date' => $weekStart->copy()->addDays(5), 'quantities' => [], 'total' => 0],
                'Monday' => ['date' => $weekStart->copy()->addDays(6), 'quantities' => [], 'total' => 0],
            ];


            // Get stock data for the week
            $stockData = Stockmaintaince::where('branch_id', Auth::user()->branch_id)
                ->where('cloth_type', 'Dirty')
                ->whereBetween('date', [$weekStart, $weekEnd])
                ->selectRaw('product_id, DATE(date) as stock_date, SUM(quantity) as total_quantity')
                ->groupBy('product_id', 'stock_date')
                ->get();

                // dd($stockData);
            // Process stock data
            foreach ($stockData as $stock) {
                $stockDate = Carbon::parse($stock->stock_date);
                $dayName = $stockDate->format('l');

                if (isset($days[$dayName])) {
                    $days[$dayName]['quantities'][$stock->product_id] = $stock->total_quantity;
                    $days[$dayName]['total'] += $stock->total_quantity;

                    // Calculate totals for first three and last four days
                    if (in_array($dayName, ['Tuesday', 'Wednesday', 'Thursday'])) {
                        $weekData['first_three_days_total'] += $stock->total_quantity;
                    } else {
                        $weekData['last_four_days_total'] += $stock->total_quantity;
                    }
                    $weekData['total_outgoing'] += $stock->total_quantity;
                }
            }

            $weekData['days'] = $days;
            $reportData[] = $weekData;
            $weeklyTotals[] = $weekData['total_outgoing'];
        }

        // Handle PDF download
        if ($request->has('download')) {
            $pdf = PDF::loadView('admin.reports.dirtyStockReportPdf', compact(
                'reportData',
                'products',
                'startDate',
                'endDate',
                'weeklyTotals',
                'branchName'
            ))->setPaper('a4', 'landscape');
            return $pdf->download('Dirty_Stock_Report_' . $branchName . '_' . $startDate->format('Y-m-d') . '_to_' . $endDate->format('Y-m-d') . '.pdf');
        }

        return view('admin.reports.dirtyStockReport', compact(
            'reportData',
            'products',
            'startDate',
            'endDate',
            'weeklyTotals',
            'branchName'
        ));
    }

}
