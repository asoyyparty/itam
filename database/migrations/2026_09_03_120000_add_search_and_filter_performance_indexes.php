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
        Schema::table('maintenances', function (Blueprint $table) {
            $table->index('status', 'idx_maint_status');
            $table->index('type', 'idx_maint_type');
            $table->index('start_date', 'idx_maint_start_date');
            $table->index('asset_id', 'idx_maint_asset_id');
        });

        Schema::table('asset_assignments', function (Blueprint $table) {
            $table->index('status', 'idx_assign_status');
            $table->index('assigned_date', 'idx_assign_assigned_date');
            $table->index('return_date', 'idx_assign_return_date');
            $table->index('asset_id', 'idx_assign_asset_id');
            $table->index('employee_id', 'idx_assign_employee_id');
        });

        Schema::table('assets', function (Blueprint $table) {
            $table->index('status', 'idx_assets_status');
            $table->index('category_id', 'idx_assets_category_id');
            $table->index('brand_id', 'idx_assets_brand_id');
            $table->index('location_id', 'idx_assets_location_id');
            $table->index('department_id', 'idx_assets_department_id');
        });

        Schema::table('tickets', function (Blueprint $table) {
            $table->index('status', 'idx_tickets_status');
            $table->index('priority', 'idx_tickets_priority');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('maintenances', function (Blueprint $table) {
            $table->dropIndex('idx_maint_status');
            $table->dropIndex('idx_maint_type');
            $table->dropIndex('idx_maint_start_date');
            $table->dropIndex('idx_maint_asset_id');
        });

        Schema::table('asset_assignments', function (Blueprint $table) {
            $table->dropIndex('idx_assign_status');
            $table->dropIndex('idx_assign_assigned_date');
            $table->dropIndex('idx_assign_return_date');
            $table->dropIndex('idx_assign_asset_id');
            $table->dropIndex('idx_assign_employee_id');
        });

        Schema::table('assets', function (Blueprint $table) {
            $table->dropIndex('idx_assets_status');
            $table->dropIndex('idx_assets_category_id');
            $table->dropIndex('idx_assets_brand_id');
            $table->dropIndex('idx_assets_location_id');
            $table->dropIndex('idx_assets_department_id');
        });

        Schema::table('tickets', function (Blueprint $table) {
            $table->dropIndex('idx_tickets_status');
            $table->dropIndex('idx_tickets_priority');
        });
    }
};
