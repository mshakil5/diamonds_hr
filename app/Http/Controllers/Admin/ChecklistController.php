<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChecklistCategory;
use App\Models\ChecklistItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ChecklistController extends Controller
{
    public function category()
    {
        $data = ChecklistCategory::orderby('id', 'DESC')->get();
        return view('admin.checklist.category', compact('data'));
    }

    public function storeCategory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'status' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 422, 'message' => $validator->errors()->first()]);
        }

        $category = new ChecklistCategory();
        $category->name = $request->name;
        $category->slug = $request->slug;
        $category->status = $request->status;
        $category->created_by = auth()->id();
        $category->save();

        return response()->json(['status' => 200, 'message' => 'Category created successfully.']);
    }

    public function editCategory($id)
    {
        $data = ChecklistCategory::findOrFail($id);
        return response()->json($data);
    }

    public function updateCategory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'status' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 422, 'message' => $validator->errors()->first()]);
        }

        $category = ChecklistCategory::findOrFail($request->codeid);
        $category->name = $request->name;
        $category->slug = $request->slug;
        $category->status = $request->status;
        $category->updated_by = auth()->id();
        $category->save();

        return response()->json(['status' => 200, 'message' => 'Category updated successfully.']);
    }

    public function deleteCategory($id)
    {
        $category = ChecklistCategory::findOrFail($id);
        $category->delete();

        return response()->json(['status' => 200, 'message' => 'Category deleted successfully.']);
    }

    public function updateStatus(Request $request, $id)
    {
        $category = ChecklistCategory::findOrFail($id);
        $category->status = $request->status;
        $category->save();

        return response()->json(['status' => 200, 'message' => 'Status updated successfully.']);
    }


    public function checklistItems()
    {
        $data = ChecklistItem::orderby('id', 'DESC')->get();
        $categories = ChecklistCategory::where('status', 1)->get();
        return view('admin.checklist.checklistitem', compact('data','categories'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|string|max:255',
            'status' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 422, 'message' => $validator->errors()->first()]);
        }

        $data = new ChecklistItem();
        $data->name = $request->name;
        $data->category_id = $request->category_id;
        $data->status = $request->status;
        $data->created_by = auth()->id();
        $data->save();

        return response()->json(['status' => 200, 'message' => 'Data created successfully.']);
    }

    public function edit($id)
    {
        $data = ChecklistItem::findOrFail($id);
        return response()->json($data);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|string|max:255',
            'status' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 422, 'message' => $validator->errors()->first()]);
        }

        $data = ChecklistItem::findOrFail($request->codeid);
        $data->name = $request->name;
        $data->category_id = $request->category_id;
        $data->status = $request->status;
        $data->updated_by = auth()->id();
        $data->save();

        return response()->json(['status' => 200, 'message' => 'Data updated successfully.']);
    }

    public function delete($id)
    {
        $data = ChecklistItem::findOrFail($id);
        $data->delete();

        return response()->json(['status' => 200, 'message' => 'Data deleted successfully.']);
    }




}
