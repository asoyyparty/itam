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
        $aiSettings = [
            [
                'key' => 'ai_provider',
                'value' => 'gemini',
                'type' => 'text',
                'group' => 'ai',
            ],
            [
                'key' => 'gemini_api_key',
                'value' => '',
                'type' => 'text',
                'group' => 'ai',
            ],
            [
                'key' => 'openai_api_key',
                'value' => '',
                'type' => 'text',
                'group' => 'ai',
            ],
        ];

        foreach ($aiSettings as $setting) {
            Setting::firstOrCreate(['key' => $setting['key']], $setting);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Setting::whereIn('key', ['ai_provider', 'gemini_api_key', 'openai_api_key'])->delete();
    }
};
