<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class OptimizeCache extends Command
{
    protected $signature = 'cache:optimize';
    protected $description = 'Bersihkan semua cache, config, route, dan view untuk mengoptimalkan performa aplikasi';

    public function handle()
    {
        $this->info('Memulai proses pembersihan cache dan optimasi...');
        $this->newLine();

        $commands = [
            'cache:clear'          => 'Cache aplikasi',
            'config:clear'        => 'Config cache',
            'route:clear'          => 'Route cache',
            'view:clear'          => 'View compiled',
            'event:clear'         => 'Event cache',
            'component:clear'     => 'Component cache',
            'symfony:cache:clear' => 'Symfony cache',
        ];

        $success = 0;
        $failed  = 0;

        foreach ($commands as $command => $label) {
            try {
                Artisan::call($command);
                $this->info("  [OK] {$label}");
                $success++;
            } catch (\Throwable $e) {
                $this->warn("  [SKIP] {$label} ({$e->getMessage()})");
                $failed++;
            }
        }

        // Optimasi ulang (kecuali config:cache & route:cache karena di dev mode)
        try {
            Artisan::call('optimize:clear');
            $this->info('  [OK] Optimize clear');
            $success++;
        } catch (\Throwable $e) {
            $this->warn("  [SKIP] Optimize clear ({$e->getMessage()})");
            $failed++;
        }

        $this->newLine();
        $this->info("Selesai. Berhasil: {$success}, Gagal/Skip: {$failed}");

        return 0;
    }
}