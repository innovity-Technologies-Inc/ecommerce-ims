<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\RoleStoreRequest;
use App\Http\Requests\RoleUpdateRequest;
use App\Services\RoleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RoleController extends Controller
{
    public function __construct(protected RoleService $roleService) {}

    /**
     * Display a listing of the roles.
     */
    public function index(Request $request)
    {
        $roles = $this->roleService->getAllRoles($request->all());

        if ($request->ajax()) {
            return view('admin.roles.partials.table', compact('roles'))->render();
        }

        return view('admin.roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new role.
     */
    public function create(): View
    {
        $title = 'Create Role';

        return view('admin.roles.form', compact('title'));
    }

    /**
     * Store a newly created role in storage.
     */
    public function store(RoleStoreRequest $request): RedirectResponse
    {
        $this->roleService->storeRole($request->validated());

        return redirect()->route('admin.roles.index')->with([
            'message' => 'Role created successfully',
            'alert-type' => 'success',
        ]);
    }

    /**
     * Show the form for editing the specified role.
     */
    public function edit(int $id): View
    {
        $title = 'Edit Role';
        $role = $this->roleService->findRole($id);

        return view('admin.roles.form', compact('title', 'role'));
    }

    /**
     * Update the specified role in storage.
     */
    public function update(int $id, RoleUpdateRequest $request): RedirectResponse
    {
        $this->roleService->updateRole($id, $request->validated());

        return redirect()->route('admin.roles.index')->with([
            'message' => 'Role updated successfully',
            'alert-type' => 'success',
        ]);
    }

    /**
     * Remove the specified role from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $this->roleService->deleteRole($id);

        return redirect()->route('admin.roles.index')->with([
            'message' => 'Role deleted successfully',
            'alert-type' => 'success',
        ]);
    }
}
