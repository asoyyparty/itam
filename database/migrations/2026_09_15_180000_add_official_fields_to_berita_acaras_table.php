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
        Schema::table('berita_acaras', function (Blueprint $table) {
            $table->string('nomor_ba')->nullable()->after('id'); // e.g. 16/BA/IT/06/2026
            $table->string('lokasi')->default('Cikande')->after('nomor_ba');
            $table->string('hal')->default('Pemberitahuan Barang/Jasa OK')->after('lokasi');
            $table->string('lampiran')->nullable()->default('-')->after('hal');
            $table->string('kepada_yth')->default('Ka. Pembelian')->after('lampiran');
            $table->string('cc')->default('Ka. Pabrik, Ka. Keuangan')->after('kepada_yth');
            $table->string('nama_perusahaan')->default('PT. CBA Chemical Industry Pabrik')->after('cc');
            $table->string('mengetahui_1')->default('Martin')->after('unit_penerima');
            $table->string('mengetahui_2')->default('Edi Awaludin')->after('mengetahui_1');
            $table->string('menyetujui_1')->default('Herinata')->after('mengetahui_2');
            $table->string('menyetujui_2')->default('David')->after('menyetujui_1');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('berita_acaras', function (Blueprint $table) {
            $table->dropColumn([
                'nomor_ba',
                'lokasi',
                'hal',
                'lampiran',
                'kepada_yth',
                'cc',
                'nama_perusahaan',
                'mengetahui_1',
                'mengetahui_2',
                'menyetujui_1',
                'menyetujui_2',
            ]);
        });
    }
};
