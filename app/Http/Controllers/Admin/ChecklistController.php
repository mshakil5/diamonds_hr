<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\ChecklistCategory;
use App\Models\ChecklistItem;
use App\Models\Employee;
use App\Models\Floor;
use App\Models\RoomInspection;
use App\Models\RoomInspectionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

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
            'category_id' => 'nullable',
            'status' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 422, 'message' => $validator->errors()->first()]);
        }

        try {
            $data = new ChecklistItem();
            $data->name = $request->name;
            $data->checklist_category_id = $request->category_id;
            $data->status = $request->status;
            
            // Safety check for auth
            if (auth()->check()) {
                $data->created_by = auth()->id();
            } else {
                return response()->json(['status' => 401, 'message' => 'Your session has expired. Please login again.']);
            }
            
            $data->save();
            return response()->json(['status' => 200, 'message' => 'Data created successfully.']);
            
        } catch (\Exception $e) {
            // This will catch DB errors and return them as JSON so you can see them
            return response()->json(['status' => 500, 'message' => $e->getMessage()]);
        }
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
            'category_id' => 'nullable',
            'status' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 422, 'message' => $validator->errors()->first()]);
        }

        $data = ChecklistItem::findOrFail($request->codeid);
        $data->name = $request->name;
        $data->checklist_category_id = $request->category_id;
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


    public function roomcheck()
    {

        $data = RoomInspection::with('items')->where('branch_id', Auth::user()->branch_id)->orderby('id', 'DESC')->get();

        $categories = ChecklistCategory::with('item')->where('status', 1)->get();
        $branches = Branch::where('status', 1)->get();
        $floors = Floor::where('status', 1)->get();
        return view('admin.checklist.roomcheck', compact('data','categories','branches','floors'));
    }


    public function inspectionEdit($id)
    {
        $inspection = RoomInspection::with('items')->find($id);
        return response()->json($inspection);
    }

    public function inspectionStore(Request $request)
    {
        $request->validate([
            'branch_id' => 'required',
            'floor_id'  => 'required',
            'room'      => 'required',
        ]);

        try {
            return DB::transaction(function () use ($request) {
                $employee = Employee::where('user_id', auth()->id())->first();
                
                // Ensure ID is null if empty string
                $id = $request->inspection_id ?: null;

                $inspection = RoomInspection::updateOrCreate(
                    ['id' => $id], 
                    [
                        'user_id'     => auth()->id(),
                        'employee_id' => $employee->id ?? null,
                        'branch_id'   => $request->branch_id,
                        'floor_id'    => $request->floor_id,
                        'room'        => $request->room,
                        'note'        => $request->note,
                        'date'        => now()->format('Y-m-d'),
                    ]
                );

                // Sync Checked Items
                RoomInspectionItem::where('room_inspection_id', $inspection->id)->delete();
                
                if ($request->has('checkitem')) {
                    $items = [];
                    foreach ($request->checkitem as $itemId) {
                        $items[] = [
                            'room_inspection_id' => $inspection->id,
                            'checklist_item_id'  => $itemId,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                    RoomInspectionItem::insert($items); // Bulk insert is faster
                }

                return response()->json(['success' => true, 'message' => 'Inspection saved successfully!']);
            });
            
        } catch (\Exception $e) {
            // This will now catch DB errors and tell you exactly what happened
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function updateNote(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:room_inspections,id',
            'note' => 'nullable|string'
        ]);

        $inspection = RoomInspection::find($request->id);
        $inspection->inspection_note = $request->note; 
        $inspection->inspection_by = Auth::user()->id; 
        $inspection->save();

        return response()->json(['success' => true, 'message' => 'Note updated successfully!']);
    }




}
