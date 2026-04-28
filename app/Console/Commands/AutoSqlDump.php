<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class AutoSqlDump extends Command
{
    protected $signature = 'db:dump';
    protected $description = 'Dump database ke file SQL dan timpa file lama';

    public function handle(): int
    {
        $filename = 'dabb_backup.sql';
        $path = base_path($filename);

        $host     = config('database.connections.mysql.host');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');
        $database = config('database.connections.mysql.database');

        $this->info("Memulai backup database ke $filename...");

        // Build mysqldump command with flags to avoid:
        // 1. PROCESS privilege error when dumping tablespaces  → --no-tablespaces
        // 2. Column statistics warning (MySQL 8+)             → --column-statistics=0
        // 3. Lock tables during dump                         → --skip-lock-tables
        $command = sprintf(
        'mysqldump --no-tablespaces --column-statistics=0 --skip-lock-tables ' .
        '-h %s -u %s --password=%s %s > %s 2> %s',
        escapeshellarg($host),
        escapeshellarg($username),
        escapeshellarg($password),
        escapeshellarg($database),
        escapeshellarg($path),
        escapeshellarg($path . '.log') // simpan error/warning ke file log terpisah
        );


        $output = shell_exec($command);

        // Check if file was created successfully
        if (!file_exists($path) || filesize($path) === 0) {
            $this->error("Gagal membuat file backup!");
            if ($output) {
                $this->line("<comment>mysqldump output:</comment> " . trim($output));
            }
            return Command::FAILURE;
        }

        $size = filesize($path);
        $sizeFormatted = $size >= 1048576
            ? round($size / 1048576, 2) . ' MB'
            : round($size / 1024, 2) . ' KB';

        $this->info("Berhasil! File <fgcyan>{$filename}</> ({$sizeFormatted}) telah diperbarui.");

        return Command::SUCCESS;
    }
}
