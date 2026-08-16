<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\IpAddress;
use App\Models\Setting;
use App\Models\Ticket;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected string $token;
    protected string $defaultPhone;
    protected bool $enabled;
    protected string $provider;
    protected string $apiUrl;

    public function __construct()
    {
        $this->enabled = (bool) Setting::where('key', 'whatsapp_enabled')->value('value');
        $this->provider = Setting::where('key', 'whatsapp_provider')->value('value') ?: 'fonnte';
        $this->token = Setting::where('key', 'whatsapp_api_token')->value('value') ?: env('WHATSAPP_API_TOKEN', '');
        $this->apiUrl = Setting::where('key', 'whatsapp_api_url')->value('value') ?: ($this->provider === 'wablas' ? 'https://kudus.wablas.com/api/send-message' : 'https://api.fonnte.com/send');
        $this->defaultPhone = Setting::where('key', 'whatsapp_admin_phone')->value('value') ?: env('WHATSAPP_ADMIN_PHONE', '');
    }

    /**
     * Format phone number to numeric digits (auto-prepend 0 if starting with 8)
     */
    protected function formatPhoneNumber(string $phone): string
    {
        $cleaned = preg_replace('/[^0-9]/', '', $phone);
        if (str_starts_with($cleaned, '8')) {
            $cleaned = '0' . $cleaned;
        }
        return $cleaned ?: '';
    }

    /**
     * Send raw WhatsApp message to a target phone number
     */
    public function sendMessage(?string $targetPhone, string $message): bool
    {
        $phone = $this->formatPhoneNumber($targetPhone ?: $this->defaultPhone);

        if (!$this->enabled) {
            Log::info("WhatsApp Service is disabled. Message payload to {$phone}: {$message}");
            return false;
        }

        if (empty($this->token)) {
            Log::warning("WhatsApp API Token is empty. Cannot send message to {$phone}.");
            return false;
        }

        if (empty($phone)) {
            Log::warning("WhatsApp target phone number is empty.");
            return false;
        }

        try {
            $apiUrl = $this->apiUrl;
            if (empty($apiUrl)) {
                $apiUrl = ($this->provider === 'wablas') ? 'https://kudus.wablas.com/api/send-message' : 'https://api.fonnte.com/send';
            }

            if ($this->provider === 'fonnte') {
                $response = Http::withHeaders([
                    'Authorization' => $this->token,
                ])->timeout(12)->post($apiUrl, [
                    'target' => $phone,
                    'message' => $message,
                ]);

                $json = $response->json();
                if (isset($json['status']) && ($json['status'] === false || $json['status'] === 'false')) {
                    $reason = $json['reason'] ?? $json['detail'] ?? 'Fonnte error';
                    Log::error("Fonnte API Rejected Request: {$reason}. Full response: " . $response->body());
                    return false;
                }
            } elseif ($this->provider === 'wablas') {
                $response = Http::withHeaders([
                    'Authorization' => $this->token,
                ])->timeout(12)->post($apiUrl, [
                    'phone' => $phone,
                    'message' => $message,
                ]);

                $json = $response->json();
                if (isset($json['status']) && ($json['status'] === false || $json['status'] === 'false')) {
                    return false;
                }
            } else {
                // Generic HTTP POST / Webhook API
                $response = Http::withHeaders([
                    'Authorization' => $this->token,
                    'Content-Type' => 'application/json',
                ])->timeout(12)->post($apiUrl, [
                    'target' => $phone,
                    'phone' => $phone,
                    'message' => $message,
                ]);
            }

            if ($response->failed()) {
                Log::error("WhatsApp Gateway API Error ({$this->provider}): " . $response->body());
                return false;
            }

            return true;
        } catch (\Exception $e) {
            Log::error("WhatsApp Gateway Connection Failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send IP Offline Alert to WhatsApp
     */
    public function sendIpOfflineAlert(IpAddress $ip, ?string $reason = null, ?string $targetPhone = null): bool
    {
        $ip->loadMissing(['asset', 'employee', 'vlan']);

        $assetInfo = $ip->asset ? "{$ip->asset->name} (Tag: {$ip->asset->asset_tag})" : 'Perangkat Tidak Terdaftar';
        $userInfo = $ip->employee ? $ip->employee->name : '-';
        $vlanInfo = $ip->vlan->vlan_name ?? 'Default Subnet';

        $msg = "*🔴 WHATSAPP NETWORK ALERT: IP OFFLINE 🔴*\n\n";
        $msg .= "*Alamat IP:* `" . $ip->ip_address . "`\n";
        $msg .= "*MAC Address:* `" . ($ip->mac_address ?? '-') . "`\n";
        $msg .= "*Subnet / VLAN:* " . $vlanInfo . "\n";
        $msg .= "*Aset Terikat:* " . $assetInfo . "\n";
        $msg .= "*Pengguna:* " . $userInfo . "\n";
        $msg .= "*Waktu Terdeteksi:* " . date('Y-m-d H:i:s') . "\n";
        if ($reason) {
            $msg .= "*Catatan:* " . $reason . "\n";
        }
        $msg .= "\n⚠️ _Perangkat tidak merespon Ping ICMP / Koneksi Terputus dari jaringan!_";

        return $this->sendMessage($targetPhone, $msg);
    }

    /**
     * Send IP Online (Recovery) Alert to WhatsApp
     */
    public function sendIpOnlineAlert(IpAddress $ip, ?string $targetPhone = null): bool
    {
        $ip->loadMissing(['asset', 'employee', 'vlan']);

        $assetInfo = $ip->asset ? "{$ip->asset->name} (Tag: {$ip->asset->asset_tag})" : 'Perangkat Tidak Terdaftar';
        $userInfo = $ip->employee ? $ip->employee->name : '-';
        $vlanInfo = $ip->vlan->vlan_name ?? 'Default Subnet';

        $msg = "*🟢 RECOVERY ALERT: IP ONLINE KEMBALI 🟢*\n\n";
        $msg .= "*Alamat IP:* `" . $ip->ip_address . "`\n";
        $msg .= "*MAC Address:* `" . ($ip->mac_address ?? '-') . "`\n";
        $msg .= "*Subnet / VLAN:* " . $vlanInfo . "\n";
        $msg .= "*Aset Terikat:* " . $assetInfo . "\n";
        $msg .= "*Pengguna:* " . $userInfo . "\n";
        $msg .= "*Waktu Pemulihan:* " . date('Y-m-d H:i:s') . "\n";
        $msg .= "\n✅ _Perangkat telah terhubung kembali dan merespon Ping ICMP dengan normal._";

        return $this->sendMessage($targetPhone, $msg);
    }

    /**
     * Send New Helpdesk Ticket Notification
     */
    public function sendNewTicketNotification(Ticket $ticket, ?string $targetPhone = null): bool
    {
        $msg = "*🎫 TIKET HELPDESK BARU 🎫*\n\n";
        $msg .= "*No Tiket:* `" . $ticket->ticket_number . "`\n";
        $msg .= "*Judul:* " . $ticket->title . "\n";
        $msg .= "*Pelapor:* " . ($ticket->employee->name ?? 'User') . "\n";
        $msg .= "*Kategori:* " . ($ticket->category ?? 'General') . "\n";
        $msg .= "*Prioritas:* " . ($ticket->priority ?? 'Medium') . "\n\n";
        $msg .= "*Deskripsi:*\n" . ($ticket->description ?? '-') . "\n";

        return $this->sendMessage($targetPhone, $msg);
    }

    /**
     * Send Rogue Device / IP Anomaly Alert
     */
    public function sendRogueAlert(array $anom, ?string $targetPhone = null): bool
    {
        $msg = "*🚨 WHATSAPP NETWORK ANOMALY ALERT 🚨*\n\n";
        $msg .= "*Tipe:* " . ($anom['type'] ?? 'Unknown') . "\n";
        $msg .= "*IP Address:* `" . ($anom['ip_address'] ?? '-') . "`\n";
        $msg .= "*MAC Address:* `" . ($anom['mac_address'] ?? '-') . "`\n";
        $msg .= "*Subnet/VLAN:* " . ($anom['vlan'] ?? 'Default Subnet') . "\n";
        $msg .= "*Severity:* " . ($anom['severity'] ?? 'Critical') . "\n\n";
        $msg .= "*Deskripsi:*\n" . ($anom['description'] ?? '-') . "\n";
        $msg .= "\n_Waktu: " . date('Y-m-d H:i:s') . "_";

        return $this->sendMessage($targetPhone, $msg);
    }

    /**
     * Send Critical Asset Health Score Alert
     */
    public function sendCriticalHealthAlert(Asset $asset, array $health, ?string $targetPhone = null): bool
    {
        $msg = "*🩺 ITAM AI HEALTH AUDIT ALERT 🩺*\n\n";
        $msg .= "*Aset Tag:* `" . $asset->asset_tag . "`\n";
        $msg .= "*Nama Aset:* " . $asset->name . "\n";
        $msg .= "*Kategori:* " . ($asset->category->name ?? '-') . "\n";
        $msg .= "*Skor Kesehatan:* 🔴 *" . ($health['health_score'] ?? 0) . "% (CRITICAL)*\n\n";
        $msg .= "*Rekomendasi AI:*\n" . ($health['recommendation'] ?? '-') . "\n";

        if (!empty($health['reasons'])) {
            $msg .= "\n*Faktor Risiko Detected:*\n";
            foreach ($health['reasons'] as $reason) {
                $msg .= "• " . $reason . "\n";
            }
        }

        return $this->sendMessage($targetPhone, $msg);
    }
}
