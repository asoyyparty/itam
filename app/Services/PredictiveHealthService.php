<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\Maintenance;
use App\Models\Ticket;
use Carbon\Carbon;

class PredictiveHealthService
{
    /**
     * Calculate health score (0 - 100%), risk level, remaining life, and AI recommendations for an asset.
     */
    public function calculateAssetHealth(Asset $asset): array
    {
        $asset->loadMissing(['category', 'brand', 'location', 'computer', 'printer', 'networkDetail', 'cctv', 'maintenances', 'currentAssignment.employee']);

        $score = 100;
        $reasons = [];
        $isEn = app()->getLocale() === 'en';

        // 1. Age & Warranty Factor (Max 30% Deduction)
        $dateReceived = $asset->date_received ? Carbon::parse($asset->date_received) : null;
        $warrantyMonths = (int) ($asset->warranty_months ?? 0);
        $assetAgeMonths = $dateReceived ? $dateReceived->diffInMonths(Carbon::now()) : 0;
        $assetAgeYears = round($assetAgeMonths / 12, 1);

        if ($dateReceived) {
            if ($assetAgeMonths > 60) { // Older than 5 years
                $score -= 25;
                $reasons[] = $isEn 
                    ? "Device age exceeds 5 years ({$assetAgeYears} years)."
                    : "Usia perangkat melebihi 5 tahun ({$assetAgeYears} tahun).";
            } elseif ($assetAgeMonths > 36) { // Older than 3 years
                $score -= 15;
                $reasons[] = $isEn
                    ? "Device age reaches {$assetAgeYears} years."
                    : "Usia perangkat mencapai {$assetAgeYears} tahun.";
            }

            // Warranty Expiry
            if ($warrantyMonths > 0) {
                $expiryDate = (clone $dateReceived)->addMonths($warrantyMonths);
                if ($expiryDate->isPast()) {
                    $monthsExpired = $expiryDate->diffInMonths(Carbon::now());
                    $score -= 10;
                    $reasons[] = $isEn
                        ? "Manufacturer warranty expired {$monthsExpired} months ago."
                        : "Garansi resmi pabrikan telah habis sejak {$monthsExpired} bulan lalu.";
                }
            }
        }

        // 2. Ticket Frequency Factor (Max 30% Deduction)
        $ticketCount = Ticket::where('asset_id', $asset->id)
            ->where('created_at', '>=', Carbon::now()->subMonths(12))
            ->count();

        if ($ticketCount >= 5) {
            $score -= 30;
            $reasons[] = $isEn
                ? "High vulnerability: Registered {$ticketCount} issue tickets in the past 12 months."
                : "Tingkat kerentanan tinggi: Terdaftar {$ticketCount} tiket kendala dalam 12 bulan terakhir.";
        } elseif ($ticketCount >= 3) {
            $score -= 20;
            $reasons[] = $isEn
                ? "Recorded {$ticketCount} technical issue reports in the past 1 year."
                : "Tercatat {$ticketCount} kali laporan kendala teknis dalam 1 tahun terakhir.";
        } elseif ($ticketCount >= 1) {
            $score -= 10;
            $reasons[] = $isEn
                ? "Experienced operational issues ({$ticketCount} tickets)."
                : "Pernah mengalami kendala operasional ({$ticketCount} tiket).";
        }

        // 3. Maintenance Cost & History Factor (Max 25% Deduction)
        $maintenanceCount = $asset->maintenances->count();
        $totalCost = $asset->maintenances->sum('cost');

        if ($totalCost >= 2000000) {
            $score -= 20;
            $reasons[] = $isEn
                ? 'Accumulated repair cost is too high (Rp ' . number_format($totalCost, 0, ',', '.') . ').'
                : 'Akumulasi biaya perbaikan terlampau tinggi (Rp ' . number_format($totalCost, 0, ',', '.') . ').';
        } elseif ($maintenanceCount >= 3) {
            $score -= 15;
            $reasons[] = $isEn
                ? "Undergone {$maintenanceCount} maintenance / service processes."
                : "Telah menjalani {$maintenanceCount} kali proses maintenance / servis.";
        }

        // 4. Hardware Component Risk Factor (Max 15% Deduction)
        if ($asset->computer) {
            $os = mb_strtolower($asset->computer->os ?? '');
            if (str_contains($os, 'windows 7') || str_contains($os, 'windows 8') || str_contains($os, 'xp')) {
                $score -= 15;
                $reasons[] = $isEn
                    ? "Operating System ({$asset->computer->os}) is end-of-life and no longer receives security updates."
                    : "Sistem Operasi ({$asset->computer->os}) sudah terdepresiasi dan tidak mendapat pembaruan keamanan.";
            }
            if ($asset->computer->hdd > 0 && ($asset->computer->ssd ?? 0) == 0) {
                $score -= 10;
                $reasons[] = $isEn
                    ? 'Still relying on mechanical Hard Drive (HDD) without SSD, high risk of slow performance & disk failure.'
                    : 'Masih menggunakan Harddisk mekanis (HDD) tanpa SSD, berisiko slow performance & disk failure.';
            }
        }

        // Final score capping (0 - 100)
        $healthScore = max(5, min(100, $score));

        // Risk Level Classification
        if ($healthScore >= 80) {
            $status = 'Healthy';
            $badgeClass = 'badge-success';
            $textColor = '#10b981';
            $remainingLife = __('messages.rul_3_years');
        } elseif ($healthScore >= 50) {
            $status = 'Warning';
            $badgeClass = 'badge-warning';
            $textColor = '#f59e0b';
            $remainingLife = __('messages.rul_6_12_months');
        } else {
            $status = 'Critical';
            $badgeClass = 'badge-danger';
            $textColor = '#ef4444';
            $remainingLife = __('messages.rul_critical');
        }

        // Generate Category-Aware & Risk-Specific AI Recommendation
        $recommendation = $this->generateCategoryRecommendation($asset, $healthScore, $reasons);

        return [
            'asset_id' => $asset->id,
            'health_score' => $healthScore,
            'status' => $status,
            'badge_class' => $badgeClass,
            'text_color' => $textColor,
            'age_years' => $assetAgeYears,
            'ticket_count' => $ticketCount,
            'maintenance_cost' => $totalCost,
            'remaining_life' => $remainingLife,
            'reasons' => $reasons,
            'recommendation' => $recommendation,
        ];
    }

