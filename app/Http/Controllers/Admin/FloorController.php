<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Floor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FloorController extends Controller
{
    public function index()
    {
        $data = Floor::latest()->get();
        return view('admin.floor.index', compact('data'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:floors,name',
            'status' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 422, 'message' => $validator->errors()->first()]);
        }

        $data = new Floor();
        $data->name = $request->name;
        $data->status = $request->status;
        $data->created_by = auth()->id();
        $data->save();

        return response()->json(['status' => 200, 'message' => 'Floor created successfully.']);
    }

    public function edit($id)
    {
        $data = Floor::findOrFail($id);
        return response()->json($data);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:floors,name,' . $request->codeid,
            'status' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 422, 'message' => $validator->errors()->first()]);
        }

        $data = Floor::findOrFail($request->codeid);
        $data->name = $request->name;
        $data->status = $request->status;
        $data->updated_by = auth()->id();
        $data->save();

        return response()->json(['status' => 200, 'message' => 'Floor updated successfully.']);
    }

    public function delete($id)
    {
        $data = Floor::findOrFail($id);
        $data->deleted_by = auth()->id();
        $data->save();
        $data->delete();

        return response()->json(['status' => 200, 'message' => 'Floor deleted successfully.']);
    }

    public function updateStatus(Request $request)
    {
        $data = Floor::findOrFail($request->id);
        $data->status = $request->status;
        $data->updated_by = auth()->id();
        $data->save();

        return response()->json(['status' => 200, 'message' => 'Status updated successfully.']);
    }
}