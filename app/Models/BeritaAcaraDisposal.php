<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BeritaAcaraDisposal extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'berita_acara_disposals';

    protected $fillable = [
        'nomor_ba',
        'lokasi',
        'tanggal_ba',
        'hal',
        'lampiran_label',
        'kepada_yth',
        'cc',
        'form_code',
        'form_rev',
        'divisi',
        'narasi_pembuka',
        'nama_pembuat',
        'jabatan_pembuat',
        'nama_pemeriksa_1',
        'jabatan_pemeriksa_1',
        'nama_pemeriksa_2',
        'jabatan_pemeriksa_2',
        'nama_penyetuju_1',
        'jabatan_penyetuju_1',
        'nama_penyetuju_2',
        'jabatan_penyetuju_2',
        'items',
        'foto_lampiran',
        'created_by',
    ];

    protected $casts = [
        'items' => 'array',
        'foto_lampiran' => 'array',
        'tanggal_ba' => 'date',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Hitung total kuantitas aset yang didisposal
     */
    public function getTotalQuantityAttribute(): int
    {
        if (!is_array($this->items)) {
            return 0;
        }

        return array_reduce($this->items, function ($carry, $item) {
            return $carry + (int) ($item['qty'] ?? ($item['quantity'] ?? 0));
        }, 0);
    }

    /**
     * Generate Nomor BA Disposal otomatis berikutnya
     */
    public static function generateNextNomorBa(): string
    {
        $currentMonth = date('m');
        $currentYear = date('Y');

        $latest = self::whereYear('tanggal_ba', $currentYear)
            ->whereMonth('tanggal_ba', $currentMonth)
            ->orderBy('id', 'desc')
            ->first();

        $sequence = 1;
        if ($latest && preg_match('/^(\d+)\/BA\/Disposal\//i', $latest->nomor_ba, $matches)) {
            $sequence = (int) $matches[1] + 1;
        } else {
            $sequence = self::whereYear('tanggal_ba', $currentYear)->count() + 1;
        }

        $seqStr = str_pad($sequence, 2, '0', STR_PAD_LEFT);
        return "{$seqStr}/BA/{$currentMonth}/{$currentYear}/IT";
    }

    /**
     * Default narasi pembuka sesuai standar CBA/MTN/F-023
     */
    public static function defaultPreamble(): string
    {
        return "Berdasarkan pemeriksaan dari team IT pabrik, diketahui bahwa terdapat kerusakan pada beberapa asset IT. Kerusakan pada unit tersebut tidak bisa diperbaiki, maka dari itu diperlukan tindakan jual berat pada aset tersebut. Keterangan kerusakan dan penjelasan asset dicantumkan pada halaman lampiran.";
    }
}
