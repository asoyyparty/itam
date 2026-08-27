<?php

namespace App\Helpers;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class ActivityLogger
{
    /**
     * Log a model event.
     */
    public static function log($model, string $action)
    {
        try {
            $user = Auth::user();
            
            // Log actions by all users (including Super Admin)
            $operator = $user ? $user->name : 'System';

            $modelName = class_basename($model);

            // Do not log automatic background system activities for IpAddress
            if ($operator === 'System' && $modelName === 'IpAddress') {
                return;
            }

            $targetName = self::getTargetName($model, $modelName);
            $details = self::getDetails($model, $modelName, $action);

            // Avoid logging minor internal updates that don't represent real data changes
            if ($action === 'updated') {
                if (method_exists($model, 'wasChanged')) {
                    if (!$model->wasChanged()) {
                        return;
                    }

                    // Ignore background telemetry updates (e.g. ping monitoring)
                    if ($modelName === 'IpAddress') {
                        $changes = array_keys($model->getChanges());
                        $meaningfulChanges = array_diff($changes, ['last_ping_at', 'is_online', 'updated_at']);
                        if (empty($meaningfulChanges)) {
                            return;
                        }
                    }
                }
            }

            $log = ActivityLog::create([
                'user_id' => $user ? $user->id : null,
                'operator' => $operator,
                'action' => $action,
                'model_type' => get_class($model),
                'model_id' => $model->id,
                'target_name' => $targetName,
                'details' => $details,
            ]);

            try {
                $botService = app(\App\Services\TelegramBotService::class);
                $botService->sendActivityLogNotification($log);
            } catch (\Exception $e) {
                logger()->error('Telegram Activity logging failed: ' . $e->getMessage());
            }
        } catch (\Exception $e) {
            logger()->error('Activity logging failed: ' . $e->getMessage());
        }
    }

    /**
     * Log a custom activity (Login, Logout, Import, Export, Settings, etc.)
     */
    public static function logCustom(string $action, string $targetName, ?array $details = null, ?string $modelType = null, ?int $modelId = null)
    {
        try {
            $user = Auth::user();
            $operator = $user ? $user->name : 'System';

            $log = ActivityLog::create([
                'user_id' => $user ? $user->id : null,
                'operator' => $operator,
                'action' => $action,
                'model_type' => $modelType,
                'model_id' => $modelId,
                'target_name' => $targetName,
                'details' => $details,
            ]);

            try {
                $botService = app(\App\Services\TelegramBotService::class);
                $botService->sendActivityLogNotification($log);
            } catch (\Exception $e) {
                logger()->error('Telegram Activity logging failed: ' . $e->getMessage());
            }
        } catch (\Exception $e) {
            logger()->error('Custom activity logging failed: ' . $e->getMessage());
        }
    }

    /**
     * Resolve a descriptive target name for the model.
     */
    private static function getTargetName($model, string $modelName): string
    {
        switch ($modelName) {
            case 'Asset':
                return ($model->name ?? 'Unknown Asset') . ' (' . ($model->asset_tag ?? 'N/A') . ')';

            case 'AssetAssignment':
                $asset = $model->asset;
                return $asset ? "{$asset->name} ({$asset->asset_tag})" : 'Unknown Asset';

            case 'Maintenance':
                $asset = $model->asset;
                return $asset ? "{$asset->name} ({$asset->asset_tag})" : 'Unknown Asset';

            case 'Ticket':
                return '#' . $model->id . ' - ' . ($model->title ?? 'Untitled Ticket');

            case 'IpAddress':
                return $model->ip_address . ($model->status ? " ({$model->status})" : '');

            case 'SoftwareLicense':
                return ($model->name ?? 'Unknown License') . ' (' . ($model->license_key ?? 'N/A') . ')';

            case 'PasswordVault':
                return ($model->name ?? 'Vault') . ' (' . ($model->username ?? 'N/A') . ')';

            case 'Employee':
                return ($model->name ?? 'Unknown Employee') . ($model->nik ? " [{$model->nik}]" : '');

            case 'User':
                return $model->name ?? $model->username ?? 'Unknown User';

            case 'Vendor':
            case 'Location':
            case 'Brand':
            case 'Category':
            case 'Department':
            case 'Vlan':
            case 'Pic':
                return $model->name ?? ($modelName . ' #' . $model->id);

            default:
                if (isset($model->name)) {
                    return $model->name;
                }
                if (isset($model->title)) {
                    return $model->title;
                }
                return $modelName . ' #' . $model->id;
        }
    }

