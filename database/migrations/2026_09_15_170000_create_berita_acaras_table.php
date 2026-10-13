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
        Schema::create('berita_acaras', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_surat')->index(); // No SJ (e.g. 045/LGT/VII/2026)
            $table->string('nomor_po')->nullable()->index(); // No PO (e.g. 2601436)
            $table->date('tanggal_surat')->nullable(); // Tanggal di Surat Jalan
            $table->string('kepada')->nullable(); // Kepada (e.g. Abdul / Bagas (IT Pabrik))
            $table->string('supir_kendaraan')->nullable(); // Supir / No Kendaraan
            $table->string('nama_penerima')->nullable(); // Penerima / Verifikator (e.g. Bagas Purnomo)
            $table->string('unit_penerima')->nullable()->default('IT Pabrik'); // Unit (e.g. IT Pabrik)
            $table->date('tanggal_verifikasi')->nullable(); // Tanggal TTB / OK (e.g. 2026-07-22)
            $table->string('status_verifikasi')->default('TTB / LGT ok'); // Status / Stamp (e.g. TTB / LGT ok)
            $table->json('items'); // Daftar item: [{no, kode, nama_item, uom, quantity, keterangan}]
            $table->string('file_lampiran')->nullable(); // File scan / foto asli
            $table->text('catatan')->nullable(); // Catatan tambahan
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('berita_acaras');
    }
};
