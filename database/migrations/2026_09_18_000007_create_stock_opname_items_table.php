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
        Schema::create('stock_opname_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('stock_opname_sessions')->cascadeOnDelete();
            $table->foreignId('asset_id')->constrained('assets')->cascadeOnDelete();
            $table->foreignId('expected_location_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->foreignId('scanned_location_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->foreignId('scanned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('scanned_at')->nullable();
            
            $table->enum('status', ['pending', 'matched', 'displaced', 'missing', 'unknown'])->default('pending');
            $table->enum('physical_condition', ['good', 'damaged', 'needs_repair'])->default('good');
            $table->text('notes')->nullable();

            $table->timestamps();
            
            $table->unique(['session_id', 'asset_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_opname_items');
    }
};
