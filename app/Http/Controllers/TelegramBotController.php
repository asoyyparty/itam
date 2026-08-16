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
    public function handleWebhook(Request $request): JsonResponse
    {
        $data = $request->all();
        Log::info('Telegram Webhook Received:', $data);

        // 1. Handle Callback Query (Inline Button Click)
        if (isset($data['callback_query'])) {
            $this->handleCallbackQuery($data['callback_query']);
            return response()->json(['status' => 'callback_processed']);
        }

        // 2. Handle Text Commands
        if (isset($data['message']['text'])) {
            $chatId = $data['message']['chat']['id'];
            $text = trim($data['message']['text']);

            $this->processCommand($chatId, $text, $data['message']);
            return response()->json(['status' => 'command_processed']);
        }

        return response()->json(['status' => 'ignored']);
    }

    /**
     * Process Telegram Bot Command Router
     */
    protected function processCommand(int|string $chatId, string $text, array $messageData): void
    {
        $parts = explode(' ', $text, 2);
        $command = mb_strtolower($parts[0]);
        $param = trim($parts[1] ?? '');

        switch ($command) {
            case '/start':
            case '/help':
            case '/menu':
                $this->sendHelpMenu($chatId);
                break;

            case '/cek':
            case '/asset':
                $this->handleAssetLookup($chatId, $param);
                break;

            case '/ip':
                $this->handleIpLookup($chatId, $param);
                break;

            case '/tiket':
            case '/ticket':
                $this->handleTicketList($chatId);
                break;

            case '/ai':
            case '/tanya':
                $this->handleAiQuery($chatId, $param);
                break;

            case '/chatid':
            case '/id':
                $this->botService->sendMessage($chatId, "📌 <b>Chat ID Anda:</b> <code>{$chatId}</code>");
                break;

            default:
                if (str_starts_with($command, '/')) {
                    $this->botService->sendMessage($chatId, "⚠️ Perintah <code>{$command}</code> tidak dikenali. Ketik /help untuk melihat daftar perintah.");
                }
                break;
        }
    }

    /**
     * Help Menu
     */
    protected function sendHelpMenu(int|string $chatId): void
    {
        $msg = "<b>🤖 SELAMAT DATANG DI ITAM BOT ASSISTANT 🤖</b>\n\n";
        $msg .= "Berikut daftar perintah yang dapat Anda gunakan:\n\n";
        $msg .= "🔍 <b>/cek [Tag/Nama]</b> - Cek spesifikasi, user, & kesehatan AI aset\n";
        $msg .= "🌐 <b>/ip [Alamat IP]</b> - Cek status IP, MAC Address, & Subnet\n";
        $msg .= "🎫 <b>/tiket</b> - Daftar tiket kendala IT aktif & update status\n";
        $msg .= "🤖 <b>/ai [Pertanyaan]</b> - Tanya rekomendasi AI / Inventaris ITAM\n";
        $msg .= "📌 <b>/id</b> - Cek Telegram Chat ID Anda\n\n";
        $msg .= "<i>Chat ID Anda saat ini: <code>{$chatId}</code></i>";

        $this->botService->sendMessage($chatId, $msg);
    }

    /**
     * Asset Lookup Command
     */
    protected function handleAssetLookup(int|string $chatId, string $query): void
    {
        if (empty($query)) {
            $this->botService->sendMessage($chatId, "⚠️ Harap masukkan Tag Aset atau Nama Perangkat.\nContoh: <code>/cek AST-2026-001</code>");
            return;
        }

        $asset = Asset::with(['category', 'brand', 'location', 'currentAssignment.employee'])
            ->where('asset_tag', 'like', "%{$query}%")
            ->orWhere('name', 'like', "%{$query}%")
            ->first();

        if (!$asset) {
            $this->botService->sendMessage($chatId, "❌ Aset dengan kata kunci '<b>{$query}</b>' tidak ditemukan.");
            return;
        }

        $healthService = app(PredictiveHealthService::class);
        $health = $healthService->calculateAssetHealth($asset);

        $msg = "<b>💻 INFORMASI ASET ITAM 💻</b>\n\n";
        $msg .= "<b>Tag Aset:</b> <code>{$asset->asset_tag}</code>\n";
        $msg .= "<b>Nama:</b> {$asset->name}\n";
        $msg .= "<b>Kategori:</b> " . ($asset->category->name ?? '-') . "\n";
        $msg .= "<b>Brand:</b> " . ($asset->brand->name ?? '-') . "\n";
        $msg .= "<b>Lokasi:</b> " . ($asset->location->name ?? '-') . "\n";
        $msg .= "<b>Pengguna:</b> " . ($asset->currentAssignment->employee->name ?? 'Belum diserahterimakan') . "\n";
        $msg .= "<b>Status Aset:</b> " . strtoupper($asset->status) . "\n\n";
        $msg .= "<b>🩺 Kesehatan AI:</b> <b>{$health['health_score']}% ({$health['status']})</b>\n";
        $msg .= "<b>Sisa Usia:</b> {$health['remaining_life']}\n";
        $msg .= "<b>Rekomendasi AI:</b>\n" . htmlspecialchars($health['recommendation']);

        $this->botService->sendMessage($chatId, $msg);
    }

    /**
     * IP Lookup Command
     */
    protected function handleIpLookup(int|string $chatId, string $ipQuery): void
    {
        if (empty($ipQuery)) {
            $this->botService->sendMessage($chatId, "⚠️ Harap masukkan Alamat IP.\nContoh: <code>/ip 192.168.6.28</code>");
            return;
        }

        $ip = IpAddress::with(['asset', 'employee', 'vlan'])
            ->where('ip_address', $ipQuery)
            ->first();

        if (!$ip) {
            $this->botService->sendMessage($chatId, "❌ Alamat IP '<b>{$ipQuery}</b>' tidak terdaftar dalam database IPAM.");
            return;
        }

        $statusEmoji = $ip->is_online ? '🟢 Online' : '🔴 Offline';

        $msg = "<b>🌐 INFORMASI IP ADDRESS 🌐</b>\n\n";
        $msg .= "<b>Alamat IP:</b> <code>{$ip->ip_address}</code> ({$statusEmoji})\n";
        $msg .= "<b>MAC Address:</b> <code>" . ($ip->mac_address ?? '-') . "</code>\n";
        $msg .= "<b>Subnet/VLAN:</b> " . ($ip->vlan->vlan_name ?? 'Default Subnet') . "\n";
        $msg .= "<b>Status Alokasi:</b> {$ip->status}\n";
        $msg .= "<b>Terikat Aset:</b> " . ($ip->asset ? $ip->asset->name . " ({$ip->asset->asset_tag})" : '-') . "\n";
        $msg .= "<b>Terikat Pegawai:</b> " . ($ip->employee ? $ip->employee->name : '-') . "\n";

        $this->botService->sendMessage($chatId, $msg);
    }

    /**
     * Active Helpdesk Ticket List Command
     */
    protected function handleTicketList(int|string $chatId): void
    {
        $tickets = Ticket::with('employee')
            ->whereIn('status', ['Open', 'In Progress'])
            ->latest()
            ->take(5)
            ->get();

        if ($tickets->isEmpty()) {
            $this->botService->sendMessage($chatId, "🎉 Tidak ada tiket kendala aktif saat ini. Semua kendala IT telah terselesaikan!");
            return;
        }

        $msg = "<b>🎫 TIKET KENDALA IT AKTIF 🎫</b>\n\n";
        foreach ($tickets as $idx => $t) {
            $num = $idx + 1;
            $msg .= "<b>{$num}. [<code>{$t->ticket_number}</code>]</b> {$t->title}\n";
            $msg .= "   Pelapor: " . ($t->employee->name ?? 'User') . " | Status: <b>{$t->status}</b>\n\n";
        }
        $msg .= "<i>Gunakan tombol aksi di bawah untuk update status langsung.</i>";

        $keyboard = [
            'inline_keyboard' => []
        ];

        foreach ($tickets as $t) {
            $keyboard['inline_keyboard'][] = [
                ['text' => "⚙️ Progress {$t->ticket_number}", 'callback_data' => "ticket_progress_{$t->id}"],
                ['text' => "✅ Selesai {$t->ticket_number}", 'callback_data' => "ticket_resolve_{$t->id}"],
            ];
        }

        $this->botService->sendMessage($chatId, $msg, $keyboard);
    }

    /**
     * Tanya ITAM AI Query Command
     */
    protected function handleAiQuery(int|string $chatId, string $query): void
    {
        if (empty($query)) {
            $this->botService->sendMessage($chatId, "🤖 <i>Halo! Saya Tanya ITAM AI. Silakan tulis pertanyaan Anda.</i>\nContoh: <code>/ai Berapa jumlah PC yang berisiko tinggi?</code>");
            return;
        }

        $healthService = app(PredictiveHealthService::class);
        $summary = $healthService->getDashboardSummary();

        $answer = "<b>🤖 TANYA ITAM AI RESPONSE 🤖</b>\n\n";
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

        $this->botService->sendMessage($chatId, $answer);
    }

    /**
     * Inline Keyboard Callback Handler
     */
    protected function handleCallbackQuery(array $callback): void
    {
        $chatId = $callback['message']['chat']['id'] ?? null;
        $data = $callback['data'] ?? '';

        if (!$chatId || empty($data)) return;

        if (str_starts_with($data, 'ticket_progress_')) {
            $ticketId = (int) str_replace('ticket_progress_', '', $data);
            $ticket = Ticket::find($ticketId);
            if ($ticket) {
                $ticket->update(['status' => 'In Progress']);
                $this->botService->sendMessage($chatId, "⚙️ Status Tiket <code>{$ticket->ticket_number}</code> berhasil diubah menjadi <b>IN PROGRESS</b>.");
            }
        } elseif (str_starts_with($data, 'ticket_resolve_')) {
            $ticketId = (int) str_replace('ticket_resolve_', '', $data);
            $ticket = Ticket::find($ticketId);
            if ($ticket) {
                $ticket->update(['status' => 'Resolved', 'completed_at' => now()]);
                $this->botService->sendMessage($chatId, "✅ Status Tiket <code>{$ticket->ticket_number}</code> telah ditandai <b>RESOLVED (SELESAI)</b>.");
            }
        } elseif (str_starts_with($data, 'resolve_anom_')) {
            $ipId = (int) str_replace('resolve_anom_', '', $data);
            $ip = IpAddress::find($ipId);
            if ($ip) {
                $ip->update(['is_online' => false]);
                $this->botService->sendMessage($chatId, "✅ Anomali IP <code>{$ip->ip_address}</code> telah ditandai <b>REMEDIATED (RESOLVED)</b>.");
            }
        } elseif (str_starts_with($data, 'register_rogue_')) {
            $ipId = (int) str_replace('register_rogue_', '', $data);
            $ip = IpAddress::find($ipId);
            if ($ip) {
                $this->botService->sendMessage($chatId, "➕ IP <code>{$ip->ip_address}</code> siap didaftarkan. Buka portal ITAM web pada menu <b>Katalog Aset -> Tambah Aset</b>.");
            }
        } elseif (str_starts_with($data, 'reping_ip_')) {
            $ipId = (int) str_replace('reping_ip_', '', $data);
            $ip = IpAddress::find($ipId);
            if ($ip) {
                $statusEmoji = $ip->is_online ? '🟢 ONLINE' : '🔴 OFFLINE';
                $this->botService->sendMessage($chatId, "🔄 <b>HASIL RE-PING TELEMETRY:</b>\nIP <code>{$ip->ip_address}</code> saat ini <b>{$statusEmoji}</b>.");
            }
        } elseif (str_starts_with($data, 'ip_info_')) {
            $ipId = (int) str_replace('ip_info_', '', $data);
            $ip = IpAddress::find($ipId);
            if ($ip) {
                $this->handleIpLookup($chatId, $ip->ip_address);
            }
        }
    }

    /**
     * Admin Panel Test Notification Route
     */
    public function sendTestNotification(Request $request): JsonResponse
    {
        $chatId = $request->input('chat_id') ?: env('TELEGRAM_ADMIN_CHAT_ID');

        if (empty($chatId)) {
            return response()->json(['success' => false, 'message' => 'Chat ID Telegram belum diatur.']);
        }

        $msg = "<b>🔔 ITAM TELEGRAM TEST NOTIFICATION 🔔</b>\n\n";
        $msg .= "Koneksi Bot Telegram ITAM Enterprise berhasil terhubung dengan sistem secara sempurna!\n";
        $msg .= "<i>Waktu Pengujian: " . date('Y-m-d H:i:s') . "</i>";

        $success = $this->botService->sendMessage($chatId, $msg);

        return response()->json([
            'success' => $success,
            'message' => $success ? 'Pesan pengujian Telegram berhasil terkirim!' : 'Gagal mengirim pesan Telegram. Periksa Token & Chat ID.'
        ]);
    }
}
