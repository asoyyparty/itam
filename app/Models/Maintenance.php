<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Maintenance extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_id',
        'type',
        'description',
        'cost',
        'start_date',
        'end_date',
        'status',
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }
}
