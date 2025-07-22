<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Branch;
use App\Models\Floor;

class LocationController extends Controller
{
    public function index()
    {
        $data = Location::where('branch_id', auth()->user()->branch_id)->latest()->get();
        $branches = Branch::where('status', 1)->get();
        $floors = Floor::where('status', 1)->get();
        return view('admin.location.index', compact('data', 'branches', 'floors'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'branch_id' => 'required',
            'floor_id' => 'required',
            'room' => 'required',
            'status' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 422, 'message' => $validator->errors()->first()]);
        }

        $data = new Location();
        $data->branch_id = $request->branch_id;
        $data->floor_id = $request->floor_id;
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
            'branch_id' => 'required',
            'floor_id' => 'required',
            'room' => 'required',
            'status' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 422, 'message' => $validator->errors()->first()]);
        }

        $data = Location::findOrFail($request->codeid);
        $data->branch_id = $request->branch_id;
        $data->floor_id = $request->floor_id;
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

    public function getLocationsByBranchAndFloor($branchId, $floorId)
    {
        $locations = Location::where('branch_id', $branchId)
                            ->where('floor_id', $floorId)
                            ->where('status', 1)
                            ->get();
        
        return response()->json($locations);
    }
}