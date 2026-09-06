<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::withCount('permissions', 'users')->orderBy('id', 'asc')->get();
        $totalPermissionsCount = Permission::count();

        return view('roles.index', compact('roles', 'totalPermissionsCount'));
    }

    public function create()
    {
        $permissions = Permission::all();

        return view('roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name|max:255',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|exists:permissions,name',
        ], [
            'name.required' => __('messages.role_name') . ' ' . (app()->getLocale() == 'en' ? 'is required.' : 'wajib diisi.'),
            'name.unique' => app()->getLocale() == 'en' ? 'Role name already exists.' : 'Nama peran sudah ada di sistem.',
            'name.max' => app()->getLocale() == 'en' ? 'Role name may not exceed 255 characters.' : 'Nama peran maksimal 255 karakter.',
        ]);

        $role = Role::create(['name' => trim($request->name), 'guard_name' => 'web']);

        if ($request->filled('permissions')) {
            $role->syncPermissions($request->input('permissions', []));
        }

        // Ensure Super Admin always retains 100% full permissions
        $superAdmin = Role::where('name', 'Super Admin')->first();
        if ($superAdmin) {
            $superAdmin->syncPermissions(Permission::all());
        }

        // Clear Spatie permission cache
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->route('roles.index')->with('success', __('messages.role_created_success', ['name' => $role->name]));
    }

    public function edit(Role $role)
    {
        if ($role->name === 'Super Admin') {
            return redirect()->route('roles.index')->with('error', __('messages.role_superadmin_protected'));
        }

        $permissions = Permission::all();
        $rolePermissions = $role->permissions->pluck('name')->toArray();

        return view('roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(Request $request, Role $role)
    {
        if ($role->name === 'Super Admin') {
            return redirect()->route('roles.index')->with('error', __('messages.role_superadmin_protected'));
        }

        $request->validate([
            'name' => 'sometimes|required|max:255|unique:roles,name,' . $role->id,
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        if ($request->filled('name') && !in_array($role->name, ['Admin', 'User'])) {
            $role->name = trim($request->name);
            $role->save();
        }

        $permissions = $request->input('permissions', []);

        // Sync permissions for this role
        $role->syncPermissions($permissions);

        // Ensure Super Admin always retains 100% full permissions
        $superAdmin = Role::where('name', 'Super Admin')->first();
        if ($superAdmin) {
            $superAdmin->syncPermissions(Permission::all());
        }

        // Clear Spatie permission cache
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->route('roles.index')->with('success', __('messages.role_updated_success', ['name' => $role->name]));
    }

    public function destroy(Role $role)
    {
        if (in_array($role->name, ['Super Admin', 'Admin', 'User'])) {
            return redirect()->route('roles.index')->with('error', __('messages.role_delete_system_error', ['name' => $role->name]));
        }

        if ($role->users()->count() > 0) {
            return redirect()->route('roles.index')->with('error', __('messages.role_delete_in_use_error', ['name' => $role->name, 'count' => $role->users()->count()]));
        }

        $roleName = $role->name;
        $role->delete();

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->route('roles.index')->with('success', __('messages.role_deleted_success', ['name' => $roleName]));
    }
}
