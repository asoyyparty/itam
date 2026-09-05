<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Asset;
use App\Models\Department;
use App\Models\Employee;
use App\Models\IpAddress;
use App\Models\Maintenance;
use App\Models\PasswordVault;
use App\Models\SoftwareLicense;
use App\Models\Ticket;
use App\Models\Vendor;
use App\Models\Vlan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class AiService
{
    protected ?string $apiKey;
    protected string $provider;

    public function __construct()
    {
        $dbProvider = \App\Models\Setting::where('key', 'ai_provider')->value('value') ?: 'gemini';
        $selectedProvider = env('AI_PROVIDER') ?: $dbProvider;
        $dbGeminiKey = \App\Models\Setting::where('key', 'gemini_api_key')->value('value');
        $dbOpenaiKey = \App\Models\Setting::where('key', 'openai_api_key')->value('value');
        $deepseekKey = env('DEEPSEEK_API_KEY');

        if ($selectedProvider === 'deepseek' && !empty($deepseekKey)) {
            $this->provider = 'deepseek';
            $this->apiKey = $deepseekKey;
        } elseif ($selectedProvider === 'openai' && !empty($dbOpenaiKey)) {
            $this->provider = 'openai';
            $this->apiKey = $dbOpenaiKey;
        } elseif ($selectedProvider === 'gemini' && !empty(env('GEMINI_API_KEY'))) {
            $this->provider = 'gemini';
            $this->apiKey = env('GEMINI_API_KEY');
        } elseif ($selectedProvider === 'gemini' && !empty($dbGeminiKey)) {
            $this->provider = 'gemini';
            $this->apiKey = $dbGeminiKey;
        } elseif ($selectedProvider === 'off') {
            $this->provider = 'none';
            $this->apiKey = null;
        } elseif ($selectedProvider === 'deepseek') {
            $this->provider = 'none';
            $this->apiKey = null;
        } else {
            $this->apiKey = config('services.ai.key') ?: env('GEMINI_API_KEY') ?: env('OPENAI_API_KEY') ?: $deepseekKey;
            $this->provider = env('GEMINI_API_KEY') ? 'gemini' : (env('OPENAI_API_KEY') ? 'openai' : ($deepseekKey ? 'deepseek' : 'none'));
        }
    }

    /**
     * Analyze IT Support Ticket using AI
     */
    public function analyzeTicket(array $data): array
    {
        $title = $data['title'] ?? '';
        $description = $data['description'] ?? '';
        $employeeName = $data['employee_name'] ?? 'User';
        $assetName = $data['asset_name'] ?? null;
        $assetSpecs = $data['asset_specs'] ?? null;

        if ($this->apiKey && $this->provider !== 'none') {
            try {
                return $this->callAiApiForTicket($title, $description, $employeeName, $assetName, $assetSpecs);
            } catch (\Throwable $e) {
                \Log::warning('AI API call failed, falling back to smart local engine: ' . $e->getMessage());
            }
        }

        // Smart Local Engine Fallback
        return $this->localTicketAnalysis($title, $description, $employeeName, $assetName, $assetSpecs);
    }

    /**
     * Natural Language Query / Search Assistant
     */
    public function queryNaturalLanguage(string $prompt, array $history = []): array
    {
        $promptTrimmed = trim($prompt);
        $promptLower = mb_strtolower($promptTrimmed);

        // 0. Handle Greetings & Conversational Prompts
        if (in_array($promptLower, ['hai', 'halo', 'hi', 'helo', 'hello', 'selamat pagi', 'selamat siang', 'selamat sore', 'selamat malam', 'siapa anda', 'siapa kamu', 'help', 'bantuan'])) {
            return [
                'summary' => 'Halo! Saya adalah ITAM AI.',
                'type' => 'greeting',
                'answer' => 'Saya siap membantu Anda mengelola dan melacak aset IT, tiket kendala, alamat IP jaringan, lisensi software, dan data pengguna perusahaan. Anda dapat mengetik pertanyaan atau memilih rekomendasi di bawah.',
                'items' => [],
            ];
        }

        // 0.5. Answer current day and date questions locally and accurately.
        if (preg_match('/\b(hari apa|tanggal berapa|tanggal hari ini|hari ini tanggal berapa|sekarang tanggal berapa)\b/i', $promptLower)) {
            $today = now()->locale('id')->translatedFormat('l, d F Y');

            return [
                'summary' => 'Hari ini adalah ' . $today . '.',
                'type' => 'date',
                'answer' => 'Berdasarkan waktu aplikasi, hari ini adalah ' . $today . '.',
                'items' => [],
                'sources' => [],
            ];
        }

        // 0.5. Answer identity and capability questions before entity searches.
        if (preg_match('/\b(ini|aplikasi ini|sistem ini|kamu|anda)\b.*\b(apa|siapa|untuk apa|fungsinya|kegunaannya)\b/i', $promptLower)
            || preg_match('/\b(jelaskan|jelaskanlah|terangkan)\b.*\b(fungsi|kegunaan|cara kerja)\b.*\b(aplikasi|sistem|itam)\b/i', $promptLower)
            || preg_match('/^\s*(aplikasi|sistem)\s+(apa|ini)\s*[?!.]*\s*$/i', $promptLower)) {
            return [
                'summary' => 'Ini adalah ITAM AI, asisten untuk pengelolaan aset dan layanan IT.',
                'type' => 'identity',
                'answer' => 'ITAM adalah aplikasi IT Asset Management. ITAM AI membantu Anda mencari informasi aset, assignment, tiket support, alamat IP, maintenance, lisensi software, dan data operasional IT perusahaan. Untuk pertanyaan teknis umum, saya juga dapat menggunakan referensi internet dan menampilkan sumbernya.',
                'items' => [],
                'sources' => [],
            ];
        }

        if (preg_match('/\b(tampilkan|lihatkan|jelaskan|ringkas|cek)\b.*\b(semua|seluruh|total)\b.*\b(data|modul|informasi)\b/i', $promptLower)
            || preg_match('/\bsemua data\b.*\b(aplikasi|sistem|itam)\b/i', $promptLower)) {
            return $this->querySystemOverviewNL();
        }

        // 1. Direct IP Address Detection (e.g. "192.168.1.186")
        if (preg_match('/\b(?:[0-9]{1,3}\.){3}[0-9]{1,3}\b/', $promptTrimmed)) {
            return $this->queryIpsNL($promptTrimmed);
        }

        $normalizedPrompt = preg_replace('/\b(snydesk|anydes[kc]|anydek|anydesk)\b/i', 'anydesk', $promptLower);
        if (Str::contains($normalizedPrompt, 'anydesk')
            && Str::contains($normalizedPrompt, ['tidak punya', 'tanpa', 'tidak memiliki', 'belum punya', 'belum memiliki', 'tidak ada', 'belum ada'])) {
            return $this->queryEmployeesNL($normalizedPrompt);
        }

        if (Str::contains($promptLower, ['printer', 'cetak'])
            && Str::contains($promptLower, ['punya', 'memiliki', 'diberi', 'memakai', 'digunakan'])) {
            return $this->queryEmployeePrinterNL($promptLower);
        }

        if ($this->containsAnyKeyword($promptLower, ['divisi', 'departemen', 'department'])
            && $this->containsAnyKeyword($promptLower, ['asset', 'aset', 'perangkat'])
            && $this->containsAnyKeyword($promptLower, ['paling banyak', 'terbanyak', 'tertinggi', 'terbesar', 'banyak'])) {
            return $this->queryDepartmentAssetRankingNL();
        }

        if (Str::contains($promptLower, ['asset', 'aset', 'perangkat'])
            && Str::contains($promptLower, ['terkait', 'punya', 'memiliki', 'diberi', 'memakai', 'digunakan', 'milik'])) {
            return $this->queryEmployeeAssetsNL($promptLower);
        }

        $matchingEmployee = Employee::where('name', 'like', '%' . $promptTrimmed . '%')
            ->orWhere('employee_id', 'like', '%' . $promptTrimmed . '%')
            ->exists();
        if ($matchingEmployee) {
            return $this->queryEmployeesNL($promptLower);
        }

        $internalResult = $this->queryInternalIntentNL($promptLower);
        if ($internalResult !== null) {
            return $internalResult;
        }

        // 1.5. Coba cari di General DB dulu sebelum fallback ke AI
        $generalResult = $this->queryGeneralNL($promptLower, true);
        if ($generalResult !== null) {
            return $generalResult;
        }

        if (env('AI_CHAT_MODE', 'internal') === 'internal') {
            return $this->queryGeneralNL($promptLower, false) ?? [
                'summary' => "Pencarian tidak ditemukan.",
                'type' => 'general',
                'items' => []
            ];
        }

        // 1.8. If API Key is configured and no internal DB matched, attempt Live AI API Call
        if ($this->apiKey && $this->provider !== 'none') {
            try {
                $liveResult = $this->callAiApiForQuery($promptTrimmed, $history);
                if (!empty($liveResult)) {
                    return $liveResult;
                }
            } catch (\Throwable $e) {
                \Log::warning('Live AI Query call failed, falling back to local NLP engine: ' . $e->getMessage());
            }
        }

        // 2. Search Maintenance / Servis / Perawatan / Biaya
        if (Str::contains($promptLower, ['maintenance', 'perawatan', 'servis', 'service', 'reparasi', 'biaya', 'cost', 'repair', 'upgrade', 'pengeluaran', 'nota', 'ganti sparepart', 'perbaikan', 'ongoing maintenance', 'servis berjalan'])) {
            return $this->queryMaintenancesNL($promptLower);
        }

        // 3. Search Password Vault / Brankas Sandi / Kredensial
        if (Str::contains($promptLower, ['vault', 'password', 'sandi', 'brankas', 'kredensial', 'akun', 'login', 'pass', 'username', 'akses perangkat', 'cctv pass', 'router pass', 'switch pass'])) {
            return $this->queryVaultsNL($promptLower);
        }

        // 4. Search VLAN & Subnets
        if (Str::contains($promptLower, ['vlan', 'subnet', 'segmentasi', 'netmask', 'gateway', 'vlan id', 'vlan number', 'vlan server', 'vlan wifi'])) {
            return $this->queryVlansNL($promptLower);
        }

        // 5. Search Vendor / Pemasok / Supplier
        if (Str::contains($promptLower, ['vendor', 'pemasok', 'supplier', 'toko', 'distributor', 'mitra', 'sales', 'pembelian'])) {
            return $this->queryVendorsNL($promptLower);
        }

        // 6. Search Activity Log / Audit Trail
        if (Str::contains($promptLower, ['log', 'aktivitas', 'activity', 'riwayat', 'history', 'audit', 'operator', 'siapa yang', 'kejadian', 'perubahan', 'tambah aset', 'hapus aset', 'edit aset'])) {
            return $this->queryActivityLogsNL($promptLower);
        }

        // 7. Search Assets & Hardware Specs
        if (Str::contains($promptLower, ['asset', 'aset', 'komputer', 'laptop', 'printer', 'cctv', 'switch', 'access point', 'ap', 'router', 'perangkat', 'garansi', 'warranty', 'expired', 'pc', 'desktop', 'notebook', 'server', 'monitor', 'layar', 'display', 'storage', 'harddisk', 'hdd', 'ssd', 'ram', 'memory', 'cpu', 'processor', 'intel', 'amd', 'core i3', 'core i5', 'core i7', 'ryzen', 'vga', 'gpu', 'os', 'windows', 'linux', 'macbook', 'macos', 'tag', 'kode aset', 'sn', 'serial number', 'surat jalan', 'do', 'lenovo', 'dell', 'hp', 'epson', 'canon', 'asus', 'acer', 'mikrotik', 'unifi', 'hikvision', 'logitech', 'brother', 'oumax', 'toshiba', 'solution', 'cooca', 'yamaha'])) {
            return $this->queryAssetsNL($promptLower);
        }

        // 8. Search IP / Network
        if (Str::contains($promptLower, ['ip', 'ip address', 'alamat ip', 'jaringan', 'network', 'online', 'offline', 'mac', 'mac address', 'ping', 'rto', 'eth', 'lan', 'wifi', 'ssid', 'pass wifi'])) {
            return $this->queryIpsNL($promptLower);
        }

        // 9. Search Tickets
        if (Str::contains($promptLower, ['tiket', 'ticket', 'kendala', 'laporan', 'masalah', 'trouble', 'lapor', 'komplain', 'issue', 'open', 'progress', 'proses', 'closed', 'resolved', 'selesai', 'prioritas', 'low', 'medium', 'high', 'critical', 'kritis', 'bantuan', 'support'])) {
            return $this->queryTicketsNL($promptLower);
        }

        // 10. Search Employees / Users / AnyDesk / Extension
        if (Str::contains($promptLower, ['user', 'pengguna', 'karyawan', 'pegawai', 'staf', 'staff', 'divisi', 'department', 'dept', 'jabatan', 'position', 'nik', 'nomor induk', 'orang', 'supervisor', 'atasan', 'anydesk', 'anydesk id', 'pass anydesk', 'password anydesk', 'remote', 'remote desktop', 'telepon', 'ext', 'ekstensi', 'extension', 'email', 'surel', 'phone', 'hp', 'lokasi', 'gedung', 'ruangan', 'lantai'])) {
            return $this->queryEmployeesNL($promptLower);
        }

        // 11. Search Software / Licenses
        if (Str::contains($promptLower, ['lisensi', 'license', 'software', 'perangkat lunak', 'aplikasi', 'kunci', 'key', 'license key', 'pic', 'seats', 'lifetime', 'expired license'])) {
            return $this->queryLicensesNL($promptLower);
        }

        // General multi-entity fallback search
        return $this->queryGeneralNL($promptLower);
    }

    /**
     * Call Live Gemini / OpenAI API for Natural Language Query
     */
    protected function callAiApiForQuery(string $prompt, array $history = []): ?array
    {
        $totalAssets = Asset::count();
        $totalTickets = Ticket::count();
        $openTickets = Ticket::where('status', 'Open')->count();
        $totalIps = IpAddress::count();
        $usedIps = IpAddress::where('status', 'Used')->count();
        $totalEmployees = Employee::count();
        $totalMaintenances = Maintenance::count();
        $ongoingMaintenances = Maintenance::where('status', 'Ongoing')->count();
        $totalLicenses = SoftwareLicense::count();
        $totalVaults = PasswordVault::count();
        $totalVlans = Vlan::count();

        $systemPrompt = "Anda adalah ITAM AI, asisten cerdas untuk sistem manajemen aset IT perusahaan.
Data Ringkasan Sistem ITAM Saat Ini:
- Total Aset IT: {$totalAssets}
- Total Tiket Support: {$totalTickets} (Open: {$openTickets})
- Total IP Address: {$totalIps} (Terpakai: {$usedIps})
- Total Pengguna/Karyawan: {$totalEmployees}
- Record Perawatan/Maintenance: {$totalMaintenances} (Ongoing: {$ongoingMaintenances})
- Total Lisensi Software: {$totalLicenses}
- Total Entri Brankas Sandi (Vault): {$totalVaults}
- Total VLAN Jaringan: {$totalVlans}

Tugas: Jawab pertanyaan pengguna secara ringkas, ramah, dan profesional dalam Bahasa Indonesia. Gunakan data internal ITAM sebagai sumber utama untuk pertanyaan tentang perusahaan. Gunakan pengetahuan internet hanya sebagai referensi tambahan untuk solusi teknis umum. Jangan mengarang fakta, angka, konfigurasi, atau status yang tidak ada dalam data. 
PENTING: Jika kueri pencarian ini sampai kepada Anda, berarti mesin pencari lokal ITAM telah menelusuri seluruh tabel database (Aset, Karyawan, IP, Tiket, Lisensi, Vault, Vendor) dan TIDAK menemukan hasil. Beritahukan pengguna bahwa data tersebut benar-benar tidak ditemukan di seluruh sistem aplikasi, dan jangan mengasumsikan data tersebut hanya milik satu modul tertentu (seperti Brankas Sandi). Kembalikan format JSON valid tanpa markdown formatting dengan key:
- summary: Ringkasan singkat jawaban (1-2 kalimat)
- answer: Jawaban/penjelasan detail atau panduan teknis yang bermanfaat bagi pengguna
- items: Array kosong [] jika tidak ada record spesifik yang ditautkan";

    $webSources = [];
    $contents = [];
        foreach (array_slice($history, -10) as $message) {
            $role = in_array($message['role'] ?? '', ['bot', 'model', 'assistant'], true) ? 'model' : 'user';
            $content = trim((string) ($message['content'] ?? (($message['summary'] ?? '') . "\n" . ($message['answer'] ?? ''))));
            if ($content !== '') {
                $contents[] = ['role' => $role, 'parts' => [['text' => $content]]];
            }
        }
        $contents[] = ['role' => 'user', 'parts' => [['text' => $prompt]]];

        if ($this->provider === 'gemini') {
            $model = env('GEMINI_MODEL', 'gemini-3.5-flash-lite');
            $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$this->apiKey}";
            $response = Http::timeout(25)->connectTimeout(5)->post($url, [
                'contents' => $contents,
                'systemInstruction' => [
                    'parts' => [['text' => $systemPrompt]],
                ],
                ...(filter_var(env('GEMINI_GOOGLE_SEARCH', false), FILTER_VALIDATE_BOOL) ? [
                    'tools' => [['google_search' => new \stdClass()]],
                ] : []),
                'generationConfig' => [
                    'responseMimeType' => 'application/json',
                ],
            ]);

            if ($response->successful()) {
                $text = $response->json('candidates.0.content.parts.0.text', '');
                $cleanJson = preg_replace('/```json\s*|\s*```/', '', trim($text));
                $parsed = json_decode($cleanJson, true);
                if (is_array($parsed) && isset($parsed['summary'])) {
                    $parsed['type'] = 'live_ai';
                    $parsed['items'] = $parsed['items'] ?? [];
                    $parsed['sources'] = $this->mergeGroundingSources($webSources, $response->json('candidates.0.groundingMetadata'));
                    return $parsed;
                }
            }
            if ($response->failed()) {
                \Log::warning('Gemini query request failed', [
                    'status' => $response->status(),
                    'body' => \Illuminate\Support\Str::limit($response->body(), 500),
                ]);
            }
        } elseif (in_array($this->provider, ['openai', 'deepseek'], true)) {
            $messages = [['role' => 'system', 'content' => $systemPrompt]];
            foreach (array_slice($history, -10) as $message) {
                $role = in_array($message['role'] ?? '', ['bot', 'model', 'assistant'], true) ? 'assistant' : 'user';
                $content = trim((string) ($message['content'] ?? (($message['summary'] ?? '') . "\n" . ($message['answer'] ?? ''))));
                if ($content !== '') {
                    $messages[] = ['role' => $role, 'content' => $content];
                }
            }
            $messages[] = ['role' => 'user', 'content' => $prompt];
            $apiUrl = $this->provider === 'deepseek'
                ? 'https://api.deepseek.com/chat/completions'
                : 'https://api.openai.com/v1/chat/completions';
            $response = Http::timeout(25)->connectTimeout(5)->withToken($this->apiKey)->post($apiUrl, [
                'model' => $this->provider === 'deepseek' ? env('DEEPSEEK_MODEL', 'deepseek-chat') : 'gpt-4o-mini',
                'messages' => $messages,
                'response_format' => ['type' => 'json_object']
            ]);

            if ($response->successful()) {
                $parsed = json_decode($response->json('choices.0.message.content'), true);
                if (is_array($parsed) && isset($parsed['summary'])) {
                    $parsed['type'] = 'live_ai';
                    $parsed['items'] = $parsed['items'] ?? [];
                    $parsed['sources'] = $webSources;
                    return $parsed;
                }
            }
        }

        return null;
    }

    /**
     * Convert Gemini grounding citations into the same source shape used by the UI.
     */
    protected function mergeGroundingSources(array $sources, ?array $groundingMetadata): array
    {
        $merged = $sources;
        $chunks = $groundingMetadata['groundingChunks'] ?? [];

        foreach ($chunks as $chunk) {
            $web = $chunk['web'] ?? [];
            if (empty($web['uri'])) {
                continue;
            }

            $alreadyExists = collect($merged)->contains(fn ($source) => ($source['url'] ?? null) === $web['uri']);
            if (!$alreadyExists) {
                $merged[] = [
                    'title' => $web['title'] ?? 'Google Search source',
                    'url' => $web['uri'],
                    'snippet' => '',
                ];
            }
        }

        return array_values(array_slice($merged, 0, 6));
    }

    /**
     * Call Live Gemini / OpenAI API for Ticket Analysis
     */
    protected function callAiApiForTicket(string $title, string $description, string $employee, ?string $asset, ?string $specs): array
    {
        $systemPrompt = "Anda adalah Senior IT Support Engineer AI. Analisis tiket IT support berikut dan berikan jawaban berformat JSON valid tanpa markdown formatting yang berisi key:
- suggested_category: Pilih salah satu dari ['Hardware & Software Support', 'Infrastruktur Jaringan', 'CCTV & Security System', 'Server, Backup & Security', 'Helpdesk & User Support', 'Support Sistem Produksi', 'Multimedia Meeting & Event Support']
- suggested_priority: Pilih salah satu dari ['Low', 'Medium', 'High', 'Critical']
- diagnosis: Analisis akar penyebab masalah teknis secara ringkas dan profesional
- resolution_steps: Array 3-5 langkah teknis penanganan untuk IT Support
- reply_draft: Draf balasan ramah dan profesional untuk dikirim ke user pelapor

Tiket:
- Judul: {$title}
- Deskripsi: {$description}
- Pelapor: {$employee}
- Aset Terkait: " . ($asset ? "{$asset} (Specs: {$specs})" : "Tidak ada");

        if ($this->provider === 'gemini') {
            $model = env('GEMINI_MODEL', 'gemini-3.5-flash-lite');
            $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$this->apiKey}";
            $response = Http::timeout(25)->connectTimeout(5)->post($url, [
                'contents' => [
                    ['parts' => [['text' => $systemPrompt]]]
                ],
                'generationConfig' => [
                    'responseMimeType' => 'application/json',
                ],
            ]);

            if ($response->successful()) {
                $text = $response->json('candidates.0.content.parts.0.text', '');
                $cleanJson = preg_replace('/```json\s*|\s*```/', '', trim($text));
                $parsed = json_decode($cleanJson, true);
                if (is_array($parsed)) {
                    return $parsed;
                }
            }
            if ($response->failed()) {
                \Log::warning('Gemini ticket analysis request failed', [
                    'status' => $response->status(),
                    'body' => \Illuminate\Support\Str::limit($response->body(), 500),
                ]);
            }
        } elseif (in_array($this->provider, ['openai', 'deepseek'], true)) {
            $apiUrl = $this->provider === 'deepseek'
                ? 'https://api.deepseek.com/chat/completions'
                : 'https://api.openai.com/v1/chat/completions';
            $response = Http::timeout(25)->connectTimeout(5)->withToken($this->apiKey)->post($apiUrl, [
                'model' => $this->provider === 'deepseek' ? env('DEEPSEEK_MODEL', 'deepseek-chat') : 'gpt-4o-mini',
                'messages' => [
                    ['role' => 'system', 'content' => 'Return valid JSON only.'],
                    ['role' => 'user', 'content' => $systemPrompt]
                ],
                'response_format' => ['type' => 'json_object']
            ]);

            if ($response->successful()) {
                $parsed = json_decode($response->json('choices.0.message.content'), true);
                if (is_array($parsed)) {
                    return $parsed;
                }
            }
        }

        return $this->localTicketAnalysis($title, $description, $employee, $asset, $specs);
    }

    /**
     * Smart Rule-Based Local AI Ticket Analyzer Engine
     */
    protected function localTicketAnalysis(string $title, string $description, string $employee, ?string $asset, ?string $specs): array
    {
        $text = mb_strtolower($title . ' ' . $description . ' ' . ($specs ?? ''));

        // Category & Priority Detection
        $category = 'Hardware & Software Support';
        $priority = 'Medium';
        $diagnosis = 'Terjadi indikasi kendala operasional pada perangkat atau sistem pengguna.';
        $steps = [];

        if (Str::contains($text, ['wifi', 'internet', 'lemot', 'lan', 'jaringan', 'router', 'switch', 'rto', 'ip', 'ping', 'kabel', 'dc', 'terputus'])) {
            $category = 'Infrastruktur Jaringan';
            $priority = Str::contains($text, ['mati', 'total', 'semua', 'down']) ? 'Critical' : 'High';
            $diagnosis = 'Potensi kendala pada alokasi IP Address, kabel LAN terlepas, atau kelebihan beban traffic pada Access Point/Switch.';
            $steps = [
                'Lakukan ping test ke gateway dan IP target perangkat user.',
                'Periksa fisik sambungan kabel LAN dan indikator LED port switch.',
                'Verifikasi alokasi IP Address dan subnet mask pada menu Network / IP.',
                'Restart interface WiFi/Ethernet pada laptop atau restart Access Point terdekat jika diperlukan.'
            ];
        } elseif (Str::contains($text, ['printer', 'cetak', 'kertas', 'toner', 'merekat', 'macet', 'jammed', 'tinta'])) {
            $category = 'Hardware & Software Support';
            $priority = 'Medium';
            $diagnosis = 'Kendala fisik mekanis printer, penyumbatan toner/tinta, atau spooler driver print bermasalah.';
            $steps = [
                'Cek status indikator error pada layar/lampu printer.',
                'Periksa tingkat sisa toner/cartridge dan bersihkan piringan kertas dari paper jam.',
                'Restart service "Print Spooler" pada sistem operasi Windows user.',
                'Cek koneksi kabel USB atau pastikan IP Printer dapat di-ping dari jaringan.'
            ];
        } elseif (Str::contains($text, ['cctv', 'kamera', 'nvr', 'dvr', 'rekaman', 'blur', 'layar hitam'])) {
            $category = 'CCTV & Security System';
            $priority = Str::contains($text, ['kehilangan', 'investigasi', 'mati']) ? 'High' : 'Medium';
            $diagnosis = 'Gangguan suplai daya POE Switch, NVR channel terputus, atau kerusakan sensor kamera CCTV.';
            $steps = [
                'Periksa status port POE pada switch jaringan CCTV.',
                'Cek rekaman dan status channel pada IP NVR.',
                'Lakukan restart koneksi channel kamera terkait.',
                'Lakukan inspeksi fisik fisik kabel UTP/konektor RJ45 outdoor.'
            ];
        } elseif (Str::contains($text, ['blue screen', 'bsod', 'hang', 'restart', 'mati total', 'harddisk', 'ram', 'ssd'])) {
            $category = 'Hardware & Software Support';
            $priority = 'High';
            $diagnosis = 'Potensi kegagalan hardware (RAM/SSD/Harddisk corrupt) atau overheated CPU thermal paste.';
            $steps = [
                'Lakukan diagnosa Memory Diagnostics Tool (RAM) dan Cek Health SSD/Harddisk.',
                'Bersihkan kotoran/debu pada slot RAM dan kipas pendingin processor.',
                'Lakukan pemeriksaan system event log Windows untuk melacak kode stop BSOD.',
                'Siapkan perangkat cadangan (replacement asset) jika dibutuhkan perbaikan intensif.'
            ];
        } else {
            $steps = [
                'Lakukan verifikasi kendala secara langsung di lokasi pengguna (On-site Support).',
                'Periksa log aktivitas dan spesifikasi teknis perangkat terkait.',
                'Lakukan update/re-install aplikasi atau perbaiki konfigurasi sistem.',
                'Konfirmasi kembali ke pengguna bahwa sistem sudah berfungsi normal.'
            ];
        }

        $replyDraft = "Halo Kak {$employee},\n\nTerima kasih telah melaporkan kendala terkait \"{$title}\". Tim IT Support telah menerima tiket Anda dan sedang melakukan analisis teknis. Kami akan segera menghubungi atau mendatangi lokasi Anda untuk penanganan lebih lanjut.\n\nSalam hangat,\nTim IT Support";

        return [
            'suggested_category' => $category,
            'suggested_priority' => $priority,
            'diagnosis' => $diagnosis,
            'resolution_steps' => $steps,
            'reply_draft' => $replyDraft,
            'is_ai_generated' => (bool) $this->apiKey,
        ];
    }

    /**
     * Query Assets via Natural Language
     */
    protected function queryAssetsNL(string $prompt): array
    {
        $promptLower = mb_strtolower($prompt);
        $query = Asset::with(['category', 'brand', 'location', 'currentAssignment.employee']);

        // Check for Warranty Expired query
        if (Str::contains($promptLower, ['garansi', 'habis', 'expired'])) {
            $allExpired = Asset::with(['category', 'location'])->whereNotNull('date_received')
                ->get()
                ->filter(function ($a) {
                    if (!$a->date_received) return false;
                    $exp = $a->expiry_date;
                    return $exp && $exp->isPast();
                });

            $totalExpired = $allExpired->count();
            $items = $allExpired->take(10)->map(fn($a) => [
                'title' => $a->name . ' (' . $a->asset_tag . ')',
                'subtitle' => 'Garansi Habis: ' . ($a->expiry_date ? $a->expiry_date->format('d M Y') : '-'),
                'badge' => 'badge-danger',
                'badge_text' => $a->status,
                'url' => route('assets.show', $a->id),
            ])->values()->all();

            $summaryText = $totalExpired > 10 
                ? "Ditemukan {$totalExpired} unit perangkat IT yang masa garansinya telah habis (menampilkan 10 data terbaru)."
                : "Ditemukan {$totalExpired} unit perangkat IT yang masa garansinya telah habis.";

            return [
                'summary' => $summaryText,
                'type' => 'assets',
                'items' => $items,
            ];
        }

        // Extract Brand from prompt
        $knownBrands = ['lenovo', 'dell', 'hp', 'epson', 'canon', 'asus', 'acer', 'mikrotik', 'unifi', 'hikvision', 'logitech', 'brother', 'oumax', 'toshiba', 'cooca', 'yamaha', 'solution'];
        $foundBrand = null;
        foreach ($knownBrands as $b) {
            if (Str::contains($promptLower, $b)) {
                $foundBrand = $b;
                break;
            }
        }

        // Extract Category from prompt
        $isComputerQuery = Str::contains($promptLower, ['laptop', 'komputer', 'pc', 'computer']);
        $isPrinterQuery = Str::contains($promptLower, ['printer', 'cetak']);
        $isCctvQuery = Str::contains($promptLower, ['cctv', 'kamera']);
        $isSwitchQuery = Str::contains($promptLower, ['switch', 'router']);

        if ($foundBrand) {
            $query->where(function($q) use ($foundBrand) {
                $q->whereHas('brand', fn($b) => $b->where('name', 'like', "%{$foundBrand}%"))
                  ->orWhere('name', 'like', "%{$foundBrand}%");
            });
        }

        if ($isComputerQuery) {
            $query->whereHas('category', fn($q) => $q->whereIn('name', ['Computer', 'Laptop', 'Komputer', 'PC']));
        } elseif ($isPrinterQuery) {
            $query->whereHas('category', fn($q) => $q->where('name', 'like', '%printer%'));
        } elseif ($isCctvQuery) {
            $query->whereHas('category', fn($q) => $q->where('name', 'like', '%cctv%'));
        } elseif ($isSwitchQuery) {
            $query->whereHas('category', fn($q) => $q->where('name', 'like', '%switch%'));
        }

        $locationTerms = [];
        if (preg_match('/\b(?:lokasi|di|area|ruangan)\s+([\p{L}\p{N}][\p{L}\p{N}\s-]*?)(?:\s+(?:yang|dan|atau|apa)\b|\?|$)/iu', $promptLower, $locationMatch)) {
            $locationTerms[] = trim($locationMatch[1]);
        }
        if (Str::contains($promptLower, ['lab'])) {
            $locationTerms[] = 'lab';
        }
        $locationTerms = array_values(array_unique(array_filter($locationTerms)));
        if (!empty($locationTerms)) {
            $query->whereHas('location', function ($locationQuery) use ($locationTerms) {
                $locationQuery->where(function ($query) use ($locationTerms) {
                    foreach ($locationTerms as $locationTerm) {
                        $query->orWhere('name', 'like', '%' . $locationTerm . '%');
                    }
                });
            });
        }

        // Stop words cleanup to allow filtering by the remaining exact words
        $stopWords = ['asset', 'aset', 'komputer', 'laptop', 'printer', 'cctv', 'switch', 'access point', 'ap', 'router', 'perangkat', 'garansi', 'warranty', 'expired', 'pc', 'desktop', 'notebook', 'server', 'monitor', 'layar', 'display', 'storage', 'harddisk', 'hdd', 'ssd', 'ram', 'memory', 'cpu', 'processor', 'intel', 'amd', 'core i3', 'core i5', 'core i7', 'ryzen', 'vga', 'gpu', 'os', 'windows', 'linux', 'macbook', 'macos', 'tag', 'kode aset', 'sn', 'serial number', 'surat jalan', 'do', 'lenovo', 'dell', 'hp', 'epson', 'canon', 'asus', 'acer', 'mikrotik', 'unifi', 'hikvision', 'logitech', 'brother', 'oumax', 'toshiba', 'solution', 'cooca', 'yamaha', 'lokasi', 'di', 'area', 'ruangan', 'lab', 'available', 'tersedia', 'assigned', 'diserahkan', 'dipakai', 'maintenance', 'rusak', 'perawatan', 'tampilkan', 'cari', 'data', 'semua'];
        $cleanPrompt = preg_replace('/\b(' . implode('|', $stopWords) . ')\b/i', '', $promptLower);
        $cleanPrompt = trim(preg_replace('/\s+/', ' ', $cleanPrompt));

        if (!empty($cleanPrompt) && strlen($cleanPrompt) >= 2) {
            $query->where(function($q) use ($cleanPrompt) {
                $q->where('name', 'like', "%{$cleanPrompt}%")
                  ->orWhere('asset_tag', 'like', "%{$cleanPrompt}%")
                  ->orWhere('serial_number', 'like', "%{$cleanPrompt}%");
            });
        }

        // Status Filter
        if (Str::contains($promptLower, ['available', 'tersedia'])) {
            $query->where('status', 'Available');
        } elseif (Str::contains($promptLower, ['assigned', 'diserahkan', 'dipakai'])) {
            $query->where('status', 'Assigned');
        } elseif (Str::contains($promptLower, ['maintenance', 'rusak', 'perawatan'])) {
            $query->where('status', 'Maintenance');
        }

        $totalCount = (clone $query)->count();
        $assets = $query->latest()->take(10)->get();

        $items = $assets->map(fn($a) => [
            'title' => $a->name . ' [' . $a->asset_tag . ']',
            'subtitle' => 'Brand: ' . ($a->brand->name ?? '-') . ' • Cat: ' . ($a->category->name ?? 'Aset') . ' • Lokasi: ' . ($a->location->name ?? 'Tanpa Lokasi') . ($a->currentAssignment ? ' • User: ' . $a->currentAssignment->employee->name : ''),
            'badge' => $a->status === 'Available' ? 'badge-success' : ($a->status === 'Assigned' ? 'badge-info' : 'badge-warning'),
            'badge_text' => $a->status,
            'url' => route('assets.show', $a->id),
        ])->toArray();

        $brandTitle = $foundBrand ? ucfirst($foundBrand) : '';
        $showingSuffix = $totalCount > 10 ? " (menampilkan 10 data terbaru)" : "";
        
        if ($totalCount === 0) {
            $summary = "Tidak ditemukan perangkat aset IT {$brandTitle} yang sesuai dengan pencarian Anda.";
        } else {
            $summary = "Ditemukan {$totalCount} unit perangkat aset IT {$brandTitle}{$showingSuffix}.";
        }

        $answer = $totalCount === 0 && $foundBrand 
            ? "Perangkat merk {$brandTitle} tidak ditemukan dalam database. Silakan periksa daftar Merk atau Aset." 
            : null;

        return [
            'summary' => $summary,
            'answer' => $answer,
            'type' => 'assets',
            'items' => $items,
        ];
    }

    /**
     * Query IP Addresses via Natural Language
     */
    protected function queryIpsNL(string $prompt): array
    {
        $promptLower = mb_strtolower($prompt);

        // 1. Check if a specific IP Address pattern is mentioned in the prompt (e.g., 192.168.1.186)
        if (preg_match('/\b(?:[0-9]{1,3}\.){3}[0-9]{1,3}\b/', $prompt, $ipMatches)) {
            $targetIp = $ipMatches[0];
            $ipRecord = IpAddress::with(['asset.category', 'asset.brand', 'employee.department', 'vlan'])
                ->where('ip_address', $targetIp)
                ->orWhere('ip_address', 'like', "%{$targetIp}%")
                ->first();

            if ($ipRecord) {
                $statusText = $ipRecord->is_online === true 
                    ? 'ONLINE (Aktif / Terhubung)' 
                    : ($ipRecord->is_online === false ? 'OFFLINE (Mati / Tidak merespon)' : 'Belum Di-ping (' . $ipRecord->status . ')');

                $allocatedTo = $ipRecord->employee 
                    ? 'Terhubung ke Karyawan: ' . $ipRecord->employee->name . ' (' . ($ipRecord->employee->department->name ?? 'Divisi') . ')'
                    : ($ipRecord->asset ? 'Terhubung ke Perangkat: ' . $ipRecord->asset->name . ' [' . $ipRecord->asset->asset_tag . ']' : 'Belum dipetakan (Unmapped)');

                $summary = "IP Address {$ipRecord->ip_address} saat ini berstatus {$statusText}.";
                
                $details = [];
                $details[] = "Penggunaan: {$ipRecord->status}";
                $details[] = $allocatedTo;
                if ($ipRecord->mac_address) $details[] = "MAC: " . $ipRecord->mac_address;
                if ($ipRecord->gateway) $details[] = "Gateway: " . $ipRecord->gateway;
                if ($ipRecord->vlan) $details[] = "VLAN: " . $ipRecord->vlan->vlan_number . ' (' . $ipRecord->vlan->name . ')';

                $answer = implode(' • ', $details);

                $items = [];
                // Primary IP Item Card
                $items[] = [
                    'title' => '[IP ADDRESS] ' . $ipRecord->ip_address . ($ipRecord->vlan ? ' (VLAN ' . $ipRecord->vlan->vlan_number . ')' : ''),
                    'subtitle' => 'Status: ' . $ipRecord->status . ' • Gateway: ' . ($ipRecord->gateway ?? '-') . ($ipRecord->mac_address ? ' • MAC: ' . $ipRecord->mac_address : ''),
                    'badge' => $ipRecord->is_online === true ? 'badge-success' : ($ipRecord->is_online === false ? 'badge-danger' : 'badge-secondary'),
                    'badge_text' => $ipRecord->is_online === true ? 'Online' : ($ipRecord->is_online === false ? 'Offline' : $ipRecord->status),
                    'url' => route('ips.index', ['search' => $ipRecord->ip_address]),
                ];

                // Linked Employee Card
                if ($ipRecord->employee) {
                    $emp = $ipRecord->employee;
                    $empSubtitle = 'Divisi: ' . ($emp->department->name ?? 'Divisi');
                    if ($emp->anydesk_id) $empSubtitle .= ' • AnyDesk: ' . $emp->anydesk_id;
                    if ($emp->telephone_extension) $empSubtitle .= ' • Ext: ' . $emp->telephone_extension;

                    $items[] = [
                        'title' => '[PENGGUNA TERHUBUNG] ' . $emp->name . ' (NIK: ' . $emp->employee_id . ')',
                        'subtitle' => $empSubtitle,
                        'badge' => 'badge-info',
                        'badge_text' => 'User',
                        'url' => route('employees.show', $emp->id),
                    ];
                }

                // Linked Asset Card
                if ($ipRecord->asset) {
                    $ast = $ipRecord->asset;
                    $items[] = [
                        'title' => '[ASET TERHUBUNG] ' . $ast->name . ' [' . $ast->asset_tag . ']',
                        'subtitle' => 'Merk: ' . ($ast->brand->name ?? '-') . ' • Cat: ' . ($ast->category->name ?? 'Aset') . ' • S/N: ' . ($ast->serial_number ?? '-'),
                        'badge' => 'badge-primary',
                        'badge_text' => $ast->status,
                        'url' => route('assets.show', $ast->id),
                    ];
                }

                return [
                    'summary' => $summary,
                    'answer' => $answer,
                    'type' => 'ips',
                    'items' => $items,
                ];
            } else {
                return [
                    'summary' => "IP Address {$targetIp} tidak ditemukan dalam catatan database ITAM.",
                    'answer' => "Silakan periksa kembali nomor IP atau tambahkan IP {$targetIp} melalui menu Manajemen IP Address.",
                    'type' => 'ips',
                    'items' => [],
                ];
            }
        }

        // 2. General Listing / Filtering IP Query
        $query = IpAddress::with(['asset', 'employee', 'vlan']);

        // Detect VLAN filter in prompt (e.g. "vlan 1", "vlan id 10", "vlan number 2", "vlan 1")
        $vlanNum = null;
        if (preg_match('/\bvlan\s*(?:id|number|nomor)?\s*([0-9]+)\b/i', $promptLower, $vlanMatch)) {
            $vlanNum = (int)$vlanMatch[1];
        }

        if ($vlanNum !== null) {
            $query->whereHas('vlan', function ($q) use ($vlanNum) {
                $q->where('vlan_number', $vlanNum);
            });
        }

        // Status filter
        $statusLabel = '';
        if (Str::contains($promptLower, ['available', 'kosong', 'bebas', 'belum terpakai', 'belum dipakai'])) {
            $query->where('status', 'Available');
            $statusLabel = 'Available (kosong)';
        } elseif (Str::contains($promptLower, ['used', 'terpakai', 'dipakai', 'digunakan'])) {
            $query->where('status', 'Used');
            $statusLabel = 'Used (terpakai)';
        } elseif (Str::contains($promptLower, ['reserved', 'dipesan'])) {
            $query->where('status', 'Reserved');
            $statusLabel = 'Reserved (dipesan)';
        }

        if (Str::contains($promptLower, ['online', 'aktif'])) {
            $query->where('is_online', true);
            $statusLabel .= ($statusLabel ? ' & ' : '') . 'Online';
        } elseif (Str::contains($promptLower, ['offline', 'mati'])) {
            $query->where('is_online', false);
            $statusLabel .= ($statusLabel ? ' & ' : '') . 'Offline';
        }

        $totalCount = (clone $query)->count();
        $ips = $query->orderByRaw('INET_ATON(ip_address)')->take(10)->get();

        $items = $ips->map(fn($ip) => [
            'title' => $ip->ip_address . ($ip->vlan ? ' (VLAN ' . $ip->vlan->vlan_number . ' - ' . $ip->vlan->name . ')' : ''),
            'subtitle' => ($ip->employee ? 'User: ' . $ip->employee->name : ($ip->asset ? 'Perangkat: ' . $ip->asset->name : 'Unmapped')) . ' • Gateway: ' . ($ip->gateway ?? '-'),
            'badge' => $ip->is_online === true ? 'badge-success' : ($ip->is_online === false ? 'badge-danger' : ($ip->status === 'Available' ? 'badge-success' : 'badge-secondary')),
            'badge_text' => $ip->is_online === true ? 'Online' : ($ip->is_online === false ? 'Offline' : $ip->status),
            'url' => route('ips.index', ['search' => $ip->ip_address]),
        ])->toArray();

        $showingSuffix = $totalCount > 10 ? " (menampilkan 10 data pertama)" : "";
        $vlanSuffix = $vlanNum !== null ? " pada VLAN {$vlanNum}" : "";
        $statusSuffix = $statusLabel ? " berstatus {$statusLabel}" : "";

        if ($totalCount === 0) {
            $summary = "Tidak ditemukan IP Address{$statusSuffix}{$vlanSuffix}.";
        } else {
            $summary = "Ditemukan {$totalCount} IP Address{$statusSuffix}{$vlanSuffix}{$showingSuffix}.";
        }

        return [
            'summary' => $summary,
            'type' => 'ips',
            'items' => $items,
        ];
    }

    /**
     * Query Tickets via Natural Language
     */
    protected function queryTicketsNL(string $prompt): array
    {
        $query = Ticket::with(['employee', 'asset']);

        if (Str::contains($prompt, ['open', 'terbuka'])) {
            $query->where('status', 'Open');
        } elseif (Str::contains($prompt, ['progress', 'proses'])) {
            $query->where('status', 'In Progress');
        } elseif (Str::contains($prompt, ['closed', 'resolved', 'selesai'])) {
            $query->whereIn('status', ['Closed', 'Resolved']);
        }

        $stopWords = ['tiket', 'ticket', 'kendala', 'laporan', 'masalah', 'trouble', 'komplain', 'issue', 'open', 'progress', 'closed', 'resolved', 'prioritas', 'support', 'terbuka', 'proses', 'selesai', 'tampilkan', 'cari', 'data'];
        $cleanPrompt = preg_replace('/\b(' . implode('|', $stopWords) . ')\b/i', '', mb_strtolower($prompt));
        $cleanPrompt = trim(preg_replace('/\s+/', ' ', $cleanPrompt));

        if (!empty($cleanPrompt) && strlen($cleanPrompt) >= 2) {
            $query->where(function($q) use ($cleanPrompt) {
                $q->where('title', 'like', "%{$cleanPrompt}%")
                  ->orWhere('ticket_number', 'like', "%{$cleanPrompt}%");
            });
        }

        $totalCount = (clone $query)->count();
        $tickets = $query->latest()->take(10)->get();

        $items = $tickets->map(fn($t) => [
            'title' => '#' . $t->ticket_number . ' - ' . $t->title,
            'subtitle' => 'Pelapor: ' . ($t->employee->name ?? '-') . ' • Prioritas: ' . $t->priority . ' • Kategori: ' . ($t->category ?? '-'),
            'badge' => $t->status === 'Open' ? 'badge-danger' : ($t->status === 'In Progress' ? 'badge-warning' : 'badge-success'),
            'badge_text' => $t->status,
            'url' => route('tickets.edit', $t->id),
        ])->toArray();

        $showingSuffix = $totalCount > 10 ? " (menampilkan 10 tiket terbaru)" : "";

        return [
            'summary' => "Ditemukan {$totalCount} tiket IT Support{$showingSuffix}.",
            'type' => 'tickets',
            'items' => $items,
        ];
    }

    /**
     * Query Employees & AnyDesk Data via Natural Language
     */
    protected function queryInternalIntentNL(string $prompt): ?array
    {
        if ($this->containsAnyKeyword($prompt, ['maintenance', 'perawatan', 'servis', 'service', 'reparasi', 'biaya', 'repair', 'upgrade', 'pengeluaran', 'perbaikan'])) {
            return $this->queryMaintenancesNL($prompt);
        }
        if ($this->containsAnyKeyword($prompt, ['vault', 'password', 'sandi', 'brankas', 'kredensial', 'akun', 'login'])) {
            return $this->queryVaultsNL($prompt);
        }
        if ($this->containsAnyKeyword($prompt, ['vlan', 'subnet', 'segmentasi', 'netmask', 'gateway'])) {
            if ($this->containsAnyKeyword($prompt, ['ip', 'alamat ip', 'kosong', 'available', 'terpakai', 'used', 'online', 'offline', 'bebas'])) {
                return $this->queryIpsNL($prompt);
            }
            return $this->queryVlansNL($prompt);
        }
        if ($this->containsAnyKeyword($prompt, ['vendor', 'pemasok', 'supplier', 'distributor', 'mitra', 'sales', 'pembelian'])) {
            return $this->queryVendorsNL($prompt);
        }
        if ($this->containsAnyKeyword($prompt, ['log', 'aktivitas', 'activity', 'riwayat', 'history', 'audit', 'operator', 'kejadian', 'perubahan'])) {
            return $this->queryActivityLogsNL($prompt);
        }
        if ($this->containsAnyKeyword($prompt, ['asset', 'aset', 'komputer', 'laptop', 'printer', 'cctv', 'switch', 'router', 'perangkat', 'garansi', 'warranty', 'expired', 'pc', 'desktop', 'notebook', 'server', 'monitor', 'ssd', 'ram', 'cpu', 'processor'])) {
            return $this->queryAssetsNL($prompt);
        }
        if ($this->containsAnyKeyword($prompt, ['ip', 'ip address', 'alamat ip', 'jaringan', 'network', 'online', 'offline', 'mac', 'ping', 'wifi', 'ssid'])) {
            return $this->queryIpsNL($prompt);
        }
        if ($this->containsAnyKeyword($prompt, ['tiket', 'ticket', 'kendala', 'laporan', 'masalah', 'trouble', 'komplain', 'issue', 'open', 'progress', 'closed', 'resolved', 'prioritas', 'support'])) {
            return $this->queryTicketsNL($prompt);
        }
        if ($this->containsAnyKeyword($prompt, ['lisensi', 'license', 'software', 'perangkat lunak', 'license key', 'seats'])) {
            return $this->queryLicensesNL($prompt);
        }
        if ($this->containsAnyKeyword($prompt, ['user', 'pengguna', 'karyawan', 'pegawai', 'staf', 'staff', 'divisi', 'department', 'jabatan', 'position', 'nik', 'anydesk', 'remote', 'telepon', 'extension', 'email', 'lokasi'])) {
            return $this->queryEmployeesNL($prompt);
        }

        return null;
    }

    protected function containsAnyKeyword(string $prompt, array $keywords): bool
    {
        $escapedKeywords = array_map(fn ($keyword) => preg_quote($keyword, '/'), $keywords);
        return (bool) preg_match('/(?:^|[^\p{L}\p{N}])(?:' . implode('|', $escapedKeywords) . ')(?=$|[^\p{L}\p{N}])/iu', $prompt);
    }

    protected function queryEmployeeAssetsNL(string $prompt, ?string $category = null): array
    {
        $stopWords = ['apakah', 'ini', 'itu', 'user', 'karyawan', 'pegawai', 'punya', 'memiliki', 'asset', 'aset', 'perangkat', 'terkait', 'diberi', 'memakai', 'digunakan', 'milik', 'tampilkan', 'data', 'yang', 'printer', 'cetak'];
        $cleanPrompt = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $prompt);
        $nameTokens = array_values(array_filter(explode(' ', $cleanPrompt), fn ($word) => !in_array($word, $stopWords, true) && strlen($word) > 1));
        $employees = Employee::with(['department', 'location'])->get();
        $employee = $employees->sortByDesc(function ($candidate) use ($nameTokens) {
            $name = mb_strtolower($candidate->name);
            $nameParts = preg_split('/\s+/', $name);
            return collect($nameTokens)->filter(fn ($token) => collect($nameParts)->contains(fn ($namePart) => $namePart === $token || (strlen($token) > 3 && levenshtein($namePart, $token) <= 1)))->count();
        })->first(function ($candidate) use ($nameTokens) {
            $name = mb_strtolower($candidate->name);
            $nameParts = preg_split('/\s+/', $name);
            return count($nameTokens) > 0 && collect($nameTokens)->every(fn ($token) => collect($nameParts)->contains(fn ($namePart) => $namePart === $token || (strlen($token) > 3 && levenshtein($namePart, $token) <= 1)));
        });

        if (!$employee) {
            return [
                'summary' => 'Nama karyawan tidak ditemukan dalam database ITAM.',
                'type' => 'employee_assets',
                'answer' => 'Silakan gunakan nama karyawan yang tercatat di ITAM.',
                'items' => [],
                'sources' => [],
            ];
        }

        $assignments = $employee->assignments()
            ->with(['asset.category', 'asset.brand', 'asset.location'])
            ->where('status', 'Assigned');
        if ($category !== null) {
            $assignments->whereHas('asset.category', fn ($query) => $query->where('name', 'like', '%' . $category . '%'));
        }
        $assignments = $assignments->latest()->get();

        $items = $assignments->map(fn ($assignment) => [
            'title' => $assignment->asset->name . ' [' . $assignment->asset->asset_tag . ']',
            'subtitle' => 'User: ' . $employee->name . ' • Brand: ' . ($assignment->asset->brand->name ?? '-') . ' • Lokasi: ' . ($assignment->asset->location->name ?? 'Tanpa Lokasi'),
            'badge' => 'badge-info',
            'badge_text' => 'Assigned',
            'url' => route('assets.show', $assignment->asset->id),
        ])->values()->all();

        $hasAssets = count($items) > 0;

        return [
            'summary' => $hasAssets
                ? $employee->name . ' memiliki ' . count($items) . ' asset yang sedang di-assign.'
                : $employee->name . ' tidak memiliki asset yang sedang di-assign.',
            'type' => 'employee_assets',
            'answer' => $hasAssets ? 'Berikut asset yang terhubung ke karyawan tersebut.' : 'Tidak ditemukan assignment asset aktif untuk karyawan tersebut.',
            'items' => $items,
            'sources' => [],
        ];
    }

    protected function queryEmployeePrinterNL(string $prompt): array
    {
        $result = $this->queryEmployeeAssetsNL($prompt, 'printer');
        $count = count($result['items']);
        $employeeName = preg_replace('/ memiliki \d+ asset.*| tidak memiliki asset.*$/', '', $result['summary']);
        $result['summary'] = $count > 0
            ? $employeeName . ' memiliki ' . $count . ' printer yang sedang di-assign.'
            : $employeeName . ' tidak memiliki printer yang sedang di-assign.';
        $result['answer'] = $count > 0 ? 'Berikut printer yang terhubung ke karyawan tersebut.' : 'Tidak ditemukan assignment printer aktif untuk karyawan tersebut.';

        return $result;
    }

    protected function queryDepartmentAssetRankingNL(): array
    {
        $ranking = \App\Models\Department::query()
            ->select('departments.id', 'departments.name')
            ->selectRaw('COUNT(DISTINCT asset_assignments.asset_id) as asset_count')
            ->leftJoin('employees', 'employees.department_id', '=', 'departments.id')
            ->leftJoin('asset_assignments', function ($join) {
                $join->on('asset_assignments.employee_id', '=', 'employees.id')
                    ->where('asset_assignments.status', '=', 'Assigned');
            })
            ->groupBy('departments.id', 'departments.name')
            ->orderByDesc('asset_count')
            ->orderBy('departments.name')
            ->get();

        $topCount = (int) ($ranking->first()->asset_count ?? 0);
        $leaders = $ranking->filter(fn ($department) => (int) $department->asset_count === $topCount && $topCount > 0);
        $items = $ranking->take(10)->map(fn ($department) => [
            'title' => $department->name,
            'subtitle' => 'Asset yang sedang di-assign: ' . number_format((int) $department->asset_count, 0, ',', '.'),
            'badge' => (int) $department->asset_count === $topCount && $topCount > 0 ? 'badge-primary' : 'badge-info',
            'badge_text' => (int) $department->asset_count === $topCount && $topCount > 0 ? 'Terbanyak' : 'Asset',
            'url' => route('departments.index', ['search' => $department->name]),
        ])->values()->all();

        if ($topCount === 0) {
            return [
                'summary' => 'Belum ada asset yang sedang di-assign kepada karyawan berdasarkan divisi.',
                'type' => 'department_assets',
                'answer' => 'Data assignment aktif belum cukup untuk menentukan divisi dengan asset terbanyak.',
                'items' => $items,
                'sources' => [],
            ];
        }

        $leaderNames = $leaders->pluck('name')->implode(', ');

        return [
            'summary' => $leaderNames . ' memiliki asset aktif paling banyak, yaitu ' . number_format($topCount, 0, ',', '.') . ' asset.',
            'type' => 'department_assets',
            'answer' => 'Perhitungan berdasarkan asset yang memiliki assignment aktif kepada karyawan pada masing-masing divisi. Berikut ranking 10 divisi teratas.',
            'items' => $items,
            'sources' => [],
        ];
    }

    protected function querySystemOverviewNL(): array
    {
        $modules = [
            'Aset' => Asset::count(),
            'Assignment aset' => \App\Models\AssetAssignment::count(),
            'Karyawan/pengguna' => Employee::count(),
            'Tiket support' => Ticket::count(),
            'Alamat IP' => IpAddress::count(),
            'Maintenance' => Maintenance::count(),
            'Lisensi software' => SoftwareLicense::count(),
            'Password vault' => PasswordVault::count(),
            'VLAN' => Vlan::count(),
            'Vendor' => Vendor::count(),
            'Log aktivitas' => ActivityLog::count(),
            'Kategori' => \App\Models\Category::count(),
            'Merek' => \App\Models\Brand::count(),
            'Departemen' => Department::count(),
            'Lokasi' => \App\Models\Location::count(),
        ];

        $answer = collect($modules)
            ->map(fn ($count, $module) => '- ' . $module . ': ' . number_format($count, 0, ',', '.'))
            ->implode("\n");

        return [
            'summary' => 'Ringkasan seluruh modul ITAM berhasil dibaca dari database.',
            'type' => 'system_overview',
            'answer' => $answer . "\n\nCatatan: isi password vault, token, dan kredensial sensitif tidak ditampilkan di chatbot.",
            'items' => [],
            'sources' => [],
        ];
    }

    protected function queryEmployeesNL(string $prompt): array
    {
        $promptLower = mb_strtolower($prompt);
        $promptLower = preg_replace('/\b(snydesk|anydes[kc]|anydek|anydesk)\b/i', 'anydesk', $promptLower);
        $query = Employee::with(['department', 'location', 'assignments']);
        
        $isAnyDeskQuery = Str::contains($promptLower, ['anydesk', 'remote', 'password anydesk', 'id anydesk']);
        $isMissingAnyDeskQuery = $isAnyDeskQuery && Str::contains($promptLower, ['tidak punya', 'tanpa', 'tidak memiliki', 'belum punya', 'belum memiliki', 'tidak ada', 'belum ada']);
        $isNoAssetQuery = Str::contains($promptLower, ['tidak punya', 'tanpa', 'tidak memiliki', 'bebas', '0 aset', 'belum punya']);

        if ($isMissingAnyDeskQuery) {
            $query->where(function ($q) {
                $q->whereNull('anydesk_id')->orWhere('anydesk_id', '');
            });
        } elseif ($isNoAssetQuery && !$isAnyDeskQuery) {
            $query->whereDoesntHave('assignments', fn($q) => $q->where('status', 'Assigned'));
        }

        if ($isAnyDeskQuery) {
            $searchTerm = trim(str_replace(['anydesk', 'remote', 'password', 'id', 'data', 'karyawan', 'user', 'pegawai', 'minta', 'cari', 'tolong', 'tampilkan', 'daftar', 'yang', 'tidak', 'memiliki', 'punya', 'tanpa', 'belum', 'ada'], '', $promptLower));
            if (!$isMissingAnyDeskQuery && !empty($searchTerm) && strlen($searchTerm) >= 2) {
                $query->where(function($q) use ($searchTerm) {
                    $q->where('name', 'like', "%{$searchTerm}%")
                      ->orWhere('employee_id', 'like', "%{$searchTerm}%")
                      ->orWhere('anydesk_id', 'like', "%{$searchTerm}%");
                });
            } elseif (!$isMissingAnyDeskQuery) {
                $query->whereNotNull('anydesk_id')->where('anydesk_id', '!=', '');
            }
        } else {
            $words = explode(' ', $promptLower);
            $searchWords = array_filter($words, fn($w) => !in_array($w, ['user', 'karyawan', 'pegawai', 'divisi', 'department', 'nik', 'orang', 'supervisor', 'cari', 'tampilkan', 'daftar', 'siapa', 'minta']));
            if (!empty($searchWords)) {
                $nameSearch = implode(' ', $searchWords);
                if (strlen($nameSearch) >= 2) {
                    $query->where(function($q) use ($nameSearch) {
                        $q->where('name', 'like', "%{$nameSearch}%")
                          ->orWhere('employee_id', 'like', "%{$nameSearch}%")
                          ->orWhere('login_username', 'like', "%{$nameSearch}%")
                          ->orWhere('email', 'like', "%{$nameSearch}%");
                    });
                }
            }
        }

        $totalCount = (clone $query)->count();
        $employees = $query->latest()->take(10)->get();

        $items = $employees->map(function($e) {
            $subtitle = 'Divisi: ' . ($e->department->name ?? 'Divisi') . ' • Lokasi: ' . ($e->location->name ?? 'Lokasi');
            
            if ($e->anydesk_id) {
                $subtitle .= ' • AnyDesk ID: ' . $e->anydesk_id . ($e->anydesk_password ? ' (Pass: ' . $e->anydesk_password . ')' : '');
            }
            if ($e->telephone_extension) {
                $subtitle .= ' • Ext: ' . $e->telephone_extension;
            }

            return [
                'title' => $e->name . ' (NIK: ' . $e->employee_id . ')',
                'subtitle' => $subtitle,
                'badge' => $e->anydesk_id ? 'badge-success' : 'badge-info',
                'badge_text' => $e->anydesk_id ? 'AnyDesk' : $e->status,
                'url' => route('employees.show', $e->id),
            ];
        })->toArray();

        $showingSuffix = $totalCount > 10 ? " (menampilkan 10 data terbaru)" : "";
        if ($isMissingAnyDeskQuery) {
            $summary = "Ditemukan {$totalCount} pengguna yang belum memiliki AnyDesk{$showingSuffix}.";
        } elseif ($isAnyDeskQuery) {
            $summary = "Ditemukan {$totalCount} data koneksi AnyDesk remote karyawan{$showingSuffix}.";
        } elseif ($isNoAssetQuery) {
            $summary = "Terdapat {$totalCount} pengguna/karyawan yang tidak memiliki aset IT terdaftar{$showingSuffix}.";
        } else {
            $summary = "Ditemukan {$totalCount} data pengguna ITAM{$showingSuffix}.";
        }

        return [
            'summary' => $summary,
            'type' => 'employees',
            'items' => $items,
        ];
    }

    /**
     * Query Software Licenses via Natural Language
     */
    protected function queryLicensesNL(string $prompt): array
    {
        $query = SoftwareLicense::with('pic');

        $stopWords = ['lisensi', 'license', 'software', 'perangkat lunak', 'license key', 'seats', 'tampilkan', 'cari', 'data'];
        $cleanPrompt = preg_replace('/\b(' . implode('|', $stopWords) . ')\b/i', '', mb_strtolower($prompt));
        $cleanPrompt = trim(preg_replace('/\s+/', ' ', $cleanPrompt));

        if (!empty($cleanPrompt) && strlen($cleanPrompt) >= 2) {
            $query->where(function($q) use ($cleanPrompt) {
                $q->where('name', 'like', "%{$cleanPrompt}%")
                  ->orWhere('license_key', 'like', "%{$cleanPrompt}%");
            });
        }

        $totalCount = (clone $query)->count();
        $licenses = $query->latest()->take(10)->get();

        $items = $licenses->map(fn($l) => [
            'title' => $l->name,
            'subtitle' => 'Kunci: ' . Str::limit($l->license_key, 20) . ' • PIC: ' . ($l->pic->name ?? '-') . ' • Seats: ' . $l->total_seats,
            'badge' => 'badge-primary',
            'badge_text' => $l->expiry_date ? $l->expiry_date->format('d M Y') : 'Lifetime',
            'url' => route('software_licenses.index', ['search' => $l->name]),
        ])->toArray();

        return [
            'summary' => "Ditemukan {$totalCount} data lisensi perangkat lunak.",
            'type' => 'licenses',
            'items' => $items,
        ];
    }

    /**
     * Query Maintenance / Servis / Perawatan via Natural Language
     */
    protected function queryMaintenancesNL(string $prompt): array
    {
        $query = Maintenance::with(['asset', 'vendor']);

        if (Str::contains($prompt, ['ongoing', 'proses', 'berjalan'])) {
            $query->where('status', 'Ongoing');
        } elseif (Str::contains($prompt, ['completed', 'selesai'])) {
            $query->where('status', 'Completed');
        }

        $stopWords = ['maintenance', 'perawatan', 'servis', 'service', 'reparasi', 'biaya', 'repair', 'upgrade', 'pengeluaran', 'perbaikan', 'ongoing', 'proses', 'berjalan', 'completed', 'selesai', 'tampilkan', 'cari', 'data'];
        $cleanPrompt = preg_replace('/\b(' . implode('|', $stopWords) . ')\b/i', '', mb_strtolower($prompt));
        $cleanPrompt = trim(preg_replace('/\s+/', ' ', $cleanPrompt));

        if (!empty($cleanPrompt) && strlen($cleanPrompt) >= 2) {
            $query->where(function($q) use ($cleanPrompt) {
                $q->where('description', 'like', "%{$cleanPrompt}%")
                  ->orWhereHas('asset', fn($aq) => $aq->where('name', 'like', "%{$cleanPrompt}%")->orWhere('asset_tag', 'like', "%{$cleanPrompt}%"));
            });
        }

        $totalCount = (clone $query)->count();
        $maintenances = $query->latest()->take(10)->get();

        $items = $maintenances->map(fn($m) => [
            'title' => 'Servis Aset: ' . ($m->asset->name ?? 'Perangkat') . ' [' . ($m->asset->asset_tag ?? '-') . ']',
            'subtitle' => 'Tipe: ' . $m->type . ' • Biaya: Rp ' . number_format($m->cost, 0, ',', '.') . ' • Vendor: ' . ($m->vendor->name ?? '-'),
            'badge' => $m->status === 'Ongoing' ? 'badge-warning' : 'badge-success',
            'badge_text' => $m->status,
            'url' => route('maintenances.index', ['search' => $m->asset->name ?? '']),
        ])->toArray();

        $totalCost = Maintenance::sum('cost');
        $formattedCost = 'Rp ' . number_format($totalCost, 0, ',', '.');
        $showingSuffix = $totalCount > 10 ? " (menampilkan 10 data terbaru)" : "";

        return [
            'summary' => "Ditemukan {$totalCount} catatan perbaikan/maintenance{$showingSuffix}. Total akumulasi biaya servis: {$formattedCost}.",
            'type' => 'maintenances',
            'items' => $items,
        ];
    }

    /**
     * Query Password Vault via Natural Language
     */
    protected function queryVaultsNL(string $prompt): array
    {
        $query = PasswordVault::query();

        $stopWords = ['vault', 'password', 'sandi', 'brankas', 'kredensial', 'akun', 'login', 'pass', 'username', 'akses', 'perangkat', 'cctv', 'router', 'switch', 'tampilkan', 'cari', 'data', 'siapa', 'yang', 'untuk'];
        $cleanPrompt = preg_replace('/\b(' . implode('|', $stopWords) . ')\b/i', '', mb_strtolower($prompt));
        $cleanPrompt = trim(preg_replace('/\s+/', ' ', $cleanPrompt));

        if (!empty($cleanPrompt) && strlen($cleanPrompt) >= 2) {
            $query->where(function($q) use ($cleanPrompt) {
                $q->where('device_name', 'like', "%{$cleanPrompt}%")
                  ->orWhere('username', 'like', "%{$cleanPrompt}%")
                  ->orWhere('category', 'like', "%{$cleanPrompt}%");
            });
        }

        $totalCount = (clone $query)->count();
        $vaults = $query->latest()->take(10)->get();

        $items = $vaults->map(fn($v) => [
            'title' => 'Akun/Perangkat: ' . $v->device_name,
            'subtitle' => 'Username: ' . $v->username . ' • Kategori: ' . ($v->category ?? 'Umum'),
            'badge' => 'badge-info',
            'badge_text' => 'Brankas Sandi',
            'url' => route('password_vaults.index', ['search' => $v->device_name]),
        ])->toArray();

        return [
            'summary' => "Ditemukan {$totalCount} entri brankas kata sandi perangkat/sistem.",
            'type' => 'password_vaults',
            'items' => $items,
        ];
    }

    /**
     * Query VLANs via Natural Language
     */
    protected function queryVlansNL(string $prompt): array
    {
        $totalCount = Vlan::count();
        $vlans = Vlan::orderBy('vlan_number')->take(10)->get();

        $items = $vlans->map(fn($v) => [
            'title' => 'VLAN ' . $v->vlan_number . ' - ' . $v->name,
            'subtitle' => 'Subnet: ' . $v->subnet . ' • Gateway: ' . ($v->gateway ?? '-'),
            'badge' => 'badge-primary',
            'badge_text' => 'VLAN ' . $v->vlan_number,
            'url' => route('vlans.index', ['search' => $v->vlan_number]),
        ])->toArray();

        return [
            'summary' => "Ditemukan {$totalCount} konfigurasi segmentasi VLAN jaringan.",
            'type' => 'vlans',
            'items' => $items,
        ];
    }

    /**
     * Query Vendors via Natural Language
     */
    protected function queryVendorsNL(string $prompt): array
    {
        $query = Vendor::query();

        $stopWords = ['vendor', 'pemasok', 'supplier', 'distributor', 'mitra', 'sales', 'pembelian', 'tampilkan', 'cari', 'data'];
        $cleanPrompt = preg_replace('/\b(' . implode('|', $stopWords) . ')\b/i', '', mb_strtolower($prompt));
        $cleanPrompt = trim(preg_replace('/\s+/', ' ', $cleanPrompt));

        if (!empty($cleanPrompt) && strlen($cleanPrompt) >= 2) {
            $query->where(function($q) use ($cleanPrompt) {
                $q->where('name', 'like', "%{$cleanPrompt}%")
                  ->orWhere('contact_person', 'like', "%{$cleanPrompt}%");
            });
        }

        $totalCount = (clone $query)->count();
        $vendors = $query->latest()->take(10)->get();

        $items = $vendors->map(fn($vd) => [
            'title' => $vd->name,
            'subtitle' => 'Kontak: ' . ($vd->contact_person ?? '-') . ' • Telp: ' . ($vd->phone ?? '-') . ' • Email: ' . ($vd->email ?? '-'),
            'badge' => 'badge-secondary',
            'badge_text' => 'Vendor',
            'url' => route('vendors.index', ['search' => $vd->name]),
        ])->toArray();

        return [
            'summary' => "Ditemukan {$totalCount} data vendor/pemasok perangkat.",
            'type' => 'vendors',
            'items' => $items,
        ];
    }

    /**
     * Query Activity Log Audit Trail via Natural Language
     */
    protected function queryActivityLogsNL(string $prompt): array
    {
        $totalCount = ActivityLog::count();
        $logs = ActivityLog::with('user')->latest()->take(10)->get();

        $items = $logs->map(fn($l) => [
            'title' => '[' . strtoupper($l->action) . '] ' . $l->description,
            'subtitle' => 'Operator: ' . ($l->user->name ?? 'Sistem') . ' • Waktu: ' . ($l->created_at ? $l->created_at->format('d M Y H:i') : '-'),
            'badge' => $l->action === 'created' ? 'badge-success' : ($l->action === 'deleted' ? 'badge-danger' : 'badge-info'),
            'badge_text' => ucfirst($l->action),
            'url' => route('dashboard'),
        ])->toArray();

        return [
            'summary' => "Ditemukan {$totalCount} log rekam aktivitas audit trail sistem.",
            'type' => 'activity_logs',
            'items' => $items,
        ];
    }

    /**
     * Comprehensive App-Wide Multi-Entity Search & Deep Connected Data Resolver
     */
    protected function queryGeneralNL(string $prompt, bool $returnNullIfEmpty = false): ?array
    {
        $promptTrimmed = trim($prompt);
        $promptLower = mb_strtolower($promptTrimmed);

        // 1. Search across all primary models
        $assets = Asset::with(['category', 'brand', 'location', 'currentAssignment.employee'])
            ->where('name', 'like', "%{$promptTrimmed}%")
            ->orWhere('asset_tag', 'like', "%{$promptTrimmed}%")
            ->orWhere('serial_number', 'like', "%{$promptTrimmed}%")
            ->take(5)->get();

        $employees = Employee::with(['department', 'location', 'assignments.asset'])
            ->where('name', 'like', "%{$promptTrimmed}%")
            ->orWhere('employee_id', 'like', "%{$promptTrimmed}%")
            ->orWhere('anydesk_id', 'like', "%{$promptTrimmed}%")
            ->orWhere('login_username', 'like', "%{$promptTrimmed}%")
            ->orWhere('email', 'like', "%{$promptTrimmed}%")
            ->take(5)->get();

        $tickets = Ticket::with(['employee', 'asset'])
            ->where('title', 'like', "%{$promptTrimmed}%")
            ->orWhere('ticket_number', 'like', "%{$promptTrimmed}%")
            ->take(5)->get();

        $ips = IpAddress::with(['asset', 'employee', 'vlan'])
            ->where('ip_address', 'like', "%{$promptTrimmed}%")
            ->orWhere('mac_address', 'like', "%{$promptTrimmed}%")
            ->take(5)->get();

        $maintenances = Maintenance::with(['asset', 'vendor'])
            ->where('description', 'like', "%{$promptTrimmed}%")
            ->take(3)->get();

        $licenses = SoftwareLicense::with('pic')
            ->where('name', 'like', "%{$promptTrimmed}%")
            ->orWhere('license_key', 'like', "%{$promptTrimmed}%")
            ->take(3)->get();

        $vaults = PasswordVault::where('device_name', 'like', "%{$promptTrimmed}%")
            ->orWhere('username', 'like', "%{$promptTrimmed}%")
            ->take(3)->get();

        $vendors = Vendor::where('name', 'like', "%{$promptTrimmed}%")
            ->take(3)->get();

        // 2. Single Exact Match Deep Expansion (If query matches 1 specific Employee or 1 specific Asset)
        if ($employees->count() === 1 && $assets->count() === 0 && $tickets->count() === 0) {
            $emp = $employees->first();
            $empAssets = Asset::whereHas('assignments', fn($q) => $q->where('employee_id', $emp->id)->where('status', 'Assigned'))->get();
            $empIps = IpAddress::where('employee_id', $emp->id)->get();
            $empTickets = Ticket::where('employee_id', $emp->id)->latest()->take(3)->get();

            $summary = "Data Detail Pengguna: {$emp->name} (NIK: {$emp->employee_id})";
            
            $details = [];
            $details[] = "Divisi: " . ($emp->department->name ?? '-');
            $details[] = "Lokasi: " . ($emp->location->name ?? '-');
            if ($emp->email) $details[] = "Email: " . $emp->email;
            if ($emp->phone) $details[] = "Telp: " . $emp->phone;
            if ($emp->anydesk_id) $details[] = "AnyDesk: " . $emp->anydesk_id . ($emp->anydesk_password ? " (Pass: {$emp->anydesk_password})" : "");
            if ($emp->telephone_extension) $details[] = "Ext: " . $emp->telephone_extension;
            $details[] = "Total Aset Dipegang: " . $empAssets->count() . " unit";

            $answer = implode(' • ', $details);

            $items = [];
            $items[] = [
                'title' => '[PENGGUNA] ' . $emp->name . ' (NIK: ' . $emp->employee_id . ')',
                'subtitle' => 'Divisi: ' . ($emp->department->name ?? '-') . ($emp->anydesk_id ? ' • AnyDesk: ' . $emp->anydesk_id : '') . ($emp->telephone_extension ? ' • Ext: ' . $emp->telephone_extension : ''),
                'badge' => 'badge-success',
                'badge_text' => 'User',
                'url' => route('employees.show', $emp->id),
            ];

            foreach ($empAssets as $a) {
                $items[] = [
                    'title' => '[ASET DIPEGANG] ' . $a->name . ' [' . $a->asset_tag . ']',
                    'subtitle' => 'Merk: ' . ($a->brand->name ?? '-') . ' • Cat: ' . ($a->category->name ?? 'Aset') . ' • S/N: ' . ($a->serial_number ?? '-'),
                    'badge' => 'badge-primary',
                    'badge_text' => $a->status,
                    'url' => route('assets.show', $a->id),
                ];
            }

            foreach ($empIps as $ip) {
                $items[] = [
                    'title' => '[ALAMAT IP] ' . $ip->ip_address,
                    'subtitle' => 'Status: ' . $ip->status . ' • Gateway: ' . ($ip->gateway ?? '-'),
                    'badge' => $ip->is_online ? 'badge-success' : 'badge-secondary',
                    'badge_text' => $ip->is_online ? 'Online' : $ip->status,
                    'url' => route('ips.index', ['search' => $ip->ip_address]),
                ];
            }

            foreach ($empTickets as $t) {
                $items[] = [
                    'title' => '[TIKET KHUSUS] #' . $t->ticket_number . ' - ' . $t->title,
                    'subtitle' => 'Prioritas: ' . $t->priority . ' • Status: ' . $t->status,
                    'badge' => 'badge-warning',
                    'badge_text' => $t->status,
                    'url' => route('tickets.edit', $t->id),
                ];
            }

            return ['summary' => $summary, 'answer' => $answer, 'type' => 'employees', 'items' => $items];
        }

        if ($assets->count() === 1 && $employees->count() === 0 && $tickets->count() === 0) {
            $ast = $assets->first();
            $assignedEmp = $ast->currentAssignment->employee ?? null;
            $astIps = IpAddress::where('asset_id', $ast->id)->get();
            $astTickets = Ticket::where('asset_id', $ast->id)->latest()->take(3)->get();
            $astMaintenances = Maintenance::where('asset_id', $ast->id)->latest()->take(3)->get();

            $summary = "Data Detail Aset: {$ast->name} [{$ast->asset_tag}]";

            $details = [];
            $details[] = "Merk: " . ($ast->brand->name ?? '-');
            $details[] = "Kategori: " . ($ast->category->name ?? '-');
            $details[] = "Status: " . $ast->status;
            if ($ast->serial_number) $details[] = "S/N: " . $ast->serial_number;
            if ($assignedEmp) $details[] = "Dipegang Oleh: " . $assignedEmp->name . ' (' . ($assignedEmp->department->name ?? 'Divisi') . ')';

            $answer = implode(' • ', $details);

            $items = [];
            $items[] = [
                'title' => '[ASET] ' . $ast->name . ' [' . $ast->asset_tag . ']',
                'subtitle' => 'Merk: ' . ($ast->brand->name ?? '-') . ' • Status: ' . $ast->status . ' • S/N: ' . ($ast->serial_number ?? '-'),
                'badge' => 'badge-primary',
                'badge_text' => $ast->status,
                'url' => route('assets.show', $ast->id),
            ];

            if ($assignedEmp) {
                $items[] = [
                    'title' => '[PENGGUNA SAAT INI] ' . $assignedEmp->name . ' (NIK: ' . $assignedEmp->employee_id . ')',
                    'subtitle' => 'Divisi: ' . ($assignedEmp->department->name ?? '-') . ($assignedEmp->anydesk_id ? ' • AnyDesk: ' . $assignedEmp->anydesk_id : ''),
                    'badge' => 'badge-info',
                    'badge_text' => 'User',
                    'url' => route('employees.show', $assignedEmp->id),
                ];
            }

            foreach ($astIps as $ip) {
                $items[] = [
                    'title' => '[ALAMAT IP] ' . $ip->ip_address,
                    'subtitle' => 'Status: ' . $ip->status . ' • Gateway: ' . ($ip->gateway ?? '-'),
                    'badge' => $ip->is_online ? 'badge-success' : 'badge-secondary',
                    'badge_text' => $ip->is_online ? 'Online' : $ip->status,
                    'url' => route('ips.index', ['search' => $ip->ip_address]),
                ];
            }

            foreach ($astMaintenances as $m) {
                $items[] = [
                    'title' => '[RIWAYAT SERVIS] ' . Str::limit($m->description, 35),
                    'subtitle' => 'Tipe: ' . $m->type . ' • Biaya: Rp ' . number_format($m->cost, 0, ',', '.'),
                    'badge' => 'badge-secondary',
                    'badge_text' => $m->status,
                    'url' => route('maintenances.index', ['search' => $ast->name]),
                ];
            }

            return ['summary' => $summary, 'answer' => $answer, 'type' => 'assets', 'items' => $items];
        }

        // 3. Multi-Entity General Search Output
        $items = [];
        foreach ($assets as $a) {
            $items[] = [
                'title' => '[ASET] ' . $a->name . ' [' . $a->asset_tag . ']',
                'subtitle' => 'Merk: ' . ($a->brand->name ?? '-') . ' • Cat: ' . ($a->category->name ?? '-') . ' • Status: ' . $a->status . ($a->currentAssignment ? ' • User: ' . $a->currentAssignment->employee->name : ''),
                'badge' => 'badge-primary',
                'badge_text' => $a->status,
                'url' => route('assets.show', $a->id),
            ];
        }

        foreach ($employees as $e) {
            $empSub = 'Divisi: ' . ($e->department->name ?? '-') . ' • NIK: ' . $e->employee_id;
            if ($e->anydesk_id) $empSub .= ' • AnyDesk: ' . $e->anydesk_id;
            $items[] = [
                'title' => '[USER] ' . $e->name . ' (NIK: ' . $e->employee_id . ')',
                'subtitle' => $empSub,
                'badge' => 'badge-success',
                'badge_text' => 'User',
                'url' => route('employees.show', $e->id),
            ];
        }

        foreach ($tickets as $t) {
            $items[] = [
                'title' => '[TIKET] #' . $t->ticket_number . ' - ' . $t->title,
                'subtitle' => 'Status: ' . $t->status . ' • Prioritas: ' . $t->priority . ' • Pelapor: ' . ($t->employee->name ?? '-'),
                'badge' => 'badge-warning',
                'badge_text' => $t->status,
                'url' => route('tickets.edit', $t->id),
            ];
        }

        foreach ($ips as $ip) {
            $items[] = [
                'title' => '[IP] ' . $ip->ip_address,
                'subtitle' => 'Status: ' . $ip->status . ' • ' . ($ip->employee ? 'User: ' . $ip->employee->name : ($ip->asset ? 'Perangkat: ' . $ip->asset->name : 'Unmapped')),
                'badge' => 'badge-info',
                'badge_text' => $ip->status,
                'url' => route('ips.index', ['search' => $ip->ip_address]),
            ];
        }

        foreach ($maintenances as $m) {
            $items[] = [
                'title' => '[SERVIS] ' . ($m->asset->name ?? 'Perangkat') . ' - ' . Str::limit($m->description, 30),
                'subtitle' => 'Biaya: Rp ' . number_format($m->cost, 0, ',', '.') . ' • Status: ' . $m->status,
                'badge' => 'badge-secondary',
                'badge_text' => $m->status,
                'url' => route('maintenances.index', ['search' => $m->description]),
            ];
        }

        foreach ($licenses as $l) {
            $items[] = [
                'title' => '[LISENSI] ' . $l->name,
                'subtitle' => 'Seats: ' . $l->total_seats . ' • PIC: ' . ($l->pic->name ?? '-'),
                'badge' => 'badge-dark',
                'badge_text' => 'Lisensi',
                'url' => route('software_licenses.index', ['search' => $l->name]),
            ];
        }

        foreach ($vaults as $v) {
            $items[] = [
                'title' => '[VAULT] ' . $v->device_name,
                'subtitle' => 'Username: ' . $v->username . ' • Kategori: ' . ($v->category ?? 'Umum'),
                'badge' => 'badge-dark',
                'badge_text' => 'Brankas Sandi',
                'url' => route('password_vaults.index', ['search' => $v->device_name]),
            ];
        }

        foreach ($vendors as $vd) {
            $items[] = [
                'title' => '[VENDOR] ' . $vd->name,
                'subtitle' => 'Kontak: ' . ($vd->contact_person ?? '-') . ' • Telp: ' . ($vd->phone ?? '-'),
                'badge' => 'badge-secondary',
                'badge_text' => 'Vendor',
                'url' => route('vendors.index', ['search' => $vd->name]),
            ];
        }

        if (count($items) > 0) {
            $summary = 'Menampilkan ' . count($items) . ' hasil data terkait untuk pencarian "' . $promptTrimmed . '".';
            $answer = null;
        } else {
            if ($returnNullIfEmpty) {
                return null;
            }

            // Live Web Search Fallback for broader internet knowledge
            $webSources = $this->fetchWebKnowledgeSources($promptTrimmed);
            $webResult = collect($webSources)->pluck('snippet')->filter()->implode("\n\n");
            if (!empty($webResult)) {
                $summary = 'Hasil Pencarian & Pengetahuan Internet untuk "' . $promptTrimmed . '":';
                $answer = $webResult;
            } else {
                $summary = 'Pencarian untuk "' . $promptTrimmed . '" tidak menemukan data yang cocok di database maupun internet.';
                $answer = 'Cobalah kata kunci seperti nama karyawan, tag aset (AST-xxx), merk (Lenovo, Dell), IP address, atau tiket.';
            }
        }

        return [
            'summary' => $summary,
            'answer' => $answer,
            'type' => 'general',
            'items' => $items,
            'sources' => $webSources ?? [],
        ];
    }

    /**
     * Fetch Live Technical Knowledge from Internet (DuckDuckGo / Open Web Search)
     */
    public function fetchWebKnowledge(string $query): ?string
    {
        $sources = $this->fetchWebKnowledgeSources($query);

        if (empty($sources)) {
            return null;
        }

        return collect($sources)->pluck('snippet')->filter()->implode("\n\n");
    }

    /**
     * Fetch concise web references while retaining their URLs for citations.
     */
    public function fetchWebKnowledgeSources(string $query): array
    {
        try {
            $encoded = urlencode($query);
            $response = Http::timeout(4)->get("https://api.duckduckgo.com/?q={$encoded}&format=json&no_html=1&skip_disambig=1");

            if ($response->successful()) {
                $data = $response->json();
                $abstract = $data['AbstractText'] ?? '';
                $heading = $data['Heading'] ?? '';
                $sources = [];

                if (!empty($abstract) && !empty($data['AbstractURL'])) {
                    $sources[] = [
                        'title' => $heading ?: 'Referensi web',
                        'url' => $data['AbstractURL'],
                        'snippet' => $abstract,
                    ];
                }

                if (!empty($data['RelatedTopics'])) {
                    foreach (array_slice($data['RelatedTopics'], 0, 3) as $topic) {
                        if (isset($topic['Text'], $topic['FirstURL']) && is_string($topic['Text']) && is_string($topic['FirstURL'])) {
                            $sources[] = [
                                'title' => Str::limit($topic['Text'], 80),
                                'url' => $topic['FirstURL'],
                                'snippet' => $topic['Text'],
                            ];
                        }
                    }
                }

                return $sources;
            }
        } catch (\Throwable $e) {
            \Log::warning("Web Knowledge Search Failed: " . $e->getMessage());
        }

        return [];
    }
}
