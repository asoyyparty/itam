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

        // Tier 1: User's Configured AI Vision API (Gemini Vision / OpenAI Vision)
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

        // Tier 2: Free Real OCR Engine (OCR.space API)
        if (empty($result) || empty($result['items'])) {
            $extractedText = $this->callFreeOcrSpaceApi($base64Image, $mimeType);
            if (!empty($extractedText)) {
                $result = $this->parseExtractedTextToBeritaAcaraData($extractedText, $filename);
            }
        }

        // Tier 3: Emergency Fallback Template
        if (empty($result) || empty($result['items'])) {
            $result = $this->fallbackDefaultData($filename);
            $result['_is_fallback'] = true;
        }

        return $this->cleanAndNormalizeResult($result);
    }

    /**
     * Call Google Gemini Vision API
     */
    protected function callGeminiVision(string $base64Data, string $mimeType): ?array
    {
        $prompt = <<<'PROMPT'
You are an expert OCR and procurement document assistant for Indonesian Logistics and IT asset management.
Carefully analyze this "SURAT JALAN" / Delivery Order document image.

Your task is to extract all details to generate an official "BERITA ACARA OK" document for IT Pabrik.

Extract all details into this exact JSON structure:
{
  "document_title": "SURAT JALAN",
  "kepada": "string (e.g. Bagas / Abdul (IT Pabrik))",
  "tanggal_surat": "YYYY-MM-DD (Date on the document header e.g. 2026-10-13)",
  "no_sj": "string (Surat Jalan number, e.g. 062/LGT/X/2026)",
  "no_po": "string or null (Purchase Order number(s), e.g. 2602032, 2602033)",
  "supir_kendaraan": "string or null",
  "tanggal_verifikasi": "YYYY-MM-DD (Receipt or verification date from handwritten notes/signatures e.g. 2026-10-14)",
  "verifikasi_ok": "string (Verification text from handwritten notes, e.g. TTB / SJ ok 14/10 2026)",
  "nama_pemeriksa": "string (Name written in verification / receiver e.g. Bagas Purnomo)",
  "unit_pemeriksa": "string (e.g. IT Pabrik)",
  "items": [
    {
      "no": 1,
      "kode": "string or null (e.g. ITM-00000002)",
      "nama_item": "string (Full ITEM DESCRIPTION in uppercase, e.g. PRINTER EPSON L3251 HITAM ALL IN ONE PRINTER WIRELESS)",
      "keterangan": "string (Appropriate purpose/keperluan for IT Pabrik in uppercase, e.g. 'UNTUK CETAK DOKUMEN & OPERASIONAL KANTOR', 'UNTUK CADANGAN BATERAI UPS', 'UNTUK UPGRADE PSU PC', 'UNTUK PENYIMPANAN DATA', 'UNTUK PRINT SERVER JARINGAN')",
      "uom": "string (e.g. Unit, Pieces)",
      "quantity": 1,
      "tgl": "string (Date received in Indonesian format, e.g. 14 Oktober 2026)",
      "supplier": "string (Supplier/vendor name e.g. MIRACLE SOLUSINDO, CV)",
      "no_suratjalan": "string (Surat Jalan number)",
      "no_po": "string (PO number for this item, e.g. 2602032)"
    }
  ]
}

CRITICAL RULES:
1. Every row in the items table must be captured with its exact sequence number, full item description, quantity, uom, supplier (from the Surat Jalan notes/keterangan column), No Surat Jalan, and No PO.
2. If text wraps into multiple lines within one item row, merge it cleanly into "nama_item".
3. Extract supplier name and PO number from the "Keterangan" column. If subsequent rows have empty Keterangan in the original table, inherit the supplier and PO from the respective order block (e.g., items 2 & 3 belong to PO 2602032, item 5 belongs to PO 2602033).
4. Provide a sensible uppercase Indonesian IT purpose for "keterangan" (e.g. 'UNTUK KEBUTUHAN OPERASIONAL PABRIK', 'UNTUK PENGGANTIAN BATERAI UPS RUSAK', 'UNTUK PSU KOMPUTER PC', etc.) matching the item type.
5. "quantity" must be an integer.
6. Return ONLY valid raw JSON without markdown or code fences.
PROMPT;

        // Model priority order: gemini-3.1-flash-lite is fastest and highly available
        $modelsToTry = [
            'gemini-3.1-flash-lite',
            'gemini-3.7-flash',
            'gemini-3.8-flash',
            'gemini-flash-lite-latest',
            'gemini-2.5-flash',
            'gemini-3.6-flash',
            'gemini-flash-latest'
        ];

        foreach ($modelsToTry as $model) {
            try {
                $response = Http::withoutVerifying()->timeout(45)->post(
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
                        if (is_array($decoded) && isset($decoded['items']) && count($decoded['items']) > 0) {
                            $decoded['_model_used'] = $model;
                            return $decoded;
                        }
                    }
                } else {
                    Log::warning("Gemini Vision model {$model} returned status " . $response->status() . ": " . substr($response->body(), 0, 200));
                }
            } catch (\Throwable $e) {
                Log::warning("Gemini Vision model {$model} exception: " . $e->getMessage());
            }
        }

        return null;
    }

    /**
     * Call OpenAI Vision API
     */
    protected function callOpenAiVision(string $base64Data, string $mimeType): ?array
    {
        $prompt = "Extract Surat Jalan document data into structured JSON with: kepada, tanggal_surat (YYYY-MM-DD), no_sj, no_po, supir_kendaraan, items (array of no, kode, nama_item, uom, quantity, keterangan, supplier, no_suratjalan, no_po), verifikasi_ok, nama_pemeriksa, unit_pemeriksa, tanggal_verifikasi. Return valid JSON only.";

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
            $decoded = json_decode($content, true);
            if (is_array($decoded) && !empty($decoded['items'])) {
                $decoded['_model_used'] = 'gpt-4o';
                return $decoded;
            }
        }

        return null;
    }

    /**
     * Call Free OCR.Space API as Tier 2 Fallback
     */
    protected function callFreeOcrSpaceApi(string $base64Data, string $mimeType): ?string
    {
        try {
            $params = [
                'apikey' => 'K87814888888957',
                'base64Image' => "data:{$mimeType};base64,{$base64Data}",
                'isOverlayRequired' => 'false',
                'scale' => 'true',
                'isTable' => 'true',
            ];
            if (str_contains(strtolower($mimeType), 'pdf')) {
                $params['filetype'] = 'PDF';
            }

            $response = Http::withoutVerifying()->asForm()->timeout(25)->post('https://api.ocr.space/parse/image', $params);

            if ($response->successful()) {
                $parsedResults = $response->json('ParsedResults');
                if (!empty($parsedResults[0]['ParsedText'])) {
                    return $parsedResults[0]['ParsedText'];
                }
            }
        } catch (\Throwable $e) {
            Log::warning('Free OCR Space API call failed in BeritaAcaraService: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Parse raw extracted text from OCR.space into structured Berita Acara data
     */
    protected function parseExtractedTextToBeritaAcaraData(string $rawText, string $filename): ?array
    {
        $lines = explode("\n", $rawText);
        $cleanLines = array_values(array_filter(array_map('trim', $lines)));

        $noSj = '';
        $tanggalSurat = now()->format('Y-m-d');
        $kepada = 'Abdul / Bagas (IT Pabrik)';
        $noPo = '';
        $pemeriksa = 'Bagas Purnomo';
        $verifikasiOk = 'TTB / SJ ok';
        $tanggalVerif = now()->format('Y-m-d');
        $items = [];

        foreach ($cleanLines as $line) {
            if (preg_match('/(?:No\s*SJ|No\.?\s*Surat\s*Jalan)\s*[:=\-]?\s*([A-Za-z0-9\/\.\-]+)/i', $line, $m)) {
                $noSj = trim($m[1]);
            }
            if (preg_match('/Tanggal\s*[:=\-]?\s*([0-9]{1,2}\s+[A-Za-z]{3,}\s+[0-9]{4}|[0-9]{4}-[0-9]{2}-[0-9]{2})/i', $line, $m)) {
                $parsedDate = date('Y-m-d', strtotime(trim($m[1])));
                if ($parsedDate) {
                    $tanggalSurat = $parsedDate;
                }
            }
            if (preg_match('/Kepada\s*[:=\-]?\s*(.+)/i', $line, $m)) {
                $kepada = trim($m[1]);
            }
            if (preg_match('/(?:PO|P\.O\.)\s*[:=\-]?\s*([0-9]{6,})/i', $line, $m)) {
                if (empty($noPo)) {
                    $noPo = trim($m[1]);
                }
            }
            if (preg_match('/(TTB\s*\/?[^,\n]+)/i', $line, $m)) {
                $verifikasiOk = trim($m[1]);
            }
            if (preg_match('/(Bagas\s+Purnomo)/i', $line, $m)) {
                $pemeriksa = trim($m[1]);
            }
        }

        // Table rows parsing: lines beginning with 1, 2, 3...
        $itemIndex = 1;
        $currentSupplier = 'MIRACLE SOLUSINDO, CV';
        $currentPo = $noPo;

        foreach ($cleanLines as $line) {
            if (preg_match('/^([1-9])\s+(ITM-[0-9]+)?\s*(.+)/i', $line, $m)) {
                $rowNum = (int)$m[1];
                $code = trim($m[2] ?? '');
                $rest = trim($m[3] ?? '');

                // Extract quantity and uom from line if present
                $qty = 1;
                $uom = 'Unit';
                if (preg_match('/\b(Unit|Pieces|Pcs|Set)\s+([0-9]+)\b/i', $rest, $qm)) {
                    $uom = $qm[1];
                    $qty = (int)$qm[2];
                    $rest = trim(str_replace($qm[0], '', $rest));
                }

                if (preg_match('/(PO\s*[0-9]+)/i', $rest, $pom)) {
                    $currentPo = preg_replace('/[^0-9]/', '', $pom[1]);
                }

                $items[] = [
                    'no' => $rowNum,
                    'kode' => $code,
                    'nama_item' => strtoupper($rest),
                    'keterangan' => 'UNTUK KEBUTUHAN OPERASIONAL PABRIK',
                    'quantity' => $qty,
                    'uom' => $uom,
                    'tgl' => \Carbon\Carbon::parse($tanggalVerif)->translatedFormat('d F Y'),
                    'supplier' => $currentSupplier,
                    'no_suratjalan' => $noSj,
                    'no_po' => $currentPo,
                ];
            }
        }

        if (count($items) === 0) {
            return null;
        }

        return [
            'document_title' => 'SURAT JALAN',
            'kepada' => $kepada,
            'tanggal_surat' => $tanggalSurat,
            'no_sj' => $noSj,
            'no_po' => $noPo,
            'supir_kendaraan' => '',
            'tanggal_verifikasi' => $tanggalVerif,
            'verifikasi_ok' => $verifikasiOk,
            'nama_pemeriksa' => $pemeriksa,
            'unit_pemeriksa' => 'IT Pabrik',
            'items' => $items,
            '_model_used' => 'ocr_space_fallback',
        ];
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
                    'keterangan' => 'UNTUK KEBUTUHAN OPERASIONAL IT PABRIK',
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
                    'nama_item' => strtoupper(trim($item['nama_item'] ?? $item['item_description'] ?? '')),
                    'keterangan' => strtoupper(trim($item['keterangan'] ?? 'UNTUK OPERASIONAL PABRIK')),
                    'quantity' => $qty,
                    'uom' => trim($item['uom'] ?? 'Unit') ?: 'Unit',
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
            '_is_fallback' => !empty($data['_is_fallback']),
            '_model_used' => $data['_model_used'] ?? null,
        ];
    }
}
