<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('document_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('color')->default('#6366f1');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Seed initial default categories
        $defaultCategories = [
            ['name' => 'Berita Acara', 'color' => '#818cf8', 'description' => 'Dokumen Berita Acara IT & Disposal'],
            ['name' => 'Kontrak/SLA', 'color' => '#38bdf8', 'description' => 'Perjanjian Kerjasama & SLA Vendor'],
            ['name' => 'Invoice/PO', 'color' => '#34d399', 'description' => 'Invoice, PO & Bukti Tagihan Pembelian'],
            ['name' => 'Faktur Pajak', 'color' => '#fbbf24', 'description' => 'Dokumen Perpajakan & Faktur'],
            ['name' => 'Garansi/Sertifikat', 'color' => '#f472b6', 'description' => 'Sertifikat & Kartu Garansi Aset'],
            ['name' => 'Dokumen Asset', 'color' => '#a78bfa', 'description' => 'Spesifikasi & Manual Book Aset'],
            ['name' => 'Lain-lain', 'color' => '#94a3b8', 'description' => 'Kategori Dokumen Umum'],
        ];

        $now = now();
        foreach ($defaultCategories as $cat) {
            DB::table('document_categories')->insert([
                'name' => $cat['name'],
                'color' => $cat['color'],
                'description' => $cat['description'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_categories');
    }
};
