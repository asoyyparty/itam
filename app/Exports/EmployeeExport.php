<?php

namespace App\Exports;

use App\Models\Employee;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class EmployeeExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping
{
    protected ?Request $request;
    protected int $rowNum = 0;

    public function __construct(?Request $request = null)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = Employee::with(['department', 'location', 'supervisor']);

        if ($this->request) {
            if ($this->request->filled('search')) {
                $search = $this->request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%$search%")
                        ->orWhere('employee_id', 'like', "%$search%")
                        ->orWhere('email', 'like', "%$search%")
                        ->orWhere('anydesk_id', 'like', "%$search%")
                        ->orWhere('login_username', 'like', "%$search%");
                });
            }

            if ($this->request->filled('department_id')) {
                $query->where('department_id', $this->request->department_id);
            }

            if ($this->request->filled('supervisor_id')) {
                $query->where('supervisor_id', $this->request->supervisor_id);
            }

            if ($this->request->filled('status')) {
                $query->where('status', $this->request->status);
            }
        }

        return $query->orderBy('name')->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'NIK',
            'Nama Karyawan',
            'Email',
            'Telepon',
            'Departemen',
            'Lokasi',
            'AnyDesk ID',
            'AnyDesk Password',
            'PC Username',
            'PC Password',
            'Supervisor',
            'Status',
        ];
    }

    public function map($employee): array
    {
        $this->rowNum++;

        return [
            $this->rowNum,
            $employee->employee_id ?: '-',
            $employee->name,
            $employee->email ?: '-',
            $employee->phone ?: '-',
            $employee->department ? $employee->department->name : '-',
            $employee->location ? $employee->location->name : '-',
            $employee->anydesk_id ?: '-',
            $employee->anydesk_password ?: '-',
            $employee->login_username ?: '-',
            $employee->login_password ?: '-',
            $employee->supervisor ? $employee->supervisor->name : '-',
            $employee->status,
        ];
    }
}
