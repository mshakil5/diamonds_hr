<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AssetType;
use App\Models\Location;
use App\Models\Stock;
use App\Models\StockAssetType;
use Illuminate\Support\Facades\Validator;
use App\Models\Maintenance;
use App\Models\Branch;
use App\Models\Floor;

class AssetStockController extends Controller
{
    public function index()
    {
        $data = Stock::with('stockAssetTypes', 'assetType')->latest()->get();
        $assetTypes = AssetType::where('status', 1)->get();
        $locations = Location::where('status', 1)->get();
        $floors = Floor::where('status', 1)->get();
        $branches = Branch::with('locations')
            ->where('status', 1)
            ->whereHas('locations', function($q) {
                $q->where('status', 1);
            })
            ->get();
            
        $maintainances = Maintenance::where('status', 1)->get();

        foreach ($data as $stock) {
            $stock->assigned_count = $stock->stockAssetTypes->where('asset_status', 1)->count();
            $stock->storage_count = $stock->stockAssetTypes->where('asset_status', 2)->count();
            $stock->repair_count = $stock->stockAssetTypes->where('asset_status', 3)->count();
            $stock->damaged_count = $stock->stockAssetTypes->where('asset_status', 4)->count();
            $stock->reported_count = $stock->stockAssetTypes->where('asset_status', 5)->count();
        }

        return view('admin.stock_asset.index', compact('data', 'assetTypes', 'locations', 'branches', 'maintainances', 'floors'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required',
            'asset_type_id' => 'required|numeric',
            'quantity' => 'required|numeric|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 422, 'message' => $validator->errors()->first()]);
        }

        $assetTypeId = $request->asset_type_id;

        $codes = collect($request->product_code);
        $uniqueCodes = $codes->unique();

        foreach ($codes as $code) {
            if (!str_starts_with($code, $assetTypeId . '-')) {
                return response()->json([
                    'status' => 422,
                    'message' => "Invalid format: $code (must start with {$assetTypeId}-)"
                ]);
            }

            if ($codes->count() !== $uniqueCodes->count()) {
                return response()->json([
                    'status' => 422,
                    'message' => "Duplicate product codes found in the form."
                ]);
            }

            if (StockAssetType::where('product_code', $code)->exists()) {
                return response()->json([
                    'status' => 422,
                    'message' => "Product code already exists: $code"
                ]);
            }
        }

        $data = new Stock();
        $data->date = $request->date;
        $data->asset_type_id = $assetTypeId;
        $data->branch_id = auth()->user()->branch_id;
        $data->brand = $request->brand;
        $data->model = $request->model;
        $data->quantity = $request->quantity;
        $data->note = $request->note;
        $data->created_by = auth()->id();
        $data->save();

        foreach ($request->product_code as $index => $code) {
            $assetType = new StockAssetType();
            $assetType->stock_id = $data->id;
            $assetType->asset_type_id = $assetTypeId;
            $assetType->product_code = $code;
            $assetType->asset_status = $request->asset_status[$index] ?? null;
            $assetType->branch_id = $request->branch_id[$index] ?? null;
            $assetType->location_id = $request->location_id[$index] ?? null;
            $assetType->maintenance_id = $request->maintenance_id[$index] ?? null;
            $assetType->floor_id = $request->floor_id[$index] ?? null;
            $assetType->assigned_by = auth()->id();
            $assetType->created_by = auth()->id();
            $assetType->save();
        }

        return response()->json(['status' => 200, 'message' => 'Data created successfully.']);
    }

    public function edit($id)
    {
        $data = Stock::with(['stockAssetTypes.location', 'stockAssetTypes.branch', 'stockAssetTypes.maintenance'])->find($id);
        if (!$data) {
            return response()->json(['status' => 404, 'message' => 'Stock not found']);
        }
        
        return response()->json(['status' => 200, 'data' => $data]);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required',
            'asset_type_id' => 'required|numeric',
            'quantity' => 'required|numeric|min:1',
            'codeid' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 422, 'message' => $validator->errors()->first()]);
        }

        $data = Stock::find($request->codeid);
        if (!$data) {
            return response()->json(['status' => 404, 'message' => 'Stock not found']);
        }

        $assetTypeId = $request->asset_type_id;
        $codes = collect($request->product_code);
        $uniqueCodes = $codes->unique();

        if ($codes->count() !== $uniqueCodes->count()) {
            return response()->json([
                'status' => 422,
                'message' => "Duplicate product codes found in the form."
            ]);
        }

        foreach ($codes as $code) {
            if (!str_starts_with($code, $assetTypeId . '-')) {
                return response()->json([
                    'status' => 422,
                    'message' => "Invalid format: $code (must start with {$assetTypeId}-)"
                ]);
            }

            $exists = StockAssetType::where('product_code', $code)
                ->where('stock_id', '!=', $data->id)
                ->exists();

            if ($exists) {
                return response()->json([
                    'status' => 422,
                    'message' => "Product code already exists: $code"
                ]);
            }
        }

        $data->date = $request->date;
        $data->asset_type_id = $assetTypeId;
        $data->brand = $request->brand;
        $data->model = $request->model;
        $data->quantity = $request->quantity;
        $data->note = $request->note;
        $data->updated_by = auth()->id();
        $data->save();

        StockAssetType::where('stock_id', $data->id)->delete();

        foreach ($request->product_code as $index => $code) {
            StockAssetType::create([
                'stock_id' => $data->id,
                'asset_type_id' => $assetTypeId,
                'product_code' => $code,
                'asset_status' => $request->asset_status[$index] ?? null,
                'branch_id' => $request->branch_id[$index] ?? null,
                'location_id' => $request->location_id[$index] ?? null,
                'maintenance_id' => $request->maintenance_id[$index] ?? null,
                'floor_id' => $request->floor_id[$index] ?? null,
                'assigned_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);
        }

        return response()->json(['status' => 200, 'message' => 'Data updated successfully.']);
    }

    public function delete($id)
    {
        $data = Stock::find($id);
        if (!$data) {
            return response()->json(['status' => 404, 'message' => 'Stock not found']);
        }

        // Delete associated asset types first
        StockAssetType::where('stock_id', $id)->delete();
        
        // Then delete the stock
        $data->delete();

        return response()->json(['status' => 200, 'message' => 'Data deleted successfully.']);
    }

    public function viewByStatus($stockId = null, $status)
    {
        $stock = null;
        if ($stockId) {
            $stock = Stock::with('assetType')->findOrFail($stockId);
        }

        $query = StockAssetType::with(['location.flooor', 'branch', 'maintenance']);
        if ($stockId) {
            $query->where('stock_id', $stockId);
        }
        $assets = $query->where('asset_status', $status)->get();

        $branches = Branch::where('status', 1)->get();
        $floors = Floor::where('status', 1)->get();
        $maintenances = Maintenance::where('status', 1)->get();

        $statuses = [
            1 => 'Assigned',
            2 => 'In Storage',
            3 => 'Under Repair',
            4 => 'Damaged',
            5 => 'Reported',
        ];

        return view('admin.stock_asset.view_status', compact('stock', 'assets', 'status', 'branches', 'floors', 'maintenances', 'statuses'));
    }

    public function viewByStockStatus($status)
    {
        

        $query = StockAssetType::with(['location.flooor', 'branch', 'maintenance']);
        
        $assets = $query->where('asset_status', $status)->get();
        $branches = Branch::where('status', 1)->get();
        $floors = Floor::where('status', 1)->get();
        $maintenances = Maintenance::where('status', 1)->get();

        $statuses = [
            1 => 'Assigned',
            2 => 'In Storage',
            3 => 'Under Repair',
            4 => 'Damaged',
            5 => 'Reported',
        ];

        $stock = null;

        return view('admin.stock_asset.view_status', compact('stock', 'assets', 'status', 'branches', 'floors', 'maintenances', 'statuses'));
    }

    public function getLatestCode($assetTypeId)
    {
        $lastCode = StockAssetType::where('asset_type_id', $assetTypeId)
            ->where('product_code', 'like', $assetTypeId . '-%')
            ->orderByRaw("CAST(SUBSTRING_INDEX(product_code, '-', -1) AS UNSIGNED) DESC")
            ->first();

        $lastNumber = 0;

        if ($lastCode && $lastCode->product_code) {
            $codeParts = explode('-', $lastCode->product_code);
            $lastNumber = isset($codeParts[1]) ? (int) $codeParts[1] : 0;
        }

        return response()->json([
            'status' => 200,
            'lastNumber' => $lastNumber
        ]);
    }

}
