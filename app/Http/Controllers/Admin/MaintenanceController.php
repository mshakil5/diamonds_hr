<?php

namespace App\Http\Controllers\Admin;

use App\Models\Maintenance;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class MaintenanceController extends Controller
{
    public function index()
    {
        $data = Maintenance::all();
        return view('admin.maintenance.index', compact('data'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:maintenances,name',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'business_name' => 'nullable|string|max:255',
            'description' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 422, 'message' => $validator->errors()->first()]);
        }

        $data = new Maintenance();
        $data->fill($request->only(['name', 'phone', 'email', 'business_name', 'description']));
        $data->created_by = auth()->id();
        $data->save();

        return response()->json(['status' => 200, 'message' => 'Maintenance record created.']);
    }

    public function edit($id)
    {
        return response()->json(Maintenance::findOrFail($id));
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:maintenances,name,' . $request->codeid,
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'business_name' => 'nullable|string|max:255',
            'description' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 422, 'message' => $validator->errors()->first()]);
        }

        $data = Maintenance::findOrFail($request->codeid);
        $data->fill($request->only(['name', 'phone', 'email', 'business_name', 'description']));
        $data->updated_by = auth()->id();
        $data->save();

        return response()->json(['status' => 200, 'message' => 'Maintenance updated successfully.']);
    }

    public function delete($id)
    {
        $data = Maintenance::findOrFail($id);
        $data->deleted_by = auth()->id();
        $data->save();
        $data->delete();

        return response()->json(['status' => 200, 'message' => 'Maintenance deleted successfully.']);
    }

    public function updateStatus(Request $request)
    {
        $data = Maintenance::findOrFail($request->id);
        $data->status = $request->status;
        $data->save();

        return response()->json(['status' => 200, 'message' => 'Status updated.']);
    }
}