    /**
     * Generate Smart Category-Aware AI Recommendations
     */
    private function generateCategoryRecommendation(Asset $asset, int $healthScore, array $reasons): string
    {
        $isEn = app()->getLocale() === 'en';
        $catName = mb_strtolower($asset->category->name ?? '');

        // 1. Computer / Laptop / Server / Mini PC / Thin Client
        if (in_array($catName, ['komputer', 'computer', 'laptop', 'mini pc', 'thin client', 'server'])) {
            if ($healthScore >= 80) {
                return $isEn
                    ? 'Computer asset is in prime condition. Perform routine internal dust cleaning and OS maintenance.'
                    : 'Perangkat komputer dalam kondisi sangat baik. Lakukan pembersihan debu internal dan inspeksi sistem secara berkala.';
            } elseif ($healthScore >= 50) {
                if ($asset->computer && $asset->computer->hdd > 0 && ($asset->computer->ssd ?? 0) == 0) {
                    return $isEn
                        ? 'Storage upgrade to SSD NVMe/SATA and RAM expansion recommended to prevent HDD failure and boost performance.'
                        : 'Disarankan upgrade storage ke SSD NVMe/SATA dan tambah RAM untuk meningkatkan respon kerja serta mencegah failure HDD.';
                }
                return $isEn
                    ? 'PC health declining. Recommended CPU thermal paste re-application, blower fan cleaning, and memory tuning.'
                    : 'Kondisi PC menurun. Disarankan penggantian pasta thermal CPU, pembersihan blower fan, dan tuning memori.';
            } else {
                return $isEn
                    ? 'Unit Replacement Recommended! Motherboard and critical hardware components carry a high failure risk.'
                    : 'Rekomendasi Replacement / Peremajaan Unit! Motherboard dan komponen vital sudah berisiko tinggi breakdown.';
            }
        }

        // 2. Monitor / Display / Screen / TV
        if (in_array($catName, ['monitor', 'display', 'screen', 'tv'])) {
            if ($healthScore >= 80) {
                return $isEn
                    ? 'Monitor is operating optimally. Maintain power voltage stability and panel cleanliness.'
                    : 'Monitor beroperasi optimal. Jaga kestabilan voltase listrik dan kebersihan panel layar.';
            } elseif ($healthScore >= 50) {
                return $isEn
                    ? 'Display panel or backlight showing degradation. Inspect display cables (HDMI/VGA) and power adapter.'
                    : 'Panel monitor atau backlight menunjukkan penurunan kualitas. Periksa kabel display (HDMI/VGA) dan power adapter.';
            } else {
                return $isEn
                    ? 'Monitor replacement recommended! High risk of panel flickering, dead pixels, or power board failure.'
                    : 'Rekomendasi penggantian unit monitor! Panel berisiko flickering, dead pixels, atau kegagalan modul power board.';
            }
        }

        // 3. Printer / Scanner / Fax / Multifunction
        if (in_array($catName, ['printer', 'scanner', 'fax', 'multifunction'])) {
            if ($healthScore >= 80) {
                return $isEn
                    ? 'Printer is in normal condition. Perform periodic roller cleaning and print head calibration.'
                    : 'Printer dalam kondisi normal. Lakukan pembersihan roller dan kalibrasi head print secara berkala.';
            } elseif ($healthScore >= 50) {
                return $isEn
                    ? 'Printer performance declining. Recommended toner/ink cartridge replacement, paper pickup roller cleaning, and drum servicing.'
                    : 'Kinerja printer menurun. Disarankan penggantian cartridge toner/tinta original, pembersihan paper pickup roller, dan servis drum unit.';
            } else {
                return $isEn
                    ? 'Printer replacement recommended! Mechanical gears and fuser unit worn out with high risk of chronic paper jams.'
                    : 'Rekomendasi penggantian unit printer! Gear mekanis dan fuser unit sudah aus berisiko papertrap kronis.';
            }
        }

        // 4. Switch / Router / Access Point / Firewall / Network / Access Switch
        if (in_array($catName, ['switch', 'router', 'access point', 'firewall', 'jaringan', 'network'])) {
            if ($healthScore >= 80) {
                return $isEn
                    ? 'Network device is stable. Ensure periodic firmware updates and rack server airflow clearance.'
                    : 'Perangkat jaringan stabil. Pastikan pembaruan firmware berkala dan sirkulasi udara rak server terjaga.';
            } elseif ($healthScore >= 50) {
                return $isEn
                    ? 'Network device experiencing latency spikes / high load. Recommended firmware upgrade, RJ45 port cleaning, and throughput testing.'
                    : 'Perangkat jaringan mengalami lonjakan latensi / beban tinggi. Disarankan update firmware, pembersihan port RJ45, dan pengujian throughput.';
            } else {
                return $isEn
                    ? 'Switch/router replacement recommended! Ethernet ports and power supply module at high risk of sudden outage.'
                    : 'Rekomendasi peremajaan unit switch/router! Port ethernet dan modul power supply berisiko mati mendadak yang memicu downtime.';
            }
        }

        // 5. UPS / Power / Battery / Inverter
        if (in_array($catName, ['ups', 'power', 'battery', 'inverter'])) {
            if ($healthScore >= 80) {
                return $isEn
                    ? 'UPS and battery in prime condition. Conduct periodic power outage simulation tests.'
                    : 'UPS dan aki dalam kondisi prima. Lakukan tes simulasi pemadaman listrik secara berkala.';
            } elseif ($healthScore >= 50) {
                return $isEn
                    ? 'UPS battery capacity declining. Recommended cell calibration and VRLA dry battery replacement.'
                    : 'Kapasitas aki UPS mulai menurun. Disarankan kalibrasi sel baterai dan replacement baterai kering VRLA.';
            } else {
                return $isEn
                    ? 'UPS battery / unit replacement recommended! High risk of battery swelling, leakage, or backup failure.'
                    : 'Rekomendasi ganti baterai / unit UPS! Aki berisiko bocor, kembung, atau gagal backup saat mati listrik.';
            }
        }

        // 6. CCTV / Camera / NVR
        if (in_array($catName, ['cctv', 'camera', 'kamera', 'nvr'])) {
            if ($healthScore >= 80) {
                return $isEn
                    ? 'CCTV camera and NVR recording operating normally. Perform lens cleaning and NVR HDD health test.'
                    : 'Kamera CCTV dan rekaman NVR berfungsi normal. Lakukan pembersihan lensa dan tes integritas HDD NVR.';
            } elseif ($healthScore >= 50) {
                return $isEn
                    ? 'Recording quality or PoE connection degraded. Inspect UTP/PoE cabling, RJ45 connectors, and bracket mounting.'
                    : 'Kualitas rekaman atau koneksi PoE menurun. Disarankan inspeksi kabel UTP/PoE, konektor RJ45, dan kencangkan braket.';
            } else {
                return $isEn
                    ? 'Camera/NVR replacement recommended! Infrared sensor and mainboard at risk of permanent video loss.'
                    : 'Rekomendasi ganti unit kamera/NVR! Sensor inframerah dan modul board berisiko hilang sinyal video secara permanen.';
            }
        }

        // Generic Fallback
        if ($healthScore >= 80) {
            return $isEn
                ? 'Asset condition is prime. Perform routine periodic inspection and maintenance.'
                : 'Kondisi aset sangat prima. Lakukan pemeliharaan dan inspeksi rutin berkala.';
        } elseif ($healthScore >= 50) {
            return $isEn
                ? 'Asset condition declining. Recommended physical cleaning, electrical check, and periodic servicing.'
                : 'Kondisi aset mulai menurun. Disarankan pembersihan fisik, pemeriksaan kelistrikan, dan servis berkala.';
        } else {
            return $isEn
                ? 'Unit Replacement Recommended! High operational failure risk detected for this asset.'
                : 'Rekomendasi Replacement / Peremajaan! Aset berisiko tinggi mengalami kegagalan fungsi operasional.';
        }
    }

