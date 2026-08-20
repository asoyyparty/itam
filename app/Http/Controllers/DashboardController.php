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

class DashboardController extends Controller
{
    public function index()
    {
        $total_assets = Asset::count();
        $total_employees = Employee::count();
        $active_assets = Asset::whereIn('status', ['Available', 'Assigned'])->count();
        $maintenance_assets = Asset::where('status', 'Maintenance')->count();

        $open_tickets = Ticket::whereIn('status', ['Open', 'In Progress'])->count();
        $used_ips = IpAddress::where('status', 'Used')->count();
        $assigned_assets = Asset::where('status', 'Assigned')->count();

        // Specific Category Counts for Dashboard Widgets (optimized single query)
        $categoryCounts = Asset::join('categories', 'assets.category_id', '=', 'categories.id')
            ->select('categories.name', \DB::raw('count(*) as total'))
            ->groupBy('categories.name')
            ->pluck('total', 'name');

        $accessories_count = $categoryCounts->get('Accessories', 0);
        $printer_count = $categoryCounts->get('Printer', 0);
        $computer_count = $categoryCounts->get('Computer', 0);
        $network_count = $categoryCounts->get('Network', 0);
        $storage_count = $categoryCounts->get('Storage', 0);
        $other_it_asset_count = $categoryCounts->get('Other IT Asset', 0);

        // Expiring Warranties (Disabled)
        $expiring_warranty_count = 0;

        // Charts data
        // 1. Assets by Age (bucketed based on date_received/created_at using lightweight select)
        $now = Carbon::now();
        $age_buckets = [
            '0_3' => 0,
            '4_6' => 0,
            '7_10' => 0,
            'over_10' => 0,
        ];
        Asset::select('date_received', 'created_at')->get()->each(function ($asset) use ($now, &$age_buckets) {
            $date = $asset->date_received ? Carbon::parse($asset->date_received) : $asset->created_at;
            $years = $date->diffInYears($now);
            if ($years <= 3) {
                $age_buckets['0_3']++;
            } elseif ($years <= 6) {
                $age_buckets['4_6']++;
            } elseif ($years <= 10) {
                $age_buckets['7_10']++;
            } else {
                $age_buckets['over_10']++;
            }
        });

        // 2. Assets by Location
        $locations_data = Asset::leftJoin('locations', 'assets.location_id', '=', 'locations.id')
            ->select(\DB::raw('COALESCE(locations.name, "Unassigned") as name'), \DB::raw('count(*) as count'))
            ->groupBy(\DB::raw('COALESCE(locations.name, "Unassigned")'))
            ->get();

        // 3. IP Address Distribution
        $ip_data = IpAddress::select('status', \DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        // 4. Ticket Status Distribution
        $ticket_data = Ticket::select('status', \DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        $recent_activities = $this->getRecentActivities();

        return view('dashboard', compact(
            'total_assets',
            'total_employees',
            'active_assets',
            'maintenance_assets',
            'open_tickets',
            'used_ips',
            'assigned_assets',
            'recent_activities',
            'accessories_count',
            'printer_count',
            'computer_count',
            'network_count',
            'storage_count',
            'other_it_asset_count',
            'expiring_warranty_count',
            'age_buckets',
            'locations_data',
            'ip_data',
            'ticket_data'
        ));
    }

    public function activities()
    {
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
        $logs = ActivityLog::latest()->take(10)->get();

        return $logs->map(function ($log) {
            $badge = 'info';
            if ($log->action === 'created' || $log->action === 'assigned') {
                $badge = 'success';
            } elseif ($log->action === 'deleted') {
                $badge = 'danger';
            } elseif ($log->action === 'maintenance') {
                $badge = 'warning';
            }

            $statusEvent = $log->action;
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
            }

            $modelBasename = class_basename($log->model_type);
            $targetText = $log->target_name;

            if ($log->action === 'assigned' && isset($log->details['employee_name'])) {
                $targetText = $log->target_name . ' -> ' . $log->details['employee_name'];
            } elseif ($log->action === 'returned' && isset($log->details['employee_name'])) {
                $targetText = $log->target_name . ' <- ' . $log->details['employee_name'];
            } else {
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
