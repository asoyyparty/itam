<?php

namespace App\Http\Middleware;

use App\Models\PerformanceMetric;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class PerformanceTelemetryMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);
        $queryCount = 0;
        $queryTotalTimeMs = 0.0;
        $slowestQuery = null;
        $slowestTimeMs = 0.0;
        $queryLogList = [];

        // Listen for queries executed during this request
        DB::listen(function ($query) use (&$queryCount, &$queryTotalTimeMs, &$slowestQuery, &$slowestTimeMs, &$queryLogList) {
            $queryCount++;
            $time = (float) $query->time;
            $queryTotalTimeMs += $time;

            if ($time > $slowestTimeMs) {
                $slowestTimeMs = $time;
                $slowestQuery = $query->sql;
            }

            // Keep up to 40 queries for the current request's mini HUD drawer
            if (count($queryLogList) < 40) {
                $queryLogList[] = [
                    'sql' => $query->sql,
                    'time' => round($time, 2),
                ];
            }
        });

        $response = $next($request);

        $durationMs = (microtime(true) - $startTime) * 1000;
        $memoryPeakMb = memory_get_peak_usage(true) / 1024 / 1024;

        $hudTelemetry = [
            'duration_ms' => round($durationMs, 1),
            'query_count' => $queryCount,
            'query_time_ms' => round($queryTotalTimeMs, 1),
            'memory_peak_mb' => round($memoryPeakMb, 1),
            'slowest_query' => $slowestTimeMs > 40 ? $slowestQuery : null,
            'queries' => $queryLogList,
            'route' => optional($request->route())->getName() ?? $request->path(),
        ];

        $request->attributes->set('performance_telemetry', $hudTelemetry);

        // Inject diagnostic headers for network inspection
        $response->headers->set('X-ITAM-Response-Time', round($durationMs, 1) . 'ms');
        $response->headers->set('X-ITAM-Query-Count', (string) $queryCount);
        $response->headers->set('X-ITAM-Memory-Peak', round($memoryPeakMb, 1) . 'MB');

        // If authorized admin and HTML response, inject telemetry payload for Mini HUD
        $user = auth()->user();
        if ($user && ($user->hasRole('Super Admin') || $user->can('menu_settings'))
            && $response instanceof \Symfony\Component\HttpFoundation\Response
            && str_contains($response->headers->get('Content-Type', ''), 'text/html')
            && is_string($response->getContent())) {
            $content = $response->getContent();
            $pos = strripos($content, '</body>');
            if ($pos !== false) {
                $script = "\n<script id=\"itam-telemetry-data\">window.__itamTelemetry = " . json_encode($hudTelemetry) . "; if (typeof window.__itamRenderHud === 'function') { window.__itamRenderHud(); }</script>\n";
                $content = substr_replace($content, $script . '</body>', $pos, 7);
                $response->setContent($content);
            }
        }

        return $response;
    }

    /**
     * Handle tasks after the response has been sent to the browser.
     */
    public function terminate(Request $request, Response $response): void
    {
        // Don't log if telemetry data missing
        $telemetry = $request->attributes->get('performance_telemetry');
        if (!$telemetry) {
            return;
        }

        // Don't log static assets, favicon, or internal telemetry polling requests
        $path = $request->path();
        if (
            $request->isMethod('OPTIONS') ||
            $request->isMethod('HEAD') ||
            $request->is('system-performance/api-stats') ||
            $request->is('system-performance/clear') ||
            $request->is('*.css') ||
            $request->is('*.js') ||
            $request->is('*.png') ||
            $request->is('*.jpg') ||
            $request->is('*.ico') ||
            $request->is('vendor/*')
        ) {
            return;
        }

        try {
            PerformanceMetric::create([
                'route_name' => optional($request->route())->getName() ?? substr($path, 0, 120),
                'method' => $request->method(),
                'url' => substr($request->fullUrl(), 0, 500),
                'status_code' => $response->getStatusCode(),
                'duration_ms' => $telemetry['duration_ms'] ?? 0,
                'db_query_count' => $telemetry['query_count'] ?? 0,
                'db_query_time_ms' => $telemetry['query_time_ms'] ?? 0,
                'memory_peak_mb' => $telemetry['memory_peak_mb'] ?? 0,
                'slowest_query' => $telemetry['slowest_query'] ?? null,
                'user_id' => auth()->id(),
                'ip_address' => $request->ip(),
                'user_agent' => substr($request->userAgent() ?? '', 0, 255),
                'created_at' => now(),
            ]);
        } catch (\Throwable $e) {
            // Silently suppress logging failures so request is never broken
        }
    }
}
