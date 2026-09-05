<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SoftwareLicense extends Model
{

    protected $fillable = [
        'name',
        'license_key',
        'expiry_date',
        'total_seats',
        'pic_id',
        'notes',
    ];

    protected $casts = [
        'expiry_date' => 'date',
    ];

    public function pic()
    {
        return $this->belongsTo(Employee::class, 'pic_id');
    }
}
