<?php

namespace App\Http\Controllers;

use App\Models\PerformanceMetric;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SystemPerformanceController extends Controller
{
    /**
     * Display the System Performance & Telemetry dashboard.
     */
    public function index(Request $request): View
    {
        $this->authorizeAccess();

        $range = $request->get('range', '24h');
        if (!in_array($range, ['1h', '24h', '7d', '30d'])) {
            $range = '24h';
        }

        $metrics = $this->gatherTelemetry($range);
        $serverEnv = $this->gatherServerEnvironment();

        return view('system_performance.index', array_merge($metrics, [
            'serverEnv' => $serverEnv,
            'selectedRange' => $range,
        ]));
    }

    /**
     * JSON Endpoint for live auto-refresh and range switching without page reload.
     */
    public function apiStats(Request $request): JsonResponse
    {
        $this->authorizeAccess();

        $range = $request->get('range', '24h');
        if (!in_array($range, ['1h', '24h', '7d', '30d'])) {
            $range = '24h';
        }

        $metrics = $this->gatherTelemetry($range);
        $serverEnv = $this->gatherServerEnvironment();

        return response()->json(array_merge($metrics, [
            'serverEnv' => $serverEnv,
            'selectedRange' => $range,
            'timestamp' => now()->toIso8601String(),
        ]));
    }

    /**
     * Purge all telemetry logs.
     */
    public function clearLogs(Request $request): RedirectResponse|JsonResponse
    {
        $this->authorizeAccess();

        try {
            PerformanceMetric::truncate();
            foreach (['1h', '24h', '7d', '30d'] as $r) {
                \Illuminate\Support\Facades\Cache::forget('telemetry_chart_trend_' . $r);
            }
            $message = App()->getLocale() == 'en'
                ? 'Telemetry and performance logs cleared successfully.'
                : 'Log telemetri dan riwayat performa berhasil dibersihkan.';

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => $message]);
            }

            return redirect()->route('system-performance.index')->with('success', $message);
        } catch (\Throwable $e) {
            $errorMsg = App()->getLocale() == 'en' ? 'Failed to clear logs: ' . $e->getMessage() : 'Gagal membersihkan log: ' . $e->getMessage();
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $errorMsg], 500);
            }
            return redirect()->route('system-performance.index')->with('error', $errorMsg);
        }
    }

    /**
     * Guard: Ensure user is Super Admin or has management clearance.
     */
    protected function authorizeAccess(): void
    {
        $user = auth()->user();
        if (!$user) {
            abort(401);
        }

        $isSuperAdmin = $user->hasRole('Super Admin');
        $canManage = $user->can('menu_settings') || $user->can('action_manage_settings') || $user->hasRole('Admin');

        if (!$isSuperAdmin && !$canManage) {
            abort(403, App()->getLocale() == 'en' ? 'Unauthorized access to System Telemetry.' : 'Akses tidak sah ke Telemetri Sistem.');
        }
    }

    /**
     * Calculate telemetry metrics across the given time range.
     */
    protected function gatherTelemetry(string $range): array
    {
        $stats = PerformanceMetric::timeRange($range)
            ->selectRaw('
                COUNT(*) as total_requests,
                AVG(duration_ms) as avg_duration,
                AVG(db_query_time_ms) as avg_query_time,
                AVG(db_query_count) as avg_query_count,
                MAX(memory_peak_mb) as peak_memory,
                SUM(CASE WHEN duration_ms >= 300 THEN 1 ELSE 0 END) as slow_requests_count,
                SUM(CASE WHEN slowest_query IS NOT NULL THEN 1 ELSE 0 END) as slow_queries_count,
                SUM(CASE WHEN status_code BETWEEN 200 AND 299 THEN 1 ELSE 0 END) as status_2xx,
                SUM(CASE WHEN status_code BETWEEN 300 AND 399 THEN 1 ELSE 0 END) as status_3xx,
                SUM(CASE WHEN status_code BETWEEN 400 AND 499 THEN 1 ELSE 0 END) as status_4xx,
                SUM(CASE WHEN status_code BETWEEN 500 AND 599 THEN 1 ELSE 0 END) as status_5xx
            ')
            ->first();

        $totalRequests = (int) ($stats->total_requests ?? 0);
        $avgDuration = round((float) ($stats->avg_duration ?? 0), 1);
        $avgQueryTime = round((float) ($stats->avg_query_time ?? 0), 1);
        $avgQueryCount = round((float) ($stats->avg_query_count ?? 0), 1);
        $peakMemory = round((float) ($stats->peak_memory ?? 0), 1);

        $slowRequestsCount = (int) ($stats->slow_requests_count ?? 0);
        $slowQueriesCount = (int) ($stats->slow_queries_count ?? 0);

        $status2xx = (int) ($stats->status_2xx ?? 0);
        $status3xx = (int) ($stats->status_3xx ?? 0);
        $status4xx = (int) ($stats->status_4xx ?? 0);
        $status5xx = (int) ($stats->status_5xx ?? 0);

        $reliabilityScore = $totalRequests > 0
            ? round((($status2xx + $status3xx) / $totalRequests) * 100, 1)
            : 100.0;

        // Top slowest endpoints (Single query)
        $slowestRoutes = PerformanceMetric::timeRange($range)
            ->selectRaw('route_name, method, count(*) as count, avg(duration_ms) as avg_duration, max(duration_ms) as max_duration')
            ->groupBy('route_name', 'method')
            ->orderByDesc('avg_duration')
            ->limit(6)
            ->get()
            ->map(function ($row) {
                return [
                    'route_name' => $row->route_name ?: '(Unnamed URL)',
                    'method' => $row->method,
                    'count' => (int) $row->count,
                    'avg_duration' => round((float) $row->avg_duration, 1),
                    'max_duration' => round((float) $row->max_duration, 1),
                ];
            });

        // Slow queries table (Single query)
        $slowQueries = PerformanceMetric::timeRange($range)
            ->whereNotNull('slowest_query')
            ->orderByDesc('id')
            ->limit(10)
            ->get(['id', 'route_name', 'duration_ms', 'db_query_time_ms', 'slowest_query', 'created_at'])
            ->map(function ($sq) {
                return [
                    'id' => $sq->id,
                    'route_name' => $sq->route_name ?: '-',
                    'duration_ms' => $sq->duration_ms,
                    'query_time_ms' => $sq->db_query_time_ms,
                    'sql' => $sq->slowest_query,
                    'time_ago' => $sq->created_at ? $sq->created_at->locale(app()->getLocale())->diffForHumans() : '-',
                ];
            });

        // Timeline Trend Points for Chart.js (Cached with combined queries)
        $trend = $this->calculateTrend($range);

        return [
            'totalRequests' => $totalRequests,
            'avgDuration' => $avgDuration,
            'avgQueryTime' => $avgQueryTime,
            'avgQueryCount' => $avgQueryCount,
            'peakMemory' => $peakMemory,
            'slowRequestsCount' => $slowRequestsCount,
            'slowQueriesCount' => $slowQueriesCount,
            'status2xx' => $status2xx,
            'status3xx' => $status3xx,
            'status4xx' => $status4xx,
            'status5xx' => $status5xx,
            'reliabilityScore' => $reliabilityScore,
            'slowestRoutes' => $slowestRoutes,
            'slowQueries' => $slowQueries,
            'chartLabels' => $trend['labels'],
            'chartLatency' => $trend['latency'],
            'chartThroughput' => $trend['throughput'],
        ];
    }

    /**
     * Compute trend points grouped by appropriate intervals with 60s cache.
     */
    protected function calculateTrend(string $range): array
    {
        return \Illuminate\Support\Facades\Cache::remember('telemetry_chart_trend_' . $range, 60, function () use ($range) {
            $labels = [];
            $latency = [];
            $throughput = [];

            if ($range === '1h') {
                // 6 intervals of 10 minutes
                for ($i = 5; $i >= 0; $i--) {
                    $start = now()->subMinutes(($i + 1) * 10);
                    $end = now()->subMinutes($i * 10);
                    $labels[] = $end->format('H:i');

                    $row = PerformanceMetric::whereBetween('created_at', [$start, $end])
                        ->selectRaw('COUNT(*) as cnt, AVG(duration_ms) as avg_dur')
                        ->first();
                    $throughput[] = (int) ($row->cnt ?? 0);
                    $latency[] = round((float) ($row->avg_dur ?? 0), 1);
                }
            } elseif ($range === '7d' || $range === '30d') {
                // Days
                $days = ($range === '7d') ? 7 : 30;
                for ($i = $days - 1; $i >= 0; $i--) {
                    $day = now()->subDays($i);
                    $labels[] = $day->format('d M');

                    $row = PerformanceMetric::whereDate('created_at', $day->toDateString())
                        ->selectRaw('COUNT(*) as cnt, AVG(duration_ms) as avg_dur')
                        ->first();
                    $throughput[] = (int) ($row->cnt ?? 0);
                    $latency[] = round((float) ($row->avg_dur ?? 0), 1);
                }
            } else {
                // Default 24h: 12 intervals of 2 hours
                for ($i = 11; $i >= 0; $i--) {
                    $start = now()->subHours(($i + 1) * 2);
                    $end = now()->subHours($i * 2);
                    $labels[] = $end->format('H:i');

                    $row = PerformanceMetric::whereBetween('created_at', [$start, $end])
                        ->selectRaw('COUNT(*) as cnt, AVG(duration_ms) as avg_dur')
                        ->first();
                    $throughput[] = (int) ($row->cnt ?? 0);
                    $latency[] = round((float) ($row->avg_dur ?? 0), 1);
                }
            }

            return [
                'labels' => $labels,
                'latency' => $latency,
                'throughput' => $throughput,
            ];
        });
    }

    /**
     * Inspect server resources and environment.
     */
    protected function gatherServerEnvironment(): array
    {
        $diskPath = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' ? 'C:' : '/';
        $diskFree = @disk_free_space($diskPath);
        $diskTotal = @disk_total_space($diskPath);

        $diskUsedPercent = ($diskTotal && $diskTotal > 0) ? round((($diskTotal - $diskFree) / $diskTotal) * 100, 1) : 0;
        $diskFreeGb = $diskFree ? round($diskFree / 1024 / 1024 / 1024, 1) : 0;
        $diskTotalGb = $diskTotal ? round($diskTotal / 1024 / 1024 / 1024, 1) : 0;

        $dbVersion = 'Unknown';
        try {
            $dbRes = DB::select('SELECT VERSION() as v');
            $dbVersion = $dbRes[0]->v ?? 'Unknown';
        } catch (\Throwable $e) {}

        return [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'memory_limit' => ini_get('memory_limit') ?: '512M',
            'opcache_enabled' => function_exists('opcache_get_status') && !empty(opcache_get_status()['opcache_enabled']),
            'db_driver' => config('database.default'),
            'db_version' => $dbVersion,
            'cache_driver' => config('cache.default'),
            'session_driver' => config('session.driver'),
            'disk_free_gb' => $diskFreeGb,
            'disk_total_gb' => $diskTotalGb,
            'disk_used_percent' => $diskUsedPercent,
            'server_os' => PHP_OS,
            'current_time' => now()->format('Y-m-d H:i:s'),
        ];
    }
}
