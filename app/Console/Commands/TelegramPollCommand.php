<?php

namespace App\Console\Commands;

use App\Http\Controllers\TelegramBotController;
use App\Services\TelegramBotService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramPollCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:poll {--timeout=30}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run long polling worker for Telegram Bot to reply to commands on localhost 24/7';

    /**
     * Execute the console command.
     */
    public function handle(TelegramBotController $botController, TelegramBotService $botService): int
    {
        $token = env('TELEGRAM_BOT_TOKEN');
        if (empty($token)) {
            $this->error('TELEGRAM_BOT_TOKEN is not set in .env!');
            return 1;
        }

        $this->info("🤖 ITAM Telegram Bot Polling Worker Started...");
        $this->info("Bot Token: {$token}");
        $this->info("Press Ctrl+C to stop.\n");

        $offset = 0;

        while (true) {
            try {
                $response = Http::timeout(35)->get("https://api.telegram.org/bot{$token}/getUpdates", [
                    'offset' => $offset,
                    'timeout' => 25,
                ]);

                if ($response->successful()) {
                    $updates = $response->json('result') ?? [];

                    foreach ($updates as $update) {
                        $offset = $update['update_id'] + 1;

                        // Create fake Request to reuse TelegramBotController logic
                        $request = new \Illuminate\Http\Request();
                        $request->replace($update);

                        $botController->handleWebhook($request);

                        $this->info("Processed update ID: {$update['update_id']}");
                    }
                }
            } catch (\Exception $e) {
                $this->error("Polling error: " . $e->getMessage());
                sleep(2);
            }

            usleep(200000); // 0.2s sleep between polling iterations
        }

        return 0;
    }
}
