<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BeritaAcaraService
{
    protected ?string $apiKey;
    protected string $provider;

    public function __construct()
    {
        $dbProvider = Setting::where('key', 'ai_provider')->value('value') ?: 'gemini';
        $dbGeminiKey = Setting::where('key', 'gemini_api_key')->value('value');
        $dbOpenaiKey = Setting::where('key', 'openai_api_key')->value('value');

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
     * Parse Surat Jalan / Delivery Order document using AI Vision OCR
     */
    public function parseSuratJalan(UploadedFile $file): array
    {
        $mimeType = $file->getMimeType();
        $base64Image = base64_encode(file_get_contents($file->getRealPath()));
        $filename = $file->getClientOriginalName();

        $result = null;

        if ($this->apiKey && $this->provider !== 'none') {
            try {
                if ($this->provider === 'gemini') {
                    $result = $this->callGeminiVision($base64Image, $mimeType);
                } elseif ($this->provider === 'openai') {
                    $result = $this->callOpenAiVision($base64Image, $mimeType);
                }
            } catch (\Throwable $e) {
                Log::warning('AI Vision OCR call failed for Surat Jalan: ' . $e->getMessage());
            }
        }

        if (empty($result) || empty($result['items'])) {
            $result = $this->fallbackDefaultData($filename);
        }

        return $this->cleanAndNormalizeResult($result);
    }

    /**
     * Call Google Gemini Vision API (gemini-3.6-flash)
     */
    protected function callGeminiVision(string $base64Data, string $mimeType): ?array
    {
        $prompt = <<<'PROMPT'
You are an expert OCR and procurement document assistant for Indonesian Logistics and IT asset management.
Carefully analyze this "SURAT JALAN" / Delivery Order document image.

Your task is to extract information to generate an official "BERITA ACARA OK" document for IT Pabrik.

Extract all details into this exact JSON structure:
{
  "document_title": "SURAT JALAN",
  "kepada": "string (e.g. Abdul / Bagas (IT Pabrik))",
  "tanggal_surat": "YYYY-MM-DD (Date on the document header)",
  "no_sj": "string (Surat Jalan number, e.g. 045/LGT/VII/2026)",
  "no_po": "string (Purchase Order number, e.g. 2601436)",
  "supir_kendaraan": "string or null (Driver / Vehicle No)",
  "tanggal_verifikasi": "YYYY-MM-DD (Receipt or verification date from stamp/notes e.g. 2026-07-22)",
  "verifikasi_ok": "string (Verification text, default: TTB / LGT ok)",
  "nama_pemeriksa": "string (Name written in verification / receiver, e.g. Bagas Purnomo)",
  "unit_pemeriksa": "string (e.g. IT Pabrik)",
  "items": [
    {
      "no": 1,
      "kode": "string or null (e.g. ITM-00000001, ATK-00000001)",
      "nama_item": "string (Full ITEM DESCRIPTION in uppercase)",
      "keterangan": "string (Realistic purpose/keperluan for IT Pabrik in uppercase, e.g. 'UNTUK MENGGANTI BATERAI LAPTOP YANG SUDAH RUSAK', 'UNTUK KEBUTUHAN SERVER', 'UNTUK ISI ULANG TINTA PRINTER')",
      "uom": "string (e.g. PCS, UNIT, SET)",
      "quantity": 1,
      "tgl": "string (Date received, e.g. 22 Juli 2026 or YYYY-MM-DD)",
      "supplier": "string (Supplier/vendor name from Surat Jalan notes e.g. TOKOPEDIA, SHOPEE, DORAN SUKSES INDONESIA, PT)",
      "no_suratjalan": "string (Surat Jalan number for this item, e.g. 045/LGT/VII/2026)",
      "no_po": "string (PO number for this item, e.g. 2601382 or the main PO)"
    }
  ]
}

CRITICAL RULES:
1. Every row in the items table must be captured with its exact sequence number, full item description, quantity, supplier (from the Surat Jalan notes/keterangan column), No Surat Jalan, and No PO.
2. If text wraps into multiple lines within one item row, merge it cleanly into "nama_item".
3. Provide a sensible uppercase Indonesian IT purpose for "keterangan" (e.g., 'UNTUK MENGGANTI...', 'UNTUK KEBUTUHAN...') if not explicitly stated.
4. "quantity" must be an integer.
5. Return ONLY valid raw JSON without markdown or code fences.
PROMPT;

        $modelsToTry = ['gemini-3.6-flash', 'gemini-flash-latest'];

        foreach ($modelsToTry as $model) {
            try {
                $response = Http::withoutVerifying()->timeout(60)->post(
                    "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$this->apiKey}",
                    [
                        'contents' => [
                            [
                                'parts' => [
                                    ['text' => $prompt],
                                    [
                                        'inlineData' => [
                                            'mimeType' => $mimeType,
                                            'data' => $base64Data,
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        'generationConfig' => [
                            'temperature' => 0.1,
                            'responseMimeType' => 'application/json',
                        ],
                    ]
                );

                if ($response->successful()) {
                    $jsonText = $response->json('candidates.0.content.parts.0.text');
                    if ($jsonText) {
                        $jsonText = trim($jsonText);
                        $jsonText = preg_replace('/^```(?:json)?\s*(.*?)\s*```$/s', '$1', $jsonText);
                        $decoded = json_decode($jsonText, true);
                        if (is_array($decoded) && isset($decoded['items'])) {
                            return $decoded;
                        }
                    }
                }
            } catch (\Throwable $e) {
                Log::warning("Gemini Vision model {$model} failed: " . $e->getMessage());
            }
        }

        return null;
    }

    /**
     * Call OpenAI Vision API
     */
    protected function callOpenAiVision(string $base64Data, string $mimeType): ?array
    {
        $prompt = "Extract Surat Jalan document data into structured JSON with: kepada, tanggal_surat, no_sj, no_po, supir_kendaraan, items (array of no, kode, nama_item, uom, quantity, keterangan), verifikasi_ok, nama_pemeriksa, unit_pemeriksa, tanggal_verifikasi. Return valid JSON only.";

        $dataUri = "data:{$mimeType};base64,{$base64Data}";

        $response = Http::withoutVerifying()->timeout(60)->withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-4o',
            'response_format' => ['type' => 'json_object'],
            'messages' => [
                [
                    'role' => 'user',
                    'content' => [
                        ['type' => 'text', 'text' => $prompt],
                        ['type' => 'image_url', 'image_url' => ['url' => $dataUri]],
                    ],
                ],
            ],
            'temperature' => 0.1,
        ]);

        if ($response->successful()) {
            $content = $response->json('choices.0.message.content');
            return json_decode($content, true);
        }

        return null;
    }

    /**
     * Fallback template when offline or parsing returns empty
     */
    protected function fallbackDefaultData(string $filename): array
    {
        return [
            'document_title' => 'SURAT JALAN',
            'kepada' => 'Abdul / Bagas (IT Pabrik)',
            'tanggal_surat' => now()->format('Y-m-d'),
            'no_sj' => '',
            'no_po' => '',
            'supir_kendaraan' => '',
            'items' => [
                [
                    'no' => 1,
                    'kode' => 'ITM-00000001',
                    'nama_item' => 'Barang Hasil Pemindaian: ' . pathinfo($filename, PATHINFO_FILENAME),
                    'uom' => 'Pieces',
                    'quantity' => 1,
                    'keterangan' => '',
                ],
            ],
            'verifikasi_ok' => 'TTB / LGT ok',
            'nama_pemeriksa' => auth()->user() ? auth()->user()->name : 'Bagas Purnomo',
            'unit_pemeriksa' => 'IT Pabrik',
            'tanggal_verifikasi' => now()->format('Y-m-d'),
        ];
    }

    /**
     * Clean and normalize data structure
     */
    protected function cleanAndNormalizeResult(array $data): array
    {
        $mainNoSj = trim($data['no_sj'] ?? '');
        $mainNoPo = trim($data['no_po'] ?? '');
        $mainDateVerif = !empty($data['tanggal_verifikasi']) ? date('Y-m-d', strtotime($data['tanggal_verifikasi'])) : now()->format('Y-m-d');
        $formattedDateVerif = \Carbon\Carbon::parse($mainDateVerif)->translatedFormat('d F Y');

        $items = [];
        if (!empty($data['items']) && is_array($data['items'])) {
            $idx = 1;
            foreach ($data['items'] as $item) {
                $qty = (int) preg_replace('/[^0-9]/', '', (string) ($item['quantity'] ?? 1)) ?: 1;
                $items[] = [
                    'no' => (int) ($item['no'] ?? $idx),
                    'kode' => trim($item['kode'] ?? ''),
                    'nama_item' => trim($item['nama_item'] ?? $item['item_description'] ?? ''),
                    'keterangan' => trim($item['keterangan'] ?? ''),
                    'quantity' => $qty,
                    'uom' => strtoupper(trim($item['uom'] ?? 'PCS')) ?: 'PCS',
                    'tgl' => trim($item['tgl'] ?? $formattedDateVerif),
                    'supplier' => trim($item['supplier'] ?? ''),
                    'no_suratjalan' => trim($item['no_suratjalan'] ?? $mainNoSj),
                    'no_po' => trim($item['no_po'] ?? $mainNoPo),
                ];
                $idx++;
            }
        }

        return [
            'nomor_ba' => \App\Models\BeritaAcara::generateNextNomorBa(),
            'lokasi' => 'Cikande',
            'hal' => 'Pemberitahuan Barang/Jasa OK',
            'lampiran' => '-',
            'kepada_yth' => 'Ka. Pembelian',
            'cc' => 'Ka. Pabrik, Ka. Keuangan',
            'nama_perusahaan' => 'PT. CBA Chemical Industry Pabrik',
            'kepada' => trim($data['kepada'] ?? 'Abdul / Bagas (IT Pabrik)'),
            'tanggal_surat' => !empty($data['tanggal_surat']) ? date('Y-m-d', strtotime($data['tanggal_surat'])) : now()->format('Y-m-d'),
            'no_sj' => $mainNoSj,
            'no_po' => $mainNoPo,
            'supir_kendaraan' => trim($data['supir_kendaraan'] ?? ''),
            'nama_penerima' => trim($data['nama_pemeriksa'] ?? (auth()->user() ? auth()->user()->name : 'Bagas Purnomo')),
            'unit_penerima' => trim($data['unit_pemeriksa'] ?? 'IT Pabrik'),
            'tanggal_verifikasi' => $mainDateVerif,
            'status_verifikasi' => trim($data['verifikasi_ok'] ?? 'TTB / LGT ok'),
            'mengetahui_1' => 'Martin',
            'mengetahui_2' => 'Edi Awaludin',
            'menyetujui_1' => 'Herinata',
            'menyetujui_2' => 'David',
            'items' => $items,
        ];
    }
}
