<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\IpAddress;
use App\Models\Ticket;
use App\Services\PredictiveHealthService;
use App\Services\TelegramBotService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TelegramBotController extends Controller
{
    protected TelegramBotService $botService;

    public function __construct(TelegramBotService $botService)
    {
        $this->botService = $botService;
    }

    /**
     * Inbound Telegram Webhook Router
     */
    public function handleWebhook(Request $request, ?string $botType = null): JsonResponse
    {
        $data = $request->all();
        Log::info("Telegram Webhook Received [Bot: {$botType}]:", $data);

        // Resolve token corresponding to the receiving bot
        $token = null;
        if ($botType === 'ip') {
            $token = \App\Models\Setting::where('key', 'telegram_ip_bot_token')->value('value') ?: env('TELEGRAM_BOT_TOKEN');
        } elseif ($botType === 'primary') {
            $token = \App\Models\Setting::where('key', 'telegram_bot_token')->value('value') ?: env('TELEGRAM_BOT_TOKEN');
        }

        // 1. Handle Callback Query (Inline Button Click)
        if (isset($data['callback_query'])) {
            $this->handleCallbackQuery($data['callback_query'], $token);
            return response()->json(['status' => 'callback_processed']);
        }

        // 2. Handle Text Commands
        if (isset($data['message']['text'])) {
            $chatId = $data['message']['chat']['id'];
            $text = trim($data['message']['text']);

            $this->processCommand($chatId, $text, $data['message'], $token);
            return response()->json(['status' => 'command_processed']);
        }

        return response()->json(['status' => 'ignored']);
    }

    /**
     * Process Telegram Bot Command Router
     */
    protected function processCommand(int|string $chatId, string $text, array $messageData, ?string $token = null): void
    {
        $parts = explode(' ', $text, 2);
        $command = mb_strtolower($parts[0]);
        $param = trim($parts[1] ?? '');

        switch ($command) {
            case '/start':
            case '/help':
            case '/menu':
                $this->sendHelpMenu($chatId, $token);
                break;

            case '/cek':
            case '/asset':
                $this->handleAssetLookup($chatId, $param, $token);
                break;

            case '/ip':
                $this->handleIpLookup($chatId, $param, $token);
                break;

            case '/tiket':
            case '/ticket':
                $this->handleTicketList($chatId, $token);
                break;

            case '/ai':
            case '/tanya':
                $this->handleAiQuery($chatId, $param, $token);
                break;

            case '/chatid':
            case '/id':
                $this->botService->sendMessage($chatId, "📌 <b>Chat ID Anda:</b> <code>{$chatId}</code>", null, $token);
                break;

            default:
                if (str_starts_with($command, '/')) {
                    $this->botService->sendMessage($chatId, "⚠️ Perintah <code>{$command}</code> tidak dikenali. Ketik /help untuk melihat daftar perintah.", null, $token);
                }
                break;
        }
    }

    /**
     * Help Menu
     */
    protected function sendHelpMenu(int|string $chatId, ?string $token = null): void
    {
        $msg = "<b>[ITAM ASSISTANT] PANDUAN INTERAKSI SISTEM BOT</b>\n";
        $msg .= "----------------------------------------\n";
        $msg .= "Daftar perintah interaktif yang dapat Anda gunakan:\n\n";
        $msg .= "<b>/cek [Tag/Nama]</b> - Rincian spesifikasi aset, pengguna, & audit AI\n";
        $msg .= "<b>/ip [Alamat IP]</b> - Informasi status telemetri IP, MAC, & VLAN\n";
        $msg .= "<b>/tiket</b> - Daftar tiket kendala IT aktif & update status\n";
        $msg .= "<b>/ai [Pertanyaan]</b> - Kueri analisis AI & rekomendasi inventaris\n";
        $msg .= "<b>/id</b> - Tampilkan Telegram Chat ID Anda\n\n";
        $msg .= "----------------------------------------\n";
        $msg .= "<i>Chat ID Anda saat ini: <code>{$chatId}</code></i>";

        $this->botService->sendMessage($chatId, $msg, null, $token);
    }

    /**
     * Asset Lookup Command
     */
    protected function handleAssetLookup(int|string $chatId, string $query, ?string $token = null): void
    {
        if (empty($query)) {
            $this->botService->sendMessage($chatId, "<b>[SISTEM WARNING]</b> Harap masukkan Tag Aset atau Nama Perangkat.\nContoh: <code>/cek AST-2026-001</code>", null, $token);
            return;
        }

        $asset = Asset::with(['category', 'brand', 'location', 'currentAssignment.employee'])
            ->where('asset_tag', 'like', "%{$query}%")
            ->orWhere('name', 'like', "%{$query}%")
            ->first();

        if (!$asset) {
            $this->botService->sendMessage($chatId, "<b>[SISTEM NOTICE]</b> Aset dengan kata kunci '<b>{$query}</b>' tidak ditemukan dalam inventaris.", null, $token);
            return;
        }

        $healthService = app(PredictiveHealthService::class);
        $health = $healthService->calculateAssetHealth($asset);

        $msg = "<b>[RINCIAN ASET IT] INVENTARIS PERANGKAT</b>\n";
        $msg .= "----------------------------------------\n";
        $msg .= "<b>Kode Tag Aset:</b> <code>{$asset->asset_tag}</code>\n";
        $msg .= "<b>Nama Perangkat:</b> {$asset->name}\n";
        $msg .= "<b>Kategori & Brand:</b> " . ($asset->category->name ?? '-') . " • " . ($asset->brand->name ?? '-') . "\n";
        $msg .= "<b>Lokasi Penempatan:</b> " . ($asset->location->name ?? '-') . "\n";
        $msg .= "<b>Pengguna Saat Ini:</b> " . ($asset->currentAssignment->employee->name ?? 'Belum Diserahterimakan') . "\n";
        $msg .= "<b>Status Operasional:</b> " . strtoupper($asset->status) . "\n";
        $msg .= "----------------------------------------\n";
        $msg .= "<b>Audit Kesehatan AI:</b> <b>{$health['health_score']}% ({$health['status']})</b>\n";
        $msg .= "<b>Estimasi Sisa Usia:</b> {$health['remaining_life']}\n";
        $msg .= "<b>Rekomendasi Pemeliharaan AI:</b>\n" . htmlspecialchars($health['recommendation']);

        $this->botService->sendMessage($chatId, $msg, null, $token);
    }

    /**
     * IP Lookup Command
     */
    protected function handleIpLookup(int|string $chatId, string $ipQuery, ?string $token = null): void
    {
        if (empty($ipQuery)) {
            $this->botService->sendMessage($chatId, "<b>[SISTEM WARNING]</b> Harap masukkan Alamat IP.\nContoh: <code>/ip 192.168.1.186</code>", null, $token);
            return;
        }

        $ip = IpAddress::with(['asset', 'employee', 'vlan'])
            ->where('ip_address', $ipQuery)
            ->first();

        if (!$ip) {
            $this->botService->sendMessage($chatId, "<b>[SISTEM NOTICE]</b> Alamat IP '<b>{$ipQuery}</b>' tidak terdaftar dalam database IPAM.", null, $token);
            return;
        }

        $statusStr = $ip->is_online ? 'ONLINE (Aktif / Terhubung)' : 'OFFLINE (Mati / Unreachable)';

        $msg = "<b>[TELEMETRI IP ADDRESS] INFORMASI JARINGAN</b>\n";
        $msg .= "----------------------------------------\n";
        $msg .= "<b>Alamat IP:</b> <code>{$ip->ip_address}</code>\n";
        $msg .= "<b>Status Telemetri:</b> {$statusStr}\n";
        $msg .= "<b>MAC Address:</b> <code>" . ($ip->mac_address ?? '-') . "</code>\n";
        $msg .= "<b>Subnet / VLAN:</b> " . ($ip->vlan->vlan_name ?? 'Default Subnet') . "\n";
        $msg .= "<b>Status Alokasi:</b> {$ip->status}\n";
        $msg .= "<b>Aset Terikat:</b> " . ($ip->asset ? $ip->asset->name . " ({$ip->asset->asset_tag})" : 'Belum Dipetakan') . "\n";
        $msg .= "<b>Pengguna Terkait:</b> " . ($ip->employee ? $ip->employee->name : 'Belum Dipetakan') . "\n";

        $this->botService->sendMessage($chatId, $msg, null, $token);
    }

    /**
     * Active Helpdesk Ticket List Command
     */
    protected function handleTicketList(int|string $chatId, ?string $token = null): void
    {
        $tickets = Ticket::with('employee')
            ->whereIn('status', ['Open', 'In Progress'])
            ->latest()
            ->take(5)
            ->get();

        if ($tickets->isEmpty()) {
            $this->botService->sendMessage($chatId, "<b>[HELPDESK STATUS]</b> Tidak ada tiket kendala IT aktif saat ini. Seluruh tiket telah selesai ditangani.", null, $token);
            return;
        }

        $msg = "<b>[HELPDESK DAFTAR TIKET AKTIF]</b>\n";
        $msg .= "----------------------------------------\n";
        foreach ($tickets as $idx => $t) {
            $num = $idx + 1;
            $msg .= "<b>{$num}. [<code>{$t->ticket_number}</code>]</b> {$t->title}\n";
            $msg .= "   Pelapor: " . ($t->employee->name ?? 'User') . " | Status: <b>{$t->status}</b> | Prioritas: {$t->priority}\n\n";
        }
        $msg .= "<i>Gunakan tombol aksi di bawah untuk memperbarui status tiket.</i>";

        $keyboard = [
            'inline_keyboard' => []
        ];

        foreach ($tickets as $t) {
            $keyboard['inline_keyboard'][] = [
                ['text' => "Proses {$t->ticket_number}", 'callback_data' => "ticket_progress_{$t->id}"],
                ['text' => "Selesai {$t->ticket_number}", 'callback_data' => "ticket_resolve_{$t->id}"],
            ];
        }

        $this->botService->sendMessage($chatId, $msg, $keyboard, $token);
    }

    /**
     * Tanya ITAM AI Query Command
     */
    protected function handleAiQuery(int|string $chatId, string $query, ?string $token = null): void
    {
        if (empty($query)) {
            $this->botService->sendMessage($chatId, "<b>[ITAM AI RESPONSE]</b> Silakan tuliskan pertanyaan kueri Anda.\nContoh: <code>/ai Berapa jumlah PC yang berisiko tinggi?</code>", null, $token);
            return;
        }

        $healthService = app(PredictiveHealthService::class);
        $summary = $healthService->getDashboardSummary();

        $answer = "<b>[ITAM AI RESPONSE] LAPORAN ANALISIS AI</b>\n";
        $answer .= "----------------------------------------\n";
        $answer .= "<b>Pertanyaan:</b> <i>" . htmlspecialchars($query) . "</i>\n\n";

        if (str_contains(mb_strtolower($query), 'risiko') || str_contains(mb_strtolower($query), 'kesehatan') || str_contains(mb_strtolower($query), 'health')) {
            $answer .= "Berdasarkan audit prediktif AI terbaru:\n";
            $answer .= "• Total Aset Di-Audit: <b>{$summary['total_assets']} unit</b>\n";
            $answer .= "• Kondisi Sehat (Healthy): <b>{$summary['healthy_count']} unit</b>\n";
            $answer .= "• Status Warning: <b>{$summary['warning_count']} unit</b>\n";
            $answer .= "• Risiko Tinggi (Critical): <b>{$summary['critical_count']} unit</b>\n";
            $answer .= "• Estimasi Anggaran Peremajaan: <b>Rp " . number_format($summary['estimated_replacement_budget'], 0, ',', '.') . "</b>";
        } else {
            $answer .= "Sistem ITAM merekam total <b>{$summary['total_assets']} aset IT</b> terdaftar. ";
            $answer .= "Untuk analisis lebih mendalam mengenai unit atau subnet tertentu, Anda dapat menggunakan perintah <code>/cek [Tag_Aset]</code> atau <code>/ip [Alamat_IP]</code>.";
        }

        $this->botService->sendMessage($chatId, $answer, null, $token);
    }

    /**
     * Inline Keyboard Callback Handler
     */
    protected function handleCallbackQuery(array $callback, ?string $token = null): void
    {
        $chatId = $callback['message']['chat']['id'] ?? null;
        $data = $callback['data'] ?? '';

        if (!$chatId || empty($data)) return;

        if (str_starts_with($data, 'ticket_progress_')) {
            $ticketId = (int) str_replace('ticket_progress_', '', $data);
            $ticket = Ticket::find($ticketId);
            if ($ticket) {
                $ticket->update(['status' => 'In Progress']);
                $this->botService->sendMessage($chatId, "<b>[STATUS UPDATE]</b> Status Tiket <code>{$ticket->ticket_number}</code> diubah menjadi <b>IN PROGRESS</b>.", null, $token);
            }
        } elseif (str_starts_with($data, 'ticket_resolve_')) {
            $ticketId = (int) str_replace('ticket_resolve_', '', $data);
            $ticket = Ticket::find($ticketId);
            if ($ticket) {
                $ticket->update(['status' => 'Resolved', 'completed_at' => now()]);
                $this->botService->sendMessage($chatId, "<b>[STATUS UPDATE]</b> Status Tiket <code>{$ticket->ticket_number}</code> diubah menjadi <b>RESOLVED (SELESAI)</b>.", null, $token);
            }
        } elseif (str_starts_with($data, 'resolve_anom_')) {
            $ipId = (int) str_replace('resolve_anom_', '', $data);
            $ip = IpAddress::find($ipId);
            if ($ip) {
                $ip->update(['is_online' => false]);
                $this->botService->sendMessage($chatId, "<b>[STATUS UPDATE]</b> Anomali IP <code>{$ip->ip_address}</code> ditandai <b>REMEDIATED (RESOLVED)</b>.", null, $token);
            }
        } elseif (str_starts_with($data, 'register_rogue_')) {
            $ipId = (int) str_replace('register_rogue_', '', $data);
            $ip = IpAddress::find($ipId);
            if ($ip) {
                $this->botService->sendMessage($chatId, "<b>[SISTEM ACTION]</b> IP <code>{$ip->ip_address}</code> siap didaftarkan via portal web ITAM pada menu <b>Katalog Aset -> Tambah Aset</b>.", null, $token);
            }
        } elseif (str_starts_with($data, 'reping_ip_')) {
            $ipId = (int) str_replace('reping_ip_', '', $data);
            $ip = IpAddress::find($ipId);
            if ($ip) {
                $statusStr = $ip->is_online ? 'ONLINE (Aktif / Terhubung)' : 'OFFLINE (Mati / Unreachable)';
                $this->botService->sendMessage($chatId, "<b>[HASIL RE-PING TELEMETRI]</b>\nIP <code>{$ip->ip_address}</code> saat ini <b>{$statusStr}</b>.", null, $token);
            }
        } elseif (str_starts_with($data, 'ip_info_')) {
            $ipId = (int) str_replace('ip_info_', '', $data);
            $ip = IpAddress::find($ipId);
            if ($ip) {
                $this->handleIpLookup($chatId, $ip->ip_address, $token);
            }
        }
    }

    /**
     * Admin Panel Test Notification Route
     */
    public function sendTestNotification(Request $request): JsonResponse
    {
        $primaryToken = \App\Models\Setting::where('key', 'telegram_bot_token')->value('value') ?: env('TELEGRAM_BOT_TOKEN', '');
        $primaryChatId = \App\Models\Setting::where('key', 'telegram_chat_id')->value('value') ?: env('TELEGRAM_ADMIN_CHAT_ID', '');

        $ipToken = \App\Models\Setting::where('key', 'telegram_ip_bot_token')->value('value') ?: '';
        $ipChatId = \App\Models\Setting::where('key', 'telegram_ip_chat_id')->value('value') ?: '';

        if (empty($primaryToken) || empty($primaryChatId)) {
            return response()->json(['success' => false, 'message' => 'Token Bot Utama atau Chat ID belum diatur.']);
        }

        $results = [];

        // 1. Test Primary Bot (Activity Log & Main)
        $msgPrimary = "<b>[TEST NOTIFICATION] TELEGRAM BOT UTAMA / ACTIVITY LOG</b>\n";
        $msgPrimary .= "----------------------------------------\n";
        $msgPrimary .= "Koneksi Bot Telegram Utama terhubung dengan sistem secara sempurna.\n";
        $msgPrimary .= "Waktu Pengujian: " . date('d M Y, H:i:s') . " WIB";

        $resPrimary = $this->botService->sendMessage($primaryChatId, $msgPrimary, null, $primaryToken);
        $results['primary'] = [
            'name' => 'Bot Utama / Activity Log',
            'success' => $resPrimary
        ];

        // 2. Test IP Address Bot (If dedicated IP bot token or IP chat ID is configured)
        if (!empty($ipToken) || !empty($ipChatId)) {
            $targetIpToken = !empty($ipToken) ? $ipToken : $primaryToken;
            $targetIpChatId = !empty($ipChatId) ? $ipChatId : $primaryChatId;

            $msgIp = "<b>[TEST NOTIFICATION] TELEGRAM BOT KHUSUS IP ADDRESS & NETWORK</b>\n";
            $msgIp .= "----------------------------------------\n";
            $msgIp .= "Koneksi Bot Telegram Khusus IP Address & Jaringan terhubung dengan sempurna.\n";
            $msgIp .= "Waktu Pengujian: " . date('d M Y, H:i:s') . " WIB";

            $resIp = $this->botService->sendMessage($targetIpChatId, $msgIp, null, $targetIpToken);
            $results['ip_bot'] = [
                'name' => 'Bot Khusus IP Address',
                'success' => $resIp
            ];
        }

        $allSuccessful = true;
        $statusMessages = [];

        foreach ($results as $item) {
            if ($item['success']) {
                $statusMessages[] = $item['name'] . ": Berhasil (Connected)";
            } else {
                $allSuccessful = false;
                $statusMessages[] = $item['name'] . ": Gagal (Error)";
            }
        }

        $finalMsg = implode(' | ', $statusMessages);

        return response()->json([
            'success' => $allSuccessful,
            'message' => "Hasil Uji Coba: " . $finalMsg
        ]);
    }
}
