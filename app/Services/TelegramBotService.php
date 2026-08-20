<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\Ticket;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramBotService
{
    protected string $token;
    protected string $defaultChatId;
    protected string $ipToken;
    protected string $ipChatId;
    protected bool $enabled;
    protected string $baseUrl;

    public function __construct()
    {
        try {
            $this->token = \App\Models\Setting::where('key', 'telegram_bot_token')->value('value') ?: env('TELEGRAM_BOT_TOKEN', '');
            $this->defaultChatId = \App\Models\Setting::where('key', 'telegram_chat_id')->value('value') ?: env('TELEGRAM_ADMIN_CHAT_ID', '');
            
            $ipTokenSetting = \App\Models\Setting::where('key', 'telegram_ip_bot_token')->value('value');
            $this->ipToken = !empty($ipTokenSetting) ? $ipTokenSetting : $this->token;

            $ipChatIdSetting = \App\Models\Setting::where('key', 'telegram_ip_chat_id')->value('value');
            $this->ipChatId = !empty($ipChatIdSetting) ? $ipChatIdSetting : $this->defaultChatId;

            $enabledSetting = \App\Models\Setting::where('key', 'telegram_enabled')->value('value');
            $this->enabled = $enabledSetting !== null ? (bool)$enabledSetting : (bool) env('TELEGRAM_BOT_ENABLED', true);
        } catch (\Exception $e) {
            $this->token = env('TELEGRAM_BOT_TOKEN', '');
            $this->defaultChatId = env('TELEGRAM_ADMIN_CHAT_ID', '');
            $this->ipToken = $this->token;
            $this->ipChatId = $this->defaultChatId;
            $this->enabled = (bool) env('TELEGRAM_BOT_ENABLED', true);
        }
        $this->baseUrl = "https://api.telegram.org/bot{$this->token}";
    }

    /**
     * Send raw message to a Telegram Chat ID
     */
    public function sendMessage(string|int $chatId, string $text, ?array $replyMarkup = null, ?string $customToken = null): bool
    {
        $activeToken = !empty($customToken) ? $customToken : $this->token;

        if (!$this->enabled || empty($activeToken)) {
            Log::info("Telegram Bot API disabled or token empty. Message payload: {$text}");
            return false;
        }

        try {
            $targetChat = (string) ($chatId ?: $this->defaultChatId);
            if (!empty($targetChat) && !is_numeric($targetChat) && !str_starts_with($targetChat, '@') && !str_starts_with($targetChat, '-')) {
                $targetChat = '@' . $targetChat;
            }

            $payload = [
                'chat_id' => $targetChat,
                'text' => $text,
                'parse_mode' => 'HTML',
                'disable_web_page_preview' => true,
            ];

            if ($replyMarkup) {
                $payload['reply_markup'] = json_encode($replyMarkup);
            }

            usleep(300000);

            $endpoint = "https://api.telegram.org/bot{$activeToken}/sendMessage";
            $response = Http::timeout(10)->post($endpoint, $payload);

            if ($response->status() === 429) {
                $retryAfter = (int) ($response->json('parameters.retry_after') ?? 2);
                Log::warning("Telegram 429 Rate Limit. Pausing for {$retryAfter}s...");
                sleep(min($retryAfter, 3));
                $response = Http::timeout(10)->post($endpoint, $payload);
            }

            if ($response->failed()) {
                Log::error("Telegram API Error: " . $response->body());
                return false;
            }

            return true;
        } catch (\Exception $e) {
            Log::error("Telegram Connection Failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send Rogue Device / IP Anomaly Alert (Simple Format)
     */
    public function sendRogueAlert(array $anom, ?string $chatId = null): bool
    {
        $targetChat = $chatId ?: $this->ipChatId;
        if (!$targetChat) return false;

        $msg = "🚨 <b>ANOMALI JARINGAN</b>\n\n";
        $msg .= "<b>Jenis:</b> " . htmlspecialchars($anom['type'] ?? 'Rogue Device') . "\n";
        $msg .= "<b>IP:</b> <code>" . htmlspecialchars($anom['ip_address'] ?? '-') . "</code>\n";
        $msg .= "<b>MAC:</b> <code>" . htmlspecialchars($anom['mac_address'] ?? '-') . "</code>\n";
        $msg .= "<b>VLAN:</b> " . htmlspecialchars($anom['vlan'] ?? '-') . "\n";
        $msg .= "<b>Severity:</b> " . htmlspecialchars(strtoupper($anom['severity'] ?? 'CRITICAL')) . "\n";
        $msg .= "<b>Waktu:</b> 🕒 " . date('d M, H:i') . " WIB\n";

        $keyboard = [
            'inline_keyboard' => [
                [
                    ['text' => '✅ Resolve', 'callback_data' => 'resolve_anom_' . ($anom['ip_id'] ?? 0)],
                    ['text' => '➕ Register', 'callback_data' => 'register_rogue_' . ($anom['ip_id'] ?? 0)],
                ]
            ]
        ];

        return $this->sendMessage($targetChat, $msg, $keyboard, $this->ipToken);
    }

    /**
     * Send Critical Asset Health Score Alert
     */
    public function sendCriticalHealthAlert(Asset $asset, array $health, ?string $chatId = null): bool
    {
        $targetChat = $chatId ?: $this->defaultChatId;
        if (!$targetChat) return false;

        $msg = "<b>🩺 ITAM AI HEALTH AUDIT ALERT 🩺</b>\n\n";
        $msg .= "<b>Aset Tag:</b> <code>" . htmlspecialchars($asset->asset_tag) . "</code>\n";
        $msg .= "<b>Nama Aset:</b> " . htmlspecialchars($asset->name) . "\n";
        $msg .= "<b>Kategori:</b> " . htmlspecialchars($asset->category->name ?? '-') . "\n";
        $msg .= "<b>Skor Kesehatan:</b> 🔴 <b>" . ($health['health_score'] ?? 0) . "% (CRITICAL)</b>\n\n";
        $msg .= "<b>Rekomendasi AI:</b>\n" . htmlspecialchars($health['recommendation'] ?? '-') . "\n";

        if (!empty($health['reasons'])) {
            $msg .= "\n<b>Faktor Risiko Detected:</b>\n";
            foreach ($health['reasons'] as $reason) {
                $msg .= "• " . htmlspecialchars($reason) . "\n";
            }
        }

        return $this->sendMessage($targetChat, $msg);
    }

    /**
     * Send New Ticket Notification
     */
    public function sendNewTicketNotification(Ticket $ticket, ?string $chatId = null): bool
    {
        $targetChat = $chatId ?: $this->defaultChatId;
        if (!$targetChat) return false;

        $msg = "<b>🎫 TIKET HELPDESK BARU 🎫</b>\n\n";
        $msg .= "<b>No Tiket:</b> <code>" . htmlspecialchars($ticket->ticket_number) . "</code>\n";
        $msg .= "<b>Judul:</b> " . htmlspecialchars($ticket->title) . "\n";
        $msg .= "<b>Pelapor:</b> " . htmlspecialchars($ticket->employee->name ?? 'User') . "\n";
        $msg .= "<b>Kategori:</b> " . htmlspecialchars($ticket->category ?? 'General') . "\n";
        $msg .= "<b>Prioritas:</b> " . htmlspecialchars($ticket->priority ?? 'Medium') . "\n\n";
        $msg .= "<b>Deskripsi:</b>\n" . htmlspecialchars($ticket->description ?? '-') . "\n";

        $keyboard = [
            'inline_keyboard' => [
                [
                    ['text' => '⚙️ In Progress', 'callback_data' => 'ticket_progress_' . $ticket->id],
                    ['text' => '✅ Selesaikan (Resolved)', 'callback_data' => 'ticket_resolve_' . $ticket->id],
                ]
            ]
        ];

        return $this->sendMessage($targetChat, $msg, $keyboard);
    }

    /**
     * Resolve VLAN label accurately even if vlan_name is empty or vlan_id is unassigned
     */
    protected function formatVlanInfo(?\App\Models\IpAddress $ip): string
    {
        if (!$ip) return 'Default Subnet';

        $vlan = $ip->vlan;

        if (!$vlan && !empty($ip->ip_address)) {
            $ipParts = explode('.', $ip->ip_address);
            if (count($ipParts) === 4) {
                $subnetPrefix = "{$ipParts[0]}.{$ipParts[1]}.{$ipParts[2]}.";
                $vlan = \App\Models\Vlan::where('subnet', 'LIKE', "{$subnetPrefix}%")->first();
            }
        }

        if ($vlan) {
            $name = trim((string)($vlan->vlan_name ?? ''));
            $num = $vlan->vlan_number ?? '';
            $subnet = $vlan->subnet ?? '';

            if (!empty($name) && !empty($num)) {
                return "VLAN {$num} - {$name}";
            } elseif (!empty($num)) {
                return "VLAN {$num}" . (!empty($subnet) ? " ({$subnet})" : '');
            } elseif (!empty($name)) {
                return $name;
            } elseif (!empty($subnet)) {
                return "Subnet {$subnet}";
            }
        }

        if (!empty($ip->ip_address)) {
            $parts = explode('.', $ip->ip_address);
            if (count($parts) === 4) {
                return "Subnet {$parts[0]}.{$parts[1]}.{$parts[2]}.0/24";
            }
        }

        return 'Default Subnet';
    }

    /**
     * Send IP Offline Alert to Telegram (Detailed Network Monitoring)
     */
    public function sendIpOfflineAlert(\App\Models\IpAddress $ip, ?string $reason = null, ?string $chatId = null): bool
    {
        $targetChat = $chatId ?: $this->ipChatId;
        if (!$targetChat) return false;

        $ip->loadMissing(['asset', 'employee', 'vlan']);

        $assetInfo = $ip->asset ? "{$ip->asset->name} (Tag: {$ip->asset->asset_tag})" : 'Perangkat Tidak Terdaftar';
        $userInfo = $ip->employee ? $ip->employee->name : '-';
        $vlanInfo = $this->formatVlanInfo($ip);

        $msg = "🔴 <b>TELEGRAM NETWORK ALERT: IP OFFLINE</b> 🔴\n\n";
        $msg .= "<b>Alamat IP:</b> <code>" . htmlspecialchars($ip->ip_address) . "</code>\n";
        $msg .= "<b>MAC Address:</b> <code>" . htmlspecialchars($ip->mac_address ?? '-') . "</code>\n";
        $msg .= "<b>Subnet / VLAN:</b> " . htmlspecialchars($vlanInfo) . "\n";
        $msg .= "<b>Aset Terikat:</b> " . htmlspecialchars($assetInfo) . "\n";
        $msg .= "<b>Pengguna:</b> " . htmlspecialchars($userInfo) . "\n";
        $msg .= "<b>Waktu Terdeteksi:</b> " . date('Y-m-d H:i:s') . "\n";
        if ($reason) {
            $msg .= "<b>Catatan:</b> " . htmlspecialchars($reason) . "\n";
        }
        $msg .= "\n⚠️ <i>Perangkat tidak merespon Ping ICMP / Koneksi Terputus dari jaringan!</i>";

        $keyboard = [
            'inline_keyboard' => [
                [
                    ['text' => '🔄 Ping Ulang IP', 'callback_data' => 'reping_ip_' . $ip->id],
                    ['text' => '🔍 Detail IP', 'callback_data' => 'ip_info_' . $ip->id],
                ]
            ]
        ];

        return $this->sendMessage($targetChat, $msg, $keyboard, $this->ipToken);
    }

    /**
     * Send IP Online (Recovery) Alert to Telegram (Detailed Network Monitoring)
     */
    public function sendIpOnlineAlert(\App\Models\IpAddress $ip, ?string $chatId = null): bool
    {
        $targetChat = $chatId ?: $this->ipChatId;
        if (!$targetChat) return false;

        $ip->loadMissing(['asset.category', 'employee.department', 'vlan']);

        $assetName = $ip->asset ? $ip->asset->name : null;
        $assetTag = $ip->asset ? $ip->asset->asset_tag : null;
        $assetCategory = $ip->asset && $ip->asset->category ? $ip->asset->category->name : null;

        $assetInfo = $assetName ? "{$assetName} (Tag: {$assetTag})" . ($assetCategory ? " [{$assetCategory}]" : '') : 'Perangkat Tidak Terdaftar';
        $userInfo = $ip->employee ? $ip->employee->name . ($ip->employee->department ? " ({$ip->employee->department->name})" : '') : 'Tidak Ada Pengguna';
        $vlanInfo = $this->formatVlanInfo($ip);
        $hostname = $ip->hostname ?: ($assetName ?: '-');

        $msg = "<b>🟢 RECOVERY ALERT: IP RECONNECTED / ONLINE 🟢</b>\n\n";
        $msg .= "<b>Alamat IP:</b> <code>" . htmlspecialchars($ip->ip_address) . "</code>\n";
        $msg .= "<b>Status Alokasi:</b> <code>" . htmlspecialchars($ip->status ?? 'Used') . "</code>\n";
        $msg .= "<b>Hostname / Device:</b> " . htmlspecialchars($hostname) . "\n";
        $msg .= "<b>MAC Address:</b> <code>" . htmlspecialchars($ip->mac_address ?? '-') . "</code>\n";
        $msg .= "<b>Subnet / VLAN:</b> " . htmlspecialchars($vlanInfo) . "\n";
        $msg .= "<b>Aset Terikat:</b> " . htmlspecialchars($assetInfo) . "\n";
        $msg .= "<b>Pengguna Terkait:</b> 👤 " . htmlspecialchars($userInfo) . "\n";
        $msg .= "<b>Waktu Pemulihan:</b> 🕒 " . date('d M Y, H:i:s') . " WIB\n";
        $msg .= "\n✅ <i>Perangkat telah terhubung kembali ke jaringan dan merespon ICMP Ping dengan normal.</i>";

        $keyboard = [
            'inline_keyboard' => [
                [
                    ['text' => '🔍 Detail IP', 'callback_data' => 'ip_info_' . $ip->id],
                ]
            ]
        ];

        return $this->sendMessage($targetChat, $msg, $keyboard, $this->ipToken);
    }

    /**
     * Register Bot Webhook Endpoint with Telegram API
     */
    public function setWebhook(string $webhookUrl): array
    {
        if (empty($this->token)) {
            return ['success' => false, 'message' => 'Telegram Bot Token is empty.'];
        }

        try {
            $response = Http::post("{$this->baseUrl}/setWebhook", [
                'url' => $webhookUrl
            ]);

            return [
                'success' => $response->successful(),
                'message' => $response->json('description') ?? 'Webhook request sent.'
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    /**
     * Send Activity Log Notification with detailed field diffs & metadata
     */
    public function sendActivityLogNotification(\App\Models\ActivityLog $log): bool
    {
        $targetChat = $this->defaultChatId;
        if (!$targetChat || !$this->enabled) return false;

        $actionIcon = match ($log->action) {
            'created' => '🟢 DATA DITAMBAHKAN',
            'updated' => '🟡 DATA DIPERBARUI',
            'deleted' => '🔴 DATA DIHAPUS',
            'assigned' => '🤝 PENYERAHAN ASSET',
            'returned' => '🔄 PENGEMBALIAN ASSET',
            'maintenance' => '🛠️ MAINTENANCE ASSET',
            default => '⚪ AKTIVITAS SISTEM',
        };

        $modelBasename = class_basename($log->model_type);
        $moduleName = match ($modelBasename) {
            'Asset' => '💻 Asset Hardware',
            'AssetAssignment' => '📋 Penyerahan Asset',
            'Maintenance' => '🛠️ Maintenance Asset',
            'Ticket' => '🎫 Ticket Helpdesk',
            'IpAddress' => '🌐 IP Address',
            'SoftwareLicense' => '🔑 Lisensi Software',
            'PasswordVault' => '🔐 Vault Password',
            'Employee' => '👤 Data Karyawan',
            'User' => '👑 User Admin/System',
            default => $modelBasename ?: 'System',
        };

        $msg = "<b>📝 LOG AKTIVITAS SYSTEM</b>\n";
        $msg .= "━━━━━━━━━━━━━━━━━━━\n";
        $msg .= "<b>Status:</b> {$actionIcon}\n";
        $msg .= "<b>Operator:</b> 👤 " . htmlspecialchars($log->operator ?? 'System') . "\n";
        $msg .= "<b>Modul:</b> {$moduleName}\n";
        $msg .= "<b>Target:</b> 📌 <b>" . htmlspecialchars($log->target_name ?? '-') . "</b>\n";
        $msg .= "<b>Waktu:</b> 🕒 " . $log->created_at->format('d M Y, H:i:s') . " WIB\n";

        if (!empty($log->details) && is_array($log->details)) {
            $msg .= "\n<b>📋 Detail Perubahan:</b>\n";

            // 1. Employee Name for Assignments
            if (isset($log->details['employee_name'])) {
                $msg .= "• <b>Penerima/Karyawan:</b> " . htmlspecialchars($log->details['employee_name']) . "\n";
            }

            // 2. Field Diffs
            if (!empty($log->details['diffs']) && is_array($log->details['diffs'])) {
                foreach ($log->details['diffs'] as $diff) {
                    $field = htmlspecialchars($diff['field'] ?? 'Field');
                    $old = htmlspecialchars((string)($diff['old'] ?? '-'));
                    $new = htmlspecialchars((string)($diff['new'] ?? '-'));
                    $msg .= "• <b>{$field}:</b> <code>{$old}</code> ➔ <b><code>{$new}</code></b>\n";
                }
            }

            // 3. Summary for Created items
            if (!empty($log->details['summary']) && is_array($log->details['summary'])) {
                foreach ($log->details['summary'] as $key => $val) {
                    $k = htmlspecialchars($key);
                    $v = htmlspecialchars((string)$val);
                    $msg .= "• <b>{$k}:</b> <code>{$v}</code>\n";
                }
            }

            // 4. Fallback key-values
            foreach ($log->details as $key => $val) {
                if (in_array($key, ['diffs', 'summary', 'employee_name'])) continue;
                if (is_string($val) || is_numeric($val)) {
                    $k = htmlspecialchars(ucwords(str_replace('_', ' ', $key)));
                    $v = htmlspecialchars((string)$val);
                    $msg .= "• <b>{$k}:</b> {$v}\n";
                }
            }
        }

        return $this->sendMessage($targetChat, $msg);
    }
}
