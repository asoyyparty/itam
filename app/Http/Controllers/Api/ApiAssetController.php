<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use Illuminate\Http\Request;

class ApiAssetController extends Controller
{
    /**
     * Get paginated asset catalog for Flutter app with search and filters.
     */
    public function index(Request $request)
    {
        $query = Asset::with(['category', 'brand', 'location', 'currentAssignment.employee']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('asset_tag', 'like', "%{$search}%")
                  ->orWhere('serial_number', 'like', "%{$search}%")
                  ->orWhere('delivery_order', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->input('brand_id'));
        }

        if ($request->filled('location_id')) {
            $query->where('location_id', $request->input('location_id'));
        }

        $perPage = (int) $request->input('per_page', 15);
        $assets = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $assets,
        ]);
    }

    /**
     * Get detailed asset payload including all specifications and history.
     */
    public function show($id)
    {
        $asset = Asset::with([
            'category',
            'brand',
            'location',
            'vendor',
            'department',
            'computer',
            'printer',
            'networkDetail',
            'cctv',
            'customFields',
            'ipAddresses',
            'currentAssignment.employee',
            'maintenances',
            'tickets',
        ])->find($id);

        if (!$asset) {
            return response()->json([
                'success' => false,
                'message' => 'Aset tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $asset,
        ]);
    }

    /**
     * Look up asset by asset_tag or serial_number (Designed specifically for Mobile Camera QR / Barcode Scanner).
     */
    public function scanQr($tag)
    {
        $tag = trim($tag);

        $asset = Asset::with([
            'category',
            'brand',
            'location',
            'computer',
            'printer',
            'networkDetail',
            'cctv',
            'currentAssignment.employee',
        ])
        ->where('asset_tag', $tag)
        ->orWhere('serial_number', $tag)
        ->orWhere('id', $tag)
        ->first();

        if (!$asset) {
            return response()->json([
                'success' => false,
                'message' => 'Aset dengan tag/barcode "' . $tag . '" tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Aset berhasil ditemukan dari hasil scan.',
            'data' => $asset,
        ]);
    }
}
