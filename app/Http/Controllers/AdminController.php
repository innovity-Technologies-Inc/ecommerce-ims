<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\StoreAdminRequest;
use App\Http\Requests\Admin\UpdateAdminRequest;
use App\Services\AdminService;
use App\Services\RoleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        protected AdminService $adminService,
        protected RoleService $roleService
    ) {}

    /**
     * Display a listing of the admins.
     */
    public function index(Request $request)
    {
        $users = $this->adminService->getAllAdmins($request->all());

        if ($request->ajax()) {
            return view('admin.users.partials.table', compact('users'))->render();
        }

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new admin.
     */
    public function AdminCreate(): View
    {
        $title = 'User Registration';
        $roles = $this->roleService->getRolesForDropdown();

        return view('admin.users.forms', compact('title', 'roles'));
    }

    /**
     * Show the form for editing the specified admin.
     */
    public function edit(int $id): View
    {
        $title = 'User Edit';
        $user = $this->adminService->findAdmin($id);
        $roles = $this->roleService->getRolesForDropdown();

        return view('admin.users.forms', compact('title', 'user', 'roles'));
    }

    /**
     * Store a newly created admin in storage.
     */
    public function store(StoreAdminRequest $request): RedirectResponse
    {
        $this->adminService->storeAdmin($request->validated());

        return redirect()->route('admin.index')->with([
            'message' => 'User created successfully',
            'alert-type' => 'success',
        ]);
    }

    /**
     * Update the specified admin in storage.
     */
    public function update(int $id, UpdateAdminRequest $request): RedirectResponse
    {
        $this->adminService->updateAdmin($id, $request->validated());

        return redirect()->route('admin.index')->with([
            'message' => 'User updated successfully',
            'alert-type' => 'success',
        ]);
    }

    /**
     * Show the admin login form.
     */
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an admin login request.
     */
    public function login(Request $request): RedirectResponse
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

    /**
     * Handle an admin logout request.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }

    /**
     * Remove the specified admin from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $this->adminService->deleteAdmin($id);

        return redirect()->route('admin.index')->with([
            'message' => 'User deleted successfully',
            'alert-type' => 'success',
        ]);
    }
}