    /**
     * Get Overall Predictive Health Dashboard Summary
     */
    public function getDashboardSummary(): array
    {
        $assets = Asset::with(['category', 'brand', 'location', 'computer', 'printer', 'networkDetail', 'cctv', 'maintenances', 'currentAssignment.employee'])
            ->get();

        $healthyCount = 0;
        $warningCount = 0;
        $criticalCount = 0;

        $analyzedAssets = [];
        $totalReplacementEstimate = 0;

        foreach ($assets as $asset) {
            $health = $this->calculateAssetHealth($asset);
            $asset->health_data = $health;

            if ($health['status'] === 'Healthy') {
                $healthyCount++;
            } elseif ($health['status'] === 'Warning') {
                $warningCount++;
            } else {
                $criticalCount++;
                $totalReplacementEstimate += 8500000; // Estimated 8.5M IDR per critical asset replacement
            }

            $analyzedAssets[] = $asset;
        }

        // Sort assets by health score ascending (most critical first)
        usort($analyzedAssets, fn($a, $b) => $a->health_data['health_score'] <=> $b->health_data['health_score']);

        return [
            'total_assets' => count($assets),
            'healthy_count' => $healthyCount,
            'warning_count' => $warningCount,
            'critical_count' => $criticalCount,
            'estimated_replacement_budget' => $totalReplacementEstimate,
            'assets' => $analyzedAssets,
        ];
    }
}
