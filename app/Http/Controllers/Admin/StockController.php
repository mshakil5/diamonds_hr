<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Product;
use App\Models\Stockmaintaince;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class StockController extends Controller
{
    public function index(Request $request)
    {
        $data = Stockmaintaince::where('branch_id', Auth::user()->branch_id)->orderby('id','DESC')->get();

        $products = Product::where('status', 1)->where('branch_id', Auth::user()->branch_id)->get();
        return view('admin.stock.index', compact('data','products'));
    }

    public function store(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|string|max:255',
            'cloth_type' => 'required|string|max:255',
            'details' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 422, 'message' => $validator->errors()->first()]);
        }
        

        Stockmaintaince::create([
            'product_id'=>$request->input('product_id'),
            'marks'=>$request->input('marks'),
            'quantity'=>$request->input('quantity'),
            'details'=>$request->input('details'),
            'cloth_type'=>$request->input('cloth_type'),
            'date'=>$request->input('date'),
            'branch_id'=>Auth::user()->branch_id,
            'created_by'=>Auth::user()->id,
            'user_id'=>Auth::user()->id,
        ]);
        return response()->json(['status' => 200, 'message' => 'Data created successfully.']);
    }

    public function edit(Request $request, $id)
    {
        return Stockmaintaince::find($id);
    }

    public function update(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'product_id' => 'required|string|max:255',
            'cloth_type' => 'required|string|max:255',
            'details' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 422, 'message' => $validator->errors()->first()]);
        }
        


        $data = Stockmaintaince::find($request->codeid);
        $request->merge(['branch_id' => Auth::user()->branch_id]);
        $request->merge(['updated_by' => Auth::user()->id]);
        $data->update($request->all());

        return response()->json(['status' => 200, 'message' => 'Data updated successfully.']);
    }


    public function delete(Request $request, $id)
    {
        
        $data=Stockmaintaince::find($id);
        $data->delete();
        return response()->json([
            'type'=>'success',
            'message'=>'Data Deleted successfully'
        ]);
    }


    public function addUser()
    {

        
        $stocks = Stockmaintaince::all();

        foreach ($stocks as $key => $stock) {
            
            $data = Stockmaintaince::find($stock->id);
            $data->user_id = $data->created_by;
            $data->save();
        }

        return true;
    }
}
