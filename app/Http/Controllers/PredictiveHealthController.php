<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Services\PredictiveHealthService;
use Illuminate\Http\Request;

class PredictiveHealthController extends Controller
{
    protected PredictiveHealthService $healthService;

    public function __construct(PredictiveHealthService $healthService)
    {
        $this->healthService = $healthService;
    }

    public function index(Request $request)
    {
        $summary = $this->healthService->getDashboardSummary();
        $statusFilter = $request->get('status', 'all');

        $filteredAssets = collect($summary['assets']);
        if (in_array($statusFilter, ['Critical', 'Warning', 'Healthy'])) {
            $filteredAssets = $filteredAssets->filter(fn($a) => $a->health_data['status'] === $statusFilter);
        }

        return view('predictive_health.index', [
            'summary' => $summary,
            'assets' => $filteredAssets,
            'statusFilter' => $statusFilter,
        ]);
    }

    public function analyzeAsset(Asset $asset)
    {
        $health = $this->healthService->calculateAssetHealth($asset);

        return response()->json([
            'success' => true,
            'asset' => [
                'id' => $asset->id,
                'name' => $asset->name,
                'asset_tag' => $asset->asset_tag,
                'category' => $asset->category->name ?? 'Aset',
                'brand' => $asset->brand->name ?? '-',
                'location' => $asset->location->name ?? '-',
                'user' => $asset->currentAssignment->employee->name ?? 'Belum diserahkan',
            ],
            'health' => $health,
        ]);
    }
}
