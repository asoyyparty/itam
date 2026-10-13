<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockOpnameItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'asset_id',
        'expected_location_id',
        'scanned_location_id',
        'scanned_by',
        'scanned_at',
        'status',
        'physical_condition',
        'notes',
    ];

    protected $casts = [
        'scanned_at' => 'datetime',
    ];

    public function session()
    {
        return $this->belongsTo(StockOpnameSession::class, 'session_id');
    }

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function expectedLocation()
    {
        return $this->belongsTo(Location::class, 'expected_location_id');
    }

    public function scannedLocation()
    {
        return $this->belongsTo(Location::class, 'scanned_location_id');
    }

    public function scanner()
    {
        return $this->belongsTo(User::class, 'scanned_by');
    }
}
