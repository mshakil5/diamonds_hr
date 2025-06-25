<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SettingsController extends Controller
{
    // change branch

    public function changeBranch()
    {
        $branch = Branch::where('status', 1)->get();
        return view("admin.branch.changeBranch", compact('branch'));
    }

    public function branchChange(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'branch_id' => 'required|exists:branches,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 422, 'message' => $validator->errors()->first()]);
        }

        $data = User::findOrFail(auth()->id());
        $data->branch_id = $request->branch_id;
        $data->save();

        return response()->json(['status' => 200, 'message' => 'Data updated successfully.']);
    }


}
