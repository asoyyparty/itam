<?php

namespace App\Http\Controllers;

use App\Models\IpAddress;
use App\Services\NetworkAnomalyService;
use Illuminate\Http\Request;

class NetworkAnomalyController extends Controller
{
    protected NetworkAnomalyService $anomalyService;

    public function __construct(NetworkAnomalyService $anomalyService)
    {
        $this->anomalyService = $anomalyService;
    }

    public function index(Request $request)
    {
        $summary = $this->anomalyService->getNetworkSummary();
        $severityFilter = $request->get('severity', 'all');

        $filteredAnomalies = collect($summary['anomalies']);
        if (in_array($severityFilter, ['Critical', 'Warning'])) {
            $filteredAnomalies = $filteredAnomalies->filter(fn($a) => $a['severity'] === $severityFilter);
        }

        return view('network_anomaly.index', [
            'summary' => $summary,
            'anomalies' => $filteredAnomalies,
            'severityFilter' => $severityFilter,
        ]);
    }

    public function resolve(Request $request)
    {
        $ipId = $request->input('ip_id');
        $ip = IpAddress::find($ipId);

        if ($ip) {
            $ip->update([
                'status' => 'Used',
                'notes' => 'Telah diselesaikan melalui Dashboard Anomali Jaringan.',
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Anomali jaringan telah berhasil ditandai selesai/ter-remediasi!',
        ]);
    }

    public function registerRogue(Request $request)
    {
        $ipId = $request->input('ip_id');
        $ip = IpAddress::find($ipId);

        if ($ip) {
            return response()->json([
                'success' => true,
                'redirect_url' => route('assets.create', [
                    'notes' => "Perangkat terdeteksi di IP {$ip->ip_address} (MAC: {$ip->mac_address})",
                ]),
            ]);
        }

        return response()->json(['success' => false, 'message' => 'IP Address tidak ditemukan.'], 404);
    }
}
