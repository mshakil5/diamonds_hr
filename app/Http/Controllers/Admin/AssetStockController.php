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
        }

        return view('admin.stock_asset.index', compact('data', 'assetTypes', 'locations', 'branches', 'maintainances', 'floors'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required',
            'asset_type_id' => 'required',
            'quantity' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 422, 'message' => $validator->errors()->first()]);
        }

        $data = new Stock();
        $data->date = $request->date;
        $data->asset_type_id = $request->asset_type_id;
        $data->branch_id = auth()->user()->branch_id;
        $data->brand = $request->brand;
        $data->model = $request->model;
        $data->quantity = $request->quantity;
        $data->note = $request->note;
        $data->created_by = auth()->id();
        if( $data->save()){

            foreach ($request->product_code as $index => $code) {
              $assetType = new StockAssetType();
              $assetType->stock_id = $data->id;
              $assetType->asset_type_id = $request->asset_type_id;
              $assetType->product_code = $code;
              $assetType->asset_status = $request->asset_status[$index] ?? null;
              $assetType->branch_id = $request->branch_id[$index] ?? null;
              $assetType->location_id = $request->location_id[$index] ?? null;
              $assetType->maintenance_id = $request->maintenance_id[$index] ?? null;
              $assetType->floor_id = $request->floor_id[$index] ?? null;
              $assetType->assigned_by = auth()->id();
              $assetType->created_by = auth()->id();
              $assetTypeCode = AssetType::where('id', $request->asset_type_id)->value('code');
              $assetTypeCode = $assetTypeCode ?: '1';
              $maxCode = StockAssetType::where('code', 'like', $assetTypeCode . '%')->max('code');
              if ($maxCode) {
                  $suffix = (int)substr($maxCode, strlen($assetTypeCode));
                  $suffix++;
              } else {
                  $suffix = 1;
              }
              $assetType->code = $assetTypeCode . str_pad($suffix, 7, '0', STR_PAD_LEFT);
              $assetType->save();
            }
          
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
            'asset_type_id' => 'required',
            'quantity' => 'required',
            'codeid' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 422, 'message' => $validator->errors()->first()]);
        }

        $data = Stock::find($request->codeid);
        if (!$data) {
            return response()->json(['status' => 404, 'message' => 'Stock not found']);
        }

        $data->date = $request->date;
        $data->asset_type_id = $request->asset_type_id;
        $data->brand = $request->brand;
        $data->model = $request->model;
        $data->quantity = $request->quantity;
        $data->note = $request->note;
        $data->updated_by = auth()->id();

        if ($data->save()) {
            StockAssetType::where('stock_id', $data->id)->delete();

            foreach ($request->product_code as $index => $code) {
                $assetType = new StockAssetType();
                $assetType->stock_id = $data->id;
                $assetType->asset_type_id = $request->asset_type_id;
                $assetType->product_code = $code;
                $assetType->asset_status = $request->asset_status[$index] ?? null;
                $assetType->branch_id = $request->branch_id[$index] ?? null;
                $assetType->location_id = $request->location_id[$index] ?? null;
                $assetType->maintenance_id = $request->maintenance_id[$index] ?? null;
                $assetType->floor_id = $request->floor_id[$index] ?? null;
                $assetType->assigned_by = auth()->id();
                $assetType->updated_by = auth()->id();
                $assetTypeCode = AssetType::where('id', $request->asset_type_id)->value('code');
                $assetTypeCode = $assetTypeCode ?: '1';

                $maxCode = StockAssetType::where('code', 'like', $assetTypeCode . '%')->max('code');

                if ($maxCode) {
                    $suffix = (int)substr($maxCode, strlen($assetTypeCode));
                    $suffix++;
                } else {
                    $suffix = 1;
                }

                $assetType->code = $assetTypeCode . str_pad($suffix, 7, '0', STR_PAD_LEFT);

                $assetType->save();
            }
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

    public function viewByStatus($stockId, $status)
    {
        $stock = Stock::with('assetType')->findOrFail($stockId);

        $assets = StockAssetType::with(['location.flooor', 'branch', 'maintenance'])
            ->where('stock_id', $stockId)
            ->where('asset_status', $status)
            ->get();

        return view('admin.stock_asset.view_status', compact('stock', 'assets', 'status'));
    }

}
