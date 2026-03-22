<?php

namespace App\Services;

use DaiyanMozumder\LaravelFlexSearch\FlexSearch;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\Permission\Models\Role;

class RoleService
{
    /**
     * Get all roles with search and sorting.
     */
    public function getAllRoles(array $params = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = Role::query()->where('guard_name', 'admin');

        $flexSearch = app(FlexSearch::class);
        $searchTerm = $params['search'] ?? null;
        $searchableColumns = ['name'];

        // Apply Search and Filtering using FlexSearch
        $query = $flexSearch->apply($query, [], $searchTerm, $searchableColumns);

        // Apply Sorting
        $sort = $params['sort'] ?? 'latest';
        switch ($sort) {
            case 'oldest':
                $query->oldest();
                break;
            case 'a-z':
                $query->orderBy('name', 'asc');
                break;
            case 'z-a':
                $query->orderBy('name', 'desc');
                break;
            case 'latest':
            default:
                $query->latest();
                break;
        }

        return $query->paginate($perPage);
    }

    /**
     * Store a newly created role.
     */
    public function storeRole(array $data): Role
    {
        return Role::create([
            'name' => $data['name'],
            'guard_name' => 'admin',
        ]);
    }

    /**
     * Find a role by ID.
     */
    public function findRole(int $id): Role
    {
        return Role::findOrFail($id);
    }

    /**
     * Update the specified role.
     */
    public function updateRole(int $id, array $data): Role
    {
        $role = Role::findOrFail($id);
        $role->update([
            'name' => $data['name'],
        ]);

        return $role;
    }

    /**
     * Delete the specified role.
     */
    public function deleteRole(int $id): bool
    {
        $role = Role::findOrFail($id);

        return $role->delete();
    }

    /**
     * Get all roles for dropdown.
     */
    public function getRolesForDropdown()
    {
        return Role::where('guard_name', 'admin')->orderBy('name', 'asc')->get();
    }

    /**
     * Get all permissions grouped by menu name.
     */
    public function getAllGroupedPermissions(): \Illuminate\Support\Collection
    {
        $permissions = \Spatie\Permission\Models\Permission::where('guard_name', 'admin')->get();

        return $permissions->groupBy(function ($permission) {
            return explode('.', $permission->name)[0];
        });
    }
}
