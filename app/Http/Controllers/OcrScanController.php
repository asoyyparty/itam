<?php

namespace App\Http\Controllers;

use App\Services\OcrScanService;
use Illuminate\Http\Request;

class OcrScanController extends Controller
{
    protected OcrScanService $ocrService;

    public function __construct(OcrScanService $ocrService)
    {
        $this->ocrService = $ocrService;
    }

    public function scan(Request $request)
    {
        $request->validate([
            'document' => 'required|file|mimes:jpeg,png,jpg,webp,pdf|max:10240',
        ]);

        try {
            $parsedData = $this->ocrService->parseDocument($request->file('document'));

            return response()->json([
                'success' => true,
                'message' => 'Dokumen berhasil dipindai dengan AI Vision OCR!',
                'data' => $parsedData,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memindai dokumen: ' . $e->getMessage(),
            ], 500);
        }
    }
}
