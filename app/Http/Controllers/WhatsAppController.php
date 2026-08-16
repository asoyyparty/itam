<?php

namespace App\Http\Controllers;

use App\Services\WhatsAppService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WhatsAppController extends Controller
{
    protected WhatsAppService $waService;

    public function __construct(WhatsAppService $waService)
    {
        $this->waService = $waService;
    }

    /**
     * Admin Panel Test WhatsApp Notification Route
     */
    public function sendTestNotification(Request $request): JsonResponse
    {
        $targetPhone = $request->input('whatsapp_admin_phone');

        $msg = "*🔔 ITAM WHATSAPP TEST NOTIFICATION 🔔*\n\n";
        $msg .= "Koneksi Gateway WhatsApp ITAM Enterprise berhasil terhubung dengan sistem secara sempurna!\n";
        $msg .= "_Waktu Pengujian: " . date('Y-m-d H:i:s') . "_";

        $success = $this->waService->sendMessage($targetPhone, $msg);

        return response()->json([
            'success' => $success,
            'message' => $success ? 'Pesan pengujian WhatsApp berhasil terkirim!' : 'Gagal mengirim pesan WhatsApp. Periksa Token API, URL, & Nomor Telepon.'
        ]);
    }
}
