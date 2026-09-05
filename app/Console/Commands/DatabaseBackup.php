<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class DatabaseBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:backup {--retention=7 : Number of days to retain dated backups}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup the database automatically (Option 2: rolling dated backups + itam.sql snapshot)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $storagePath = storage_path('app/backups');

        if (! file_exists($storagePath)) {
            mkdir($storagePath, 0755, true);
        }

        // Dated filename (e.g. itam_2026-09-05_00-00-00.sql)
        $datedFilename = 'itam_' . date('Y-m-d_H-i-s') . '.sql';
        $datedFilePath = $storagePath . DIRECTORY_SEPARATOR . $datedFilename;
        $latestFilePath = $storagePath . DIRECTORY_SEPARATOR . 'itam.sql';

        // Retrieve database credentials securely from config
        $host = config('database.connections.mysql.host', '127.0.0.1');
        $port = config('database.connections.mysql.port', '3306');
        $username = config('database.connections.mysql.username', 'root');
        $password = config('database.connections.mysql.password', '');
        $database = config('database.connections.mysql.database', 'itam');

        $mysqldump = $this->getMysqldumpBinary();

        // Build command with safe Windows quoting
        $commandParts = [
            $mysqldump,
            '--host=' . escapeshellarg($host),
            '--port=' . escapeshellarg($port),
            '--user=' . escapeshellarg($username),
        ];

        if ($password !== '' && $password !== null) {
            $commandParts[] = '--password=' . escapeshellarg($password);
        }

        // Standard mysqldump options for reliable backup
        $commandParts[] = '--routines';
        $commandParts[] = '--triggers';
        $commandParts[] = '--single-transaction';
        $commandParts[] = escapeshellarg($database);
        $commandParts[] = '> ' . escapeshellarg($datedFilePath);

        $command = implode(' ', $commandParts);

        $this->info("Running database backup using: $mysqldump");
        $output = [];
        $returnVar = null;

        exec($command, $output, $returnVar);

        if ($returnVar === 0 && file_exists($datedFilePath) && filesize($datedFilePath) > 0) {
            // Option 2: Copy to itam.sql as the latest snapshot
            copy($datedFilePath, $latestFilePath);

            $fileSize = round(filesize($datedFilePath) / 1024, 2);
            $this->info("✓ Database backup created successfully: $datedFilename ({$fileSize} KB)");
            $this->info("✓ Updated latest snapshot: itam.sql ({$fileSize} KB)");

            Log::info("Auto Database Backup created: $datedFilename ({$fileSize} KB) and mirrored to itam.sql");

            // Clean up old dated backups (keep last 7 days by default)
            $retentionDays = (int) $this->option('retention');
            $this->cleanOldBackups($storagePath, $retentionDays);
            
            return Command::SUCCESS;
        }

        $this->error('Database backup failed!');
        $errorMsg = implode(PHP_EOL, $output);
        Log::error("Auto Database Backup failed! Exit code: $returnVar. Error: $errorMsg. Command: $command");
        
        return Command::FAILURE;
    }

    /**
     * Resolve the absolute path to mysqldump binary.
     */
    private function getMysqldumpBinary(): string
    {
        // 1. Check custom config or environment
        $configured = config('database.connections.mysql.dump.dump_binary_path');
        if ($configured && file_exists($configured)) {
            return escapeshellarg($configured);
        }

        // 2. Known standard paths on Windows
        $knownPaths = [
            'C:\\xampp\\mysql\\bin\\mysqldump.exe',
            'C:\\laragon\\bin\\mysql\\current\\bin\\mysqldump.exe',
            'C:\\Program Files\\MySQL\\MySQL Server 8.4\\bin\\mysqldump.exe',
            'C:\\Program Files\\MySQL\\MySQL Server 8.0\\bin\\mysqldump.exe',
            'C:\\Program Files\\MariaDB 10.4\\bin\\mysqldump.exe',
            'C:\\Program Files\\MariaDB 11.4\\bin\\mysqldump.exe',
        ];

        foreach ($knownPaths as $path) {
            if (file_exists($path)) {
                return escapeshellarg($path);
            }
        }

        // 3. Fallback to system PATH
        return 'mysqldump';
    }

    /**
     * Clean up backups older than retention period, protecting itam.sql.
     */
    private function cleanOldBackups(string $path, int $days = 7): void
    {
        $files = glob($path . DIRECTORY_SEPARATOR . 'itam_*.sql');
        $now = time();
        $retentionSeconds = max(1, $days) * 86400;

        foreach ($files as $file) {
            if (is_file($file) && basename($file) !== 'itam.sql') {
                if ($now - filemtime($file) >= $retentionSeconds) {
                    @unlink($file);
                    Log::info("Deleted expired database backup: " . basename($file));
                }
            }
        }
    }
}

