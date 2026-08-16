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
            $operator = $user ? $user->name : 'System';

            $modelName = class_basename($model);
            $targetName = self::getTargetName($model, $modelName);
            $details = self::getDetails($model, $modelName, $action);

            // Avoid logging minor internal updates that don't represent real data changes
            if ($action === 'updated' && method_exists($model, 'isDirty') && !$model->isDirty()) {
                // If nothing actually changed on the model, don't log it
                // Note: since this is run in "saved"/"updated" hooks, isDirty might be false.
                // We should hook into "updating" instead or allow logging if needed.
            }

            ActivityLog::create([
                'user_id' => $user ? $user->id : null,
                'operator' => $operator,
                'action' => $action,
                'model_type' => get_class($model),
                'model_id' => $model->id,
                'target_name' => $targetName,
                'details' => $details,
            ]);
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
     * Get specific metadata details for certain models.
     */
    private static function getDetails($model, string $modelName, string $action): ?array
    {
        if ($modelName === 'AssetAssignment') {
            $employee = $model->employee;
            return [
                'employee_name' => $employee ? $employee->name : 'Unknown Employee',
            ];
        }

        if ($modelName === 'Asset') {
            // Track status updates for assets in details
            if ($action === 'updated' && $model->isDirty('status')) {
                return [
                    'old_status' => $model->getOriginal('status'),
                    'new_status' => $model->status,
                ];
            }
        }

        return null;
    }
}
