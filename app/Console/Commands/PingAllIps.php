<?php

namespace App\Console\Commands;

use App\Models\IpAddress;
use App\Models\User;
use App\Notifications\OfflineIpNotification;
use Illuminate\Console\Command;

class PingAllIps extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ips:ping-all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ping all IP addresses and notify Super Admins if offline';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $ips = IpAddress::where('status', 'Used')
            ->whereHas('vlan', function ($q) {
                $q->where('status', 'Active');
            })
            ->with('asset')
            ->get();
        $offlineIps = [];

        $this->info('Starting ping for '.$ips->count().' IP addresses...');

        // Check if fping is available on Linux for ultra-fast parallel pinging (hundreds of IPs per second)
        $hasFping = ! stristr(PHP_OS, 'win') && ! empty(shell_exec('which fping 2>/dev/null'));

        if ($hasFping && $ips->count() > 0) {
            $this->info('Using parallel fping for fast multi-IP scanning...');
            $ipList = $ips->pluck('ip_address')->unique()->toArray();
            $tempFile = storage_path('app/ip_ping_list.txt');
            file_put_contents($tempFile, implode("\n", $ipList));

            // fping -a (alive IPs only) -t 300 (300ms timeout)
            $fpingOutput = shell_exec('fping -a -t 300 < '.escapeshellarg($tempFile).' 2>&1');
            $aliveIps = array_filter(array_map('trim', explode("\n", $fpingOutput ?? '')));
            $aliveMap = array_flip($aliveIps);

            @unlink($tempFile);

            foreach ($ips as $ip) {
                $isOnline = isset($aliveMap[$ip->ip_address]);
                $ip->updateQuietly([
                    'is_online' => $isOnline,
                    'last_ping_at' => now(),
                ]);

                if (! $isOnline) {
                    $offlineIps[] = [
                        'ip_address' => $ip->ip_address,
                        'name' => $ip->asset ? $ip->asset->name : ($ip->notes ?? 'Unknown'),
                    ];
                    $this->error("IP {$ip->ip_address} is OFFLINE");
                } else {
                    $this->info("IP {$ip->ip_address} is ONLINE");
                }
            }
        } else {
            foreach ($ips as $ip) {
                $ipAddress = $ip->ip_address;
                $str = PHP_OS;

                if (stristr($str, 'win')) {
                    $command = 'ping -n 1 -w 1000 '.escapeshellarg($ipAddress);
                } else {
                    $command = 'ping -c 1 -W 1 '.escapeshellarg($ipAddress);
                }

                exec($command, $outcome, $status);
                $isOnline = ($status === 0);

                $ip->updateQuietly([
                    'is_online' => $isOnline,
                    'last_ping_at' => now(),
                ]);

                if (! $isOnline) {
                    $offlineIps[] = [
                        'ip_address' => $ipAddress,
                        'name' => $ip->asset ? $ip->asset->name : ($ip->notes ?? 'Unknown'),
                    ];
                    $this->error("IP $ipAddress is OFFLINE");
                } else {
                    $this->info("IP $ipAddress is ONLINE");
                }
            }
        }

        if (count($offlineIps) > 0) {
            $this->info('Sending notification for '.count($offlineIps).' offline IPs...');

            $customRecipients = \App\Models\Setting::where('key', 'ip_offline_email_recipients')->value('value');
            if (!empty(trim($customRecipients))) {
                $emails = array_map('trim', explode(',', $customRecipients));
                foreach ($emails as $email) {
                    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        \Illuminate\Support\Facades\Notification::route('mail', $email)
                            ->notify(new OfflineIpNotification($offlineIps));
                    }
                }
            } else {
                $superAdmins = User::role('Super Admin')->get();
                foreach ($superAdmins as $admin) {
                    $admin->notify(new OfflineIpNotification($offlineIps));
                }
            }

            // Send Telegram Notification
            try {
                $telegramBot = app(\App\Services\TelegramBotService::class);
                $msg = "<b>🔴 LAPORAN HARIAN IP OFFLINE (11:00 AM) 🔴</b>\n\n";
                $msg .= "Sistem mendeteksi ada <b>".count($offlineIps)."</b> IP Address yang saat ini tidak dapat dihubungi (Offline):\n\n";
                foreach ($offlineIps as $ip) {
                    $details = "• <code>{$ip['ip_address']}</code>";
                    if (! empty($ip['name'])) {
                        $details .= ' - '.htmlspecialchars($ip['name']);
                    }
                    $msg .= $details."\n";
                }
                $msg .= "\n<i>Silakan periksa perangkat terkait.</i>";
                $telegramBot->sendMessage(null, $msg);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Error sending Telegram notification in PingAllIps: '.$e->getMessage());
            }

            $this->info('Notification sent.');
        } else {
            $this->info('All IPs are online. No notification sent.');
        }

        return 0;
    }
}
