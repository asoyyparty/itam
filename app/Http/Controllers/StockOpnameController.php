<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Category;
use App\Models\Location;
use App\Models\StockOpnameItem;
use App\Models\StockOpnameSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockOpnameController extends Controller
{
    public function index(Request $request)
    {
        $query = StockOpnameSession::with(['location', 'category', 'creator', 'items'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('location_id')) {
            $query->where('location_id', $request->location_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('session_code', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%");
            });
        }

        $sessions = $query->paginate(12);

        $locations = Location::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();

        $stats = [
            'total_sessions' => StockOpnameSession::count(),
            'in_progress' => StockOpnameSession::where('status', 'in_progress')->count(),
            'completed' => StockOpnameSession::where('status', 'completed')->count(),
            'total_audited_items' => StockOpnameItem::whereIn('status', ['matched', 'displaced'])->count(),
        ];

        return view('stock_opname.index', compact('sessions', 'locations', 'categories', 'stats'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'location_id' => 'nullable|exists:locations,id',
            'category_id' => 'nullable|exists:categories,id',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Generate Session Code SO/YYYY/MM/XXX
            $yearMonth = date('Y/m');
            $countToday = StockOpnameSession::whereYear('created_at', date('Y'))
                ->whereMonth('created_at', date('m'))
                ->count() + 1;
            $code = 'SO/' . $yearMonth . '/' . str_pad($countToday, 3, '0', STR_PAD_LEFT);

            $session = StockOpnameSession::create([
                'session_code' => $code,
                'title' => $request->title,
                'location_id' => $request->location_id,
                'category_id' => $request->category_id,
                'status' => 'in_progress',
                'started_at' => now(),
                'created_by' => Auth::id(),
                'notes' => $request->notes,
            ]);

            // Query expected assets
            $assetsQuery = Asset::query();

            if ($request->filled('location_id')) {
                $assetsQuery->where('location_id', $request->location_id);
            }

            if ($request->filled('category_id')) {
                $assetsQuery->where('category_id', $request->category_id);
            }

            $assets = $assetsQuery->get();

            $itemsData = [];
            foreach ($assets as $asset) {
                $itemsData[] = [
                    'session_id' => $session->id,
                    'asset_id' => $asset->id,
                    'expected_location_id' => $asset->location_id,
                    'scanned_location_id' => null,
                    'scanned_by' => null,
                    'scanned_at' => null,
                    'status' => 'pending',
                    'physical_condition' => 'good',
                    'notes' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            if (!empty($itemsData)) {
                StockOpnameItem::insert($itemsData);
            }

            $session->recalculateStats();

            DB::commit();

            return redirect()->route('stock-opname.show', $session->id)
                ->with('success', "Sesi Stock Opname {$session->session_code} berhasil dibuat dengan {$session->total_expected} aset target.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal membuat sesi Stock Opname: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $session = StockOpnameSession::with(['location', 'category', 'creator', 'items.asset.location', 'items.asset.category', 'items.expectedLocation', 'items.scannedLocation', 'items.scanner'])
            ->findOrFail($id);

        $locations = Location::orderBy('name')->get();

        $items = $session->items()
            ->with(['asset.location', 'asset.category', 'expectedLocation', 'scannedLocation'])
            ->get();

        return view('stock_opname.show', compact('session', 'items', 'locations'));
    }

    public function scanAsset(Request $request, $id)
    {
        $session = StockOpnameSession::findOrFail($id);

        if ($session->status === 'completed' || $session->status === 'cancelled') {
            return response()->json([
                'success' => false,
                'message' => 'Sesi Stock Opname ini sudah selesai atau dibatalkan.'
            ], 422);
        }

        $rawCode = trim($request->input('code'));
        if (empty($rawCode)) {
            return response()->json(['success' => false, 'message' => 'Kode QR / Barcode tidak boleh kosong.'], 422);
        }

        // Parse code: might be asset_tag (AST-001), serial_number, or URL (http://.../assets/12)
        $asset = null;
        if (preg_match('/assets\/(\d+)/i', $rawCode, $matches)) {
            $assetId = $matches[1];
            $asset = Asset::find($assetId);
        }

        if (!$asset) {
            $asset = Asset::where('asset_tag', $rawCode)
                ->orWhere('serial_number', $rawCode)
                ->orWhere('id', $rawCode)
                ->first();
        }

        if (!$asset) {
            return response()->json([
                'success' => false,
                'status' => 'unknown',
                'message' => "Kode QR/Barcode \"{$rawCode}\" tidak ditemukan di basis data aset ITAM."
            ], 404);
        }

        $scannedLocationId = $request->input('location_id', $session->location_id);
        $physicalCondition = $request->input('condition', 'good');
        $notes = $request->input('notes');

        // Check if item exists in session
        $item = StockOpnameItem::where('session_id', $session->id)
            ->where('asset_id', $asset->id)
            ->first();

        $isDisplaced = false;

        if ($item) {
            // Determine if matched or displaced
            if ($session->location_id) {
                $isDisplaced = ($asset->location_id != $session->location_id);
            } else if ($scannedLocationId) {
                $isDisplaced = ($asset->location_id != $scannedLocationId);
            }

            $item->status = $isDisplaced ? 'displaced' : 'matched';
            $item->scanned_location_id = $scannedLocationId;
            $item->scanned_by = Auth::id();
            $item->scanned_at = now();
            $item->physical_condition = $physicalCondition;
            if ($notes) $item->notes = $notes;
            $item->save();
        } else {
            // Asset wasn't expected in session filter (Displaced / Extra item scanned)
            $isDisplaced = true;
            $item = StockOpnameItem::create([
                'session_id' => $session->id,
                'asset_id' => $asset->id,
                'expected_location_id' => $asset->location_id,
                'scanned_location_id' => $scannedLocationId,
                'scanned_by' => Auth::id(),
                'scanned_at' => now(),
                'status' => 'displaced',
                'physical_condition' => $physicalCondition,
                'notes' => $notes ?? 'Aset dari lokasi/kategori lain dipindai',
            ]);
        }

        $session->recalculateStats();

        return response()->json([
            'success' => true,
            'status' => $item->status,
            'is_displaced' => $isDisplaced,
            'asset' => [
                'id' => $asset->id,
                'asset_tag' => $asset->asset_tag,
                'name' => $asset->name,
                'serial_number' => $asset->serial_number ?? '-',
                'category' => $asset->category->name ?? 'Uncategorized',
                'expected_location' => $asset->location->name ?? 'N/A',
                'scanned_location' => $item->scannedLocation->name ?? ($asset->location->name ?? 'N/A'),
                'condition' => $item->physical_condition,
                'scanned_at' => $item->scanned_at ? $item->scanned_at->format('d M Y H:i:s') : null,
            ],
            'session_stats' => [
                'total_expected' => $session->total_expected,
                'total_matched' => $session->total_matched,
                'total_displaced' => $session->total_displaced,
                'total_missing' => $session->total_missing,
                'progress_percentage' => $session->progress_percentage,
            ],
            'message' => $isDisplaced
                ? "Aset \"{$asset->name}\" ({$asset->asset_tag}) terdeteksi BERPINDAH dari {$asset->location->name}!"
                : "Aset \"{$asset->name}\" ({$asset->asset_tag}) TERVERIFIKASI sesuai lokasi."
        ]);
    }

    public function updateItem(Request $request, $id, $itemId)
    {
        $session = StockOpnameSession::findOrFail($id);
        $item = StockOpnameItem::where('session_id', $session->id)->findOrFail($itemId);

        $request->validate([
            'status' => 'required|in:pending,matched,displaced,missing',
            'physical_condition' => 'nullable|in:good,damaged,needs_repair',
            'notes' => 'nullable|string',
            'update_asset_location' => 'nullable|boolean',
        ]);

        $item->status = $request->status;
        if ($request->filled('physical_condition')) {
            $item->physical_condition = $request->physical_condition;
        }
        if ($request->has('notes')) {
            $item->notes = $request->notes;
        }
        $item->save();

        // Optionally update asset master location if requested
        if ($request->boolean('update_asset_location') && $item->scanned_location_id) {
            $asset = $item->asset;
            if ($asset) {
                $asset->location_id = $item->scanned_location_id;
                $asset->save();
            }
        }

        $session->recalculateStats();

        return response()->json([
            'success' => true,
            'message' => 'Status item audit berhasil diperbarui.',
            'session_stats' => [
                'total_expected' => $session->total_expected,
                'total_matched' => $session->total_matched,
                'total_displaced' => $session->total_displaced,
                'total_missing' => $session->total_missing,
                'progress_percentage' => $session->progress_percentage,
            ]
        ]);
    }

    public function complete($id)
    {
        $session = StockOpnameSession::findOrFail($id);

        DB::beginTransaction();
        try {
            // Mark remaining pending items as missing
            $session->items()->where('status', 'pending')->update([
                'status' => 'missing'
            ]);

            $session->status = 'completed';
            $session->completed_at = now();
            $session->recalculateStats();

            DB::commit();

            return redirect()->route('stock-opname.show', $session->id)
                ->with('success', "Sesi Stock Opname {$session->session_code} telah SELESAI. {$session->total_missing} aset dicatat sebagai Belum Ditemukan / Hilang.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menyelesaikan sesi Stock Opname: ' . $e->getMessage());
        }
    }

    public function exportExcel($id)
    {
        $session = StockOpnameSession::with(['location', 'category', 'creator', 'items.asset.location', 'items.asset.category', 'items.expectedLocation', 'items.scannedLocation', 'items.scanner'])
            ->findOrFail($id);

        $filename = "Laporan_Stock_Opname_" . str_replace('/', '_', $session->session_code) . "_" . date('Ymd_His') . ".csv";

        $headers = [
            "Content-type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename={$filename}",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function () use ($session) {
            $file = fopen('php://output', 'w');
            // Add UTF-8 BOM
            fputs($file, "\xEF\xBB\xBF");

            // Header info
            fputcsv($file, ["LAPORAN AUDIT STOCK OPNAME ITAM ENTERPRISE"]);
            fputcsv($file, ["Kode Sesi", $session->session_code]);
            fputcsv($file, ["Judul Sesi", $session->title]);
            fputcsv($file, ["Lokasi Target", $session->location ? $session->location->name : 'Semua Lokasi']);
            fputcsv($file, ["Status Sesi", strtoupper($session->status)]);
            fputcsv($file, ["Tanggal Mulai", $session->started_at ? $session->started_at->format('d M Y H:i') : '-']);
            fputcsv($file, ["Tanggal Selesai", $session->completed_at ? $session->completed_at->format('d M Y H:i') : '-']);
            fputcsv($file, ["Total Target", $session->total_expected]);
            fputcsv($file, ["Terverifikasi", $session->total_matched]);
            fputcsv($file, ["Berpindah Lokasi", $session->total_displaced]);
            fputcsv($file, ["Belum Ditemukan/Hilang", $session->total_missing]);
            fputcsv($file, []);

            // Data Columns
            fputcsv($file, [
                '#',
                'Kode Aset',
                'Nama Aset',
                'Nomor Seri',
                'Kategori',
                'Lokasi Ekspektasi',
                'Lokasi Hasil Scan',
                'Status Audit',
                'Kondisi Fisik',
                'Waktu Scan',
                'Petugas Scan',
                'Catatan Audit'
            ]);

            foreach ($session->items as $index => $item) {
                $statusMap = [
                    'matched' => 'Terverifikasi (Matched)',
                    'displaced' => 'Berpindah (Displaced)',
                    'missing' => 'Hilang / Belum Ditemukan',
                    'pending' => 'Belum Di-scan',
                ];

                $condMap = [
                    'good' => 'Baik',
                    'damaged' => 'Rusak',
                    'needs_repair' => 'Perlu Perbaikan',
                ];

                fputcsv($file, [
                    $index + 1,
                    $item->asset ? $item->asset->asset_tag : 'N/A',
                    $item->asset ? $item->asset->name : 'N/A',
                    $item->asset ? ($item->asset->serial_number ?? '-') : '-',
                    $item->asset && $item->asset->category ? $item->asset->category->name : '-',
                    $item->expectedLocation ? $item->expectedLocation->name : '-',
                    $item->scannedLocation ? $item->scannedLocation->name : ($item->expectedLocation ? $item->expectedLocation->name : '-'),
                    $statusMap[$item->status] ?? $item->status,
                    $condMap[$item->physical_condition] ?? $item->physical_condition,
                    $item->scanned_at ? $item->scanned_at->format('d/m/Y H:i:s') : '-',
                    $item->scanner ? $item->scanner->name : '-',
                    $item->notes ?? '-'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function destroy($id)
    {
        $session = StockOpnameSession::findOrFail($id);
        $code = $session->session_code;
        $session->delete();

        return redirect()->route('stock-opname.index')
            ->with('success', "Sesi Stock Opname {$code} berhasil dihapus.");
    }
}
