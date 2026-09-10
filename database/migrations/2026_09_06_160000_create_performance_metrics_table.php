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
        if (!Schema::hasTable('performance_metrics')) {
            Schema::create('performance_metrics', function (Blueprint $table) {
                $table->id();
                $table->string('route_name', 120)->nullable()->index();
                $table->string('method', 10);
                $table->string('url', 500);
                $table->unsignedSmallInteger('status_code')->default(200)->index();
                $table->float('duration_ms', 10, 2)->index();
                $table->unsignedInteger('db_query_count')->default(0);
                $table->float('db_query_time_ms', 10, 2)->default(0);
                $table->float('memory_peak_mb', 8, 2)->default(0);
                $table->text('slowest_query')->nullable();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('ip_address', 45)->nullable();
                $table->string('user_agent', 255)->nullable();
                $table->timestamp('created_at')->nullable()->index();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('performance_metrics');
    }
};
