<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LocationController extends Controller
{
    public function index()
    {
        $data = Location::where('branch_id', auth()->user()->branch_id)->get();
        return view('admin.location.index', compact('data'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:locations,name',
            'location' => 'nullable|string|max:255',
            'status' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 422, 'message' => $validator->errors()->first()]);
        }

        $data = new Location();
        $data->name = $request->name;
        $data->location = $request->location;
        $data->branch_id = auth()->user()->branch_id;
        $data->status = $request->status;
        $data->created_by = auth()->id();
        $data->save();

        return response()->json(['status' => 200, 'message' => 'Location created successfully.']);
    }

    public function edit($id)
    {
        $data = Location::findOrFail($id);
        return response()->json($data);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:locations,name,' . $request->codeid,
            'location' => 'nullable|string|max:255',
            'status' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 422, 'message' => $validator->errors()->first()]);
        }

        $data = Location::findOrFail($request->codeid);
        $data->name = $request->name;
        $data->location = $request->location;
        $data->status = $request->status;
        $data->updated_by = auth()->id();
        $data->save();

        return response()->json(['status' => 200, 'message' => 'Location updated successfully.']);
    }

    public function delete($id)
    {
        $data = Location::findOrFail($id);
        $data->deleted_by = auth()->id();
        $data->save();
        $data->delete();

        return response()->json(['status' => 200, 'message' => 'Location deleted successfully.']);
    }

    public function updateStatus(Request $request)
    {
        $data = Location::findOrFail($request->id);
        $data->status = $request->status;
        $data->save();

        return response()->json(['status' => 200, 'message' => 'Status updated successfully.']);
    }
}