    /**
     * Get specific metadata details & diffs for models.
     */
    private static function getDetails($model, string $modelName, string $action): ?array
    {
        $details = [];

        if ($modelName === 'AssetAssignment') {
            $employee = $model->employee;
            $details['employee_name'] = $employee ? $employee->name : 'Unknown Employee';
            if (isset($model->assigned_date)) {
                $details['assigned_date'] = (string)$model->assigned_date;
            }
            if (isset($model->notes) && !empty($model->notes)) {
                $details['notes'] = $model->notes;
            }
        }

        if ($modelName === 'Maintenance') {
            if (isset($model->type)) $details['type'] = $model->type;
            if (isset($model->cost)) $details['cost'] = 'Rp ' . number_format($model->cost, 0, ',', '.');
            if (isset($model->provider)) $details['provider'] = $model->provider;
            if (isset($model->completion_notes)) $details['notes'] = $model->completion_notes;
        }

        if ($action === 'updated' && method_exists($model, 'getChanges') && method_exists($model, 'getOriginal')) {
            $changes = $model->getChanges();
            $ignored = ['updated_at', 'created_at', 'remember_token', 'last_ping_at', 'is_online', 'password'];
            $diffs = [];

            foreach ($changes as $field => $newValue) {
                if (in_array($field, $ignored)) {
                    continue;
                }
                $oldValue = $model->getOriginal($field);

                $fieldLabel = ucwords(str_replace(['_id', '_'], [' ', ' '], $field));
                if ($field === 'status') {
                    $fieldLabel = 'Status';
                }

                $oldStr = self::formatFieldValue($field, $oldValue);
                $newStr = self::formatFieldValue($field, $newValue);

                $diffs[] = [
                    'field' => trim($fieldLabel),
                    'old' => $oldStr !== '' ? $oldStr : '-',
                    'new' => $newStr !== '' ? $newStr : '-',
                ];
            }
            if (!empty($diffs)) {
                $details['diffs'] = $diffs;
            }
        } elseif ($action === 'created') {
            $summary = [];
            if (isset($model->status)) $summary['Status'] = $model->status;
            if (isset($model->priority)) $summary['Priority'] = $model->priority;
            if (isset($model->category_id)) {
                $categoryName = \App\Models\Category::find($model->category_id)?->name;
                if ($categoryName) $summary['Category'] = $categoryName;
            }
            if (isset($model->department_id)) {
                $deptName = \App\Models\Department::find($model->department_id)?->name;
                if ($deptName) $summary['Department'] = $deptName;
            }
            if (isset($model->serial_number) && !empty($model->serial_number)) {
                $summary['Serial'] = $model->serial_number;
            }
            if (!empty($summary)) {
                $details['summary'] = $summary;
            }
        } elseif ($action === 'deleted') {
            $summary = [];
            if (isset($model->name)) $summary['Name'] = $model->name;
            if (isset($model->asset_tag)) $summary['Tag'] = $model->asset_tag;
            if (isset($model->email)) $summary['Email'] = $model->email;
            if (isset($model->ip_address)) $summary['IP'] = $model->ip_address;
            if (!empty($summary)) {
                $details['summary'] = $summary;
            }
        }

        return !empty($details) ? $details : null;
    }

    /**
     * Helper to format field values and resolve FK IDs into human readable names.
     */
    private static function formatFieldValue(string $field, mixed $value): string
    {
        if ($value === null || $value === '') return '-';

        try {
            switch ($field) {
                case 'category_id':
                    return \App\Models\Category::find($value)?->name ?? (string)$value;
                case 'department_id':
                    return \App\Models\Department::find($value)?->name ?? (string)$value;
                case 'location_id':
                    return \App\Models\Location::find($value)?->name ?? (string)$value;
                case 'vendor_id':
                    return \App\Models\Vendor::find($value)?->name ?? (string)$value;
                case 'brand_id':
                    return \App\Models\Brand::find($value)?->name ?? (string)$value;
                case 'vlan_id':
                    return \App\Models\Vlan::find($value)?->name ?? (string)$value;
                case 'employee_id':
                    return \App\Models\Employee::find($value)?->name ?? (string)$value;
                case 'asset_id':
                    $asset = \App\Models\Asset::find($value);
                    return $asset ? "{$asset->name} ({$asset->asset_tag})" : (string)$value;
                case 'user_id':
                    return \App\Models\User::find($value)?->name ?? (string)$value;
            }
        } catch (\Exception $e) {
            // Fallback if DB lookup fails
        }

        return is_scalar($value) ? (string)$value : json_encode($value);
    }
}
