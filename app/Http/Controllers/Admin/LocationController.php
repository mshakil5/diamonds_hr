<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Branch;

class LocationController extends Controller
{
    public function index()
    {
        $data = Location::where('branch_id', auth()->user()->branch_id)->get();
        $branches = Branch::where('status', 1)->get();
        return view('admin.location.index', compact('data', 'branches'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:locations,name',
            'branch_id' => 'required',
            'location' => 'nullable|string|max:255',
            'floor' => 'nullable|string|max:255',
            'room' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'status' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 422, 'message' => $validator->errors()->first()]);
        }

        $data = new Location();
        $data->name = $request->name;
        $data->location = $request->location;
        $data->branch_id = $request->branch_id;
        $data->floor = $request->floor;
        $data->room = $request->room;
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
            'branch_id' => 'required',
            'floor' => 'nullable|string|max:255',
            'room' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'status' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 422, 'message' => $validator->errors()->first()]);
        }

        $data = Location::findOrFail($request->codeid);
        $data->name = $request->name;
        $data->location = $request->location;
        $data->branch_id = $request->branch_id;
        $data->floor = $request->floor;
        $data->room = $request->room;
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

    public function getLocationsByBranch($branchId)
    {
        $locations = Location::where('branch_id', $branchId)
                            ->where('status', 1)
                            ->get(['id', 'name']);

        return response()->json($locations);
    }
}