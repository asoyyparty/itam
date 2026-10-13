<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $permissionName = 'menu_stock_opname';
        
        $permission = Permission::firstOrCreate([
            'name' => $permissionName,
            'guard_name' => 'web'
        ]);

        // Assign to Super Admin & Admin roles if exist
        $adminRoles = Role::whereIn('name', ['Super Admin', 'Admin', 'IT Support', 'Staff IT'])->get();
        foreach ($adminRoles as $role) {
            $role->givePermissionTo($permission);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Permission::where('name', 'menu_stock_opname')->delete();
    }
};
