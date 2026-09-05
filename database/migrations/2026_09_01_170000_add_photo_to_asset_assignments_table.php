<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('asset_assignments', 'photo')) {
            Schema::table('asset_assignments', function (Blueprint $table) {
                $table->string('photo')->nullable()->after('notes');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('asset_assignments', 'photo')) {
            Schema::table('asset_assignments', function (Blueprint $table) {
                $table->dropColumn('photo');
            });
        }
    }
};
