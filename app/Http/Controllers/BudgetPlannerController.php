<?php

namespace App\Http\Controllers;

use App\Services\BudgetPlannerService;
use Illuminate\Http\Request;

class BudgetPlannerController extends Controller
{
    protected BudgetPlannerService $budgetService;

    public function __construct(BudgetPlannerService $budgetService)
    {
        $this->budgetService = $budgetService;
    }

    public function index(Request $request)
    {
        $selectedYear = (int) $request->get('year', 2026);
        $fiveYearProjections = $this->budgetService->getFiveYearProjections(2026);
        $selectedProjection = $fiveYearProjections[$selectedYear] ?? $fiveYearProjections[2026];
        $deptBreakdown = $this->budgetService->getDepartmentBreakdown($selectedYear);

        return view('budget_planner.index', [
            'selectedYear' => $selectedYear,
            'projections' => $fiveYearProjections,
            'current' => $selectedProjection,
            'deptBreakdown' => $deptBreakdown,
        ]);
    }

    public function exportExcel(Request $request)
    {
        $year = (int) $request->get('year', 2026);
        $data = $this->budgetService->calculateYearlyProjection($year);
        $deptData = $this->budgetService->getDepartmentBreakdown($year);

        $filename = "IT_Budget_Plan_{$year}.csv";

        $headers = [
            "Content-type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename={$filename}",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0",
        ];

        $callback = function () use ($year, $data, $deptData) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF");

            fputcsv($file, ["PROYEKSI ANGGARAN & SIKLUS HIDUP IT - TAHUN {$year}"]);
            fputcsv($file, []);

            fputcsv($file, ['RINGKASAN ANGGARAN']);
            fputcsv($file, ['Total Biaya Peremajaan Unit (Replacement)', 'Rp ' . number_format($data['replacement_cost'], 0, ',', '.')]);
            fputcsv($file, ['Total Biaya Maintenance & Servis', 'Rp ' . number_format($data['maintenance_cost'], 0, ',', '.')]);
            fputcsv($file, ['Total Biaya Perpanjangan Lisensi Software', 'Rp ' . number_format($data['license_cost'], 0, ',', '.')]);
            fputcsv($file, ['TOTAL PROYEKSI ANGGARAN IT', 'Rp ' . number_format($data['grand_total'], 0, ',', '.')]);
            fputcsv($file, []);

            fputcsv($file, ['RINCIAN ALOKASI ANGGARAN PER DEPARTEMEN']);
            fputcsv($file, ['Nama Departemen', 'Jumlah Karyawan', 'Jumlah Aset', 'Alokasi Dana (Rp)', 'Persentase (%)']);
            foreach ($deptData as $dept) {
                fputcsv($file, [
                    $dept['department_name'],
                    $dept['employee_count'],
                    $dept['asset_count'],
                    'Rp ' . number_format($dept['allocated_budget'], 0, ',', '.'),
                    $dept['percentage'] . '%',
                ]);
            }
            fputcsv($file, []);

            fputcsv($file, ['DAFTAR ASET MASUK JADWAL REPLACEMENT TAHUN ' . $year]);
            fputcsv($file, ['Tag Aset', 'Nama Aset', 'Kategori', 'Departemen', 'Estimasi Biaya Replacement (Rp)']);
            foreach ($data['replacement_assets'] as $asset) {
                fputcsv($file, [
                    $asset['asset_tag'],
                    $asset['asset_name'],
                    $asset['category'],
                    $asset['department'],
                    'Rp ' . number_format($asset['estimated_cost'], 0, ',', '.'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
