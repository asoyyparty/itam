<?php

use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $waSettings = [
            [
                'key' => 'whatsapp_enabled',
                'value' => '0',
                'type' => 'boolean',
                'group' => 'whatsapp',
            ],
            [
                'key' => 'whatsapp_provider',
                'value' => 'fonnte',
                'type' => 'text',
                'group' => 'whatsapp',
            ],
            [
                'key' => 'whatsapp_api_token',
                'value' => '',
                'type' => 'text',
                'group' => 'whatsapp',
            ],
            [
                'key' => 'whatsapp_api_url',
                'value' => 'https://api.fonnte.com/send',
                'type' => 'text',
                'group' => 'whatsapp',
            ],
            [
                'key' => 'whatsapp_admin_phone',
                'value' => '',
                'type' => 'text',
                'group' => 'whatsapp',
            ],
        ];

        foreach ($waSettings as $setting) {
            Setting::firstOrCreate(['key' => $setting['key']], $setting);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Setting::whereIn('key', [
            'whatsapp_enabled',
            'whatsapp_provider',
            'whatsapp_api_token',
            'whatsapp_api_url',
            'whatsapp_admin_phone',
        ])->delete();
    }
};
