<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockOpnameSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_code',
        'title',
        'location_id',
        'category_id',
        'status',
        'started_at',
        'completed_at',
        'created_by',
        'notes',
        'total_expected',
        'total_matched',
        'total_displaced',
        'total_missing',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items()
    {
        return $this->hasMany(StockOpnameItem::class, 'session_id');
    }

    public function getProgressPercentageAttribute()
    {
        if ($this->total_expected <= 0) return 0;
        $scanned = $this->total_matched + $this->total_displaced;
        return min(100, round(($scanned / $this->total_expected) * 100));
    }

    public function recalculateStats()
    {
        $this->total_expected = $this->items()->count();
        $this->total_matched = $this->items()->where('status', 'matched')->count();
        $this->total_displaced = $this->items()->where('status', 'displaced')->count();
        $this->total_missing = $this->items()->where('status', 'missing')->count();
        $this->save();
    }
}
