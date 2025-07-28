<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Product;
use App\Models\Stockmaintaince;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\Stock;
use App\Models\StockAssetType;
use App\Models\Branch;
use App\Models\Floor;
use App\Models\Maintenance;
use App\Models\FaultyAssetReport;

class StockController extends Controller
{
    public function index(Request $request)
    {
        $data = Stockmaintaince::where('branch_id', Auth::user()->branch_id)->with('branch')->orderby('id','DESC')->get();

        $products = Product::where('status', 1)->where('branch_id', Auth::user()->branch_id)->get();
        return view('admin.stock.index', compact('data','products'));
    }

    public function store(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|string|max:255',
            'cloth_type' => 'required|string|max:255',
            'details' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 422, 'message' => $validator->errors()->first()]);
        }
        

        Stockmaintaince::create([
            'product_id'=>$request->input('product_id'),
            'marks'=>$request->input('marks'),
            'quantity'=>$request->input('quantity'),
            'details'=>$request->input('details'),
            'cloth_type'=>$request->input('cloth_type'),
            'date'=>$request->input('date'),
            'branch_id'=>Auth::user()->branch_id,
            'created_by'=>Auth::user()->id,
            'user_id'=>Auth::user()->id,
        ]);
        return response()->json(['status' => 200, 'message' => 'Data created successfully.']);
    }

    public function edit(Request $request, $id)
    {
        return Stockmaintaince::find($id);
    }

    public function update(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'product_id' => 'required|string|max:255',
            'cloth_type' => 'required|string|max:255',
            'details' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 422, 'message' => $validator->errors()->first()]);
        }
        


        $data = Stockmaintaince::find($request->codeid);
        $request->merge(['branch_id' => Auth::user()->branch_id]);
        $request->merge(['updated_by' => Auth::user()->id]);
        $data->update($request->all());

        return response()->json(['status' => 200, 'message' => 'Data updated successfully.']);
    }


    public function delete(Request $request, $id)
    {
        
        $data=Stockmaintaince::find($id);
        $data->delete();
        return response()->json([
            'type'=>'success',
            'message'=>'Data Deleted successfully'
        ]);
    }


    public function addUser()
    {

        
        $stocks = Stockmaintaince::all();

        foreach ($stocks as $key => $stock) {
            
            $data = Stockmaintaince::find($stock->id);
            $data->user_id = $data->created_by;
            $data->save();
        }

        return true;
    }

    public function printCodes($stockId, $status)
    {
        $stock = Stock::with(['stockAssetTypes' => function ($q) use ($status) {
            $q->where('asset_status', $status);
        }])->findOrFail($stockId);

        $codes = $stock->stockAssetTypes;

        return view('admin.stock.print-codes', compact('codes'));
    }

    public function faultyProducts(Request $request)
    {
        $results = collect();
        if ($request->has('asset_no')) {
          
            if ($request->asset_no) {
                $results = StockAssetType::with(['stock', 'assetType', 'branch', 'location.flooor', 'maintenance'])
                    ->whereHas('stock', function ($q) {
                        $q->whereNotNull('id');
                    })
                    ->where('code', 'like', '%' . $request->asset_no . '%')
                    ->get();
            }
        }

        $statuses = [
            1 => 'Assigned',
            2 => 'In Storage',
            3 => 'Under Repair',
            4 => 'Damaged',
        ];

        $branches = Branch::where('status', 1)->get();
        $floors = Floor::where('status', 1)->get();
        $maintenances = Maintenance::where('status', 1)->get();

        $reports = FaultyAssetReport::with([
            'assetType',
            'stockAssetType.stock',
            'branch',
            'location.flooor',
            'maintenance',
            'employee'
        ])->latest()->get();

        return view('admin.stock.faulty', compact('results', 'request', 'statuses', 'branches', 'floors', 'maintenances', 'reports'));
    }

    public function updateFaultyStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:stock_asset_types,id',
            'asset_status' => 'required|in:1,2,3,4',
            'branch_id' => 'nullable|exists:branches,id',
            'floor_id' => 'nullable|exists:floors,id',
            'location_id' => 'nullable|exists:locations,id',
            'maintenance_id' => 'nullable|exists:maintenances,id',
            'note' => 'nullable|string',
        ]);

        $asset = StockAssetType::findOrFail($request->id);

        $updateData = [
            'asset_status' => $request->asset_status
        ];

        if (in_array($request->asset_status, [1, 2])) {
            $updateData['branch_id'] = $request->branch_id;
            $updateData['floor_id'] = $request->floor_id;
            $updateData['location_id'] = $request->location_id;
            $updateData['maintenance_id'] = null;
        } elseif ($request->asset_status == 3) {
            $updateData['branch_id'] = null;
            $updateData['floor_id'] = null;
            $updateData['location_id'] = null;
            $updateData['maintenance_id'] = $request->maintenance_id;
        } else {
            $updateData['branch_id'] = null;
            $updateData['floor_id'] = null;
            $updateData['location_id'] = null;
            $updateData['maintenance_id'] = null;
        }

        $asset->update($updateData);

        FaultyAssetReport::create([
            'date' => now()->format('Y-m-d'),
            'asset_type_id' => $asset->asset_type_id,
            'stock_asset_type_id' => $asset->id,
            'branch_id' => $updateData['branch_id'],
            'location_id' => $updateData['location_id'],
            'maintenance_id' => $updateData['maintenance_id'],
            'employee_id' => null,
            'status' => $request->asset_status,
            'note' => $request->note,
            'created_by' => auth()->id(),
        ]);

        return response()->json(['status' => 200, 'message' => 'Status updated successfully']);
    }
}
