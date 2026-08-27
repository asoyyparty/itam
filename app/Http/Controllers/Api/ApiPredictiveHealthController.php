<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Services\PredictiveHealthService;
use Illuminate\Http\Request;

class ApiPredictiveHealthController extends Controller
{
    protected PredictiveHealthService $healthService;

    public function __construct(PredictiveHealthService $healthService)
    {
        $this->healthService = $healthService;
    }

    /**
     * Get list of assets with health scores and summary.
     */
    public function index(Request $request)
    {
        $summary = $this->healthService->getDashboardSummary();
        $statusFilter = $request->get('status', 'all');

        $filteredAssets = collect($summary['assets']);
        if (in_array($statusFilter, ['Critical', 'Warning', 'Healthy'])) {
            $filteredAssets = $filteredAssets->filter(fn($a) => $a->health_data['status'] === $statusFilter);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'summary' => [
                    'total_assets' => $summary['total_assets'],
                    'critical_count' => $summary['critical_count'],
                    'warning_count' => $summary['warning_count'],
                    'healthy_count' => $summary['healthy_count'],
                    'estimated_replacement_budget' => $summary['estimated_replacement_budget'],
                ],
                'assets' => $filteredAssets->values(),
            ],
        ]);
    }

    /**
     * Run AI Health Audit for a specific asset.
     */
    public function analyze(Asset $asset)
    {
        $health = $this->healthService->calculateAssetHealth($asset);

        return response()->json([
            'success' => true,
            'data' => [
                'asset' => [
                    'id' => $asset->id,
                    'name' => $asset->name,
                    'asset_tag' => $asset->asset_tag,
                    'category' => $asset->category->name ?? __('messages.asset'),
                    'brand' => $asset->brand->name ?? '-',
                    'location' => $asset->location->name ?? '-',
                    'user' => ($asset->currentAssignment && $asset->currentAssignment->employee) ? $asset->currentAssignment->employee->name : __('messages.not_assigned'),
                ],
                'health' => $health,
            ],
        ]);
    }
}
