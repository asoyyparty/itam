<?php

namespace App\Exports;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TicketExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping
{
    protected ?Request $request;
    protected int $rowNum = 0;

    public function __construct(?Request $request = null)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = Ticket::with(['asset', 'employee', 'pic']);

        if ($this->request) {
            if ($this->request->filled('search')) {
                $search = $this->request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('ticket_number', 'like', "%$search%")
                        ->orWhere('title', 'like', "%$search%")
                        ->orWhere('description', 'like', "%$search%");
                });
            }

            if ($this->request->filled('status')) {
                $query->where('status', $this->request->status);
            }

            if ($this->request->filled('priority')) {
                $query->where('priority', $this->request->priority);
            }

            if ($this->request->filled('category')) {
                $query->where('category', $this->request->category);
            }

            if ($this->request->filled('pic_id')) {
                $query->where('pic_id', $this->request->pic_id);
            }
        }

        return $query->latest()->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'No. Tiket',
            'Judul',
            'Kategori',
            'Prioritas',
            'Status',
            'Asset Terkait',
            'Pelapor / Karyawan',
            'PIC IT',
            'Deskripsi',
            'Tanggal Dibuat',
            'Tanggal Selesai',
        ];
    }

    public function map($ticket): array
    {
        $this->rowNum++;

        return [
            $this->rowNum,
            $ticket->ticket_number ?? 'TCK-' . str_pad($ticket->id, 5, '0', STR_PAD_LEFT),
            $ticket->title,
            $ticket->category ?: '-',
            $ticket->priority,
            $ticket->status,
            $ticket->asset ? $ticket->asset->name . ' (' . $ticket->asset->asset_tag . ')' : '-',
            $ticket->employee ? $ticket->employee->name : '-',
            $ticket->pic ? $ticket->pic->name : '-',
            $ticket->description ?: '-',
            $ticket->created_at ? $ticket->created_at->format('Y-m-d H:i:s') : '-',
            $ticket->completed_at ? \Carbon\Carbon::parse($ticket->completed_at)->format('Y-m-d H:i:s') : '-',
        ];
    }
}
