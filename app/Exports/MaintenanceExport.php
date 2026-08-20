<?php

namespace App\Exports;

use App\Models\Maintenance;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MaintenanceExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping
{
    protected ?Request $request;
    protected int $rowNum = 0;

    public function __construct(?Request $request = null)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = Maintenance::with('asset');

        if ($this->request) {
            if ($this->request->filled('search')) {
                $search = $this->request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('description', 'like', "%$search%")
                        ->orWhereHas('asset', function ($q2) use ($search) {
                            $q2->where('name', 'like', "%$search%")
                                ->orWhere('asset_tag', 'like', "%$search%");
                        });
                });
            }

            if ($this->request->filled('status')) {
                $query->where('status', $this->request->status);
            }

            if ($this->request->filled('asset_id')) {
                $query->where('asset_id', $this->request->asset_id);
            }
        }

        return $query->latest()->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Asset',
            'Asset Tag',
            'Deskripsi Perbaikan',
            'Tanggal Mulai',
            'Tanggal Selesai',
            'Biaya (Rp)',
            'Status',
            'Dibuat Pada',
        ];
    }

    public function map($maintenance): array
    {
        $this->rowNum++;

        return [
            $this->rowNum,
            $maintenance->asset ? $maintenance->asset->name : '-',
            $maintenance->asset ? $maintenance->asset->asset_tag : '-',
            $maintenance->description ?: '-',
            $maintenance->start_date ? \Carbon\Carbon::parse($maintenance->start_date)->format('Y-m-d') : '-',
            $maintenance->end_date ? \Carbon\Carbon::parse($maintenance->end_date)->format('Y-m-d') : '-',
            $maintenance->cost ? number_format($maintenance->cost, 0, ',', '.') : '0',
            $maintenance->status,
            $maintenance->created_at ? $maintenance->created_at->format('Y-m-d H:i:s') : '-',
        ];
    }
}
