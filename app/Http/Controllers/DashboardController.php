<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class DashboardController extends Controller
{
    public function dashboard()
    {
        return view('dashboard');
    }

    public function profile()
    {
        $user = Auth::user();
        return view('profile.userprofile', get_defined_vars());
    }
    public function general(Request $req)
    {
        $user = Auth::user();
        $req->validate([
            'fname' => 'required',
            'lname' => 'required',
        ]);

        $user->first_name = $req->fname;
        $user->last_name = $req->lname;

        if ($req->image) {
            $user->image = uploadFile($req->image, 'uploads/profile', $req->first_name . '-' . $req->last_name . '-' . time());
        }

        $user->save();
        return redirect()->back()->with('success', 'Profile updated successfully');
    }

    public function changePassword(Request $req)
    {
        $user = Auth::user();

        $check = Validator::make($req->all(), [
            'current_password' => 'required|password',
            'password' => 'required|min:8',
            'confirm_password' => 'required|same:password',
        ]);

        if ($check->fails()) {
            return redirect()->back()->with('error', $check->errors()->first());
        }

        $user->password = bcrypt($req->password);
        $user->save();

        return redirect()->back()->with('success', 'Password updated Successfully!');
    }

    public function changeEmail(Request $request)
    {
        $check = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email,' . Auth::id(),
            'password' => 'required',
        ]);

        if(Hash::check($request->password, Auth::user()->password) == false){
            return redirect()->back()->with('error', 'Password does not match!');
        }

        if ($check->fails()) {
            return redirect()->back()->with('error', $check->errors()->first());
        }
        
        $user = Auth::user();
        $user->email = $request->email;
        $user->save();
        return redirect()->back()->with('success', 'Email updated Successfully!');
    }
}
