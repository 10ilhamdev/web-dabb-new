<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// Panduan Waktu Menjalankan Perintah:
// ->everyMinute()              // Setiap 1 menit sekali
// ->everyTwoMinutes()           // Setiap 2 menit sekali
// ->everyThreeMinutes()         // Setiap 3 menit sekali
// ->everyFourMinutes()          // Setiap 4 menit sekali
// ->everyFiveMinutes()          // Setiap 5 menit sekali
// ->everyTenMinutes()           // Setiap 10 menit sekali
// ->everyFifteenMinutes()       // Setiap 15 menit sekali
// ->everyThirtyMinutes()        // Setiap 30 menit sekali
// ->hourly()                    // Setiap 1 jam sekali
// ->hourlyAt(17)                // Setiap jam pada menit ke-17 (contoh: 10:17, 11:17, dst)
// ->everyTwoHours()             // Setiap 2 jam sekali
// ->everyThreeHours()           // Setiap 3 jam sekali
// ->everyFourHours()            // Setiap 4 jam sekali
// ->everySixHours()             // Setiap 6 jam sekali
// ->daily()                     // Setiap hari jam 00:00
// ->cron('0 0 */2 * *')         // Setiap 2 hari sekali, jam 00:00
// ->cron('0 0 */3 * *')         // Setiap 3 hari sekali, jam 00:00
// ->dailyAt('17:00')            // Setiap hari jam 17:00
// ->twiceDaily(1, 13)           // Setiap hari jam 01:00 dan 13:00
// ->weekly()                    // Setiap minggu (Minggu 00:00)
// ->everyTwoWeeks()             // Setiap 2 Minggu Sekali
// ->weeklyOn(1, '8:00')         // Setiap Senin jam 08:00
// ->monthly()                   // Setiap bulan tanggal 1 jam 00:00
// ->monthlyOn(4, '15:00')       // Setiap tanggal 4 jam 15:00
// ->quarterly()                 // Setiap 3 bulan sekali
// ->yearly()                    // Setiap tahun tanggal 1 Januari jam 00:00

// Jalankan backup setiap 1 hari
Schedule::command('db:dump')
    ->daily()
    ->appendOutputTo(storage_path('logs/scheduler-db-dump.log'));

// Jalankan pengecekan perubahan file setiap 1 minggu.
// Command npm:build akan otomatis skip (tidak menjalankan npm run build)
// jika tidak ada perubahan pada file CSS/JS/Blade/konfigurasi.
// Hanya menjalankan npm run build ketika ada perubahan terdeteksi.
Schedule::command('npm:build')
    ->weekly()
    ->appendOutputTo(storage_path('logs/scheduler-npm-build.log'));

// Bersihkan semua cache aplikasi setiap jam 17:00 wib untuk mencegah aplikasi lag
// Melakukan: cache:clear, config:clear, route:clear, view:clear, event:clear,
// component:clear, symfony:cache:clear, dan optimize:clear
Schedule::command('cache:optimize')
    ->dailyAt('17:00') // jalan setiap hari jam 17:00 WIB
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/scheduler-cache-optimize.log'));

// Hapus semua file log setiap hari untuk meringankan beban sistem
// Mengarahkan output langsung ke stdout terminal (php://stdout)
Schedule::command('log:clear')
    ->daily()
    ->appendOutputTo(storage_path('logs/scheduler-log-clear.log'));

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
