<?php

namespace App\Exports;

use App\Models\Asset;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AssetsExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping
{
    protected ?Request $request;
    protected int $rowNum = 0;

    public function __construct(?Request $request = null)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = Asset::with(['category', 'brand', 'location', 'currentAssignment.employee']);

        if ($this->request) {
            if ($this->request->filled('search')) {
                $search = $this->request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%$search%")
                        ->orWhere('asset_tag', 'like', "%$search%")
                        ->orWhere('serial_number', 'like', "%$search%");
                });
            }

            $yearFilter = $this->request->input('date_received') ?: $this->request->input('year');
            if ($yearFilter) {
                $query->where(function ($q) use ($yearFilter) {
                    $q->whereYear('date_received', $yearFilter)
                      ->orWhere(function ($q2) use ($yearFilter) {
                          $q2->whereNull('date_received')->whereYear('created_at', $yearFilter);
                      });
                });
            }

            if ($this->request->filled('category_id')) {
                $query->where('category_id', $this->request->category_id);
            }

            if ($this->request->filled('brand_id')) {
                $query->where('brand_id', $this->request->brand_id);
            }

            if ($this->request->filled('location_id')) {
                $query->where('location_id', $this->request->location_id);
            }

            if ($this->request->filled('status')) {
                $query->where('status', $this->request->status);
            }
        }

        return $query->latest()->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Asset Tag',
            'Nama Asset',
            'Serial Number',
            'Kategori',
            'Brand',
            'Lokasi',
            'Pengguna / Karyawan',
            'Tanggal Terima',
            'No. Delivery Order',
            'Garansi (Bulan)',
            'Status',
            'Catatan',
            'Dibuat Pada',
        ];
    }

    public function map($asset): array
    {
        $this->rowNum++;

        $assignedUser = '-';
        if ($asset->currentAssignment && $asset->currentAssignment->employee) {
            $assignedUser = $asset->currentAssignment->employee->name . ' (' . ($asset->currentAssignment->employee->employee_id ?: '-') . ')';
        }

        return [
            $this->rowNum,
            $asset->asset_tag,
            $asset->name,
            $asset->serial_number ?: '-',
            $asset->category ? $asset->category->name : '-',
            $asset->brand ? $asset->brand->name : '-',
            $asset->location ? $asset->location->name : '-',
            $assignedUser,
            $asset->date_received ? \Carbon\Carbon::parse($asset->date_received)->format('Y-m-d') : '-',
            $asset->delivery_order_number ?: '-',
            $asset->warranty_months ?: '-',
            $asset->status,
            $asset->notes ?: '-',
            $asset->created_at ? $asset->created_at->format('Y-m-d H:i:s') : '-',
        ];
    }
}
