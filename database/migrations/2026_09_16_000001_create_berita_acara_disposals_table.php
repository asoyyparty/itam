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
        Schema::create('berita_acara_disposals', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_ba')->unique();
            $table->string('lokasi')->default('Cikande');
            $table->date('tanggal_ba');
            $table->string('hal')->default('Disposal Asset');
            $table->string('lampiran_label')->default('Foto Assets');
            $table->string('kepada_yth')->default('Ka. Bagian, Ka. Accounting, Ka. Asset');
            $table->string('cc')->default('Ka. Pabrik');
            $table->string('form_code')->default('CBA/MTN/F-023');
            $table->string('form_rev')->default('Rev. : 00');
            $table->string('divisi')->default('IT PABRIK');
            $table->text('narasi_pembuka')->nullable();
            
            // Signatories (5 Roles)
            $table->string('nama_pembuat')->default('Qois Abdul Qudus');
            $table->string('jabatan_pembuat')->default('IT Pabrik');
            $table->string('nama_pemeriksa_1')->default('Alex Asmel');
            $table->string('jabatan_pemeriksa_1')->default('HC & GA Head');
            $table->string('nama_pemeriksa_2')->default('Basuki');
            $table->string('jabatan_pemeriksa_2')->default('Ka. Maintenance');
            $table->string('nama_penyetuju_1')->default('Novan Prihasa');
            $table->string('jabatan_penyetuju_1')->default('Factory Head');
            $table->string('nama_penyetuju_2')->default('Agus Sapto');
            $table->string('jabatan_penyetuju_2')->default('Wakil Management');

            // Items & Photo attachments stored as JSON
            $table->json('items')->nullable();
            $table->json('foto_lampiran')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('berita_acara_disposals');
    }
};
