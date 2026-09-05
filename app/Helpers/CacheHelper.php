<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Cache;

class CacheHelper
{
    /**
     * Clear all cached dashboard statistics and health predictions.
     */
    public static function clearDashboardCache(): void
    {
        Cache::forget('dashboard_metrics_cache');
        Cache::forget('predictive_health_summary_id');
        Cache::forget('predictive_health_summary_en');
    }

    /**
     * Clear master data dropdown caches.
     */
    public static function clearMasterDataCache(?string $type = null): void
    {
        if (!$type || $type === 'categories') Cache::forget('master_categories_all');
        if (!$type || $type === 'brands') Cache::forget('master_brands_all');
        if (!$type || $type === 'locations') Cache::forget('master_locations_all');
        if (!$type || $type === 'vlans') Cache::forget('master_vlans_all');
        if (!$type || $type === 'years') Cache::forget('master_asset_years');
    }

    /**
     * Clear asset specific health prediction cache.
     */
    public static function clearAssetHealthCache(int $assetId): void
    {
        Cache::forget("asset_health_{$assetId}_id");
        Cache::forget("asset_health_{$assetId}_en");
    }

    /**
     * Purge all application caches.
     */
    public static function flushAll(): void
    {
        Cache::flush();
    }
}
