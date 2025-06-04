<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function profile()
    {
        return view('user.profile');
    } 

    public function profileUpdate(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . Auth::user()->id,
            'phone' => 'required|string|max:15',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = User::findOrFail(Auth::user()->id);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        if ($request->hasFile('coverimage')) {
            if ($user->coverimage && file_exists(public_path($user->coverimage))) {
                unlink(public_path($user->coverimage));
            }

            $image = $request->file('coverimage');
            $imageName = uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/users'), $imageName);
            $user->coverimage = '/images/users/' . $imageName;
        }

        if ($request->hasFile('profileimage')) {
            if ($user->profileimage && file_exists(public_path($user->profileimage))) {
                unlink(public_path($user->profileimage));
            }

            $image = $request->file('profileimage');
            $imageName = uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/users'), $imageName);
            $user->profileimage = '/images/users/' . $imageName;
        }

        $user->save();

        return back()->with('success', 'Your information has been saved successfully');
    }
}
