<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\Employee;
use App\Models\IpAddress;
use App\Models\Ticket;
use App\Services\PredictiveHealthService;
use Illuminate\Http\Request;

class ApiDashboardController extends Controller
{
    protected PredictiveHealthService $healthService;

    public function __construct(PredictiveHealthService $healthService)
    {
        $this->healthService = $healthService;
    }

    /**
     * Get summary metrics payload for Flutter mobile app dashboard.
     */
    public function index(Request $request)
    {
        $totalAssets = Asset::count();
        $assignedAssets = Asset::where('status', 'Assigned')->count();
        $availableAssets = Asset::where('status', 'Available')->count();
        $maintenanceAssets = Asset::where('status', 'Maintenance')->count();

        $totalUsers = Employee::count();
        $openTickets = Ticket::whereIn('status', ['Open', 'In Progress'])->count();
        $resolvedTickets = Ticket::whereIn('status', ['Resolved', 'Closed'])->count();
        $usedIps = IpAddress::where('status', 'Used')->count();

        // Get Predictive Health Overview
        $healthSummary = $this->healthService->getDashboardSummary();

        return response()->json([
            'success' => true,
            'data' => [
                'metrics' => [
                    'total_assets' => $totalAssets,
                    'assigned_assets' => $assignedAssets,
                    'available_assets' => $availableAssets,
                    'maintenance_assets' => $maintenanceAssets,
                    'total_users' => $totalUsers,
                    'open_tickets' => $openTickets,
                    'resolved_tickets' => $resolvedTickets,
                    'used_ips' => $usedIps,
                ],
                'health_summary' => [
                    'total_analyzed' => $healthSummary['total_assets'],
                    'critical_count' => $healthSummary['critical_count'],
                    'warning_count' => $healthSummary['warning_count'],
                    'healthy_count' => $healthSummary['healthy_count'],
                    'estimated_replacement_budget' => $healthSummary['estimated_replacement_budget'],
                ],
            ],
        ]);
    }
}
