<?php

namespace App\Console\Commands;

use App\Models\IpAddress;
use App\Services\TelegramBotService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class PingMonitorCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ips:ping-monitor {--interval=30} {--once}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automated ICMP Ping sweeper daemon to monitor IP health and dispatch daily Telegram report at 11:00 AM.';

    /**
     * Track offline alert timestamps in-memory
     */
    protected array $offlineAlertHistory = [];

    /**
     * Execute the console command.
     */
    public function handle(TelegramBotService $telegramBot): int
    {
        $isOnce = $this->option('once');
        $interval = (int) $this->option('interval') ?: 30;

        if ($isOnce) {
            $this->info("🚀 Executing Daily IP Ping Sweep & Telegram Report (11:00 AM)...");
            $this->runSingleSweep($telegramBot, true);
            return 0;
        }

        $this->info("🚀 Starting IP Ping Monitor Daemon (Interval: {$interval} seconds)...");
        $this->info("Press Ctrl+C to stop.\n");

        while (true) {
            $startTime = microtime(true);
            $this->runSingleSweep($telegramBot, false);
            $elapsed = round(microtime(true) - $startTime, 2);

            $sleepSeconds = max(1, $interval - (int) $elapsed);
            sleep($sleepSeconds);
        }

        return 0;
    }

    /**
     * Run a single sweep over all IP Addresses
     */
    private function runSingleSweep(TelegramBotService $telegramBot, bool $isDailyReport): void
    {
        $startTime = microtime(true);
        $ips = IpAddress::with(['asset', 'employee', 'vlan'])->get();

        $totalCount = count($ips);
        $onlineCount = 0;
        $offlineCount = 0;
        $offlineAssignedDevices = [];

        foreach ($ips as $ip) {
            $pingResult = $this->pingSingleIp($ip->ip_address);
            $isOnline = $pingResult['online'];

            // Process State Transition (Sends notification ONLY ONCE per state change!)
            $ip->processStateNotification($isOnline, 'Terdeteksi Offline saat pemindaian audit jaringan.');

            // Update status in DB
            $ip->update([
                'is_online' => $isOnline,
                'last_ping_at' => now(),
            ]);

            if ($isOnline) {
                $onlineCount++;
            } else {
                $offlineCount++;
                if ($ip->asset) {
                    $offlineAssignedDevices[] = [
                        'ip' => $ip->ip_address,
                        'name' => $ip->asset->name,
                        'tag' => $ip->asset->asset_tag,
                        'user' => $ip->employee ? $ip->employee->name : '-',
                    ];
                }
            }
        }

        $elapsed = round(microtime(true) - $startTime, 2);
        $timestamp = date('H:i:s');
        $this->info("[{$timestamp}] Ping Sweep Done in {$elapsed}s. Total: {$totalCount} | Online: {$onlineCount} | Offline: {$offlineCount}");

        // If daily report execution (11:00 AM)
        if ($isDailyReport) {
            $msg = "<b>📊 LAPORAN AUDIT JARINGAN HARIAN (11:00 AM) 📊</b>\n\n";
            $msg .= "<b>Waktu Audit:</b> " . date('Y-m-d H:i:s') . "\n";
            $msg .= "<b>Total IP Di-Audit:</b> {$totalCount} IP\n";
            $msg .= "<b>Status Online:</b> 🟢 <b>{$onlineCount} IP</b>\n";
            $msg .= "<b>Status Offline:</b> 🔴 <b>{$offlineCount} IP</b>\n\n";

            if (!empty($offlineAssignedDevices)) {
                $msg .= "<b>⚠️ Aset Terdaftar yang Offline (" . count($offlineAssignedDevices) . " unit):</b>\n";
                $count = 0;
                foreach ($offlineAssignedDevices as $dev) {
                    $count++;
                    if ($count > 10) {
                        $msg .= "<i>...dan " . (count($offlineAssignedDevices) - 10) . " aset terdaftar lainnya.</i>\n";
                        break;
                    }
                    $msg .= "• <code>{$dev['ip']}</code> - " . htmlspecialchars($dev['name']) . " (Tag: {$dev['tag']})\n";
                }
            } else {
                $msg .= "✅ <i>Semua aset terdaftar berfungsi normal dan merespon Ping dengan baik.</i>";
            }

            $chatId = env('TELEGRAM_ADMIN_CHAT_ID');
            if ($chatId) {
                $sent = $telegramBot->sendMessage($chatId, $msg);
                if ($sent) {
                    $this->info("SUCCESS: Daily 11:00 AM Telegram report sent successfully!");
                }
            }

            try {
                $waMsg = str_replace(['<b>', '</b>', '<i>', '</i>', '<code>', '</code>'], ['*', '*', '_', '_', '`', '`'], $msg);
                $waSent = app(\App\Services\WhatsAppService::class)->sendMessage(null, $waMsg);
                if ($waSent) {
                    $this->info("SUCCESS: Daily 11:00 AM WhatsApp report sent successfully!");
                }
            } catch (\Exception $e) {
                Log::error('WhatsApp daily report error: ' . $e->getMessage());
            }
        }
    }

    /**
     * Perform ICMP Ping Sweep for single IP
     */
    private function pingSingleIp(string $ipAddress): array
    {
        $online = false;
        $disabledFunctions = array_map('trim', explode(',', strtolower(ini_get('disable_functions') ?: '')));
        $canExec = function_exists('exec') && !in_array('exec', $disabledFunctions);

        if ($canExec) {
            if (stristr(PHP_OS, 'win')) {
                $command = 'ping -n 1 -w 800 ' . escapeshellarg($ipAddress);
                @exec($command, $outcome, $status);
                $fullOut = implode("\n", $outcome ?? []);
                if ($status === 0 && (stristr($fullOut, 'TTL=') || stristr($fullOut, 'bytes='))) {
                    $online = true;
                }
            } else {
                $command = 'ping -c 1 -W 1 ' . escapeshellarg($ipAddress) . ' 2>&1';
                @exec($command, $outcome, $status);
                if ($status === 0) {
                    $online = true;
                }
            }
        }

        // Socket fallback
        if (!$online) {
            $ports = [135, 445, 80, 443, 22, 3389, 8080, 554];
            foreach ($ports as $port) {
                $conn = @fsockopen($ipAddress, $port, $errno, $errstr, 0.2);
                if (is_resource($conn)) {
                    fclose($conn);
                    $online = true;
                    break;
                }
            }
        }

        return ['online' => $online];
    }
}
