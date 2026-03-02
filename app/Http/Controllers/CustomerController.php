<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CustomerController extends Controller
{
    public function accountInformation(){
        $title = "Account Information";
        $section = "Account";
        return view('client.auth.account_info', compact('title','section'));
    }

    public function profileUpdate(Request $request){
        $user = Auth::guard('web')->user();
        $user->update($request->all());
        return redirect()->back()->with([
            'message' => 'Profile updated successfully',
            'alert-type' => 'success',
        ]);
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $user = Auth::guard('web')->user();

        // Check old password
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors([
                'current_password' => 'Current password is incorrect.'
            ]);
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        // Regenerate session
        $request->session()->regenerate();

        return back()->with([
            'message' => 'Password changed successfully',
            'alert-type' => 'success',
        ]);
    }

    public function addressUpdate(Request $request){
        $user = Auth::guard('web')->user();
        $user->update($request->all());
        return redirect()->back()->with([
            'message' => 'Address updated successfully',
            'alert-type' => 'success',
        ]);
    }

}
