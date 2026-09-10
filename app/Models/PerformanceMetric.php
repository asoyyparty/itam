<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PerformanceMetric extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'route_name',
        'method',
        'url',
        'status_code',
        'duration_ms',
        'db_query_count',
        'db_query_time_ms',
        'memory_peak_mb',
        'slowest_query',
        'user_id',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    protected $casts = [
        'duration_ms' => 'float',
        'db_query_time_ms' => 'float',
        'memory_peak_mb' => 'float',
        'status_code' => 'integer',
        'db_query_count' => 'integer',
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeTimeRange($query, ?string $range = '24h')
    {
        return match ($range) {
            '1h' => $query->where('created_at', '>=', now()->subHour()),
            '7d' => $query->where('created_at', '>=', now()->subDays(7)),
            '30d' => $query->where('created_at', '>=', now()->subDays(30)),
            default => $query->where('created_at', '>=', now()->subHours(24)),
        };
    }

    public function scopeSlowRequests($query, float $thresholdMs = 300.0)
    {
        return $query->where('duration_ms', '>=', $thresholdMs);
    }
}
