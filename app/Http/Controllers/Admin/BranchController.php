<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BranchController extends Controller
{
    public function index()
    {
        $data = Branch::where('id', auth()->user()->branch_id)->get();
        return view('admin.branch.index', compact('data'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:branches,name',
            'status' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 422, 'message' => $validator->errors()->first()]);
        }

        $data = new Branch();
        $data->name = $request->name;
        $data->status = $request->status;
        $data->created_by = auth()->id();
        $data->save();

        return response()->json(['status' => 200, 'message' => 'Data created successfully.']);
    }

    public function edit($id)
    {
        $data = Branch::findOrFail($id);
        return response()->json($data);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:branches,name,' . $request->codeid,
            'status' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 422, 'message' => $validator->errors()->first()]);
        }

        $data = Branch::findOrFail($request->codeid);
        $data->name = $request->name;
        $data->status = $request->status;
        $data->updated_by = auth()->id();
        $data->save();

        return response()->json(['status' => 200, 'message' => 'Data updated successfully.']);
    }

    public function delete($id)
    {
        $data = Branch::findOrFail($id);
        $data->delete();

        return response()->json(['status' => 200, 'message' => 'Data deleted successfully.']);
    }

    public function updateStatus(Request $request)
    {
        $data = Branch::findOrFail($request->id);
        $data->status = $request->status;
        $data->save();

        return response()->json(['status' => 200, 'message' => 'Status updated successfully.']);
    }
}
