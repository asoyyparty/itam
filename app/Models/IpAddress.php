<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IpAddress extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'ip_address',
        'mac_address',
        'asset_id',
        'employee_id',
        'vlan_id',
        'gateway',
        'dns',
        'status',
        'is_online',
        'last_ping_at',
        'notes',
    ];

    protected $casts = [
        'is_online' => 'boolean',
        'last_ping_at' => 'datetime',
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function vlan()
    {
        return $this->belongsTo(Vlan::class);
    }

    /**
     * Process state change and dispatch Telegram notifications ONLY ONCE per state transition.
     */
    public function processStateNotification(bool $isOnline, ?string $reason = null): void
    {
        $cacheKey = "ip_notify_state_" . $this->id;
        $previousState = \Illuminate\Support\Facades\Cache::get($cacheKey, $this->is_online ? 'online' : ($this->is_online === false ? 'offline' : 'unknown'));

        if (!$isOnline) {
            // Current status is OFFLINE
            if ($previousState !== 'offline') {
                \Illuminate\Support\Facades\Cache::put($cacheKey, 'offline', now()->addDays(30));
                try {
                    app(\App\Services\TelegramBotService::class)->sendIpOfflineAlert($this, $reason);
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::error('Telegram IP offline alert error: ' . $e->getMessage());
                }
                try {
                    app(\App\Services\WhatsAppService::class)->sendIpOfflineAlert($this, $reason);
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::error('WhatsApp IP offline alert error: ' . $e->getMessage());
                }
            }
        } else {
            // Current status is ONLINE
            if ($previousState === 'offline') {
                \Illuminate\Support\Facades\Cache::put($cacheKey, 'online', now()->addDays(30));
                try {
                    app(\App\Services\TelegramBotService::class)->sendIpOnlineAlert($this);
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::error('Telegram IP online recovery alert error: ' . $e->getMessage());
                }
                try {
                    app(\App\Services\WhatsAppService::class)->sendIpOnlineAlert($this);
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::error('WhatsApp IP online recovery alert error: ' . $e->getMessage());
                }
            } else {
                \Illuminate\Support\Facades\Cache::put($cacheKey, 'online', now()->addDays(30));
            }
        }
    }
}
