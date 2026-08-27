<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Http\Controllers\TelegramBotController;
use App\Models\Setting;

class TelegramPollCommand extends Command
{
    protected $signature = 'telegram:poll';
    protected $description = 'Long poll Telegram updates (for local development)';

    public function handle()
    {
        $token = Setting::where('key', 'telegram_bot_token')->value('value') ?: env('TELEGRAM_BOT_TOKEN');
        
        if (empty($token)) {
            $this->error('Telegram Bot Token is not set in Settings or .env');
            return;
        }

        $this->info("Starting Telegram long polling...");
        
        // First delete any existing webhook to ensure getUpdates works
        Http::post("https://api.telegram.org/bot{$token}/deleteWebhook");

        $offset = 0;
        $controller = app(TelegramBotController::class);

        while (true) {
            try {
                $response = Http::timeout(60)->get("https://api.telegram.org/bot{$token}/getUpdates", [
                    'offset' => $offset,
                    'timeout' => 50,
                ]);

                if ($response->successful() && isset($response['result'])) {
                    $updates = $response['result'];
                    
                    foreach ($updates as $update) {
                        $this->info("Received update: " . $update['update_id']);
                        
                        // Simulate a Request object
                        $request = new Request();
                        $request->replace($update);
                        
                        // Pass to the webhook handler
                        $controller->handleWebhook($request);
                        
                        $offset = $update['update_id'] + 1;
                    }
                }
            } catch (\Exception $e) {
                $this->error("Connection error: " . $e->getMessage());
                sleep(2);
            }
        }
    }
}
