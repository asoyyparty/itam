<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Contracts\Console\Kernel;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

app()[PermissionRegistrar::class]->forgetCachedPermissions();

$permissions = [
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

foreach ($permissions as $permission) {
    Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
}

$superAdmin = Role::where('name', 'Super Admin')->first();
if ($superAdmin) {
    $superAdmin->syncPermissions(Permission::all());
}

$adminRole = Role::where('name', 'Admin')->first();
if ($adminRole) {
    $adminRole->syncPermissions(Permission::where('name', '!=', 'menu_roles')->get());
}

$userRole = Role::where('name', 'User')->first();
if ($userRole) {
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
    $userRole->syncPermissions($userPermissions);
}

echo "All feature permissions seeded successfully!\n";
