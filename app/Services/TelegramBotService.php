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
    protected bool $enabled;
    protected string $baseUrl;

    public function __construct()
    {
        $this->token = env('TELEGRAM_BOT_TOKEN', '');
        $this->defaultChatId = env('TELEGRAM_ADMIN_CHAT_ID', '');
        $this->enabled = (bool) env('TELEGRAM_BOT_ENABLED', true);
        $this->baseUrl = "https://api.telegram.org/bot{$this->token}";
    }

    /**
     * Send raw message to a Telegram Chat ID
     */
    public function sendMessage(string|int $chatId, string $text, ?array $replyMarkup = null): bool
    {
        if (!$this->enabled || empty($this->token)) {
            Log::info("Telegram Bot API disabled or token empty. Message payload: {$text}");
            return false;
        }

        try {
            $payload = [
                'chat_id' => $chatId ?: $this->defaultChatId,
                'text' => $text,
                'parse_mode' => 'HTML',
                'disable_web_page_preview' => true,
            ];

            if ($replyMarkup) {
                $payload['reply_markup'] = json_encode($replyMarkup);
            }

            // Pacing delay to strictly respect Telegram API rate limits (1 message / sec per chat)
            usleep(300000);

            $response = Http::timeout(10)->post("{$this->baseUrl}/sendMessage", $payload);

            if ($response->status() === 429) {
                $retryAfter = (int) ($response->json('parameters.retry_after') ?? 2);
                Log::warning("Telegram 429 Rate Limit. Pausing for {$retryAfter}s...");
                sleep(min($retryAfter, 3));
                $response = Http::timeout(10)->post("{$this->baseUrl}/sendMessage", $payload);
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
     * Send Rogue Device / IP Anomaly Alert
     */
    public function sendRogueAlert(array $anom, ?string $chatId = null): bool
    {
        $targetChat = $chatId ?: $this->defaultChatId;
        if (!$targetChat) return false;

        $msg = "<b>🚨 TELEGRAM NETWORK ANOMALY ALERT 🚨</b>\n\n";
        $msg .= "<b>Tipe:</b> " . htmlspecialchars($anom['type'] ?? 'Unknown') . "\n";
        $msg .= "<b>IP Address:</b> <code>" . htmlspecialchars($anom['ip_address'] ?? '-') . "</code>\n";
        $msg .= "<b>MAC Address:</b> <code>" . htmlspecialchars($anom['mac_address'] ?? '-') . "</code>\n";
        $msg .= "<b>Subnet/VLAN:</b> " . htmlspecialchars($anom['vlan'] ?? 'Default Subnet') . "\n";
        $msg .= "<b>Severity:</b> " . htmlspecialchars($anom['severity'] ?? 'Critical') . "\n\n";
        $msg .= "<b>Deskripsi:</b>\n" . htmlspecialchars($anom['description'] ?? '-') . "\n";
        $msg .= "\n<i>Waktu: " . date('Y-m-d H:i:s') . "</i>";

        $keyboard = [
            'inline_keyboard' => [
                [
                    ['text' => '✅ Remediasi (Resolve)', 'callback_data' => 'resolve_anom_' . ($anom['ip_id'] ?? 0)],
                    ['text' => '➕ Register Aset', 'callback_data' => 'register_rogue_' . ($anom['ip_id'] ?? 0)],
                ]
            ]
        ];

        return $this->sendMessage($targetChat, $msg, $keyboard);
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
     * Send IP Offline Alert to Telegram
     */
    public function sendIpOfflineAlert(\App\Models\IpAddress $ip, ?string $reason = null, ?string $chatId = null): bool
    {
        $targetChat = $chatId ?: $this->defaultChatId;
        if (!$targetChat) return false;

        $ip->loadMissing(['asset', 'employee', 'vlan']);

        $assetInfo = $ip->asset ? "{$ip->asset->name} (Tag: {$ip->asset->asset_tag})" : 'Perangkat Tidak Terdaftar';
        $userInfo = $ip->employee ? $ip->employee->name : '-';
        $vlanInfo = $ip->vlan->vlan_name ?? 'Default Subnet';

        $msg = "<b>🔴 TELEGRAM NETWORK ALERT: IP OFFLINE 🔴</b>\n\n";
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

        return $this->sendMessage($targetChat, $msg, $keyboard);
    }

    /**
     * Send IP Online (Recovery) Alert to Telegram
     */
    public function sendIpOnlineAlert(\App\Models\IpAddress $ip, ?string $chatId = null): bool
    {
        $targetChat = $chatId ?: $this->defaultChatId;
        if (!$targetChat) return false;

        $ip->loadMissing(['asset', 'employee', 'vlan']);

        $assetInfo = $ip->asset ? "{$ip->asset->name} (Tag: {$ip->asset->asset_tag})" : 'Perangkat Tidak Terdaftar';
        $userInfo = $ip->employee ? $ip->employee->name : '-';
        $vlanInfo = $ip->vlan->vlan_name ?? 'Default Subnet';

        $msg = "<b>🟢 RECOVERY ALERT: IP ONLINE KEMBALI 🟢</b>\n\n";
        $msg .= "<b>Alamat IP:</b> <code>" . htmlspecialchars($ip->ip_address) . "</code>\n";
        $msg .= "<b>MAC Address:</b> <code>" . htmlspecialchars($ip->mac_address ?? '-') . "</code>\n";
        $msg .= "<b>Subnet / VLAN:</b> " . htmlspecialchars($vlanInfo) . "\n";
        $msg .= "<b>Aset Terikat:</b> " . htmlspecialchars($assetInfo) . "\n";
        $msg .= "<b>Pengguna:</b> " . htmlspecialchars($userInfo) . "\n";
        $msg .= "<b>Waktu Pemulihan:</b> " . date('Y-m-d H:i:s') . "\n";
        $msg .= "\n✅ <i>Perangkat telah terhubung kembali dan merespon Ping ICMP dengan normal.</i>";

        $keyboard = [
            'inline_keyboard' => [
                [
                    ['text' => '🔍 Detail IP', 'callback_data' => 'ip_info_' . $ip->id],
                ]
            ]
        ];

        return $this->sendMessage($targetChat, $msg, $keyboard);
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
}
