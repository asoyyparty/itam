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
        if (!Schema::hasColumn('users', 'avatar')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('avatar')->nullable()->after('email');
            });
        }

        if (!Schema::hasColumn('employees', 'photo')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->string('photo')->nullable()->after('phone');
            });
        }

        if (!Schema::hasColumn('assets', 'image')) {
            Schema::table('assets', function (Blueprint $table) {
                $table->string('image')->nullable()->after('notes');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('users', 'avatar')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('avatar');
            });
        }

        if (Schema::hasColumn('employees', 'photo')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->dropColumn('photo');
            });
        }

        if (Schema::hasColumn('assets', 'image')) {
            Schema::table('assets', function (Blueprint $table) {
                $table->dropColumn('image');
            });
        }
    }
};
