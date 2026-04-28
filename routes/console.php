<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// Jalankan backup setiap 5 menit
Schedule::command('db:dump')
    ->everyFiveMinutes()
    ->appendOutputTo(storage_path('logs/scheduler-db-dump.log'));

// Jalankan pengecekan perubahan file setiap 1 menit.
// Command npm:build akan otomatis skip (tidak menjalankan npm run build)
// jika tidak ada perubahan pada file CSS/JS/Blade/konfigurasi.
// Hanya menjalankan npm run build ketika ada perubahan terdeteksi.
Schedule::command('npm:build')
    ->everyMinute()
    ->appendOutputTo(storage_path('logs/scheduler-npm-build.log'));

// Hapus semua file log setiap 10 menit untuk meringankan beban sistem
// Mengarahkan output langsung ke stdout terminal (php://stdout)
Schedule::command('log:clear')
    ->everyTenMinutes()
    ->appendOutputTo(storage_path('logs/scheduler-log-clear.log'));

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

