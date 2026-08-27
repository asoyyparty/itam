<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('operator');
            $table->string('action'); // created, updated, deleted, assigned, returned, maintenance
            $table->string('model_type')->nullable();
            $table->unsignedBigInteger('model_id')->nullable();
            $table->string('target_name');
            $table->text('details')->nullable(); // JSON metadata
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });

        // 1. Backfill past asset assignments
        try {
            if (Schema::hasTable('asset_assignments')) {
                $assignments = DB::table('asset_assignments')->get();
                foreach ($assignments as $a) {
                    $asset = DB::table('assets')->where('id', $a->asset_id)->first();
                    $employee = DB::table('employees')->where('id', $a->employee_id)->first();
                    
                    $assetName = $asset ? "{$asset->name} ({$asset->asset_tag})" : 'Unknown Asset';
                    $employeeName = $employee ? $employee->name : 'Unknown Employee';

                    DB::table('activity_logs')->insert([
                        'user_id' => null,
                        'operator' => 'Admin',
                        'action' => $a->status === 'Assigned' ? 'assigned' : 'returned',
                        'model_type' => 'App\\Models\\AssetAssignment',
                        'model_id' => $a->id,
                        'target_name' => $assetName,
                        'details' => json_encode(['employee_name' => $employeeName]),
                        'created_at' => $a->created_at ?: Carbon::now(),
                        'updated_at' => $a->updated_at ?: Carbon::now(),
                    ]);
                }
            }
        } catch (\Exception $e) {
            // Silence if fails
        }

        // 2. Backfill past maintenances
        try {
            if (Schema::hasTable('maintenances')) {
                $maintenances = DB::table('maintenances')->get();
                foreach ($maintenances as $m) {
                    $asset = DB::table('assets')->where('id', $m->asset_id)->first();
                    $assetName = $asset ? "{$asset->name} ({$asset->asset_tag})" : 'Unknown Asset';

                    DB::table('activity_logs')->insert([
                        'user_id' => null,
                        'operator' => 'IT Support',
                        'action' => 'maintenance',
                        'model_type' => 'App\\Models\\Maintenance',
                        'model_id' => $m->id,
                        'target_name' => $assetName,
                        'details' => null,
                        'created_at' => $m->created_at ?: Carbon::now(),
                        'updated_at' => $m->updated_at ?: Carbon::now(),
                    ]);
                }
            }
        } catch (\Exception $e) {
            // Silence if fails
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
