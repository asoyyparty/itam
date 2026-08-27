<?php

namespace App\Exports;

use App\Models\IpAddress;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class IpAddressExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping
{
    protected ?Request $request;
    protected int $rowNum = 0;

    public function __construct(?Request $request = null)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = IpAddress::with(['asset', 'employee', 'vlan']);

        if ($this->request) {
            if ($this->request->filled('search')) {
                $search = $this->request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('ip_address', 'like', "%$search%")
                        ->orWhere('notes', 'like', "%$search%")
                        ->orWhereHas('asset', function ($q2) use ($search) {
                            $q2->where('name', 'like', "%$search%")
                                ->orWhere('asset_tag', 'like', "%$search%");
                        })
                        ->orWhereHas('employee', function ($q2) use ($search) {
                            $q2->where('name', 'like', "%$search%")
                                ->orWhere('employee_id', 'like', "%$search%");
                        });
                });
            }

            if ($this->request->filled('status')) {
                $query->where('status', $this->request->status);
            }

            if ($this->request->filled('vlan_id')) {
                $query->where('vlan_id', $this->request->vlan_id);
            }

            if ($this->request->filled('ping_status')) {
                if ($this->request->ping_status === 'online') {
                    $query->where('is_online', true);
                } elseif ($this->request->ping_status === 'offline') {
                    $query->where('is_online', false);
                } elseif ($this->request->ping_status === 'unchecked') {
                    $query->whereNull('is_online');
                }
            }
        }

        return $query->orderByRaw('INET_ATON(ip_address)')->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'IP Address',
            'MAC Address',
            'Assigned Asset',
            'User / Employee',
            'VLAN',
            'Gateway',
            'DNS',
            'Status',
            'Ping Status',
            'Notes',
        ];
    }

    public function map($ip): array
    {
        $this->rowNum++;

        return [
            $this->rowNum,
            $ip->ip_address,
            $ip->mac_address ?: '-',
            $ip->asset ? $ip->asset->name . ' (' . $ip->asset->asset_tag . ')' : '-',
            $ip->employee ? $ip->employee->name . ' (' . ($ip->employee->employee_id ?: '-') . ')' : '-',
            $ip->vlan ? 'VLAN ' . $ip->vlan->vlan_number . ' - ' . $ip->vlan->name : '-',
            $ip->gateway ?: '-',
            $ip->dns ?: '-',
            $ip->status,
            $ip->is_online === true ? 'Online' : ($ip->is_online === false ? 'Offline' : 'Unchecked'),
            $ip->notes ?: '-',
        ];
    }
}
