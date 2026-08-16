<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

use App\Models\Setting;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Schema;

try {
    if (Schema::hasTable('settings')) {
        $emailEnabled = Setting::where('key', 'ip_offline_email_notification')->value('value');
        $emailTime = Setting::where('key', 'ip_offline_email_time')->value('value') ?: '08:00';

        if ($emailEnabled == '1') {
            Schedule::command('ips:ping-all')->dailyAt($emailTime);
        }
    }
} catch (Exception $e) {
    // Ignore if table doesn't exist yet
}

// Schedule daily IP audit sweep and Telegram report at 11:00 AM
Schedule::command('ips:ping-monitor --once')->dailyAt('11:00');


