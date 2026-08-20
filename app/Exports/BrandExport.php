<?php

namespace App\Exports;

use App\Models\Brand;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class BrandExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping
{
    protected ?Request $request;
    protected int $rowNum = 0;

    public function __construct(?Request $request = null)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = Brand::query();

        if ($this->request && $this->request->filled('search')) {
            $search = $this->request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('description', 'like', "%$search%");
            });
        }

        return $query->orderBy('name')->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Brand',
            'Deskripsi',
            'Dibuat Pada',
        ];
    }

    public function map($brand): array
    {
        $this->rowNum++;

        return [
            $this->rowNum,
            $brand->name,
            $brand->description ?: '-',
            $brand->created_at ? $brand->created_at->format('Y-m-d H:i:s') : '-',
        ];
    }
}
