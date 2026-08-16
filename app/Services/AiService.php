<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\Employee;
use App\Models\IpAddress;
use App\Models\SoftwareLicense;
use App\Models\Ticket;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class AiService
{
    protected ?string $apiKey;
    protected string $provider;

    public function __construct()
    {
        $dbProvider = \App\Models\Setting::where('key', 'ai_provider')->value('value') ?: 'gemini';
        $dbGeminiKey = \App\Models\Setting::where('key', 'gemini_api_key')->value('value');
        $dbOpenaiKey = \App\Models\Setting::where('key', 'openai_api_key')->value('value');

        if ($dbProvider === 'openai' && !empty($dbOpenaiKey)) {
            $this->provider = 'openai';
            $this->apiKey = $dbOpenaiKey;
        } elseif ($dbProvider === 'gemini' && !empty($dbGeminiKey)) {
            $this->provider = 'gemini';
            $this->apiKey = $dbGeminiKey;
        } elseif ($dbProvider === 'off') {
            $this->provider = 'none';
            $this->apiKey = null;
        } else {
            $this->apiKey = config('services.ai.key') ?: env('GEMINI_API_KEY') ?: env('OPENAI_API_KEY');
            $this->provider = env('GEMINI_API_KEY') ? 'gemini' : (env('OPENAI_API_KEY') ? 'openai' : 'none');
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
    public function queryNaturalLanguage(string $prompt): array
    {
        $promptTrimmed = trim($prompt);
        $promptLower = mb_strtolower($promptTrimmed);

        // 0. Handle Greetings & Conversational Prompts
        if (in_array($promptLower, ['hai', 'halo', 'hi', 'helo', 'hello', 'selamat pagi', 'selamat siang', 'selamat sore', 'selamat malam', 'siapa anda', 'siapa kamu', 'help', 'bantuan'])) {
            return [
                'summary' => '👋 Halo! Saya adalah ITAM AI Assistant.',
                'type' => 'greeting',
                'answer' => 'Saya siap membantu Anda mengelola dan melacak aset IT, tiket kendala, alamat IP jaringan, lisensi software, dan data pengguna perusahaan. Anda dapat mengetik pertanyaan atau memilih rekomendasi di bawah!',
                'items' => [],
            ];
        }

        // 1. If API Key is configured, attempt Live AI API Call
        if ($this->apiKey && $this->provider !== 'none') {
            try {
                $liveResult = $this->callAiApiForQuery($promptTrimmed);
                if (!empty($liveResult)) {
                    return $liveResult;
                }
            } catch (\Throwable $e) {
                \Log::warning('Live AI Query call failed, falling back to local NLP engine: ' . $e->getMessage());
            }
        }

        // 2. Search Assets
        if (Str::contains($promptLower, ['asset', 'komputer', 'laptop', 'printer', 'cctv', 'switch', 'perangkat', 'garansi', 'pc', 'lenovo', 'dell', 'hp', 'epson', 'canon', 'asus', 'acer', 'mikrotik', 'unifi', 'hikvision', 'logitech', 'brother', 'oumax', 'toshiba'])) {
            return $this->queryAssetsNL($promptLower);
        }

        // 3. Search IP / Network
        if (Str::contains($promptLower, ['ip', 'jaringan', 'vlan', 'online', 'offline', 'mac', 'ping'])) {
            return $this->queryIpsNL($promptLower);
        }

        // 4. Search Tickets
        if (Str::contains($promptLower, ['tiket', 'ticket', 'kendala', 'laporan', 'open', 'progress', 'closed', 'rusak'])) {
            return $this->queryTicketsNL($promptLower);
        }

        // 5. Search Employees / Users
        if (Str::contains($promptLower, ['user', 'karyawan', 'pegawai', 'divisi', 'department', 'nik', 'orang'])) {
            return $this->queryEmployeesNL($promptLower);
        }

        // 6. Search Software / Licenses
        if (Str::contains($promptLower, ['lisensi', 'license', 'software', 'aplikasi', 'kunci'])) {
            return $this->queryLicensesNL($promptLower);
        }

        // General multi-entity fallback search
        return $this->queryGeneralNL($promptLower);
    }

    /**
     * Call Live Gemini / OpenAI API for Natural Language Query
     */
    protected function callAiApiForQuery(string $prompt): ?array
    {
        $totalAssets = Asset::count();
        $totalTickets = Ticket::count();
        $openTickets = Ticket::where('status', 'Open')->count();
        $totalIps = IpAddress::count();
        $usedIps = IpAddress::where('status', 'Used')->count();
        $totalEmployees = Employee::count();

        $systemPrompt = "Anda adalah ITAM AI Assistant, asisten cerdas untuk sistem manajemen aset IT perusahaan.
Data Ringkasan Sistem ITAM Saat Ini:
- Total Aset IT: {$totalAssets}
- Total Tiket Support: {$totalTickets} (Open: {$openTickets})
- Total IP Address: {$totalIps} (Terpakai: {$usedIps})
- Total Pengguna/Karyawan: {$totalEmployees}

Tugas: Jawab pertanyaan pengguna secara ringkas, ramah, dan profesional dalam Bahasa Indonesia. Kembalikan format JSON valid tanpa markdown formatting dengan key:
- summary: Ringkasan singkat jawaban (1-2 kalimat)
- answer: Jawaban/penjelasan detail atau panduan teknis yang bermanfaat bagi pengguna
- items: Array kosong [] jika tidak ada record spesifik yang ditautkan";

        if ($this->provider === 'gemini') {
            $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$this->apiKey}";
            $response = Http::post($url, [
                'contents' => [
                    ['parts' => [['text' => $systemPrompt . "\n\nPertanyaan User: " . $prompt]]]
                ]
            ]);

            if ($response->successful()) {
                $text = $response->json('candidates.0.content.parts.0.text', '');
                $cleanJson = preg_replace('/```json\s*|\s*```/', '', trim($text));
                $parsed = json_decode($cleanJson, true);
                if (is_array($parsed) && isset($parsed['summary'])) {
                    $parsed['type'] = 'live_ai';
                    $parsed['items'] = $parsed['items'] ?? [];
                    return $parsed;
                }
            }
        } elseif ($this->provider === 'openai') {
            $response = Http::withToken($this->apiKey)->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4o-mini',
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $prompt]
                ],
                'response_format' => ['type' => 'json_object']
            ]);

            if ($response->successful()) {
                $parsed = json_decode($response->json('choices.0.message.content'), true);
                if (is_array($parsed) && isset($parsed['summary'])) {
                    $parsed['type'] = 'live_ai';
                    $parsed['items'] = $parsed['items'] ?? [];
                    return $parsed;
                }
            }
        }

        return null;
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
            $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$this->apiKey}";
            $response = Http::post($url, [
                'contents' => [
                    ['parts' => [['text' => $systemPrompt]]]
                ]
            ]);

            if ($response->successful()) {
                $text = $response->json('candidates.0.content.parts.0.text', '');
                $cleanJson = preg_replace('/```json\s*|\s*```/', '', trim($text));
                $parsed = json_decode($cleanJson, true);
                if (is_array($parsed)) {
                    return $parsed;
                }
            }
        } elseif ($this->provider === 'openai') {
            $response = Http::withToken($this->apiKey)->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4o-mini',
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
            $items = Asset::with(['category', 'location'])->whereNotNull('date_received')
                ->get()
                ->filter(function ($a) {
                    if (!$a->date_received) return false;
                    $exp = $a->expiry_date;
                    return $exp && $exp->isPast();
                })
                ->take(10)
                ->map(fn($a) => [
                    'title' => $a->name . ' (' . $a->asset_tag . ')',
                    'subtitle' => 'Garansi Habis: ' . ($a->expiry_date ? $a->expiry_date->format('d M Y') : '-'),
                    'badge' => 'badge-danger',
                    'badge_text' => $a->status,
                    'url' => route('assets.show', $a->id),
                ])->values()->all();

            return [
                'summary' => 'Ditemukan ' . count($items) . ' perangkat IT yang masa garansinya telah habis.',
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

        // Status Filter
        if (Str::contains($promptLower, ['available', 'tersedia'])) {
            $query->where('status', 'Available');
        } elseif (Str::contains($promptLower, ['assigned', 'diserahkan', 'dipakai'])) {
            $query->where('status', 'Assigned');
        } elseif (Str::contains($promptLower, ['maintenance', 'rusak', 'perawatan'])) {
            $query->where('status', 'Maintenance');
        }

        $assets = $query->latest()->take(10)->get();

        $items = $assets->map(fn($a) => [
            'title' => $a->name . ' [' . $a->asset_tag . ']',
            'subtitle' => 'Brand: ' . ($a->brand->name ?? '-') . ' • Cat: ' . ($a->category->name ?? 'Aset') . ' • Lokasi: ' . ($a->location->name ?? 'Tanpa Lokasi') . ($a->currentAssignment ? ' • User: ' . $a->currentAssignment->employee->name : ''),
            'badge' => $a->status === 'Available' ? 'badge-success' : ($a->status === 'Assigned' ? 'badge-info' : 'badge-warning'),
            'badge_text' => $a->status,
            'url' => route('assets.show', $a->id),
        ])->toArray();

        $isCountQuery = Str::contains($promptLower, ['berapa', 'jumlah', 'total']);
        $brandTitle = $foundBrand ? ucfirst($foundBrand) : '';
        
        if ($isCountQuery) {
            $summary = "Terdapat " . count($items) . " unit perangkat {$brandTitle} terdaftar di database ITAM.";
        } else {
            $summary = "Ditemukan " . count($items) . " unit perangkat aset IT {$brandTitle} yang sesuai dengan kueri Anda.";
        }

        $answer = count($items) === 0 && $foundBrand 
            ? "Perangkat merk {$brandTitle} tidak ditemukan. Silakan periksa daftar Merk di menu Master Data." 
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
        $query = IpAddress::with(['asset', 'employee', 'vlan']);

        if (Str::contains($prompt, ['available', 'kosong', 'bebas'])) {
            $query->where('status', 'Available');
        } elseif (Str::contains($prompt, ['used', 'terpakai'])) {
            $query->where('status', 'Used');
        }

        if (Str::contains($prompt, ['online', 'aktif'])) {
            $query->where('is_online', true);
        } elseif (Str::contains($prompt, ['offline', 'mati'])) {
            $query->where('is_online', false);
        }

        $ips = $query->orderByRaw('INET_ATON(ip_address)')->take(8)->get();

        $items = $ips->map(fn($ip) => [
            'title' => $ip->ip_address . ($ip->vlan ? ' (VLAN ' . $ip->vlan->vlan_number . ')' : ''),
            'subtitle' => ($ip->employee ? 'User: ' . $ip->employee->name : ($ip->asset ? 'Perangkat: ' . $ip->asset->name : 'Unmapped')) . ' • Gateway: ' . ($ip->gateway ?? '-'),
            'badge' => $ip->is_online === true ? 'badge-success' : ($ip->is_online === false ? 'badge-danger' : 'badge-secondary'),
            'badge_text' => $ip->is_online === true ? 'Online' : ($ip->is_online === false ? 'Offline' : $ip->status),
            'url' => route('ips.index', ['search' => $ip->ip_address]),
        ])->toArray();

        return [
            'summary' => 'Ditemukan ' . count($items) . ' catatan IP Address jaringan.',
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

        $tickets = $query->latest()->take(8)->get();

        $items = $tickets->map(fn($t) => [
            'title' => '#' . $t->ticket_number . ' - ' . $t->title,
            'subtitle' => 'Pelapor: ' . ($t->employee->name ?? '-') . ' • Prioritas: ' . $t->priority . ' • Kategori: ' . ($t->category ?? '-'),
            'badge' => $t->status === 'Open' ? 'badge-danger' : ($t->status === 'In Progress' ? 'badge-warning' : 'badge-success'),
            'badge_text' => $t->status,
            'url' => route('tickets.edit', $t->id),
        ])->toArray();

        return [
            'summary' => 'Ditemukan ' . count($items) . ' tiket IT Support yang cocok.',
            'type' => 'tickets',
            'items' => $items,
        ];
    }

    /**
     * Query Employees via Natural Language
     */
    protected function queryEmployeesNL(string $prompt): array
    {
        $query = Employee::with(['department', 'location', 'assignments']);
        $isNoAssetQuery = Str::contains($prompt, ['tidak punya', 'tanpa', 'tidak memiliki', 'bebas', '0 aset', 'belum punya']);

        if ($isNoAssetQuery) {
            $query->whereDoesntHave('assignments', fn($q) => $q->where('status', 'Assigned'));
        }

        $employees = $query->latest()->take(10)->get();

        $items = $employees->map(fn($e) => [
            'title' => $e->name . ' (NIK: ' . $e->employee_id . ')',
            'subtitle' => ($e->department->name ?? 'Divisi') . ' • ' . ($e->location->name ?? 'Lokasi') . ' • Total Aset: ' . $e->assignments->where('status', 'Assigned')->count(),
            'badge' => $isNoAssetQuery ? 'badge-secondary' : 'badge-info',
            'badge_text' => $isNoAssetQuery ? 'Tanpa Aset' : $e->status,
            'url' => route('employees.show', $e->id),
        ])->toArray();

        $isCountQuery = Str::contains($prompt, ['berapa', 'jumlah', 'total']);
        if ($isNoAssetQuery) {
            $summary = 'Terdapat ' . count($items) . ' pengguna/karyawan yang tidak memiliki aset IT terdaftar.';
        } elseif ($isCountQuery) {
            $summary = 'Terdapat ' . count($items) . ' data pengguna ITAM terdaftar.';
        } else {
            $summary = 'Ditemukan ' . count($items) . ' data pengguna ITAM.';
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
        $licenses = SoftwareLicense::with('pic')->latest()->take(8)->get();

        $items = $licenses->map(fn($l) => [
            'title' => $l->name,
            'subtitle' => 'Kunci: ' . Str::limit($l->license_key, 20) . ' • PIC: ' . ($l->pic->name ?? '-') . ' • Seats: ' . $l->total_seats,
            'badge' => 'badge-primary',
            'badge_text' => $l->expiry_date ? $l->expiry_date->format('d M Y') : 'Lifetime',
            'url' => route('software_licenses.index', ['search' => $l->name]),
        ])->toArray();

        return [
            'summary' => 'Ditemukan ' . count($items) . ' data lisensi perangkat lunak.',
            'type' => 'licenses',
            'items' => $items,
        ];
    }

    /**
     * General Multi-Entity Search Fallback
     */
    protected function queryGeneralNL(string $prompt): array
    {
        $assets = Asset::where('name', 'like', "%{$prompt}%")
            ->orWhere('asset_tag', 'like', "%{$prompt}%")
            ->take(4)->get();

        $tickets = Ticket::where('title', 'like', "%{$prompt}%")
            ->orWhere('ticket_number', 'like', "%{$prompt}%")
            ->take(4)->get();

        $items = [];
        foreach ($assets as $a) {
            $items[] = [
                'title' => '[ASET] ' . $a->name . ' (' . $a->asset_tag . ')',
                'subtitle' => 'Status: ' . $a->status,
                'badge' => 'badge-info',
                'badge_text' => 'Aset',
                'url' => route('assets.show', $a->id),
            ];
        }
        foreach ($tickets as $t) {
            $items[] = [
                'title' => '[TIKET] #' . $t->ticket_number . ' - ' . $t->title,
                'subtitle' => 'Status: ' . $t->status,
                'badge' => 'badge-warning',
                'badge_text' => 'Tiket',
                'url' => route('tickets.edit', $t->id),
            ];
        }

        $summary = count($items) > 0 
            ? 'Menampilkan ' . count($items) . ' hasil pencarian AI gabungan untuk "' . $prompt . '".'
            : 'Pencarian untuk "' . $prompt . '" tidak menemukan data yang cocok di database.';

        $answer = count($items) === 0 
            ? 'Cobalah menggunakan kata kunci seperti: "laptop", "printer", "garansi habis", "tiket open", "IP available", atau klik tombol pertanyaan cepat di atas.' 
            : null;

        return [
            'summary' => $summary,
            'answer' => $answer,
            'type' => 'general',
            'items' => $items,
        ];
    }
}
