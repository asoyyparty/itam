<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $allPermissions = [
            'menu_dashboard',
            'menu_assets',
            'menu_assignments',
            'menu_tickets',
            'menu_maintenances',
            'menu_predictive_health',
            'menu_departments',
            'menu_brands',
            'menu_locations',
            'menu_categories',
            'menu_vendors',
            'menu_employees',
            'menu_users',
            'menu_vlans',
            'menu_ips',
            'menu_network_anomalies',
            'menu_software_licenses',
            'menu_password_vaults',
            'menu_budget_planner',
            'menu_settings',
            'menu_roles',
            'menu_pics',
            'action_ocr_scan',
            'action_ai_assistant',
            'action_export_excel',
            'action_import_excel',
        ];

        foreach ($allPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Assign all permissions to Super Admin and Admin
        $superAdmin = Role::where('name', 'Super Admin')->first();
        if ($superAdmin) {
            $superAdmin->syncPermissions(Permission::all());
        }

        $admin = Role::where('name', 'Admin')->first();
        if ($admin) {
            $admin->syncPermissions(Permission::where('name', '!=', 'menu_roles')->get());
        }

        // Assign standard user permissions
        $user = Role::where('name', 'User')->first();
        if ($user) {
            $userPermissions = [
                'menu_dashboard',
                'menu_assets',
                'menu_assignments',
                'menu_tickets',
                'menu_maintenances',
                'menu_ips',
                'menu_predictive_health',
                'menu_network_anomalies',
                'action_ai_assistant',
            ];
            $user->syncPermissions($userPermissions);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Permission::whereIn('name', [
            'menu_predictive_health',
            'menu_network_anomalies',
            'menu_budget_planner',
            'action_ocr_scan',
            'action_ai_assistant',
            'action_export_excel',
            'action_import_excel',
        ])->delete();
    }
};
