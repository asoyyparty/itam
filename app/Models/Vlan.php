<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vlan extends Model
{

    protected $fillable = [
        'vlan_number',
        'name',
        'subnet',
        'gateway',
        'status',
        'notes',
    ];

    public function ipAddresses()
    {
        return $this->hasMany(IpAddress::class);
    }
}
