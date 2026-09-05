<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetAssignment;
use App\Models\Employee;
use App\Models\IpAddress;
use App\Models\Maintenance;
use App\Models\Ticket;
use App\Models\ActivityLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $dashboardData = Cache::remember('dashboard_metrics_cache', 300, function () {
            $total_assets = Asset::count();
            $total_employees = Employee::count();
            $active_assets = Asset::whereIn('status', ['Available', 'Assigned'])->count();
            $maintenance_assets = Asset::where('status', 'Maintenance')->count();

            $open_tickets = Ticket::whereIn('status', ['Open', 'In Progress'])->count();
            $used_ips = IpAddress::where('status', 'Used')->count();
            $assigned_assets = Asset::where('status', 'Assigned')->count();

            // Specific Category Counts for Dashboard Widgets (single SQL aggregation query)
            $categoryCounts = Asset::join('categories', 'assets.category_id', '=', 'categories.id')
                ->select('categories.name', DB::raw('count(*) as total'))
                ->groupBy('categories.name')
                ->pluck('total', 'name');

            $accessories_count = $categoryCounts->get('Accessories', 0);
            $printer_count = $categoryCounts->get('Printer', 0);
            $computer_count = $categoryCounts->get('Computer', 0);
            $network_count = $categoryCounts->get('Network', 0);
            $storage_count = $categoryCounts->get('Storage', 0);
            $other_it_asset_count = $categoryCounts->get('Other IT Asset', 0);

            $expiring_warranty_count = 0;

            // 1. Assets by Age (Direct SQL TIMESTAMPDIFF Aggregation - No Memory Overhead)
            $age_buckets = [
                '0_3' => Asset::whereRaw('TIMESTAMPDIFF(YEAR, COALESCE(date_received, created_at), NOW()) <= 3')->count(),
                '4_6' => Asset::whereRaw('TIMESTAMPDIFF(YEAR, COALESCE(date_received, created_at), NOW()) BETWEEN 4 AND 6')->count(),
                '7_10' => Asset::whereRaw('TIMESTAMPDIFF(YEAR, COALESCE(date_received, created_at), NOW()) BETWEEN 7 AND 10')->count(),
                'over_10' => Asset::whereRaw('TIMESTAMPDIFF(YEAR, COALESCE(date_received, created_at), NOW()) > 10')->count(),
            ];

            // 2. Assets by Location
            $locations_data = Asset::leftJoin('locations', 'assets.location_id', '=', 'locations.id')
                ->select(DB::raw('COALESCE(locations.name, "Unassigned") as name'), DB::raw('count(*) as count'))
                ->groupBy(DB::raw('COALESCE(locations.name, "Unassigned")'))
                ->get();

            // 3. IP Address Distribution
            $ip_data = IpAddress::select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->get();

            // 4. Ticket Status Distribution
            $ticket_data = Ticket::select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->get();

            return [
                'total_assets' => $total_assets,
                'total_employees' => $total_employees,
                'active_assets' => $active_assets,
                'maintenance_assets' => $maintenance_assets,
                'open_tickets' => $open_tickets,
                'used_ips' => $used_ips,
                'assigned_assets' => $assigned_assets,
                'accessories_count' => $accessories_count,
                'printer_count' => $printer_count,
                'computer_count' => $computer_count,
                'network_count' => $network_count,
                'storage_count' => $storage_count,
                'other_it_asset_count' => $other_it_asset_count,
                'expiring_warranty_count' => $expiring_warranty_count,
                'age_buckets' => $age_buckets,
                'locations_data' => $locations_data,
                'ip_data' => $ip_data,
                'ticket_data' => $ticket_data,
            ];
        });

        $user = auth()->user();
        $isSuperAdmin = $user && ($user->hasRole('Super Admin') || $user->hasRole('superadmin') || $user->id === 1);
        $recent_activities = $isSuperAdmin ? $this->getRecentActivities() : collect();

        return view('dashboard', array_merge($dashboardData, [
            'recent_activities' => $recent_activities,
        ]));
    }

    public function activities()
    {
        $user = auth()->user();
        $isSuperAdmin = $user && ($user->hasRole('Super Admin') || $user->hasRole('superadmin') || $user->id === 1);

        if (!$isSuperAdmin) {
            return response()->json([]);
        }

        $recent_activities = $this->getRecentActivities();

        $formatted = $recent_activities->map(function ($act) {
            $isToday = $act->date->isToday();
            $isYesterday = $act->date->isYesterday();
            if ($isToday) {
                $timeStr = __('messages.today').', '.$act->date->format('H:i');
            } elseif ($isYesterday) {
                $timeStr = __('messages.yesterday').', '.$act->date->format('H:i');
            } else {
                $timeStr = $act->date->format('d M, H:i');
            }

            return [
                'timestamp' => $timeStr,
                'operator' => $act->operator,
                'status_event' => $act->status_event,
                'badge' => $act->badge,
                'asset' => $act->asset,
                'details_text' => $act->details_text ?? '',
            ];
        });

        return response()->json($formatted->values());
    }

    private function getRecentActivities()
    {
        $logs = ActivityLog::latest()->take(15)->get();

        return $logs->map(function ($log) {
            $badge = 'info';
            if ($log->action === 'created' || $log->action === 'assigned') {
                $badge = 'success';
            } elseif ($log->action === 'deleted') {
                $badge = 'danger';
            } elseif ($log->action === 'maintenance') {
                $badge = 'warning';
            } elseif ($log->action === 'login') {
                $badge = 'primary';
            } elseif ($log->action === 'logout') {
                $badge = 'secondary';
            } elseif ($log->action === 'returned') {
                $badge = 'indigo';
            }

            $statusEvent = ucfirst($log->action);
            switch ($log->action) {
                case 'created':
                    $statusEvent = __('messages.created') ?? 'Created';
                    break;
                case 'updated':
                    $statusEvent = __('messages.updated') ?? 'Updated';
                    break;
                case 'deleted':
                    $statusEvent = __('messages.deleted') ?? 'Deleted';
                    break;
                case 'assigned':
                    $statusEvent = __('messages.assigned') ?? 'Handed Over';
                    break;
                case 'returned':
                    $statusEvent = __('messages.returned') ?? 'Returned';
                    break;
                case 'maintenance':
                    $statusEvent = __('messages.maintenance') ?? 'Maintenance';
                    break;
                case 'login':
                    $statusEvent = 'Login';
                    break;
                case 'logout':
                    $statusEvent = 'Logout';
                    break;
            }

            $modelBasename = class_basename($log->model_type ?? '');
            $targetText = $log->target_name;

            if ($log->action === 'assigned' && isset($log->details['employee_name'])) {
                $targetText = $log->target_name . ' -> ' . $log->details['employee_name'];
            } elseif ($log->action === 'returned' && isset($log->details['employee_name'])) {
                $targetText = $log->target_name . ' <- ' . $log->details['employee_name'];
            } elseif ($log->action === 'login' || $log->action === 'logout') {
                $targetText = 'Auth Session: ' . $log->operator;
            } elseif (!empty($modelBasename)) {
                $modelLabel = $modelBasename;
                if ($modelBasename === 'SoftwareLicense') {
                    $modelLabel = 'License';
                } elseif ($modelBasename === 'PasswordVault') {
                    $modelLabel = 'Credential';
                }
                $targetText = "{$modelLabel}: {$log->target_name}";
            }

            $detailsText = '';
            if (is_array($log->details)) {
                if (!empty($log->details['diffs']) && is_array($log->details['diffs'])) {
                    $parts = [];
                    foreach ($log->details['diffs'] as $diff) {
                        $parts[] = "{$diff['field']}: {$diff['old']} ➔ {$diff['new']}";
                    }
                    $detailsText = implode(' | ', $parts);
                } elseif (!empty($log->details['summary']) && is_array($log->details['summary'])) {
                    $parts = [];
                    foreach ($log->details['summary'] as $k => $v) {
                        $parts[] = "{$k}: {$v}";
                    }
                    $detailsText = implode(' | ', $parts);
                } elseif (isset($log->details['IP'])) {
                    $detailsText = 'IP Address: ' . $log->details['IP'];
                }
            }

            return (object) [
                'date' => $log->created_at,
                'operator' => $log->operator,
                'status_event' => $statusEvent,
                'badge' => $badge,
                'asset' => $targetText,
                'details_text' => $detailsText,
            ];
        });
    }
}
