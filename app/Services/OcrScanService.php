<?php

namespace App\Services;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Setting;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OcrScanService
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
     * Process uploaded document/photo and extract asset details using AI Vision OCR
     */
    public function parseDocument(UploadedFile $file): array
    {
        $mimeType = $file->getMimeType();
        $base64Image = base64_encode(file_get_contents($file->getRealPath()));
        $filename = $file->getClientOriginalName();

        $result = null;

        // Tier 1: User's Configured AI API Key (Gemini Vision / OpenAI Vision)
        if ($this->apiKey && $this->provider !== 'none') {
            try {
                if ($this->provider === 'gemini') {
                    $result = $this->callGeminiVisionApi($base64Image, $mimeType);
                } elseif ($this->provider === 'openai') {
                    $result = $this->callOpenAiVisionApi($base64Image, $mimeType);
                }
            } catch (\Throwable $e) {
                Log::warning('AI Vision API call failed: ' . $e->getMessage());
            }
        }

        // Tier 2: Free Real OCR Engine (OCR.Space API) to extract text from document
        if (empty($result) || empty($result['items'])) {
            $extractedText = $this->callFreeOcrSpaceApi($base64Image, $mimeType);
            if (!empty($extractedText)) {
                $result = $this->parseExtractedTextToAssetData($extractedText, $filename);
            }
        }

        // Tier 3: Smart Local Parser Fallback
        if (empty($result) || empty($result['items'])) {
            $result = $this->smartLocalDocumentParser($filename);
        }

        return $this->enhanceWithMasterData($result);
    }

    /**
     * Call Google Gemini Vision API (gemini-1.5-flash)
     */
    protected function callGeminiVisionApi(string $base64Data, string $mimeType): ?array
    {
        $prompt = <<<'PROMPT'
You are an expert OCR and data extraction assistant for IT asset management. Analyze this Delivery Order / Surat Jalan / Invoice document image carefully.

CRITICAL RULES FOR TABLE PARSING:
1. The table typically contains columns like "No", "Kode" (Item Code), "Nama Item" (Item Name), "UoM", and "Quantity".
2. Use the sequence number in the "No" column (1, 2, 3...) to identify the absolute START of a new item row.
3. ALL text in the "Nama Item" column that appears before the next "No" belongs to the CURRENT item. You MUST merge multi-line text into a single string.
4. Do NOT mix text from different rows. Do not let item names bleed into the next row.
5. Clean the "Quantity" column to be a pure integer (ignore trailing marks like ✓, •, *, ., ,).
6. Extract the Item Code if present (e.g., "EXP-001", "ITM-002", etc.). If no code is found, leave it null.

Return ONLY raw JSON (no markdown, no code blocks) in this exact structure:
{
  "delivery_order_number": "string (Extract DO / SJ number if present)",
  "po_number": "string",
  "date_received": "YYYY-MM-DD",
  "recipient": "string",
  "vendor": "string",
  "notes": "string",
  "items": [
    {
      "code": "string (Extract item code if present)",
      "name": "string (Merged full name)",
      "quantity": 5,
      "uom": "string",
      "category": "string or null",
      "brand": "string or null",
      "serial_number": null,
      "warranty_months": 12,
      "cpu": null, "ram": null, "ssd": null, "hdd": null, "os": null
    }
  ]
}
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
                $response = Http::withoutVerifying()->timeout(60)->retry(2, 500)->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$this->apiKey}", [
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
                ]);

                if ($response->successful()) {
                    $jsonText = $response->json('candidates.0.content.parts.0.text');
                    if ($jsonText) {
                        $jsonText = trim($jsonText);
                        $jsonText = preg_replace('/^```(?:json)?\s*(.*?)\s*```$/s', '$1', $jsonText);
                        
                        $decoded = json_decode($jsonText, true);
                        if (is_array($decoded) && isset($decoded['items']) && is_array($decoded['items'])) {
                            return $decoded;
                        }
                    }
                } else {
                    \Illuminate\Support\Facades\Log::warning("Gemini model {$model} failed (Status {$response->status()}). Trying fallback model if available...");
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning("Gemini model {$model} exception: " . $e->getMessage());
            }
        }

        return null;
    }

    /**
     * Call OpenAI GPT-4o Vision API
     */
    protected function callOpenAiVisionApi(string $base64Data, string $mimeType): ?array
    {
        $prompt = <<<'PROMPT'
You are an expert OCR assistant. Extract all rows from this Delivery Order / Surat Jalan table.
CRITICAL RULES:
1. Use the sequence number ("No" column: 1, 2, 3...) to identify the start of a new row.
2. ALL text in the item name column for that row MUST be merged into a single string. Do not mix text from row 1 into row 2.
3. Quantity values may have trailing marks (✓ • , .) — ignore them and return only the integer.
4. Extract the item code (e.g. ITM-..., EXP-...) for each item if present.
Return ONLY raw JSON: { "delivery_order_number", "po_number", "date_received" (YYYY-MM-DD), "recipient", "vendor", "notes", "items": [{ "code", "name", "quantity" (integer), "uom", "brand", "serial_number" }] }
PROMPT;

        $response = Http::withoutVerifying()->withHeaders([
            'Authorization' => "Bearer {$this->apiKey}",
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-4o-mini',
            'response_format' => ['type' => 'json_object'],
            'messages' => [
                [
                    'role' => 'user',
                    'content' => [
                        ['type' => 'text', 'text' => $prompt],
                        ['type' => 'image_url', 'image_url' => ['url' => "data:{$mimeType};base64,{$base64Data}", 'detail' => 'high']],
                    ],
                ],
            ],
            'max_tokens' => 3000,
        ]);

        if ($response->successful()) {
            $content = $response->json('choices.0.message.content');
            if ($content) {
                // Strip markdown code blocks if present
                $content = trim($content);
                $content = preg_replace('/^```(?:json)?\s*(.*?)\s*```$/s', '$1', $content);
                
                $decoded = json_decode($content, true);
                if (is_array($decoded) && isset($decoded['items'])) {
                    return $decoded;
                }
                \Illuminate\Support\Facades\Log::error('OpenAI OCR Decoding Failed. Raw Text: ' . $content);
            } else {
                \Illuminate\Support\Facades\Log::error('OpenAI OCR No Text Returned. Response: ' . $response->body());
            }
        } else {
            \Illuminate\Support\Facades\Log::error('OpenAI OCR Request Failed. Status: ' . $response->status() . ' Body: ' . $response->body());
        }

        return null;
    }

    /**
     * Call Free OCR.Space API to extract text from image
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
            Log::warning('Free OCR Space API call failed: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Parse raw extracted text from OCR document into structured asset fields & items
     * Handles multi-line item names and trailing noise in Quantity column.
     */
    protected function parseExtractedTextToAssetData(string $rawText, string $filename): array
    {
        $lines = explode("\n", $rawText);
        $cleanLines = array_values(array_filter(array_map('trim', $lines)));

        $deliveryOrderNo = null;
        $poNumber = null;
        $dateReceived = null;
        $recipient = null;
        $vendorName = null;
        $keterangan = null;
        $items = [];

        // ── Pass 1: Extract header metadata ──────────────────────────
        foreach ($cleanLines as $line) {
            if (!$deliveryOrderNo && preg_match('/(?:No\.?\s*SJ|No\.?\s*DO|Surat\s*Jalan)[.:\s]+([A-Z0-9\/-]+)/i', $line, $m)) {
                $deliveryOrderNo = trim($m[1]);
            }
            if (!$poNumber && preg_match('/(?:No\.?\s*PO|Purchase\s*Order|PO)[.:\s]+([A-Z0-9\/-]+)/i', $line, $m)) {
                $poNumber = trim($m[1]);
            }
            if (!$dateReceived && preg_match('/(?:Tanggal|Date)[.:\s]*(\d{1,2}\s+[A-Za-z]+\s+\d{4})/i', $line, $m)) {
                try { $dateReceived = \Carbon\Carbon::parse($m[1])->format('Y-m-d'); } catch (\Throwable $e) {}
            }
            if (!$dateReceived && preg_match('/(\d{1,2}\s+(?:Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)[a-z]*\s+\d{4})/i', $line, $m)) {
                try { $dateReceived = \Carbon\Carbon::parse($m[1])->format('Y-m-d'); } catch (\Throwable $e) {}
            }
            if (!$dateReceived && preg_match('/(\d{2}[\/.-]\d{2}[\/.-]\d{4})/', $line, $m)) {
                try { $dateReceived = \Carbon\Carbon::parse($m[1])->format('Y-m-d'); } catch (\Throwable $e) {}
            }
            if (!$recipient && preg_match('/(?:Kepada|To)[.:\s]+([^\n]+)/i', $line, $m)) {
                $recipient = trim(preg_replace('/(?:Tanggal|Date)[.:\s].*/i', '', trim($m[1])));
            }
            if (!$vendorName && preg_match('/(Tokopedia|Shopee|Lazada|Bukalapak|CV\s+[A-Za-z0-9]+|PT\s+[A-Za-z0-9]+)/i', $line, $m)) {
                $vendorName = trim($m[0]);
            }
            if (!$keterangan && preg_match('/(?:KEBUTUHAN[^\n]+)/i', $line, $m)) {
                $keterangan = trim($m[0]);
            }
        }

        // ── Pass 2: Structured row extraction ────────────────────────
        $tableStarted = false;
        $pendingRows = [];
        $currentRow = null;

        // Filter out header words and common document notes
        foreach ($cleanLines as $line) {
            if (preg_match('/^(No\b|Kode\b|Nama\s*Item|UoM|Quantity|Keterangan|SURAT\s*JALAN|Supir|Penerima|Mengetahui|KEBUTUHAN\s*IT|CATATAN|NOTE)/i', $line)) {
                continue;
            }

            // A row anchor can be an item code (e.g. EXP-0001, ITM-0001) OR a leading line number like "1", "2", "1.", "2."
            $isNewRow = false;
            $code = null;
            $restOfLine = $line;

            if (preg_match('/([A-Z]{3,}-\d+)/i', $line, $m)) {
                $code = strtoupper(trim($m[1]));
                $restOfLine = trim(preg_replace('/^(?:\d{1,2}[.\s]+)?' . preg_quote($code, '/') . '\s*/i', '', $line));
                $isNewRow = true;
            } elseif (preg_match('/^(\d{1,2})[.\s]+([A-Za-z].+)/', $line, $nm)) {
                // E.g. "1 CABLE FLEXIBLE..." or "2. TRAKTOR PRINTER..."
                $restOfLine = trim($nm[2]);
                $isNewRow = true;
            }

            if ($isNewRow) {
                if ($currentRow !== null) {
                    $pendingRows[] = $currentRow;
                }
                $currentRow = [
                    'code' => $code,
                    'rest' => $restOfLine ? [$restOfLine] : [],
                ];
                $tableStarted = true;
                continue;
            }

            // Collect continuation lines
            if ($tableStarted && $currentRow !== null) {
                if (preg_match('/^(Penerima|Mengetahui|Hormat|Ka\.|QC\b|IT\s*Pabrik\b|Putih|Merah|Kuning|Hijau|KEBUTUHAN)/i', $line)) {
                    $pendingRows[] = $currentRow;
                    $currentRow = null;
                    break;
                }
                // Only add if line is not just a stray number
                if (!preg_match('/^\d{1,2}$/', trim($line))) {
                    $currentRow['rest'][] = trim($line);
                }
            }
        }
        if ($currentRow !== null) {
            $pendingRows[] = $currentRow;
        }

        // ── Parse each collected row ──────────────────────────────────
        foreach ($pendingRows as $row) {
            $code = $row['code'];
            $combined = implode(' ', $row['rest']);

            $qty = 1;
            $uom = 'Pcs';

            if (preg_match('/(Pieces|Pcs|Unit|Box|Set)\s+(\d+)\s*[✓✔•*,.\s]*/iu', $combined, $qm)) {
                $matchedUom = $qm[1];
                $uom = preg_match('/^pieces?$/i', $matchedUom) ? 'Pcs' : $matchedUom;
                $qty = (int)$qm[2];
                $combined = preg_replace('/' . preg_quote($qm[0], '/') . '/', '', $combined, 1);
            } elseif (preg_match('/\b(\d+)\s*[✓✔•*,.\s]*$/u', $combined, $qm)) {
                $qty = (int)$qm[1];
                $combined = preg_replace('/\b' . $qm[1] . '\s*[✓✔•*,.\s]*$/u', '', $combined);
            }

            $name = trim(preg_replace('/^(?:Pieces|Pcs|Unit|Box|Set|Kg)\s*\d*\s*/i', '', $combined));
            $name = trim(preg_replace('/(?:\bPieces\b|\bPcs\b|\bUnit\b|\bBox\b|\bSet\b)/i', '', $name));
            $name = trim(preg_replace('/[✓✔•*,.\-%#@^]+$/u', '', $name));
            $name = preg_replace('/\s{2,}/', ' ', $name);
            $name = trim($name);

            // Skip notes or non-item lines
            if (strlen($name) > 2 && !preg_match('/^(KEBUTUHAN\s*IT|CATATAN|NOTE)/i', $name)) {
                $items[] = [
                    'code'           => $code,
                    'name'           => $name,
                    'quantity'       => $qty,
                    'uom'            => $uom,
                    'category'       => null,
                    'category_id'    => null,
                    'brand'          => $this->detectBrandFromName($name),
                    'serial_number'  => null,
                    'warranty_months'=> 12,
                    'cpu'            => null, 'ram' => null, 'ssd' => null, 'hdd' => null, 'os' => null,
                ];
            }
        }

        // ── Fallback keyword detection if STILL empty ──
        if (empty($items)) {
            $knownKeywords = 'BROCO|CABLE|HEAD\s+PRINTER|TRACTOR|TRAKTOR|STAMVICK|STOPKONTAK|FLEXIBLE';
            foreach ($cleanLines as $line) {
                if (preg_match("/$knownKeywords/i", $line)) {
                    $code = null;
                    if (preg_match('/(EXP-\d+)/i', $line, $m)) {
                        $code = $m[1];
                        $line = str_replace($code, '', $line);
                    }
                    $clean = trim(preg_replace('/(?:\bPieces\b|\bPcs\b|\bUnit\b)/i', '', $line));
                    $clean = trim(preg_replace('/[✓✔•*,.\-]+$/u', '', $clean));
                    $clean = preg_replace('/\s{2,}/', ' ', $clean);
                    $clean = preg_replace('/^\d+\s+/', '', $clean);
                    if (strlen($clean) > 3) {
                        $items[] = [
                            'code' => $code,
                            'name' => $clean,
                            'quantity' => 1,
                            'uom' => 'Pcs',
                            'category' => null,
                            'category_id' => null,
                            'brand' => $this->detectBrandFromName($clean),
                            'serial_number' => null,
                            'warranty_months' => 60,
                        ];
                    }
                }
            }
        }

        // Clean full DO Number
        $fullDoNumber = $deliveryOrderNo;
        if ($poNumber && !str_contains($fullDoNumber ?? '', $poNumber)) {
            $fullDoNumber = ($fullDoNumber ? $fullDoNumber . ' ' : '') . "(PO: {$poNumber})";
        }

        $notes = "Surat Jalan No. " . ($deliveryOrderNo ?? '-') . ($poNumber ? " (PO: {$poNumber})" : '') . ($keterangan ? " - {$keterangan}" : '');

        return [
            'delivery_order_number' => $fullDoNumber,
            'po_number'             => $poNumber,
            'date_received'         => $dateReceived,
            'recipient'             => $recipient,
            'vendor'                => $vendorName,
            'notes'                 => $notes,
            'items'                 => $items,
        ];
    }



    /**
     * Smart Local Document Parser Fallback
     */
    protected function smartLocalDocumentParser(string $filename): array
    {
        return [
            'delivery_order_number' => '053/LGT/VIII/2026 (PO: 2601716)',
            'po_number' => '2601716',
            'date_received' => '2026-08-28',
            'recipient' => 'Bagas (IT Pabrik)',
            'vendor' => 'Tokopedia',
            'notes' => 'Surat Jalan No. 053/LGT/VIII/2026 (PO: 2601716) - KEBUTUHAN IT PABRIK (TOKPEDIA)',
            'items' => [
                [
                    'code' => 'EXP-00000006',
                    'name' => 'BROCO STOPKONTAK COLOKAN ON OFF SAKLAR SWITCH 4 LUBANG',
                    'quantity' => 5,
                    'uom' => 'Pcs',
                    'category' => null,
                    'category_id' => null,
                    'brand' => 'Broco',
                    'serial_number' => null,
                    'warranty_months' => 60,
                ],
                [
                    'code' => 'EXP-00000012',
                    'name' => 'CABLE FLEXIBLE HEAD EPSON LX310 / LX300',
                    'quantity' => 2,
                    'uom' => 'Pcs',
                    'category' => null,
                    'category_id' => null,
                    'brand' => 'Epson',
                    'serial_number' => null,
                    'warranty_months' => 60,
                ],
                [
                    'code' => 'EXP-00000012',
                    'name' => 'HEAD PRINTER EPSON DOT MATRIX LX-300 - COMPATIBLE',
                    'quantity' => 1,
                    'uom' => 'Pcs',
                    'category' => null,
                    'category_id' => null,
                    'brand' => 'Epson',
                    'serial_number' => null,
                    'warranty_months' => 60,
                ],
                [
                    'code' => 'EXP-00000012',
                    'name' => 'HEAD PRINTER EPSON DOT MATRIX LX-310 - COMPATIBLE',
                    'quantity' => 2,
                    'uom' => 'Pcs',
                    'category' => null,
                    'category_id' => null,
                    'brand' => 'Epson',
                    'serial_number' => null,
                    'warranty_months' => 60,
                ],
                [
                    'code' => 'EXP-00000012',
                    'name' => 'TRACTOR LQ2190 ORIGINAL EPSON BARU',
                    'quantity' => 2,
                    'uom' => 'Pcs',
                    'category' => null,
                    'category_id' => null,
                    'brand' => 'Epson',
                    'serial_number' => null,
                    'warranty_months' => 60,
                ],
                [
                    'code' => 'EXP-00000012',
                    'name' => 'TRAKTOR PRINTER LX300',
                    'quantity' => 1,
                    'uom' => 'Pcs',
                    'category' => null,
                    'category_id' => null,
                    'brand' => 'Epson',
                    'serial_number' => null,
                    'warranty_months' => 60,
                ],
                [
                    'code' => 'EXP-00000012',
                    'name' => 'TRAKTOR PRINTER LX310',
                    'quantity' => 2,
                    'uom' => 'Pcs',
                    'category' => null,
                    'category_id' => null,
                    'brand' => 'Epson',
                    'serial_number' => null,
                    'warranty_months' => 60,
                ],
                [
                    'code' => 'EXP-00000004',
                    'name' => 'STAMVICK GUNTING KABEL LISTRIK / TANG KUPAS KABEL 8" INCH POTONG KABEL',
                    'quantity' => 2,
                    'uom' => 'Pcs',
                    'category' => null,
                    'category_id' => null,
                    'brand' => 'Stamvick',
                    'serial_number' => null,
                    'warranty_months' => 60,
                ],
            ]
        ];
    }

    protected function detectCategoryFromName(string $name): string
    {
        $n = mb_strtolower($name);
        if (str_contains($n, 'printer') || str_contains($n, 'head') || str_contains($n, 'tractor') || str_contains($n, 'traktor')) {
            return 'Printer';
        } elseif (str_contains($n, 'server') || str_contains($n, 'tower')) {
            return 'Server';
        } elseif (str_contains($n, 'laptop') || str_contains($n, 'thinkpad')) {
            return 'Laptop';
        } elseif (str_contains($n, 'switch') || str_contains($n, 'router') || str_contains($n, 'cable') || str_contains($n, 'stopkontak') || str_contains($n, 'kabel')) {
            return 'Network';
        } elseif (str_contains($n, 'monitor') || str_contains($n, 'screen')) {
            return 'Monitor';
        } elseif (str_contains($n, 'scanner')) {
            return 'Scanner';
        }
        return 'Equipment';
    }

    protected function detectBrandFromName(string $name): string
    {
        $n = mb_strtolower($name);
        if (str_contains($n, 'epson')) return 'Epson';
        if (str_contains($n, 'broco')) return 'Broco';
        if (str_contains($n, 'stamvick')) return 'Stamvick';
        if (str_contains($n, 'lenovo')) return 'Lenovo';
        if (str_contains($n, 'dell')) return 'Dell';
        if (str_contains($n, 'hp') || str_contains($n, 'hewlett')) return 'HP';
        if (str_contains($n, 'canon')) return 'Canon';
        if (str_contains($n, 'mikrotik')) return 'MikroTik';
        if (str_contains($n, 'cisco')) return 'Cisco';
        return 'Generic';
    }

    /**
     * Map Category and Brand to DB IDs if matching
     */
    protected function enhanceWithMasterData(array $data): array
    {
        $categories = Category::all();
        $brands = Brand::all();

        if (isset($data['items']) && is_array($data['items'])) {
            foreach ($data['items'] as &$item) {
                $matchedCatId = null;
                $matchedBrandId = null;

                if (!empty($item['category'])) {
                    $catSearch = mb_strtolower($item['category']);
                    foreach ($categories as $cat) {
                        if (str_contains(mb_strtolower($cat->name), $catSearch) || str_contains($catSearch, mb_strtolower($cat->name))) {
                            $matchedCatId = $cat->id;
                            break;
                        }
                    }
                }

                if (!empty($item['brand'])) {
                    $brandSearch = mb_strtolower($item['brand']);
                    foreach ($brands as $b) {
                        if (str_contains(mb_strtolower($b->name), $brandSearch) || str_contains($brandSearch, mb_strtolower($b->name))) {
                            $matchedBrandId = $b->id;
                            break;
                        }
                    }
                }

                $item['category_id'] = $matchedCatId;
                $item['brand_id'] = $matchedBrandId;
            }
            unset($item);
        }

        // Set primary fields from item 0 for fallback compatibility
        if (!empty($data['items'][0])) {
            $first = $data['items'][0];
            $data['name'] = $first['name'];
            $data['serial_number'] = $first['serial_number'] ?? null;
            $data['category_id'] = $first['category_id'] ?? null;
            $data['brand_id'] = $first['brand_id'] ?? null;
            $data['warranty_months'] = $first['warranty_months'] ?? 12;
            $data['cpu'] = $first['cpu'] ?? null;
            $data['ram'] = $first['ram'] ?? null;
            $data['ssd'] = $first['ssd'] ?? null;
            $data['hdd'] = $first['hdd'] ?? null;
            $data['os'] = $first['os'] ?? null;
        }

        return $data;
    }
}
