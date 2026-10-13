<?php

namespace App\Http\Controllers;

use App\Models\BeritaAcaraDisposal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BeritaAcaraDisposalController extends Controller
{
    /**
     * Display a listing of Berita Acara Disposal Aset.
     */
    public function index(Request $request)
    {
        $query = BeritaAcaraDisposal::with('creator')->latest();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nomor_ba', 'like', "%{$search}%")
                  ->orWhere('hal', 'like', "%{$search}%")
                  ->orWhere('divisi', 'like', "%{$search}%")
                  ->orWhere('narasi_pembuka', 'like', "%{$search}%");
            });
        }

        if ($request->filled('start_date')) {
            $query->whereDate('tanggal_ba', '>=', $request->input('start_date'));
        }

        if ($request->filled('end_date')) {
            $query->whereDate('tanggal_ba', '<=', $request->input('end_date'));
        }

        $disposals = $query->paginate(15)->appends($request->all());

        return view('berita_acara_disposal.index', compact('disposals'));
    }

    /**
     * Show the form for creating a new Berita Acara Disposal.
     */
    public function create()
    {
        $nextNomorBa = BeritaAcaraDisposal::generateNextNomorBa();
        $defaultPreamble = BeritaAcaraDisposal::defaultPreamble();

        return view('berita_acara_disposal.create', compact('nextNomorBa', 'defaultPreamble'));
    }

    /**
     * Store a newly created Berita Acara Disposal.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nomor_ba' => 'required|string|max:255',
            'lokasi' => 'nullable|string|max:255',
            'tanggal_ba' => 'required|date',
            'hal' => 'nullable|string|max:255',
            'lampiran_label' => 'nullable|string|max:255',
            'kepada_yth' => 'nullable|string|max:255',
            'cc' => 'nullable|string|max:255',
            'form_code' => 'nullable|string|max:255',
            'form_rev' => 'nullable|string|max:255',
            'divisi' => 'nullable|string|max:255',
            'narasi_pembuka' => 'nullable|string',
            'nama_pembuat' => 'nullable|string|max:255',
            'jabatan_pembuat' => 'nullable|string|max:255',
            'nama_pemeriksa_1' => 'nullable|string|max:255',
            'jabatan_pemeriksa_1' => 'nullable|string|max:255',
            'nama_pemeriksa_2' => 'nullable|string|max:255',
            'jabatan_pemeriksa_2' => 'nullable|string|max:255',
            'nama_penyetuju_1' => 'nullable|string|max:255',
            'jabatan_penyetuju_1' => 'nullable|string|max:255',
            'nama_penyetuju_2' => 'nullable|string|max:255',
            'jabatan_penyetuju_2' => 'nullable|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.nama_asset' => 'required|string',
            'items.*.qty' => 'required|numeric|min:1',
            'foto_files.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
        ]);

        // Upload photos array
        $fotoPaths = [];
        if ($request->hasFile('foto_files')) {
            foreach ($request->file('foto_files') as $file) {
                if ($file->isValid()) {
                    $fotoPaths[] = $file->store('disposal_photos', 'public');
                }
            }
        }

        // Clean items array
        $cleanedItems = [];
        $idx = 1;
        foreach ($request->input('items', []) as $item) {
            if (!empty($item['nama_asset'])) {
                $cleanedItems[] = [
                    'no' => $idx++,
                    'kode_asset' => trim($item['kode_asset'] ?? ''),
                    'nama_asset' => trim($item['nama_asset']),
                    'qty' => (int) ($item['qty'] ?? ($item['quantity'] ?? 1)),
                    'uom' => strtoupper(trim($item['uom'] ?? 'Unit')) ?: 'Unit',
                    'keterangan' => trim($item['keterangan'] ?? ''),
                ];
            }
        }

        $disposal = BeritaAcaraDisposal::create([
            'nomor_ba' => $validated['nomor_ba'],
            'lokasi' => $validated['lokasi'] ?? 'Cikande',
            'tanggal_ba' => $validated['tanggal_ba'],
            'hal' => $validated['hal'] ?? 'Disposal Asset',
            'lampiran_label' => $validated['lampiran_label'] ?? 'Foto Assets',
            'kepada_yth' => $validated['kepada_yth'] ?? 'Ka. Bagian, Ka. Accounting, Ka. Asset',
            'cc' => $validated['cc'] ?? 'Ka. Pabrik',
            'form_code' => $validated['form_code'] ?? 'CBA/MTN/F-023',
            'form_rev' => $validated['form_rev'] ?? 'Rev. : 00',
            'divisi' => $validated['divisi'] ?? 'IT PABRIK',
            'narasi_pembuka' => $validated['narasi_pembuka'] ?? BeritaAcaraDisposal::defaultPreamble(),
            'nama_pembuat' => $validated['nama_pembuat'] ?? 'Qois Abdul Qudus',
            'jabatan_pembuat' => $validated['jabatan_pembuat'] ?? 'IT Pabrik',
            'nama_pemeriksa_1' => $validated['nama_pemeriksa_1'] ?? 'Alex Asmel',
            'jabatan_pemeriksa_1' => $validated['jabatan_pemeriksa_1'] ?? 'HC & GA Head',
            'nama_pemeriksa_2' => $validated['nama_pemeriksa_2'] ?? 'Basuki',
            'jabatan_pemeriksa_2' => $validated['jabatan_pemeriksa_2'] ?? 'Ka. Maintenance',
            'nama_penyetuju_1' => $validated['nama_penyetuju_1'] ?? 'Novan Prihasa',
            'jabatan_penyetuju_1' => $validated['jabatan_penyetuju_1'] ?? 'Factory Head',
            'nama_penyetuju_2' => $validated['nama_penyetuju_2'] ?? 'Agus Sapto',
            'jabatan_penyetuju_2' => $validated['jabatan_penyetuju_2'] ?? 'Wakil Management',
            'items' => $cleanedItems,
            'foto_lampiran' => $fotoPaths,
            'created_by' => auth()->id(),
        ]);

        if ($request->boolean('and_print')) {
            return redirect()->route('berita-acara-disposal.print', ['beritaAcaraDisposal' => $disposal, 'autoprint' => 1]);
        }

        return redirect()->route('berita-acara-disposal.show', $disposal)
            ->with('success', 'Berita Acara Disposal Aset berhasil dibuat dan siap diunduh/dicetak!');
    }

    /**
     * Display the specified Berita Acara Disposal.
     */
    public function show(BeritaAcaraDisposal $beritaAcaraDisposal)
    {
        $beritaAcaraDisposal->load('creator');
        return view('berita_acara_disposal.show', compact('beritaAcaraDisposal'));
    }

    /**
     * Show the form for editing the specified Berita Acara Disposal.
     */
    public function edit(BeritaAcaraDisposal $beritaAcaraDisposal)
    {
        return view('berita_acara_disposal.edit', compact('beritaAcaraDisposal'));
    }

    /**
     * Update the specified Berita Acara Disposal.
     */
    public function update(Request $request, BeritaAcaraDisposal $beritaAcaraDisposal)
    {
        $validated = $request->validate([
            'nomor_ba' => 'required|string|max:255',
            'lokasi' => 'nullable|string|max:255',
            'tanggal_ba' => 'required|date',
            'hal' => 'nullable|string|max:255',
            'lampiran_label' => 'nullable|string|max:255',
            'kepada_yth' => 'nullable|string|max:255',
            'cc' => 'nullable|string|max:255',
            'form_code' => 'nullable|string|max:255',
            'form_rev' => 'nullable|string|max:255',
            'divisi' => 'nullable|string|max:255',
            'narasi_pembuka' => 'nullable|string',
            'nama_pembuat' => 'nullable|string|max:255',
            'jabatan_pembuat' => 'nullable|string|max:255',
            'nama_pemeriksa_1' => 'nullable|string|max:255',
            'jabatan_pemeriksa_1' => 'nullable|string|max:255',
            'nama_pemeriksa_2' => 'nullable|string|max:255',
            'jabatan_pemeriksa_2' => 'nullable|string|max:255',
            'nama_penyetuju_1' => 'nullable|string|max:255',
            'jabatan_penyetuju_1' => 'nullable|string|max:255',
            'nama_penyetuju_2' => 'nullable|string|max:255',
            'jabatan_penyetuju_2' => 'nullable|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.nama_asset' => 'required|string',
            'items.*.qty' => 'required|numeric|min:1',
            'foto_files.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
        ]);

        $fotoPaths = $beritaAcaraDisposal->foto_lampiran ?: [];
        if ($request->hasFile('foto_files')) {
            foreach ($request->file('foto_files') as $file) {
                if ($file->isValid()) {
                    $fotoPaths[] = $file->store('disposal_photos', 'public');
                }
            }
        }

        // Clean items array
        $cleanedItems = [];
        $idx = 1;
        foreach ($request->input('items', []) as $item) {
            if (!empty($item['nama_asset'])) {
                $cleanedItems[] = [
                    'no' => $idx++,
                    'kode_asset' => trim($item['kode_asset'] ?? ''),
                    'nama_asset' => trim($item['nama_asset']),
                    'qty' => (int) ($item['qty'] ?? ($item['quantity'] ?? 1)),
                    'uom' => strtoupper(trim($item['uom'] ?? 'Unit')) ?: 'Unit',
                    'keterangan' => trim($item['keterangan'] ?? ''),
                ];
            }
        }

        $beritaAcaraDisposal->update([
            'nomor_ba' => $validated['nomor_ba'],
            'lokasi' => $validated['lokasi'] ?? 'Cikande',
            'tanggal_ba' => $validated['tanggal_ba'],
            'hal' => $validated['hal'] ?? 'Disposal Asset',
            'lampiran_label' => $validated['lampiran_label'] ?? 'Foto Assets',
            'kepada_yth' => $validated['kepada_yth'] ?? 'Ka. Bagian, Ka. Accounting, Ka. Asset',
            'cc' => $validated['cc'] ?? 'Ka. Pabrik',
            'form_code' => $validated['form_code'] ?? 'CBA/MTN/F-023',
            'form_rev' => $validated['form_rev'] ?? 'Rev. : 00',
            'divisi' => $validated['divisi'] ?? 'IT PABRIK',
            'narasi_pembuka' => $validated['narasi_pembuka'] ?? BeritaAcaraDisposal::defaultPreamble(),
            'nama_pembuat' => $validated['nama_pembuat'] ?? 'Qois Abdul Qudus',
            'jabatan_pembuat' => $validated['jabatan_pembuat'] ?? 'IT Pabrik',
            'nama_pemeriksa_1' => $validated['nama_pemeriksa_1'] ?? 'Alex Asmel',
            'jabatan_pemeriksa_1' => $validated['jabatan_pemeriksa_1'] ?? 'HC & GA Head',
            'nama_pemeriksa_2' => $validated['nama_pemeriksa_2'] ?? 'Basuki',
            'jabatan_pemeriksa_2' => $validated['jabatan_pemeriksa_2'] ?? 'Ka. Maintenance',
            'nama_penyetuju_1' => $validated['nama_penyetuju_1'] ?? 'Novan Prihasa',
            'jabatan_penyetuju_1' => $validated['jabatan_penyetuju_1'] ?? 'Factory Head',
            'nama_penyetuju_2' => $validated['nama_penyetuju_2'] ?? 'Agus Sapto',
            'jabatan_penyetuju_2' => $validated['jabatan_penyetuju_2'] ?? 'Wakil Management',
            'items' => $cleanedItems,
            'foto_lampiran' => $fotoPaths,
        ]);

        if ($request->boolean('and_print')) {
            return redirect()->route('berita-acara-disposal.print', ['beritaAcaraDisposal' => $beritaAcaraDisposal, 'autoprint' => 1]);
        }

        return redirect()->route('berita-acara-disposal.show', $beritaAcaraDisposal)
            ->with('success', 'Berita Acara Disposal Aset berhasil diperbarui!');
    }

    /**
     * Remove the specified Berita Acara Disposal.
     */
    public function destroy(BeritaAcaraDisposal $beritaAcaraDisposal)
    {
        if (is_array($beritaAcaraDisposal->foto_lampiran)) {
            foreach ($beritaAcaraDisposal->foto_lampiran as $foto) {
                if (Storage::disk('public')->exists($foto)) {
                    Storage::disk('public')->delete($foto);
                }
            }
        }

        $beritaAcaraDisposal->delete();

        return redirect()->route('berita-acara-disposal.index')
            ->with('success', 'Berita Acara Disposal Aset berhasil dihapus.');
    }

    /**
     * Print View for Berita Acara Disposal (A4 format with photo grid attachment pages).
     */
    public function printView(BeritaAcaraDisposal $beritaAcaraDisposal)
    {
        return view('berita_acara_disposal.print', compact('beritaAcaraDisposal'));
    }
}
