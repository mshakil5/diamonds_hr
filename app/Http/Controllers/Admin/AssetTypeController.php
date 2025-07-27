<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AssetType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AssetTypeController extends Controller
{
    public function index()
    {
        $data = AssetType::latest()->get();
        return view('admin.asset-type.index', compact('data'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:asset_types,name',
            'brand' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'status' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 422, 'message' => $validator->errors()->first()]);
        }

        $data = new AssetType();
        $data->name = $request->name;
        $data->brand = $request->brand;
        $data->model = $request->model;
        $data->branch_id = auth()->user()->branch_id;
        $data->status = $request->status;
        $data->created_by = auth()->id();
        $lastCode = AssetType::max('code');
        $newCode = $lastCode ? ((int)$lastCode + 1) : 1;
        $data->code = $newCode;
        $data->save();

        return response()->json(['status' => 200, 'message' => 'Data created successfully.']);
    }

    public function edit($id)
    {
        $data = AssetType::findOrFail($id);
        return response()->json($data);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:asset_types,name,' . $request->codeid,
            'brand' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'status' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 422, 'message' => $validator->errors()->first()]);
        }

        $data = AssetType::findOrFail($request->codeid);
        $data->name = $request->name;
        $data->brand = $request->brand;
        $data->model = $request->model;
        $data->status = $request->status;
        $data->updated_by = auth()->id();
        $data->save();

        return response()->json(['status' => 200, 'message' => 'Data updated successfully.']);
    }

    public function delete($id)
    {
        $data = AssetType::findOrFail($id);
        $data->deleted_by = auth()->id();
        $data->save();
        $data->delete();

        return response()->json(['status' => 200, 'message' => 'Data deleted successfully.']);
    }

    public function updateStatus(Request $request)
    {
        $data = AssetType::findOrFail($request->id);
        $data->status = $request->status;
        $data->updated_by = auth()->id();
        $data->save();

        return response()->json(['status' => 200, 'message' => 'Status updated successfully.']);
    }
}