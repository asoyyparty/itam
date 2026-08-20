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
            
            // Do not log activities performed by superadmin
            if ($user && $user->hasRole('superadmin')) {
                return;
            }

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
                    // If nothing actually changed on the model, don't log it
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
            // Fail-safe to prevent application crashing if logging fails
            logger()->error('Activity logging failed: ' . $e->getMessage());
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
                return $model->name ?? 'Unknown Employee';

            case 'User':
                return $model->name ?? $model->username ?? 'Unknown User';

            default:
                // Fallbacks
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

                $fieldLabel = ucwords(str_replace(['_id', '_'], ['', ' '], $field));
                if ($field === 'status') {
                    $fieldLabel = 'Status';
                }

                $oldStr = is_scalar($oldValue) ? (string)$oldValue : json_encode($oldValue);
                $newStr = is_scalar($newValue) ? (string)$newValue : json_encode($newValue);

                $diffs[] = [
                    'field' => $fieldLabel,
                    'old' => $oldStr !== '' ? $oldStr : '-',
                    'new' => $newStr !== '' ? $newStr : '-',
                ];
            }
            if (!empty($diffs)) {
                $details['diffs'] = $diffs;
            }
        } elseif ($action === 'created') {
            $summary = [];
            if (isset($model->status)) {
                $summary['Status'] = $model->status;
            }
            if (isset($model->priority)) {
                $summary['Priority'] = $model->priority;
            }
            if (!empty($summary)) {
                $details['summary'] = $summary;
            }
        }

        return !empty($details) ? $details : null;
    }
}
