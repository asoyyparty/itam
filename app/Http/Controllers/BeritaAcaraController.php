<?php

namespace App\Http\Controllers;

use App\Models\BeritaAcara;
use App\Services\BeritaAcaraService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BeritaAcaraController extends Controller
{
    protected BeritaAcaraService $beritaAcaraService;

    public function __construct(BeritaAcaraService $beritaAcaraService)
    {
        $this->beritaAcaraService = $beritaAcaraService;
    }

    /**
     * Display a listing of Berita Acara documents.
     */
    public function index(Request $request)
    {
        $query = BeritaAcara::with('creator')->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nomor_surat', 'like', "%{$search}%")
                    ->orWhere('nomor_po', 'like', "%{$search}%")
                    ->orWhere('kepada', 'like', "%{$search}%")
                    ->orWhere('nama_penerima', 'like', "%{$search}%")
                    ->orWhere('status_verifikasi', 'like', "%{$search}%")
                    ->orWhere('items', 'like', "%{$search}%");
            });
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('tanggal_surat', [$request->start_date, $request->end_date]);
        }

        $beritaAcaras = $query->paginate(15)->withQueryString();

        return view('berita_acara.index', compact('beritaAcaras'));
    }

    /**
     * Show the form for creating a new Berita Acara (with AI Vision OCR scanner).
     */
    public function create()
    {
        return view('berita_acara.create');
    }

    /**
     * Process uploaded Surat Jalan image/PDF using AI Vision OCR.
     */
    public function scanOcr(Request $request)
    {
        $request->validate([
            'document' => 'required|file|mimes:jpeg,png,jpg,webp,pdf|max:10240',
        ]);

        try {
            $parsedData = $this->beritaAcaraService->parseSuratJalan($request->file('document'));

            return response()->json([
                'success' => true,
                'message' => 'Surat Jalan berhasil dipindai dengan AI Vision OCR!',
                'data' => $parsedData,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memindai Surat Jalan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created Berita Acara.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nomor_surat' => 'required|string|max:255',
            'nomor_po' => 'nullable|string|max:255',
            'tanggal_surat' => 'required|date',
            'nomor_ba' => 'nullable|string|max:255',
            'lokasi' => 'nullable|string|max:255',
            'hal' => 'nullable|string|max:255',
            'lampiran' => 'nullable|string|max:255',
            'kepada_yth' => 'nullable|string|max:255',
            'cc' => 'nullable|string|max:255',
            'nama_perusahaan' => 'nullable|string|max:255',
            'kepada' => 'nullable|string|max:255',
            'supir_kendaraan' => 'nullable|string|max:255',
            'nama_penerima' => 'nullable|string|max:255',
            'unit_penerima' => 'nullable|string|max:255',
            'mengetahui_1' => 'nullable|string|max:255',
            'mengetahui_2' => 'nullable|string|max:255',
            'menyetujui_1' => 'nullable|string|max:255',
            'menyetujui_2' => 'nullable|string|max:255',
            'tanggal_verifikasi' => 'nullable|date',
            'status_verifikasi' => 'nullable|string|max:255',
            'catatan' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.nama_item' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:1',
            'document_file' => 'nullable|file|mimes:jpeg,png,jpg,webp,pdf|max:10240',
        ]);

        // Handle file upload
        $filePath = null;
        if ($request->hasFile('document_file')) {
            $filePath = $request->file('document_file')->store('berita_acaras', 'public');
        }

        // Clean items array
        $cleanedItems = [];
        $idx = 1;
        foreach ($request->input('items', []) as $item) {
            if (!empty($item['nama_item'])) {
                $cleanedItems[] = [
                    'no' => $idx++,
                    'kode' => trim($item['kode'] ?? ''),
                    'nama_item' => trim($item['nama_item']),
                    'keterangan' => trim($item['keterangan'] ?? ''),
                    'quantity' => (int) ($item['quantity'] ?? 1),
                    'uom' => strtoupper(trim($item['uom'] ?? 'PCS')) ?: 'PCS',
                    'tgl' => trim($item['tgl'] ?? '') ?: ($validated['tanggal_verifikasi'] ?? $validated['tanggal_surat']),
                    'supplier' => trim($item['supplier'] ?? ''),
                    'no_suratjalan' => trim($item['no_suratjalan'] ?? $validated['nomor_surat']),
                    'no_po' => trim($item['no_po'] ?? ($validated['nomor_po'] ?? '')),
                ];
            }
        }

        $beritaAcara = BeritaAcara::create([
            'nomor_ba' => $validated['nomor_ba'] ?: BeritaAcara::generateNextNomorBa(),
            'lokasi' => $validated['lokasi'] ?? 'Cikande',
            'hal' => $validated['hal'] ?? 'Pemberitahuan Barang/Jasa OK',
            'lampiran' => $validated['lampiran'] ?? '-',
            'kepada_yth' => $validated['kepada_yth'] ?? 'Ka. Pembelian',
            'cc' => $validated['cc'] ?? 'Ka. Pabrik, Ka. Keuangan',
            'nama_perusahaan' => $validated['nama_perusahaan'] ?? 'PT. CBA Chemical Industry Pabrik',
            'nomor_surat' => $validated['nomor_surat'],
            'nomor_po' => $validated['nomor_po'] ?? null,
            'tanggal_surat' => $validated['tanggal_surat'],
            'kepada' => $validated['kepada'] ?? 'Abdul / Bagas (IT Pabrik)',
            'supir_kendaraan' => $validated['supir_kendaraan'] ?? null,
            'nama_penerima' => $validated['nama_penerima'] ?? 'Bagas Purnomo',
            'unit_penerima' => $validated['unit_penerima'] ?? 'IT Pabrik',
            'mengetahui_1' => $validated['mengetahui_1'] ?? 'Martin',
            'mengetahui_2' => $validated['mengetahui_2'] ?? 'Edi Awaludin',
            'menyetujui_1' => $validated['menyetujui_1'] ?? 'Herinata',
            'menyetujui_2' => $validated['menyetujui_2'] ?? 'David',
            'tanggal_verifikasi' => $validated['tanggal_verifikasi'] ?? $validated['tanggal_surat'],
            'status_verifikasi' => $validated['status_verifikasi'] ?? 'TTB / LGT ok',
            'catatan' => $validated['catatan'] ?? null,
            'items' => $cleanedItems,
            'file_lampiran' => $filePath,
            'created_by' => auth()->id(),
        ]);

        if ($request->boolean('and_print')) {
            return redirect()->route('berita-acara.print', ['beritaAcara' => $beritaAcara, 'autoprint' => 1]);
        }

        return redirect()->route('berita-acara.show', $beritaAcara)
            ->with('success', 'Berita Acara OK berhasil dibuat dan siap diunduh/dicetak!');
    }

    /**
     * Display the specified Berita Acara.
     */
    public function show(BeritaAcara $beritaAcara)
    {
        $beritaAcara->load('creator');
        return view('berita_acara.show', compact('beritaAcara'));
    }

    /**
     * Show the form for editing the specified Berita Acara.
     */
    public function edit(BeritaAcara $beritaAcara)
    {
        return view('berita_acara.edit', compact('beritaAcara'));
    }

    /**
     * Update the specified Berita Acara.
     */
    public function update(Request $request, BeritaAcara $beritaAcara)
    {
        $validated = $request->validate([
            'nomor_surat' => 'required|string|max:255',
            'nomor_po' => 'nullable|string|max:255',
            'tanggal_surat' => 'required|date',
            'nomor_ba' => 'nullable|string|max:255',
            'lokasi' => 'nullable|string|max:255',
            'hal' => 'nullable|string|max:255',
            'lampiran' => 'nullable|string|max:255',
            'kepada_yth' => 'nullable|string|max:255',
            'cc' => 'nullable|string|max:255',
            'nama_perusahaan' => 'nullable|string|max:255',
            'kepada' => 'nullable|string|max:255',
            'supir_kendaraan' => 'nullable|string|max:255',
            'nama_penerima' => 'nullable|string|max:255',
            'unit_penerima' => 'nullable|string|max:255',
            'mengetahui_1' => 'nullable|string|max:255',
            'mengetahui_2' => 'nullable|string|max:255',
            'menyetujui_1' => 'nullable|string|max:255',
            'menyetujui_2' => 'nullable|string|max:255',
            'tanggal_verifikasi' => 'nullable|date',
            'status_verifikasi' => 'nullable|string|max:255',
            'catatan' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.nama_item' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:1',
            'document_file' => 'nullable|file|mimes:jpeg,png,jpg,webp,pdf|max:10240',
        ]);

        if ($request->hasFile('document_file')) {
            if ($beritaAcara->file_lampiran && Storage::disk('public')->exists($beritaAcara->file_lampiran)) {
                Storage::disk('public')->delete($beritaAcara->file_lampiran);
            }
            $beritaAcara->file_lampiran = $request->file('document_file')->store('berita_acaras', 'public');
        }

        $cleanedItems = [];
        $idx = 1;
        foreach ($request->input('items', []) as $item) {
            if (!empty($item['nama_item'])) {
                $cleanedItems[] = [
                    'no' => $idx++,
                    'kode' => trim($item['kode'] ?? ''),
                    'nama_item' => trim($item['nama_item']),
                    'keterangan' => trim($item['keterangan'] ?? ''),
                    'quantity' => (int) ($item['quantity'] ?? 1),
                    'uom' => strtoupper(trim($item['uom'] ?? 'PCS')) ?: 'PCS',
                    'tgl' => trim($item['tgl'] ?? '') ?: ($validated['tanggal_verifikasi'] ?? $validated['tanggal_surat']),
                    'supplier' => trim($item['supplier'] ?? ''),
                    'no_suratjalan' => trim($item['no_suratjalan'] ?? $validated['nomor_surat']),
                    'no_po' => trim($item['no_po'] ?? ($validated['nomor_po'] ?? '')),
                ];
            }
        }

        $beritaAcara->update([
            'nomor_ba' => $validated['nomor_ba'] ?: $beritaAcara->nomor_ba,
            'lokasi' => $validated['lokasi'] ?? 'Cikande',
            'hal' => $validated['hal'] ?? 'Pemberitahuan Barang/Jasa OK',
            'lampiran' => $validated['lampiran'] ?? '-',
            'kepada_yth' => $validated['kepada_yth'] ?? 'Ka. Pembelian',
            'cc' => $validated['cc'] ?? 'Ka. Pabrik, Ka. Keuangan',
            'nama_perusahaan' => $validated['nama_perusahaan'] ?? 'PT. CBA Chemical Industry Pabrik',
            'nomor_surat' => $validated['nomor_surat'],
            'nomor_po' => $validated['nomor_po'] ?? null,
            'tanggal_surat' => $validated['tanggal_surat'],
            'kepada' => $validated['kepada'] ?? null,
            'supir_kendaraan' => $validated['supir_kendaraan'] ?? null,
            'nama_penerima' => $validated['nama_penerima'] ?? null,
            'unit_penerima' => $validated['unit_penerima'] ?? null,
            'mengetahui_1' => $validated['mengetahui_1'] ?? 'Martin',
            'mengetahui_2' => $validated['mengetahui_2'] ?? 'Edi Awaludin',
            'menyetujui_1' => $validated['menyetujui_1'] ?? 'Herinata',
            'menyetujui_2' => $validated['menyetujui_2'] ?? 'David',
            'tanggal_verifikasi' => $validated['tanggal_verifikasi'] ?? null,
            'status_verifikasi' => $validated['status_verifikasi'] ?? 'TTB / LGT ok',
            'catatan' => $validated['catatan'] ?? null,
            'items' => $cleanedItems,
        ]);

        if ($request->boolean('and_print')) {
            return redirect()->route('berita-acara.print', ['beritaAcara' => $beritaAcara, 'autoprint' => 1]);
        }

        return redirect()->route('berita-acara.show', $beritaAcara)
            ->with('success', 'Berita Acara OK berhasil diperbarui!');
    }

    /**
     * Remove the specified Berita Acara.
     */
    public function destroy(BeritaAcara $beritaAcara)
    {
        if ($beritaAcara->file_lampiran && Storage::disk('public')->exists($beritaAcara->file_lampiran)) {
            Storage::disk('public')->delete($beritaAcara->file_lampiran);
        }

        $beritaAcara->delete();

        return redirect()->route('berita-acara.index')
            ->with('success', 'Berita Acara OK berhasil dihapus!');
    }

    /**
     * Dedicated A4 Print / Save as PDF view.
     */
    public function printView(BeritaAcara $beritaAcara)
    {
        return view('berita_acara.print', compact('beritaAcara'));
    }
}
