<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('ip_addresses', function (Blueprint $table) {
            $table->index('employee_id', 'idx_ip_employee_id');
            $table->index('vlan_id', 'idx_ip_vlan_id');
            $table->index('asset_id', 'idx_ip_asset_id');
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->index('name', 'idx_emp_name');
            $table->index('employee_id', 'idx_emp_nik');
            $table->index('department_id', 'idx_emp_dept_id');
            $table->index('status', 'idx_emp_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ip_addresses', function (Blueprint $table) {
            $table->dropIndex('idx_ip_employee_id');
            $table->dropIndex('idx_ip_vlan_id');
            $table->dropIndex('idx_ip_asset_id');
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->dropIndex('idx_emp_name');
            $table->dropIndex('idx_emp_nik');
            $table->dropIndex('idx_emp_dept_id');
            $table->dropIndex('idx_emp_status');
        });
    }
};
