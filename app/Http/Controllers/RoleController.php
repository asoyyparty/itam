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

        return view('roles.index', compact('roles'));
    }

    public function edit(Role $role)
    {
        if ($role->name === 'Super Admin') {
            return redirect()->route('roles.index')->with('error', 'Peran Super Admin memiliki akses penuh ke seluruh fitur sistem dan tidak dapat diubah.');
        }

        $permissions = Permission::all();
        $rolePermissions = $role->permissions->pluck('name')->toArray();

        return view('roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(Request $request, Role $role)
    {
        if ($role->name === 'Super Admin') {
            return redirect()->route('roles.index')->with('error', 'Peran Super Admin memiliki akses penuh dan tidak dapat diubah.');
        }

        $request->validate([
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

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

        return redirect()->route('roles.index')->with('success', "Hak akses untuk peran '{$role->name}' berhasil diperbarui.");
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name|max:255',
        ], [
            'name.required' => 'Nama peran wajib diisi.',
            'name.unique' => 'Nama peran sudah ada di sistem.',
            'name.max' => 'Nama peran maksimal 255 karakter.',
        ]);

        $role = Role::create(['name' => trim($request->name), 'guard_name' => 'web']);

        return redirect()->route('roles.index')->with('success', "Peran baru '{$role->name}' berhasil ditambahkan.");
    }

    public function destroy(Role $role)
    {
        if (in_array($role->name, ['Super Admin', 'Admin', 'User'])) {
            return redirect()->route('roles.index')->with('error', "Peran sistem '{$role->name}' tidak dapat dihapus.");
        }

        if ($role->users()->count() > 0) {
            return redirect()->route('roles.index')->with('error', "Peran '{$role->name}' tidak dapat dihapus karena masih digunakan oleh {$role->users()->count()} pengguna.");
        }

        $roleName = $role->name;
        $role->delete();

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->route('roles.index')->with('success', "Peran '{$roleName}' berhasil dihapus.");
    }
}
