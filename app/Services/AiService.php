<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Asset;
use App\Models\BeritaAcara;
use App\Models\BeritaAcaraDisposal;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Department;
use App\Models\Document;
use App\Models\Employee;
use App\Models\IpAddress;
use App\Models\Location;
use App\Models\Maintenance;
use App\Models\PasswordVault;
use App\Models\SoftwareLicense;
use App\Models\StockOpnameSession;
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

        // 0.5. Answer capability & module search coverage questions
        if (preg_match('/\b(bisa|dapat)\s+(baca|cari|menampilkan|akses)\b.*\b(data|menu|apa|semua)\b/i', $promptLower)
            || preg_match('/\b(data|menu)\s+apa\s+saja\b/i', $promptLower)
            || preg_match('/\bapa\s+saja\s+yang\s+bisa\s+(dicari|dibaca)\b/i', $promptLower)) {
            return [
                'summary' => 'ITAM AI dapat membaca dan mencari seluruh data dari semua modul di aplikasi ITAM.',
                'type' => 'capabilities',
                'answer' => "Anda dapat mencari informasi dari seluruh menu aplikasi ITAM:\n\n"
                    . "1. 🖥️ **Aset IT**: Komputer, Laptop, Printer, Server, CCTV, Switch, Router, Monitor\n"
                    . "2. 🌐 **Alamat IP & Jaringan**: IP Address, MAC Address, Gateway, Status Online/Offline\n"
                    . "3. 👥 **Karyawan / Staf**: Nama, NIK, Divisi, AnyDesk ID, No. Telepon, Aset yang dipegang\n"
                    . "4. 🎫 **Tiket Support**: No. Tiket, Kendala, Prioritas, Status Solusi\n"
                    . "5. 🔧 **Maintenance & Perawatan**: Riwayat Servis, Biaya, Vendor, Status Ongoing/Completed\n"
                    . "6. 🔐 **Brankas Sandi (Vault)**: Akun & Kredensial Perangkat/Server\n"
                    . "7. 📜 **Lisensi Software**: Nama Software, Lisensi Key, Seats, Masa Berlaku\n"
                    . "8. 📄 **Berita Acara**: BA Serah Terima & BA Disposal Pemusnahan\n"
                    . "9. 📁 **Arsip Dokumen IT**: Dokumen Digital, Manual, SOP, Lampiran\n"
                    . "10. 📦 **Stock Opname**: Sesi Audit Fisik Aset & Inventarisasi\n"
                    . "11. 🏢 **Vendor & Rekanan**: Pemasok, Kontak Sales, Supplier\n"
                    . "12. 📍 **Master Data**: Lokasi/Ruangan, Departemen, Merk, Kategori\n\n"
                    . "Ketikkan kata kunci apapun (misal: 'server mtc 1', '192.168.1.143', 'reza', 'epson', 'tiket printer', 'brankas proxmox') untuk mulai mencari!",
                'items' => [],
                'sources' => [],
            ];
        }

        // 0.5. Answer identity and capability questions before entity searches.
        if (preg_match('/\b(ini|aplikasi ini|sistem ini|kamu|anda)\b.*\b(apa|siapa|untuk apa|fungsinya|kegunaannya)\b/i', $promptLower)
            || preg_match('/\b(jelaskan|jelaskanlah|terangkan)\b.*\b(fungsi|kegunaan|cara kerja)\b.*\b(aplikasi|sistem|itam)\b/i', $promptLower)
            || preg_match('/^\s*(aplikasi|sistem)\s+(apa|ini)\s*[?!.]*\s*$/i', $promptLower)) {
            return [
                'summary' => 'Ini adalah ITAM AI, asisten untuk pengelolaan seluruh aset dan operasional IT.',
                'type' => 'identity',
                'answer' => 'ITAM AI membantu Anda mencari dan melacak data dari seluruh menu aplikasi ITAM: Aset IT, Karyawan, Alamat IP & Jaringan, Tiket Support, Maintenance, Brankas Sandi, Lisensi Software, Berita Acara, Dokumen, Stock Opname, serta Vendor dan Master Data.',
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
            $ipRes = $this->queryIpsNL($promptTrimmed);
            if (!empty($ipRes['items'])) {
                return $ipRes;
            }
        }

        $normalizedPrompt = preg_replace('/\b(snydesk|anydes[kc]|anydek|anydesk)\b/i', 'anydesk', $promptLower);
        if (Str::contains($normalizedPrompt, 'anydesk')
            && Str::contains($normalizedPrompt, ['tidak punya', 'tanpa', 'tidak memiliki', 'belum punya', 'belum memiliki', 'tidak ada', 'belum ada'])) {
            $res = $this->queryEmployeesNL($normalizedPrompt);
            if (!empty($res['items'])) return $res;
        }

        if (Str::contains($promptLower, ['printer', 'cetak'])
            && Str::contains($promptLower, ['punya', 'memiliki', 'diberi', 'memakai', 'digunakan'])) {
            $res = $this->queryEmployeePrinterNL($promptLower);
            if (!empty($res['items'])) return $res;
        }

        if ($this->containsAnyKeyword($promptLower, ['divisi', 'departemen', 'department'])
            && $this->containsAnyKeyword($promptLower, ['asset', 'aset', 'perangkat'])
            && $this->containsAnyKeyword($promptLower, ['paling banyak', 'terbanyak', 'tertinggi', 'terbesar', 'banyak'])) {
            return $this->queryDepartmentAssetRankingNL();
        }

        if (Str::contains($promptLower, ['asset', 'aset', 'perangkat'])
            && Str::contains($promptLower, ['terkait', 'punya', 'memiliki', 'diberi', 'memakai', 'digunakan', 'milik'])) {
            $res = $this->queryEmployeeAssetsNL($promptLower);
            if (!empty($res['items'])) return $res;
        }

        $matchingEmployee = Employee::where('name', 'like', '%' . $promptTrimmed . '%')
            ->orWhere('employee_id', 'like', '%' . $promptTrimmed . '%')
            ->exists();
        if ($matchingEmployee) {
            $empRes = $this->queryEmployeesNL($promptLower);
            if (!empty($empRes['items'])) {
                return $empRes;
            }
        }

        // 2. Specialized Single-Domain Intent (Only returns if matching items found)
        $internalResult = $this->queryInternalIntentNL($promptLower);
        if ($internalResult !== null && !empty($internalResult['items'])) {
            return $internalResult;
        }

        // 3. Universal Multi-Module Search Engine Across ALL Modules
        $generalResult = $this->queryGeneralNL($promptTrimmed, true);
        if ($generalResult !== null && !empty($generalResult['items'])) {
            return $generalResult;
        }

        // 4. Live AI Fallback if configured
        if ($this->apiKey && $this->provider !== 'none' && env('AI_CHAT_MODE', 'internal') !== 'internal') {
            try {
                $liveResult = $this->callAiApiForQuery($promptTrimmed, $history);
                if (!empty($liveResult)) {
                    return $liveResult;
                }
            } catch (\Throwable $e) {
                \Log::warning('Live AI Query call failed, falling back to local NLP engine: ' . $e->getMessage());
            }
        }

        // 5. Final Fallback (Returns search guide or web knowledge if configured)
        return $this->queryGeneralNL($promptTrimmed, false) ?? [
            'summary' => "Pencarian \"{$promptTrimmed}\" tidak ditemukan di seluruh data modul aplikasi ITAM.",
            'type' => 'general',
            'answer' => "Tidak ditemukan data yang cocok di modul Aset, Alamat IP, Karyawan, Tiket, Maintenance, Brankas Sandi, Lisensi Software, Berita Acara, Dokumen, VLAN, Stock Opname, maupun Master Data. Coba periksa ejaan atau gunakan kata kunci lain (seperti nama staf, tag aset, alamat IP, atau merk).",
            'items' => [],
            'sources' => [],
        ];
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
        $checkAndReturn = function (?array $res) {
            if ($res !== null && !empty($res['items'])) {
                return $res;
            }
            return null;
        };

        if ($this->containsAnyKeyword($prompt, ['maintenance', 'perawatan', 'servis', 'service', 'reparasi', 'biaya', 'repair', 'upgrade', 'pengeluaran', 'perbaikan'])) {
            if ($res = $checkAndReturn($this->queryMaintenancesNL($prompt))) return $res;
        }
        if ($this->containsAnyKeyword($prompt, ['vault', 'password', 'sandi', 'brankas', 'kredensial', 'akun', 'login'])) {
            if ($res = $checkAndReturn($this->queryVaultsNL($prompt))) return $res;
        }
        if ($this->containsAnyKeyword($prompt, ['vlan', 'subnet', 'segmentasi', 'netmask', 'gateway'])) {
            if ($this->containsAnyKeyword($prompt, ['ip', 'alamat ip', 'kosong', 'available', 'terpakai', 'used', 'online', 'offline', 'bebas'])) {
                if ($res = $checkAndReturn($this->queryIpsNL($prompt))) return $res;
            }
            if ($res = $checkAndReturn($this->queryVlansNL($prompt))) return $res;
        }
        if ($this->containsAnyKeyword($prompt, ['vendor', 'pemasok', 'supplier', 'distributor', 'mitra', 'sales', 'pembelian'])) {
            if ($res = $checkAndReturn($this->queryVendorsNL($prompt))) return $res;
        }
        if ($this->containsAnyKeyword($prompt, ['berita acara', 'ba', 'disposal', 'bast', 'serah terima', 'surat ba'])) {
            if ($res = $checkAndReturn($this->queryBeritaAcaraNL($prompt))) return $res;
        }
        if ($this->containsAnyKeyword($prompt, ['dokumen', 'arsip', 'document', 'arsip dokumen', 'berkas', 'file'])) {
            if ($res = $checkAndReturn($this->queryDocumentsNL($prompt))) return $res;
        }
        if ($this->containsAnyKeyword($prompt, ['stock opname', 'opname', 'audit fisik', 'audit aset', 'so', 'cek fisik'])) {
            if ($res = $checkAndReturn($this->queryStockOpnameNL($prompt))) return $res;
        }
        if ($this->containsAnyKeyword($prompt, ['log', 'aktivitas', 'activity', 'riwayat', 'history', 'audit', 'operator', 'kejadian', 'perubahan'])) {
            if ($res = $checkAndReturn($this->queryActivityLogsNL($prompt))) return $res;
        }
        if ($this->containsAnyKeyword($prompt, ['asset', 'aset', 'komputer', 'laptop', 'printer', 'cctv', 'switch', 'router', 'perangkat', 'garansi', 'warranty', 'expired', 'pc', 'desktop', 'notebook', 'server', 'monitor', 'ssd', 'ram', 'cpu', 'processor'])) {
            if ($res = $checkAndReturn($this->queryAssetsNL($prompt))) return $res;
        }
        if ($this->containsAnyKeyword($prompt, ['ip', 'ip address', 'alamat ip', 'jaringan', 'network', 'online', 'offline', 'mac', 'ping', 'wifi', 'ssid'])) {
            if ($res = $checkAndReturn($this->queryIpsNL($prompt))) return $res;
        }
        if ($this->containsAnyKeyword($prompt, ['tiket', 'ticket', 'kendala', 'laporan', 'masalah', 'trouble', 'komplain', 'issue', 'open', 'progress', 'closed', 'resolved', 'prioritas', 'support'])) {
            if ($res = $checkAndReturn($this->queryTicketsNL($prompt))) return $res;
        }
        if ($this->containsAnyKeyword($prompt, ['lisensi', 'license', 'software', 'perangkat lunak', 'license key', 'seats'])) {
            if ($res = $checkAndReturn($this->queryLicensesNL($prompt))) return $res;
        }
        if ($this->containsAnyKeyword($prompt, ['user', 'pengguna', 'karyawan', 'pegawai', 'staf', 'staff', 'divisi', 'department', 'jabatan', 'position', 'nik', 'anydesk', 'remote', 'telepon', 'extension', 'email', 'lokasi'])) {
            if ($res = $checkAndReturn($this->queryEmployeesNL($prompt))) return $res;
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
     * Query Berita Acara (BA Serah Terima & BA Disposal) via Natural Language
     */
    protected function queryBeritaAcaraNL(string $prompt): array
    {
        $promptLower = mb_strtolower(trim($prompt));
        $cleanPrompt = preg_replace('/\b(berita acara|berita-acara|surat ba|bast|serah terima|disposal|tampilkan|daftar|cari|data|tolong)\b/i', '', $promptLower);
        $cleanPrompt = trim(preg_replace('/\s+/', ' ', $cleanPrompt));

        $baQuery = BeritaAcara::query();
        $disposalQuery = BeritaAcaraDisposal::query();

        if (!empty($cleanPrompt) && strlen($cleanPrompt) >= 2) {
            $baQuery->where(function($q) use ($cleanPrompt) {
                $q->where('nomor_ba', 'like', "%{$cleanPrompt}%")
                  ->orWhere('hal', 'like', "%{$cleanPrompt}%")
                  ->orWhere('lokasi', 'like', "%{$cleanPrompt}%")
                  ->orWhere('nama_perusahaan', 'like', "%{$cleanPrompt}%")
                  ->orWhere('nama_penerima', 'like', "%{$cleanPrompt}%")
                  ->orWhere('nomor_surat', 'like', "%{$cleanPrompt}%")
                  ->orWhere('nomor_po', 'like', "%{$cleanPrompt}%")
                  ->orWhere('items', 'like', "%{$cleanPrompt}%")
                  ->orWhere('catatan', 'like', "%{$cleanPrompt}%");
            });

            $disposalQuery->where(function($q) use ($cleanPrompt) {
                $q->where('nomor_ba', 'like', "%{$cleanPrompt}%")
                  ->orWhere('hal', 'like', "%{$cleanPrompt}%")
                  ->orWhere('lokasi', 'like', "%{$cleanPrompt}%")
                  ->orWhere('nama_pembuat', 'like', "%{$cleanPrompt}%")
                  ->orWhere('divisi', 'like', "%{$cleanPrompt}%")
                  ->orWhere('narasi_pembuka', 'like', "%{$cleanPrompt}%")
                  ->orWhere('items', 'like', "%{$cleanPrompt}%");
            });
        }

        $bas = $baQuery->latest()->take(6)->get();
        $disposals = $disposalQuery->latest()->take(6)->get();

        $items = [];
        foreach ($bas as $ba) {
            $items[] = [
                'title' => '[BA SERAH TERIMA] ' . $ba->nomor_ba . ($ba->hal ? ' - ' . Str::limit($ba->hal, 35) : ''),
                'subtitle' => 'Penerima: ' . ($ba->nama_penerima ?? '-') . ' • Lokasi: ' . ($ba->lokasi ?? '-') . ' • PT: ' . ($ba->nama_perusahaan ?? '-'),
                'badge' => 'badge-info',
                'badge_text' => 'BA OK',
                'url' => route('berita-acara.index', ['search' => $ba->nomor_ba]),
            ];
        }

        foreach ($disposals as $d) {
            $items[] = [
                'title' => '[BA DISPOSAL] ' . $d->nomor_ba . ($d->hal ? ' - ' . Str::limit($d->hal, 35) : ''),
                'subtitle' => 'Pembuat: ' . ($d->nama_pembuat ?? '-') . ' • Divisi: ' . ($d->divisi ?? '-') . ' • Lokasi: ' . ($d->lokasi ?? '-'),
                'badge' => 'badge-danger',
                'badge_text' => 'BA Disposal',
                'url' => route('berita-acara-disposal.index', ['search' => $d->nomor_ba]),
            ];
        }

        $totalCount = count($items);
        return [
            'summary' => $totalCount > 0 
                ? "Ditemukan {$totalCount} Berita Acara & BA Disposal di sistem ITAM."
                : "Tidak ditemukan dokumen Berita Acara yang cocok dengan kata kunci.",
            'type' => 'berita_acara',
            'items' => $items,
        ];
    }

    /**
     * Query Arsip Dokumen Digital via Natural Language
     */
    protected function queryDocumentsNL(string $prompt): array
    {
        $promptLower = mb_strtolower(trim($prompt));
        $cleanPrompt = preg_replace('/\b(dokumen|arsip|arsip dokumen|document|berkas|file|tampilkan|daftar|cari|data|tolong)\b/i', '', $promptLower);
        $cleanPrompt = trim(preg_replace('/\s+/', ' ', $cleanPrompt));

        $query = Document::query();
        if (!empty($cleanPrompt) && strlen($cleanPrompt) >= 2) {
            $query->where(function($q) use ($cleanPrompt) {
                $q->where('title', 'like', "%{$cleanPrompt}%")
                  ->orWhere('document_number', 'like', "%{$cleanPrompt}%")
                  ->orWhere('description', 'like', "%{$cleanPrompt}%")
                  ->orWhere('file_name', 'like', "%{$cleanPrompt}%")
                  ->orWhere('tags', 'like', "%{$cleanPrompt}%");
            });
        }

        $totalCount = (clone $query)->count();
        $docs = $query->latest()->take(10)->get();

        $items = $docs->map(fn($d) => [
            'title' => '[DOKUMEN] ' . $d->title . ($d->document_number ? " [{$d->document_number}]" : ''),
            'subtitle' => 'Kategori: ' . ($d->category ?? 'Umum') . ' • File: ' . ($d->file_name ?? '-') . ' • Tgl: ' . ($d->document_date ?? '-'),
            'badge' => 'badge-secondary',
            'badge_text' => 'Dokumen',
            'url' => route('documents.index', ['search' => $d->title]),
        ])->toArray();

        return [
            'summary' => $totalCount > 0
                ? "Ditemukan {$totalCount} arsip berkas/dokumen digital."
                : "Tidak ditemukan arsip dokumen digital yang cocok.",
            'type' => 'documents',
            'items' => $items,
        ];
    }

    /**
     * Query Stock Opname Sessions via Natural Language
     */
    protected function queryStockOpnameNL(string $prompt): array
    {
        $promptLower = mb_strtolower(trim($prompt));
        $cleanPrompt = preg_replace('/\b(stock opname|stock-opname|opname|audit fisik|audit aset|cek fisik|tampilkan|daftar|cari|data|tolong)\b/i', '', $promptLower);
        $cleanPrompt = trim(preg_replace('/\s+/', ' ', $cleanPrompt));

        $query = StockOpnameSession::query();
        if (!empty($cleanPrompt) && strlen($cleanPrompt) >= 2) {
            $query->where(function($q) use ($cleanPrompt) {
                $q->where('title', 'like', "%{$cleanPrompt}%")
                  ->orWhere('session_code', 'like', "%{$cleanPrompt}%")
                  ->orWhere('notes', 'like', "%{$cleanPrompt}%");
            });
        }

        $totalCount = (clone $query)->count();
        $sessions = $query->latest()->take(10)->get();

        $items = $sessions->map(fn($st) => [
            'title' => '[STOCK OPNAME] ' . $st->title . " [{$st->session_code}]",
            'subtitle' => 'Status: ' . $st->status . ' • Sesuai: ' . $st->total_matched . ' • Selisih/Hilang: ' . $st->total_missing,
            'badge' => $st->status === 'Completed' ? 'badge-success' : 'badge-warning',
            'badge_text' => 'SO: ' . $st->status,
            'url' => route('stock-opname.index'),
        ])->toArray();

        return [
            'summary' => $totalCount > 0
                ? "Ditemukan {$totalCount} sesi Stock Opname & audit fisik aset."
                : "Tidak ditemukan sesi Stock Opname yang cocok.",
            'type' => 'stock_opname',
            'items' => $items,
        ];
    }

    /**
     * Comprehensive App-Wide Multi-Entity Search & Deep Connected Data Resolver
     */
    protected function queryGeneralNL(string $prompt, bool $returnNullIfEmpty = false): ?array
    {
        $promptTrimmed = trim($prompt);
        if (mb_strlen($promptTrimmed) < 2) {
            return $returnNullIfEmpty ? null : [
                'summary' => 'Masukkan kata kunci pencarian minimal 2 karakter.',
                'type' => 'general',
                'items' => []
            ];
        }

        $promptLower = mb_strtolower($promptTrimmed);
        $terms = array_values(array_filter(preg_split('/[\s,\-\/]+/', $promptLower), fn($t) => mb_strlen($t) >= 1));

        $allResults = [];
        $moduleCounts = [];

        // 1. IP Addresses (IpAddress)
        $ips = IpAddress::with(['asset', 'employee.department', 'vlan'])
            ->where(function($q) use ($promptTrimmed, $terms) {
                $q->where('ip_address', 'like', "%{$promptTrimmed}%")
                  ->orWhere('notes', 'like', "%{$promptTrimmed}%")
                  ->orWhere('mac_address', 'like', "%{$promptTrimmed}%")
                  ->orWhere('gateway', 'like', "%{$promptTrimmed}%");
                if (count($terms) > 1) {
                    $q->orWhere(function($sub) use ($terms) {
                        foreach ($terms as $t) {
                            $sub->where(function($inner) use ($t) {
                                $inner->where('notes', 'like', "%{$t}%")
                                      ->orWhere('ip_address', 'like', "%{$t}%");
                            });
                        }
                    });
                }
            })->take(10)->get();

        foreach ($ips as $ip) {
            $score = 20;
            if (stripos($ip->notes ?? '', $promptTrimmed) !== false || stripos($ip->ip_address, $promptTrimmed) !== false) {
                $score += 80;
            }
            $sub = 'IP: ' . $ip->ip_address . ' • Status: ' . $ip->status;
            if ($ip->notes) $sub .= ' • ' . Str::limit($ip->notes, 40);
            if ($ip->employee) $sub .= ' • User: ' . $ip->employee->name;

            $allResults[] = [
                'score' => $score,
                'title' => '[ALAMAT IP] ' . ($ip->notes ? Str::limit($ip->notes, 45) . ' (' . $ip->ip_address . ')' : $ip->ip_address),
                'subtitle' => $sub,
                'badge' => $ip->is_online ? 'badge-success' : ($ip->status === 'Used' ? 'badge-info' : 'badge-secondary'),
                'badge_text' => $ip->is_online ? 'IP Online' : 'IP ' . $ip->status,
                'url' => route('ips.index', ['search' => $ip->ip_address]),
                'module' => 'Alamat IP',
                'raw_type' => 'ip',
                'raw' => $ip,
            ];
            $moduleCounts['Alamat IP'] = ($moduleCounts['Alamat IP'] ?? 0) + 1;
        }

        // 2. Aset IT (Asset)
        $assets = Asset::with(['category', 'brand', 'location', 'currentAssignment.employee'])
            ->where(function($q) use ($promptTrimmed, $terms) {
                $q->where('name', 'like', "%{$promptTrimmed}%")
                  ->orWhere('asset_tag', 'like', "%{$promptTrimmed}%")
                  ->orWhere('serial_number', 'like', "%{$promptTrimmed}%")
                  ->orWhere('notes', 'like', "%{$promptTrimmed}%")
                  ->orWhere('delivery_order_number', 'like', "%{$promptTrimmed}%");
                if (count($terms) > 1) {
                    $q->orWhere(function($sub) use ($terms) {
                        foreach ($terms as $t) {
                            $sub->where(function($inner) use ($t) {
                                $inner->where('name', 'like', "%{$t}%")
                                      ->orWhere('asset_tag', 'like', "%{$t}%")
                                      ->orWhere('notes', 'like', "%{$t}%")
                                      ->orWhereHas('location', fn($l) => $l->where('name', 'like', "%{$t}%"))
                                      ->orWhereHas('brand', fn($b) => $b->where('name', 'like', "%{$t}%"))
                                      ->orWhereHas('category', fn($c) => $c->where('name', 'like', "%{$t}%"));
                            });
                        }
                    });
                }
            })->take(10)->get();

        foreach ($assets as $a) {
            $score = 20;
            if (stripos($a->name, $promptTrimmed) !== false || stripos($a->asset_tag, $promptTrimmed) !== false) {
                $score += 80;
            }
            $sub = 'Merk: ' . ($a->brand->name ?? '-') . ' • Cat: ' . ($a->category->name ?? '-') . ' • Lok: ' . ($a->location->name ?? '-');
            if ($a->currentAssignment?->employee) $sub .= ' • User: ' . $a->currentAssignment->employee->name;

            $allResults[] = [
                'score' => $score,
                'title' => '[ASET] ' . $a->name . ' [' . $a->asset_tag . ']',
                'subtitle' => $sub,
                'badge' => $a->status === 'Available' ? 'badge-success' : ($a->status === 'Assigned' ? 'badge-primary' : 'badge-warning'),
                'badge_text' => 'Aset: ' . $a->status,
                'url' => route('assets.show', $a->id),
                'module' => 'Aset',
                'raw_type' => 'asset',
                'raw' => $a,
            ];
            $moduleCounts['Aset'] = ($moduleCounts['Aset'] ?? 0) + 1;
        }

        // 3. Karyawan / Pegawai (Employee)
        $employees = Employee::with(['department', 'location'])
            ->where(function($q) use ($promptTrimmed, $terms) {
                $q->where('name', 'like', "%{$promptTrimmed}%")
                  ->orWhere('employee_id', 'like', "%{$promptTrimmed}%")
                  ->orWhere('email', 'like', "%{$promptTrimmed}%")
                  ->orWhere('phone', 'like', "%{$promptTrimmed}%")
                  ->orWhere('anydesk_id', 'like', "%{$promptTrimmed}%")
                  ->orWhere('login_username', 'like', "%{$promptTrimmed}%");
                if (count($terms) > 1) {
                    $q->orWhere(function($sub) use ($terms) {
                        foreach ($terms as $t) {
                            $sub->where(function($inner) use ($t) {
                                $inner->where('name', 'like', "%{$t}%")
                                      ->orWhere('employee_id', 'like', "%{$t}%")
                                      ->orWhereHas('department', fn($d) => $d->where('name', 'like', "%{$t}%"));
                            });
                        }
                    });
                }
            })->take(5)->get();

        foreach ($employees as $e) {
            $score = 20;
            if (stripos($e->name, $promptTrimmed) !== false || stripos($e->employee_id, $promptTrimmed) !== false) {
                $score += 80;
            }
            $sub = 'Divisi: ' . ($e->department->name ?? '-') . ' • Lokasi: ' . ($e->location->name ?? '-');
            if ($e->anydesk_id) $sub .= ' • AnyDesk: ' . $e->anydesk_id;
            if ($e->phone) $sub .= ' • Telp: ' . $e->phone;

            $allResults[] = [
                'score' => $score,
                'title' => '[KARYAWAN] ' . $e->name . ' (NIK: ' . $e->employee_id . ')',
                'subtitle' => $sub,
                'badge' => 'badge-success',
                'badge_text' => 'Karyawan',
                'url' => route('employees.show', $e->id),
                'module' => 'Karyawan',
                'raw_type' => 'employee',
                'raw' => $e,
            ];
            $moduleCounts['Karyawan'] = ($moduleCounts['Karyawan'] ?? 0) + 1;
        }

        // 4. Tiket Support (Ticket)
        $tickets = Ticket::with(['employee', 'asset'])
            ->where(function($q) use ($promptTrimmed, $terms) {
                $q->where('ticket_number', 'like', "%{$promptTrimmed}%")
                  ->orWhere('title', 'like', "%{$promptTrimmed}%")
                  ->orWhere('description', 'like', "%{$promptTrimmed}%")
                  ->orWhere('category', 'like', "%{$promptTrimmed}%");
                if (count($terms) > 1) {
                    $q->orWhere(function($sub) use ($terms) {
                        foreach ($terms as $t) {
                            $sub->where(function($inner) use ($t) {
                                $inner->where('title', 'like', "%{$t}%")
                                      ->orWhere('description', 'like', "%{$t}%")
                                      ->orWhere('ticket_number', 'like', "%{$t}%");
                            });
                        }
                    });
                }
            })->take(5)->get();

        foreach ($tickets as $t) {
            $score = 20;
            if (stripos($t->title, $promptTrimmed) !== false || stripos($t->ticket_number, $promptTrimmed) !== false) {
                $score += 80;
            }
            $sub = 'Status: ' . $t->status . ' • Prioritas: ' . $t->priority . ' • Pelapor: ' . ($t->employee->name ?? '-');

            $allResults[] = [
                'score' => $score,
                'title' => '[TIKET] #' . $t->ticket_number . ' - ' . Str::limit($t->title, 40),
                'subtitle' => $sub,
                'badge' => $t->status === 'Open' ? 'badge-danger' : ($t->status === 'In Progress' ? 'badge-warning' : 'badge-success'),
                'badge_text' => 'Tiket: ' . $t->status,
                'url' => route('tickets.edit', $t->id),
                'module' => 'Tiket',
                'raw_type' => 'ticket',
                'raw' => $t,
            ];
            $moduleCounts['Tiket'] = ($moduleCounts['Tiket'] ?? 0) + 1;
        }

        // 5. Maintenance / Servis (Maintenance)
        $maints = Maintenance::with(['asset'])
            ->where(function($q) use ($promptTrimmed, $terms) {
                $q->where('description', 'like', "%{$promptTrimmed}%")
                  ->orWhere('type', 'like', "%{$promptTrimmed}%")
                  ->orWhereHas('asset', fn($aq) => $aq->where('name', 'like', "%{$promptTrimmed}%")->orWhere('asset_tag', 'like', "%{$promptTrimmed}%"));
            })->take(5)->get();

        foreach ($maints as $m) {
            $score = 20;
            if (stripos($m->description, $promptTrimmed) !== false) {
                $score += 80;
            }
            $sub = 'Tipe: ' . $m->type . ' • Biaya: Rp ' . number_format($m->cost, 0, ',', '.') . ' • Status: ' . $m->status;

            $allResults[] = [
                'score' => $score,
                'title' => '[MAINTENANCE] ' . ($m->asset ? $m->asset->name . ' - ' : '') . Str::limit($m->description, 35),
                'subtitle' => $sub,
                'badge' => $m->status === 'Ongoing' ? 'badge-warning' : 'badge-secondary',
                'badge_text' => 'Servis: ' . $m->status,
                'url' => route('maintenances.index', ['search' => $m->description ?? ($m->asset?->name ?? '')]),
                'module' => 'Maintenance',
                'raw_type' => 'maintenance',
                'raw' => $m,
            ];
            $moduleCounts['Maintenance'] = ($moduleCounts['Maintenance'] ?? 0) + 1;
        }

        // 6. Brankas Sandi (PasswordVault)
        $vaults = PasswordVault::where(function($q) use ($promptTrimmed, $terms) {
            $q->where('device_name', 'like', "%{$promptTrimmed}%")
              ->orWhere('username', 'like', "%{$promptTrimmed}%")
              ->orWhere('category', 'like', "%{$promptTrimmed}%")
              ->orWhere('notes', 'like', "%{$promptTrimmed}%");
        })->take(5)->get();

        foreach ($vaults as $v) {
            $score = 20;
            if (stripos($v->device_name, $promptTrimmed) !== false || stripos($v->username, $promptTrimmed) !== false) {
                $score += 80;
            }
            $sub = 'Username: ' . $v->username . ' • Kategori: ' . ($v->category ?? 'Umum') . ($v->notes ? ' • ' . Str::limit($v->notes, 30) : '');

            $allResults[] = [
                'score' => $score,
                'title' => '[BRANKAS SANDI] ' . $v->device_name,
                'subtitle' => $sub,
                'badge' => 'badge-dark',
                'badge_text' => 'Brankas Sandi',
                'url' => route('password_vaults.index', ['search' => $v->device_name]),
                'module' => 'Brankas Sandi',
                'raw_type' => 'vault',
                'raw' => $v,
            ];
            $moduleCounts['Brankas Sandi'] = ($moduleCounts['Brankas Sandi'] ?? 0) + 1;
        }

        // 7. Lisensi Software (SoftwareLicense)
        $licenses = SoftwareLicense::where(function($q) use ($promptTrimmed, $terms) {
            $q->where('name', 'like', "%{$promptTrimmed}%")
              ->orWhere('license_key', 'like', "%{$promptTrimmed}%")
              ->orWhere('notes', 'like', "%{$promptTrimmed}%");
        })->take(5)->get();

        foreach ($licenses as $l) {
            $score = 20;
            if (stripos($l->name, $promptTrimmed) !== false) {
                $score += 80;
            }
            $sub = 'Seats: ' . $l->total_seats . ' • Key: ' . Str::limit($l->license_key, 18);

            $allResults[] = [
                'score' => $score,
                'title' => '[LISENSI] ' . $l->name,
                'subtitle' => $sub,
                'badge' => 'badge-dark',
                'badge_text' => 'Lisensi Software',
                'url' => route('software_licenses.index', ['search' => $l->name]),
                'module' => 'Lisensi Software',
                'raw_type' => 'license',
                'raw' => $l,
            ];
            $moduleCounts['Lisensi Software'] = ($moduleCounts['Lisensi Software'] ?? 0) + 1;
        }

        // 8. VLAN & Subnets (Vlan)
        $vlans = Vlan::where(function($q) use ($promptTrimmed, $terms) {
            $q->where('name', 'like', "%{$promptTrimmed}%")
              ->orWhere('vlan_number', 'like', "%{$promptTrimmed}%")
              ->orWhere('subnet', 'like', "%{$promptTrimmed}%")
              ->orWhere('gateway', 'like', "%{$promptTrimmed}%")
              ->orWhere('notes', 'like', "%{$promptTrimmed}%");
        })->take(5)->get();

        foreach ($vlans as $vl) {
            $score = 20;
            if (stripos($vl->name, $promptTrimmed) !== false || (string)$vl->vlan_number === $promptTrimmed) {
                $score += 80;
            }
            $sub = 'Subnet: ' . $vl->subnet . ' • Gateway: ' . ($vl->gateway ?? '-') . ' • Status: ' . $vl->status;

            $allResults[] = [
                'score' => $score,
                'title' => '[VLAN] VLAN ' . $vl->vlan_number . ' - ' . $vl->name,
                'subtitle' => $sub,
                'badge' => 'badge-primary',
                'badge_text' => 'VLAN',
                'url' => route('vlans.index', ['search' => $vl->name]),
                'module' => 'VLAN',
                'raw_type' => 'vlan',
                'raw' => $vl,
            ];
            $moduleCounts['VLAN'] = ($moduleCounts['VLAN'] ?? 0) + 1;
        }

        // 9. Berita Acara Serah Terima (BeritaAcara) & Disposal (BeritaAcaraDisposal)
        $isBaKeyword = Str::contains($promptLower, ['berita acara', 'berita-acara', 'surat ba', 'ba', 'bast', 'serah terima']);
        $bas = BeritaAcara::where(function($q) use ($promptTrimmed, $terms, $isBaKeyword) {
            if ($isBaKeyword) {
                return;
            }
            $q->where('nomor_ba', 'like', "%{$promptTrimmed}%")
              ->orWhere('hal', 'like', "%{$promptTrimmed}%")
              ->orWhere('lokasi', 'like', "%{$promptTrimmed}%")
              ->orWhere('nama_perusahaan', 'like', "%{$promptTrimmed}%")
              ->orWhere('nama_penerima', 'like', "%{$promptTrimmed}%")
              ->orWhere('nomor_surat', 'like', "%{$promptTrimmed}%")
              ->orWhere('nomor_po', 'like', "%{$promptTrimmed}%")
              ->orWhere('items', 'like', "%{$promptTrimmed}%")
              ->orWhere('catatan', 'like', "%{$promptTrimmed}%");
            if (count($terms) > 1) {
                $q->orWhere(function($sub) use ($terms) {
                    foreach ($terms as $t) {
                        $sub->where(function($inner) use ($t) {
                            $inner->where('nomor_ba', 'like', "%{$t}%")
                                  ->orWhere('hal', 'like', "%{$t}%")
                                  ->orWhere('items', 'like', "%{$t}%");
                        });
                    }
                });
            }
        })->take(5)->get();

        foreach ($bas as $ba) {
            $score = 25;
            if ($isBaKeyword || stripos($ba->nomor_ba, $promptTrimmed) !== false || stripos($ba->hal ?? '', $promptTrimmed) !== false) {
                $score += 75;
            }
            $sub = 'Penerima: ' . ($ba->nama_penerima ?? '-') . ' • Lokasi: ' . ($ba->lokasi ?? '-') . ' • PT: ' . ($ba->nama_perusahaan ?? '-');

            $allResults[] = [
                'score' => $score,
                'title' => '[BA SERAH TERIMA] ' . $ba->nomor_ba . ($ba->hal ? ' - ' . Str::limit($ba->hal, 35) : ''),
                'subtitle' => $sub,
                'badge' => 'badge-info',
                'badge_text' => 'BA OK',
                'url' => route('berita-acara.index', ['search' => $ba->nomor_ba]),
                'module' => 'Berita Acara',
                'raw_type' => 'ba',
                'raw' => $ba,
            ];
            $moduleCounts['Berita Acara'] = ($moduleCounts['Berita Acara'] ?? 0) + 1;
        }

        // 9b. BA Disposal Asset (BeritaAcaraDisposal)
        $isDisposalKeyword = Str::contains($promptLower, ['disposal', 'ba disposal', 'afkir', 'jual berat', 'penghapusan', 'berita acara']);
        $disposals = BeritaAcaraDisposal::where(function($q) use ($promptTrimmed, $terms, $isDisposalKeyword) {
            if ($isDisposalKeyword) {
                return;
            }
            $q->where('nomor_ba', 'like', "%{$promptTrimmed}%")
              ->orWhere('hal', 'like', "%{$promptTrimmed}%")
              ->orWhere('lokasi', 'like', "%{$promptTrimmed}%")
              ->orWhere('nama_pembuat', 'like', "%{$promptTrimmed}%")
              ->orWhere('divisi', 'like', "%{$promptTrimmed}%")
              ->orWhere('narasi_pembuka', 'like', "%{$promptTrimmed}%")
              ->orWhere('items', 'like', "%{$promptTrimmed}%");
            if (count($terms) > 1) {
                $q->orWhere(function($sub) use ($terms) {
                    foreach ($terms as $t) {
                        $sub->where(function($inner) use ($t) {
                            $inner->where('nomor_ba', 'like', "%{$t}%")
                                  ->orWhere('hal', 'like', "%{$t}%")
                                  ->orWhere('items', 'like', "%{$t}%");
                        });
                    }
                });
            }
        })->take(5)->get();

        foreach ($disposals as $d) {
            $score = 25;
            if ($isDisposalKeyword || stripos($d->nomor_ba, $promptTrimmed) !== false || stripos($d->hal ?? '', $promptTrimmed) !== false) {
                $score += 75;
            }
            $sub = 'Pembuat: ' . ($d->nama_pembuat ?? '-') . ' • Divisi: ' . ($d->divisi ?? '-') . ' • Lokasi: ' . ($d->lokasi ?? '-');

            $allResults[] = [
                'score' => $score,
                'title' => '[BA DISPOSAL] ' . $d->nomor_ba . ($d->hal ? ' - ' . Str::limit($d->hal, 35) : ''),
                'subtitle' => $sub,
                'badge' => 'badge-danger',
                'badge_text' => 'BA Disposal',
                'url' => route('berita-acara-disposal.index', ['search' => $d->nomor_ba]),
                'module' => 'Berita Acara',
                'raw_type' => 'ba_disposal',
                'raw' => $d,
            ];
            $moduleCounts['Berita Acara'] = ($moduleCounts['Berita Acara'] ?? 0) + 1;
        }

        // 10. Arsip Dokumen Digital (Document)
        $isDocKeyword = Str::contains($promptLower, ['dokumen', 'arsip', 'document', 'file', 'berkas']);
        $docs = Document::where(function($q) use ($promptTrimmed, $terms, $isDocKeyword) {
            if ($isDocKeyword) {
                return;
            }
            $q->where('title', 'like', "%{$promptTrimmed}%")
              ->orWhere('document_number', 'like', "%{$promptTrimmed}%")
              ->orWhere('description', 'like', "%{$promptTrimmed}%")
              ->orWhere('file_name', 'like', "%{$promptTrimmed}%")
              ->orWhere('category', 'like', "%{$promptTrimmed}%")
              ->orWhere('tags', 'like', "%{$promptTrimmed}%");
            if (count($terms) > 1) {
                $q->orWhere(function($sub) use ($terms) {
                    foreach ($terms as $t) {
                        $sub->where(function($inner) use ($t) {
                            $inner->where('title', 'like', "%{$t}%")
                                  ->orWhere('document_number', 'like', "%{$t}%")
                                  ->orWhere('description', 'like', "%{$t}%");
                        });
                    }
                });
            }
        })->take(5)->get();

        foreach ($docs as $doc) {
            $score = 25;
            if ($isDocKeyword || stripos($doc->title, $promptTrimmed) !== false || stripos($doc->document_number ?? '', $promptTrimmed) !== false) {
                $score += 75;
            }
            $sub = 'Kategori: ' . ($doc->category ?? 'Umum') . ' • File: ' . ($doc->file_name ?? '-') . ' • Tgl: ' . ($doc->document_date ?? '-');

            $allResults[] = [
                'score' => $score,
                'title' => '[DOKUMEN] ' . $doc->title . ($doc->document_number ? " [{$doc->document_number}]" : ''),
                'subtitle' => $sub,
                'badge' => 'badge-secondary',
                'badge_text' => 'Dokumen',
                'url' => route('documents.index', ['search' => $doc->title]),
                'module' => 'Dokumen',
                'raw_type' => 'document',
                'raw' => $doc,
            ];
            $moduleCounts['Dokumen'] = ($moduleCounts['Dokumen'] ?? 0) + 1;
        }

        // 11. Stock Opname (StockOpnameSession)
        $isSoKeyword = Str::contains($promptLower, ['stock opname', 'stock-opname', 'opname', 'audit fisik', 'audit aset']);
        $stocks = StockOpnameSession::where(function($q) use ($promptTrimmed, $terms, $isSoKeyword) {
            if ($isSoKeyword) {
                return;
            }
            $q->where('title', 'like', "%{$promptTrimmed}%")
              ->orWhere('session_code', 'like', "%{$promptTrimmed}%")
              ->orWhere('notes', 'like', "%{$promptTrimmed}%");
        })->take(5)->get();

        foreach ($stocks as $st) {
            $score = 25;
            if ($isSoKeyword || stripos($st->title, $promptTrimmed) !== false || stripos($st->session_code, $promptTrimmed) !== false) {
                $score += 75;
            }
            $sub = 'Status: ' . $st->status . ' • Sesuai: ' . $st->total_matched . ' • Hilang: ' . $st->total_missing;

            $allResults[] = [
                'score' => $score,
                'title' => '[STOCK OPNAME] ' . $st->title . " [{$st->session_code}]",
                'subtitle' => $sub,
                'badge' => $st->status === 'Completed' ? 'badge-success' : 'badge-warning',
                'badge_text' => 'SO: ' . $st->status,
                'url' => route('stock-opname.index'),
                'module' => 'Stock Opname',
                'raw_type' => 'stock_opname',
                'raw' => $st,
            ];
            $moduleCounts['Stock Opname'] = ($moduleCounts['Stock Opname'] ?? 0) + 1;
        }

        // 12. Vendor / Supplier (Vendor)
        $vendors = Vendor::where(function($q) use ($promptTrimmed, $terms) {
            $q->where('name', 'like', "%{$promptTrimmed}%")
              ->orWhere('contact_person', 'like', "%{$promptTrimmed}%")
              ->orWhere('email', 'like', "%{$promptTrimmed}%")
              ->orWhere('phone', 'like', "%{$promptTrimmed}%")
              ->orWhere('address', 'like', "%{$promptTrimmed}%");
        })->take(5)->get();

        foreach ($vendors as $vd) {
            $score = 20;
            if (stripos($vd->name, $promptTrimmed) !== false) {
                $score += 80;
            }
            $sub = 'Kontak: ' . ($vd->contact_person ?? '-') . ' • Telp: ' . ($vd->phone ?? '-') . ' • Email: ' . ($vd->email ?? '-');

            $allResults[] = [
                'score' => $score,
                'title' => '[VENDOR] ' . $vd->name,
                'subtitle' => $sub,
                'badge' => 'badge-secondary',
                'badge_text' => 'Vendor',
                'url' => route('vendors.index', ['search' => $vd->name]),
                'module' => 'Vendor',
                'raw_type' => 'vendor',
                'raw' => $vd,
            ];
            $moduleCounts['Vendor'] = ($moduleCounts['Vendor'] ?? 0) + 1;
        }

        // 13. Master Data (Lokasi)
        $locs = Location::where(function($q) use ($promptTrimmed, $terms) {
            $q->where('name', 'like', "%{$promptTrimmed}%")
              ->orWhere('address', 'like', "%{$promptTrimmed}%")
              ->orWhere('description', 'like', "%{$promptTrimmed}%");
        })->take(3)->get();

        foreach ($locs as $loc) {
            $score = 20;
            if (stripos($loc->name, $promptTrimmed) !== false) {
                $score += 80;
            }
            $sub = 'Alamat/Gedung: ' . ($loc->address ?? ($loc->description ?? '-'));

            $allResults[] = [
                'score' => $score,
                'title' => '[LOKASI] ' . $loc->name,
                'subtitle' => $sub,
                'badge' => 'badge-info',
                'badge_text' => 'Lokasi',
                'url' => route('locations.index', ['search' => $loc->name]),
                'module' => 'Lokasi',
                'raw_type' => 'location',
                'raw' => $loc,
            ];
            $moduleCounts['Lokasi'] = ($moduleCounts['Lokasi'] ?? 0) + 1;
        }

        // Single Exact Match Deep Expansion (If query matches exactly 1 Employee or 1 Asset)
        if (count($allResults) === 1) {
            $single = $allResults[0];
            if ($single['raw_type'] === 'employee') {
                $emp = $single['raw'];
                $empAssets = Asset::whereHas('assignments', fn($q) => $q->where('employee_id', $emp->id)->where('status', 'Assigned'))->get();
                $empIps = IpAddress::where('employee_id', $emp->id)->get();
                $empTickets = Ticket::where('employee_id', $emp->id)->latest()->take(3)->get();

                $details = [];
                $details[] = "Divisi: " . ($emp->department->name ?? '-');
                $details[] = "Lokasi: " . ($emp->location->name ?? '-');
                if ($emp->email) $details[] = "Email: " . $emp->email;
                if ($emp->phone) $details[] = "Telp: " . $emp->phone;
                if ($emp->anydesk_id) $details[] = "AnyDesk: " . $emp->anydesk_id;
                $details[] = "Aset Dipegang: " . $empAssets->count() . " unit";

                $expandedItems = [$single];
                foreach ($empAssets as $ea) {
                    $expandedItems[] = [
                        'title' => '[ASET TERKAIT] ' . $ea->name . ' [' . $ea->asset_tag . ']',
                        'subtitle' => 'Merk: ' . ($ea->brand->name ?? '-') . ' • Status: ' . $ea->status,
                        'badge' => 'badge-primary',
                        'badge_text' => $ea->status,
                        'url' => route('assets.show', $ea->id),
                    ];
                }
                foreach ($empIps as $eip) {
                    $expandedItems[] = [
                        'title' => '[IP TERKAIT] ' . $eip->ip_address,
                        'subtitle' => 'Status: ' . $eip->status . ' • Gateway: ' . ($eip->gateway ?? '-'),
                        'badge' => $eip->is_online ? 'badge-success' : 'badge-secondary',
                        'badge_text' => $eip->is_online ? 'Online' : $eip->status,
                        'url' => route('ips.index', ['search' => $eip->ip_address]),
                    ];
                }
                foreach ($empTickets as $et) {
                    $expandedItems[] = [
                        'title' => '[TIKET TERKAIT] #' . $et->ticket_number . ' - ' . Str::limit($et->title, 35),
                        'subtitle' => 'Status: ' . $et->status . ' • Prioritas: ' . $et->priority,
                        'badge' => 'badge-warning',
                        'badge_text' => $et->status,
                        'url' => route('tickets.edit', $et->id),
                    ];
                }

                return [
                    'summary' => "Data Detail Karyawan: {$emp->name} (NIK: {$emp->employee_id})",
                    'answer' => implode(' • ', $details),
                    'type' => 'employees',
                    'items' => array_map(function($item) {
                        unset($item['raw'], $item['raw_type'], $item['score'], $item['module']);
                        return $item;
                    }, $expandedItems),
                ];
            } elseif ($single['raw_type'] === 'asset') {
                $ast = $single['raw'];
                $assignedEmp = $ast->currentAssignment->employee ?? null;
                $astIps = IpAddress::where('asset_id', $ast->id)->get();
                $astTickets = Ticket::where('asset_id', $ast->id)->latest()->take(3)->get();
                $astMaints = Maintenance::where('asset_id', $ast->id)->latest()->take(3)->get();

                $details = [];
                $details[] = "Merk: " . ($ast->brand->name ?? '-');
                $details[] = "Kategori: " . ($ast->category->name ?? '-');
                $details[] = "Status: " . $ast->status;
                if ($ast->serial_number) $details[] = "S/N: " . $ast->serial_number;
                if ($assignedEmp) $details[] = "Dipegang: " . $assignedEmp->name . ' (' . ($assignedEmp->department->name ?? '-') . ')';

                $expandedItems = [$single];
                if ($assignedEmp) {
                    $expandedItems[] = [
                        'title' => '[PENGGUNA SAAT INI] ' . $assignedEmp->name . ' (NIK: ' . $assignedEmp->employee_id . ')',
                        'subtitle' => 'Divisi: ' . ($assignedEmp->department->name ?? '-') . ($assignedEmp->anydesk_id ? ' • AnyDesk: ' . $assignedEmp->anydesk_id : ''),
                        'badge' => 'badge-info',
                        'badge_text' => 'User',
                        'url' => route('employees.show', $assignedEmp->id),
                    ];
                }
                foreach ($astIps as $aip) {
                    $expandedItems[] = [
                        'title' => '[IP PERANGKAT] ' . $aip->ip_address,
                        'subtitle' => 'Status: ' . $aip->status . ' • Gateway: ' . ($aip->gateway ?? '-'),
                        'badge' => $aip->is_online ? 'badge-success' : 'badge-secondary',
                        'badge_text' => $aip->is_online ? 'Online' : $aip->status,
                        'url' => route('ips.index', ['search' => $aip->ip_address]),
                    ];
                }
                foreach ($astMaints as $am) {
                    $expandedItems[] = [
                        'title' => '[RIWAYAT SERVIS] ' . Str::limit($am->description, 35),
                        'subtitle' => 'Tipe: ' . $am->type . ' • Biaya: Rp ' . number_format($am->cost, 0, ',', '.'),
                        'badge' => 'badge-secondary',
                        'badge_text' => $am->status,
                        'url' => route('maintenances.index', ['search' => $ast->name]),
                    ];
                }

                return [
                    'summary' => "Data Detail Aset: {$ast->name} [{$ast->asset_tag}]",
                    'answer' => implode(' • ', $details),
                    'type' => 'assets',
                    'items' => array_map(function($item) {
                        unset($item['raw'], $item['raw_type'], $item['score'], $item['module']);
                        return $item;
                    }, $expandedItems),
                ];
            }
        }

        // Sort all results by score DESC
        usort($allResults, fn($a, $b) => $b['score'] <=> $a['score']);

        $totalFound = count($allResults);
        if ($totalFound === 0) {
            if ($returnNullIfEmpty) {
                return null;
            }

            // Live Web Search Fallback for broader technical questions if configured
            $webSources = $this->fetchWebKnowledgeSources($promptTrimmed);
            $webResult = collect($webSources)->pluck('snippet')->filter()->implode("\n\n");
            if (!empty($webResult)) {
                $summary = 'Hasil Pencarian & Referensi untuk "' . $promptTrimmed . '":';
                $answer = $webResult;
            } else {
                $summary = "Pencarian untuk \"{$promptTrimmed}\" tidak ditemukan di seluruh modul aplikasi ITAM.";
                $answer = 'Tidak ditemukan kecocokan di data Aset, Alamat IP, Karyawan, Tiket, Maintenance, Brankas Sandi, Lisensi Software, Berita Acara, Dokumen, VLAN, Stock Opname, maupun Master Data. Cobalah kata kunci seperti nama karyawan, tag aset, alamat IP, merk, atau tiket.';
            }

            return [
                'summary' => $summary,
                'answer' => $answer,
                'type' => 'general',
                'items' => [],
                'sources' => $webSources ?? [],
            ];
        }

        $breakdowns = [];
        foreach ($moduleCounts as $mod => $cnt) {
            $breakdowns[] = "{$mod} ({$cnt})";
        }

        if (count($moduleCounts) === 1) {
            $singleMod = array_key_first($moduleCounts);
            $summary = "Ditemukan {$totalFound} data {$singleMod} untuk pencarian \"{$promptTrimmed}\".";
        } else {
            $summary = "Ditemukan {$totalFound} data terkait untuk pencarian \"{$promptTrimmed}\" di modul: " . implode(', ', $breakdowns) . ".";
        }

        $top = $allResults[0];
        $answer = null;
        if ($top['score'] >= 100) {
            $answer = "Kecocokan utama ditemukan pada {$top['title']} ({$top['subtitle']}). Silakan klik kartu di bawah untuk membuka data terkait.";
        }

        // Limit to top 20 items to keep UI fast and clean
        $items = array_map(function($r) {
            unset($r['raw'], $r['raw_type'], $r['score'], $r['module']);
            return $r;
        }, array_slice($allResults, 0, 20));

        return [
            'summary' => $summary,
            'answer' => $answer,
            'type' => 'general',
            'items' => $items,
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
