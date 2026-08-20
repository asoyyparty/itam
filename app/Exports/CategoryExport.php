<?php

namespace App\Exports;

use App\Models\Category;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CategoryExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping
{
    protected ?Request $request;
    protected int $rowNum = 0;

    public function __construct(?Request $request = null)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = Category::query();

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
            'Nama Kategori',
            'Deskripsi',
            'Dibuat Pada',
        ];
    }

    public function map($category): array
    {
        $this->rowNum++;

        return [
            $this->rowNum,
            $category->name,
            $category->description ?: '-',
            $category->created_at ? $category->created_at->format('Y-m-d H:i:s') : '-',
        ];
    }
}
