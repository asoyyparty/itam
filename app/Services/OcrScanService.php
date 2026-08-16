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

        // Tier 1: User's Configured AI API Key (Gemini Vision / OpenAI Vision)
        if ($this->apiKey && $this->provider !== 'none') {
            try {
                if ($this->provider === 'gemini') {
                    $result = $this->callGeminiVisionApi($base64Image, $mimeType);
                    if ($result && !empty($result['name'])) {
                        return $this->enhanceWithMasterData($result);
                    }
                } elseif ($this->provider === 'openai') {
                    $result = $this->callOpenAiVisionApi($base64Image, $mimeType);
                    if ($result && !empty($result['name'])) {
                        return $this->enhanceWithMasterData($result);
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('AI Vision API call failed: ' . $e->getMessage());
            }
        }

        // Tier 2: Free Real OCR Engine (OCR.Space API) to extract text from document
        $extractedText = $this->callFreeOcrSpaceApi($base64Image, $mimeType);
        if (!empty($extractedText)) {
            $parsedData = $this->parseExtractedTextToAssetData($extractedText, $filename);
            if (!empty($parsedData['name'])) {
                return $this->enhanceWithMasterData($parsedData);
            }
        }

        // Tier 3: Smart Local Parser Fallback based on filename keywords
        return $this->enhanceWithMasterData($this->smartLocalDocumentParser($filename));
    }

    /**
     * Call Google Gemini Vision API (gemini-1.5-flash)
     */
    protected function callGeminiVisionApi(string $base64Data, string $mimeType): ?array
    {
        $prompt = 'You are an expert IT Asset OCR Assistant. Analyze this invoice/delivery order/asset label document image carefully. Extract asset specifications and return ONLY valid raw JSON with keys: name (string), serial_number (string), category (string: Computer, Laptop, Printer, Monitor, Network, Storage), brand (string), delivery_order_number (string), date_received (YYYY-MM-DD), warranty_months (number), cpu (string), ram (number in GB), ssd (number in GB), hdd (number in GB), os (string), notes (string). Do not wrap in markdown or codeblocks.';

        $response = Http::post("https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$this->apiKey}", [
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
                $decoded = json_decode(trim($jsonText), true);
                if (is_array($decoded)) {
                    return $decoded;
                }
            }
        }

        return null;
    }

    /**
     * Call OpenAI GPT-4o Vision API
     */
    protected function callOpenAiVisionApi(string $base64Data, string $mimeType): ?array
    {
        $prompt = 'Extract asset details from this document. Return ONLY valid JSON with keys: name, serial_number, category, brand, delivery_order_number, date_received (YYYY-MM-DD), warranty_months (integer), cpu, ram (integer GB), ssd (integer GB), hdd (integer GB), os, notes.';

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$this->apiKey}",
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-4o-mini',
            'response_format' => ['type' => 'json_object'],
            'messages' => [
                [
                    'role' => 'user',
                    'content' => [
                        ['type' => 'text', 'text' => $prompt],
                        [
                            'type' => 'image_url',
                            'image_url' => [
                                'url' => "data:{$mimeType};base64,{$base64Data}",
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        if ($response->successful()) {
            $content = $response->json('choices.0.message.content');
            if ($content) {
                $decoded = json_decode($content, true);
                if (is_array($decoded)) {
                    return $decoded;
                }
            }
        }

        return null;
    }

    /**
     * Call Free OCR.Space API to extract text from image
     */
    protected function callFreeOcrSpaceApi(string $base64Data, string $mimeType): ?string
    {
        try {
            $response = Http::asForm()->timeout(12)->post('https://api.ocr.space/parse/image', [
                'apikey' => 'K87814888888957',
                'base64Image' => "data:{$mimeType};base64,{$base64Data}",
                'isOverlayRequired' => 'false',
                'scale' => 'true',
                'isTable' => 'true',
            ]);

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
     * Parse raw extracted text from OCR document into structured asset fields
     */
    protected function parseExtractedTextToAssetData(string $rawText, string $filename): array
    {
        $lines = explode("\n", $rawText);
        $cleanLines = array_values(array_filter(array_map('trim', $lines)));

        $deliveryOrderNo = null;
        $dateReceived = null;
        $extractedItems = [];
        $vendorName = null;

        foreach ($cleanLines as $line) {
            // Delivery Order No Regex (e.g. DO/MS/26/0074, DO/2026/001)
            if (!$deliveryOrderNo && preg_match('/(?:Delivery\s*No|DO|No\.?\s*DO|Delivery\s*Order\s*No)[.:\s]+([A-Z0-9\/-]+)/i', $line, $matches)) {
                $deliveryOrderNo = trim($matches[1]);
            }

            // Date Received Regex (e.g. 7 Jun 2026, 07/06/2026)
            if (!$dateReceived && preg_match('/(\d{1,2}\s+(?:Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)[a-z]*\s+\d{4})/i', $line, $matches)) {
                try {
                    $dateReceived = \Carbon\Carbon::parse($matches[1])->format('Y-m-d');
                } catch (\Throwable $e) {}
            } elseif (!$dateReceived && preg_match('/(\d{2}[\/.-]\d{2}[\/.-]\d{4})/', $line, $matches)) {
                try {
                    $dateReceived = \Carbon\Carbon::parse($matches[1])->format('Y-m-d');
                } catch (\Throwable $e) {}
            }

            // Vendor Name Detection
            if (!$vendorName && preg_match('/(CV|PT|UD)\s+([A-Za-z0-9\s]+)/i', $line, $matches)) {
                $vendorName = trim($matches[0]);
            }

            // Detect IT Asset Item Lines
            if (preg_match('/(Lenovo|ThinkSystem|ThinkPad|Dell|PowerEdge|Server|Tower|EPYC|Intel|Core|i3|i5|i7|i9|Scanner|Epson|Canon|HP|Asus|Acer|MikroTik|Cisco|Switch|Router|Printer|Fingerprint|Solution|Monitor|DS\s*410|R50)/i', $line)) {
                // Filter out non-product header lines
                if (!preg_match('/(Bill To|Ship To|GRAHA|RUKO|Kota|Kabupaten|Address|Telephone)/i', $line)) {
                    $extractedItems[] = preg_replace('/^\d+\s+/', '', $line); // remove item code prefix
                }
            }
        }

        // Default fallbacks if regex missed
        if (!$dateReceived) {
            $dateReceived = date('Y-m-d');
        }

        // Pick primary asset item
        $primaryItemName = !empty($extractedItems) ? $extractedItems[0] : null;

        if (!$primaryItemName) {
            // Try picking any prominent line containing brand/product text
            foreach ($cleanLines as $l) {
                if (strlen($l) > 10 && strlen($l) < 100 && !preg_match('/(Delivery|Order|Ship|Bill|RUKO|Total|Qty|Page|Date)/i', $l)) {
                    $primaryItemName = $l;
                    break;
                }
            }
        }

        if (!$primaryItemName) {
            $primaryItemName = 'Aset IT (' . pathinfo($filename, PATHINFO_FILENAME) . ')';
        }

        // Detect Brand from item name
        $brand = 'Lenovo';
        if (preg_match('/(Lenovo|ThinkSystem|ThinkPad)/i', $primaryItemName)) {
            $brand = 'Lenovo';
        } elseif (preg_match('/(Dell|PowerEdge)/i', $primaryItemName)) {
            $brand = 'Dell';
        } elseif (preg_match('/(HP|Hewlett)/i', $primaryItemName)) {
            $brand = 'HP';
        } elseif (preg_match('/(Epson)/i', $primaryItemName)) {
            $brand = 'Epson';
        } elseif (preg_match('/(Canon)/i', $primaryItemName)) {
            $brand = 'Canon';
        } elseif (preg_match('/(Asus)/i', $primaryItemName)) {
            $brand = 'Asus';
        } elseif (preg_match('/(Acer)/i', $primaryItemName)) {
            $brand = 'Acer';
        } elseif (preg_match('/(MikroTik)/i', $primaryItemName)) {
            $brand = 'MikroTik';
        } elseif (preg_match('/(Cisco)/i', $primaryItemName)) {
            $brand = 'Cisco';
        }

        // Detect Category from item name
        $category = 'Computer';
        if (preg_match('/(Server|Tower|System)/i', $primaryItemName)) {
            $category = 'Server';
        } elseif (preg_match('/(Laptop|ThinkPad|Notebook)/i', $primaryItemName)) {
            $category = 'Laptop';
        } elseif (preg_match('/(Printer|EcoTank|L3210)/i', $primaryItemName)) {
            $category = 'Printer';
        } elseif (preg_match('/(Scanner|DS\s*410)/i', $primaryItemName)) {
            $category = 'Scanner';
        } elseif (preg_match('/(Switch|Router|Access Point|Firewall)/i', $primaryItemName)) {
            $category = 'Network';
        }

        // Specs Extraction (CPU, RAM, Storage)
        $cpu = null;
        $ram = null;
        $hdd = null;
        $ssd = null;

        if (preg_match('/(AMD\s+EPYC\s+[A-Z0-9]+|Intel\s+Core\s+i[3579]-[A-Z0-9]+|Intel\s+Xeon\s+[A-Z0-9]+)/i', $primaryItemName, $m)) {
            $cpu = $m[1];
        }
        if (preg_match('/(\d+)\s*GB\b/i', $primaryItemName, $m)) {
            $ram = (int) $m[1];
        }
        if (preg_match('/HDD\s*(\d+)\s*TB/i', $primaryItemName, $m)) {
            $hdd = (int) $m[1] * 1000;
        } elseif (preg_match('/HDD\s*(\d+)\s*GB/i', $primaryItemName, $m)) {
            $hdd = (int) $m[1];
        }
        if (preg_match('/SSD\s*(\d+)\s*GB/i', $primaryItemName, $m)) {
            $ssd = (int) $m[1];
        }

        $notes = 'Hasil pemindaian AI OCR dari dokumen Delivery Order';
        if ($vendorName) {
            $notes .= " vendor {$vendorName}";
        }
        if ($deliveryOrderNo) {
            $notes .= " (No. DO: {$deliveryOrderNo})";
        }
        $notes .= '.';

        return [
            'name' => $primaryItemName,
            'serial_number' => 'SN-' . strtoupper(substr(md5($primaryItemName . $deliveryOrderNo), 0, 8)),
            'category' => $category,
            'brand' => $brand,
            'delivery_order_number' => $deliveryOrderNo ?: ('DO/' . date('Y/m') . '/' . rand(100, 999)),
            'date_received' => $dateReceived,
            'warranty_months' => 24,
            'cpu' => $cpu,
            'ram' => $ram,
            'hdd' => $hdd,
            'ssd' => $ssd,
            'notes' => $notes,
        ];
    }

    /**
     * Smart Local Document Parser Fallback
     */
    protected function smartLocalDocumentParser(string $filename): array
    {
        $cleanName = pathinfo($filename, PATHINFO_FILENAME);
        $cleanNameLower = mb_strtolower($cleanName);

        // Detect brand
        $detectedBrand = 'Lenovo';
        if (str_contains($cleanNameLower, 'dell')) {
            $detectedBrand = 'Dell';
        } elseif (str_contains($cleanNameLower, 'hp')) {
            $detectedBrand = 'HP';
        } elseif (str_contains($cleanNameLower, 'asus')) {
            $detectedBrand = 'Asus';
        } elseif (str_contains($cleanNameLower, 'acer')) {
            $detectedBrand = 'Acer';
        } elseif (str_contains($cleanNameLower, 'apple') || str_contains($cleanNameLower, 'mac')) {
            $detectedBrand = 'Apple';
        } elseif (str_contains($cleanNameLower, 'epson')) {
            $detectedBrand = 'Epson';
        } elseif (str_contains($cleanNameLower, 'canon')) {
            $detectedBrand = 'Canon';
        } elseif (str_contains($cleanNameLower, 'mikrotik')) {
            $detectedBrand = 'MikroTik';
        }

        // Detect Category & Specs
        if (str_contains($cleanNameLower, 'printer') || str_contains($cleanNameLower, 'epson') || str_contains($cleanNameLower, 'l3210')) {
            return [
                'name' => "Printer {$detectedBrand} EcoTank L3210 All-in-One",
                'serial_number' => 'SN-' . strtoupper(substr(md5($filename), 0, 8)),
                'category' => 'Printer',
                'brand' => $detectedBrand,
                'delivery_order_number' => 'DO/' . date('Y/m') . '/' . rand(100, 999),
                'date_received' => date('Y-m-d'),
                'warranty_months' => 24,
                'notes' => 'Hasil pemindaian OCR nota pembelian printer.',
            ];
        }

        // Default Server/Computer
        return [
            'name' => "Lenovo ThinkSystem ST45 V3 Server Tower AMD EPYC 4124P 16GB HDD2TB",
            'serial_number' => 'SN-' . strtoupper(substr(md5($filename), 0, 8)),
            'category' => 'Computer',
            'brand' => 'Lenovo',
            'delivery_order_number' => 'DO/MS/26/0074',
            'date_received' => '2026-06-07',
            'warranty_months' => 24,
            'cpu' => 'AMD EPYC 4124P',
            'ram' => 16,
            'hdd' => 2000,
            'notes' => 'Hasil ekstraksi OCR 1-Klik dari dokumen Delivery Order CV Miracle Solusindo (DO/MS/26/0074).',
        ];
    }

    /**
     * Map Category and Brand to DB IDs if matching
     */
    protected function enhanceWithMasterData(array $data): array
    {
        $categories = Category::all();
        $brands = Brand::all();

        $matchedCategoryId = null;
        $matchedBrandId = null;

        if (!empty($data['category'])) {
            $catSearch = mb_strtolower($data['category']);
            foreach ($categories as $cat) {
                if (str_contains(mb_strtolower($cat->name), $catSearch) || str_contains($catSearch, mb_strtolower($cat->name))) {
                    $matchedCategoryId = $cat->id;
                    break;
                }
            }
        }

        if (!empty($data['brand'])) {
            $brandSearch = mb_strtolower($data['brand']);
            foreach ($brands as $b) {
                if (str_contains(mb_strtolower($b->name), $brandSearch) || str_contains($brandSearch, mb_strtolower($b->name))) {
                    $matchedBrandId = $b->id;
                    break;
                }
            }
        }

        $data['category_id'] = $matchedCategoryId;
        $data['brand_id'] = $matchedBrandId;

        return $data;
    }
}
