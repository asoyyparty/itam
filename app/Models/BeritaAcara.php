<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BeritaAcara extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'berita_acaras';

    protected $fillable = [
        'nomor_ba',
        'lokasi',
        'hal',
        'lampiran',
        'kepada_yth',
        'cc',
        'nama_perusahaan',
        'nomor_surat',
        'nomor_po',
        'tanggal_surat',
        'kepada',
        'supir_kendaraan',
        'nama_penerima',
        'unit_penerima',
        'mengetahui_1',
        'mengetahui_2',
        'menyetujui_1',
        'menyetujui_2',
        'tanggal_verifikasi',
        'status_verifikasi',
        'items',
        'file_lampiran',
        'catatan',
        'created_by',
    ];

    protected $casts = [
        'items' => 'array',
        'tanggal_surat' => 'date',
        'tanggal_verifikasi' => 'date',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Hitung total kuantitas barang
     */
    public function getTotalQuantityAttribute(): int
    {
        if (!is_array($this->items)) {
            return 0;
        }

        return array_sum(array_column($this->items, 'quantity'));
    }

    /**
     * Hitung total jenis item barang
     */
    public function getTotalItemsCountAttribute(): int
    {
        return is_array($this->items) ? count($this->items) : 0;
    }

    /**
     * Generate format nomor berita acara berikutnya (e.g. 16/BA/IT/06/2026)
     */
    public static function generateNextNomorBa(): string
    {
        $count = static::withTrashed()->count() + 1;
        $month = date('m');
        $year = date('Y');
        return sprintf('%02d/BA/IT/%s/%s', $count, $month, $year);
    }
}
