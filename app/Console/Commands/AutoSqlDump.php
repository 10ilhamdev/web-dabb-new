<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;

class AutoSqlDump extends Command
{
    // Ini adalah perintah yang akan kita panggil
    protected $signature = 'db:dump';
    protected $description = 'Dump database ke file sql dan override file lama';

    public function handle()
    {
        // Nama file tetap agar selalu ditimpa (override)
        $filename = 'dabb_backup.sql';
        $path = base_path($filename);

        $this->info("Memulai backup ke $filename...");

        // Perintah mysqldump (disesuaikan dengan .env)
        // Kita arahkan ke host.docker.internal (Laragon)
        $command = sprintf(
            'mysqldump -h %s -u %s --password=%s %s > %s',
            config('database.connections.mysql.host'),
            config('database.connections.mysql.username'),
            config('database.connections.mysql.password'),
            config('database.connections.mysql.database'),
            $path
        );

        // Eksekusi perintah shell
        exec($command);

        $this->info("Selesai! File $filename telah diperbarui.");
    }
}
