<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function index()
    {
        $users = Admin::latest()->paginate(10);

        return view('admin.users.index', compact('users'));
    }

    public function AdminCreate(Request $request): View
    {
        $title = 'User Registration';

        return view('admin.users.forms', compact('title'));
    }

    public function edit(Request $request, $id)
    {
        $title = 'User Edit';
        $user = Admin::find($id);

        return view('admin.users.forms', compact('title', 'user'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:admins,email'],
            'password' => ['required', 'confirmed'],
        ]);

        Admin::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('admin.index')->with([
            'message' => 'User created successfully',
            'alert-type' => 'success',
        ]);
    }

    public function update($id, Request $request)
    {
        $validated = $request->validate([
            'name' => ['string', 'max:255'],
            'email' => ['string', 'email', 'max:255', 'unique:admins,email'],
            'password' => ['confirmed'],
        ]);

        $admin = Admin::find($id);
        $admin->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('admin.index')->with([
            'message' => 'User updated successfully',
            'alert-type' => 'success',
        ]);
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::guard('admin')->attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('admin.dashboard'));
        }

        return back()->withErrors([
            'email' => 'Invalid credentials',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }

    public function destroy($id)
    {
        $user = Admin::find($id);
        $user->delete();

        return redirect()->route('admin.index')->with([
            'message' => 'User deleted successfully',
            'alert-type' => 'success',
        ]);
    }
}
