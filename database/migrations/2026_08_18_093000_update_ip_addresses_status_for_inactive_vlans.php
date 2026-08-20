<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Get inactive VLAN IDs
        $inactiveVlanIds = DB::table('vlans')
            ->where('status', '!=', 'Active')
            ->orWhereNull('status')
            ->pluck('id');

        // Update IP addresses attached to inactive VLANs from Used to Available
        DB::table('ip_addresses')
            ->whereIn('vlan_id', $inactiveVlanIds)
            ->where('status', 'Used')
            ->update(['status' => 'Available']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No automated reversal for status data cleanup
    }
};
