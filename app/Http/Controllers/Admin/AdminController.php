<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;

class AdminController extends Controller
{
    public function getAdmin()
    {
        $data = User::where('is_type', '1')->orderby('id','DESC')->get();
        $branches = Branch::where('status', 1)->get();
        $roles = Role::latest()->get();
        return view('admin.admin.index', compact('data','branches','roles'));
    }

    public function adminStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'surname' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'branch_id' => 'required|integer|exists:branches,id',
            'role_id' => 'required|integer|exists:roles,id',
            'password' => [
                'required',
                'string',
                'min:6',
                function ($attribute, $value, $fail) use ($request) {
                    if ($value !== $request->confirm_password) {
                        $fail('The password confirmation does not match.');
                    }
                },
            ],
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $alert = "<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>" . implode('<br>', $messages) . "</b></div>";
            return response()->json(['status' => 422, 'message' => $alert]);
        }

        $user = new User;
        $user->name = $request->name;
        $user->surname = $request->surname;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->branch_id = $request->branch_id ?? Auth::user()->branch_id;
        $user->role_id = $request->role_id ?? '1';
        $user->is_type = 1;
        $user->password = Hash::make($request->password);

        if ($user->save()) {
            $success = "<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Data created successfully.</b></div>";
            return response()->json(['status' => 300, 'message' => $success]);
        }

        return response()->json(['status' => 303, 'message' => 'Server Error!']);
    }

    public function adminEdit($id)
    {
        $where = [
            'id'=>$id
        ];
        $info = User::where($where)->get()->first();
        return response()->json($info);
    }

    public function adminUpdate(Request $request)
    {

        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'surname' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email,' . $request->codeid,
            'phone' => 'nullable|string|max:20',
            'branch_id' => 'required|integer|exists:branches,id',
            'role_id' => 'required|integer|exists:roles,id',
            'password' => [
                'nullable',
                'string',
                'min:6',
                function ($attribute, $value, $fail) use ($request) {
                    if ($value !== $request->confirm_password) {
                        $fail('The password confirmation does not match.');
                    }
                },
            ],
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $alert = "<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>" . implode('<br>', $messages) . "</b></div>";
            return response()->json(['status' => 422, 'message' => $alert]);
        }


        $data = User::find($request->codeid);
        $data->name = $request->name;
        $data->surname = $request->surname;
        $data->phone = $request->phone;
        $data->email = $request->email;
        $data->branch_id = $request->branch_id ?? Auth::user()->branch_id;
        $data->role_id = $request->role_id ?? '1';
        if(isset($request->password)){
            $data->password = Hash::make($request->password);
        }
        if ($data->save()) {
            $success = "<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Data created successfully.</b></div>";
            return response()->json(['status' => 300, 'message' => $success]);
        }

        return response()->json(['status' => 303, 'message' => 'Server Error!']);
    }

    public function adminDelete($id)
    {

        if(User::destroy($id)){
            return response()->json(['success'=>true,'message'=>'User has been deleted successfully']);
        }else{
            return response()->json(['success'=>false,'message'=>'Delete Failed']);
        }
    }
}
