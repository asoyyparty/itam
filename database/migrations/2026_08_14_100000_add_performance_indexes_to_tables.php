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
        Schema::table('assets', function (Blueprint $table) {
            $table->index('status', 'idx_assets_status');
            $table->index('date_received', 'idx_assets_date_received');
            $table->index('name', 'idx_assets_name');
            $table->index('serial_number', 'idx_assets_serial_number');
        });

        Schema::table('ip_addresses', function (Blueprint $table) {
            $table->index('status', 'idx_ip_status');
            $table->index('is_online', 'idx_ip_is_online');
            $table->index('ip_address', 'idx_ip_address');
        });

        Schema::table('tickets', function (Blueprint $table) {
            $table->index('status', 'idx_tickets_status');
            $table->index('priority', 'idx_tickets_priority');
        });

        Schema::table('asset_assignments', function (Blueprint $table) {
            $table->index('status', 'idx_assignments_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropIndex('idx_assets_status');
            $table->dropIndex('idx_assets_date_received');
            $table->dropIndex('idx_assets_name');
            $table->dropIndex('idx_assets_serial_number');
        });

        Schema::table('ip_addresses', function (Blueprint $table) {
            $table->dropIndex('idx_ip_status');
            $table->dropIndex('idx_ip_is_online');
            $table->dropIndex('idx_ip_address');
        });

        Schema::table('tickets', function (Blueprint $table) {
            $table->dropIndex('idx_tickets_status');
            $table->dropIndex('idx_tickets_priority');
        });

        Schema::table('asset_assignments', function (Blueprint $table) {
            $table->dropIndex('idx_assignments_status');
        });
    }
};
