<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\AssetType;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AssetController extends Controller
{
    public function index()
    {
        $data = Asset::with(['assetType', 'location'])
                    ->where('branch_id', auth()->user()->branch_id)
                    ->get();
        $assetTypes = AssetType::where('status', 1)->get();
        $locations = Location::where('status', 1)->get();
        
        return view('admin.asset.index', compact('data', 'assetTypes', 'locations'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'asset_type_id' => 'required|exists:asset_types,id',
            'location_id' => 'required|exists:locations,id',
            'assigned_to' => 'nullable|string|max:255',
            'purchase_date' => 'nullable|date',
            'warranty_expiry' => 'nullable|date|after_or_equal:purchase_date',
            'status' => 'required|boolean',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 422, 'message' => $validator->errors()->first()]);
        }

        $data = new Asset();
        $data->asset_type_id = $request->asset_type_id;
        $data->location_id = $request->location_id;
        $data->assigned_to = $request->assigned_to;
        $data->purchase_date = $request->purchase_date;
        $data->warranty_expiry = $request->warranty_expiry;
        $data->status = $request->status;
        $data->notes = $request->notes;
        $data->branch_id = auth()->user()->branch_id;
        $data->save();

        return response()->json(['status' => 200, 'message' => 'Asset created successfully.']);
    }

    public function edit($id)
    {
        $data = Asset::findOrFail($id);
        return response()->json($data);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'asset_type_id' => 'required|exists:asset_types,id',
            'location_id' => 'required|exists:locations,id',
            'assigned_to' => 'nullable|string|max:255',
            'purchase_date' => 'nullable|date',
            'warranty_expiry' => 'nullable|date|after_or_equal:purchase_date',
            'status' => 'required|boolean',
            'notes' => 'nullable|string',
            'codeid' => 'required|exists:assets,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 422, 'message' => $validator->errors()->first()]);
        }

        $data = Asset::findOrFail($request->codeid);
        $data->asset_type_id = $request->asset_type_id;
        $data->location_id = $request->location_id;
        $data->assigned_to = $request->assigned_to;
        $data->purchase_date = $request->purchase_date;
        $data->warranty_expiry = $request->warranty_expiry;
        $data->status = $request->status;
        $data->notes = $request->notes;
        $data->save();

        return response()->json(['status' => 200, 'message' => 'Asset updated successfully.']);
    }

    public function delete($id)
    {
        $data = Asset::findOrFail($id);
        $data->delete();

        return response()->json(['status' => 200, 'message' => 'Asset deleted successfully.']);
    }

    public function updateStatus(Request $request)
    {
        $data = Asset::findOrFail($request->id);
        $data->status = $request->status;
        $data->save();

        return response()->json(['status' => 200, 'message' => 'Status updated successfully.']);
    }
}