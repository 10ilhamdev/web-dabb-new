<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Finder\Finder;

class CleanOrphanedStorage extends Command
{
    /**
     * Nama dan signature perintah.
     *
     * @var string
     */
    protected $signature = 'storage:clean {--dry-run : Hanya tampilkan file yang akan dihapus tanpa menghapusnya}';

    /**
     * Deskripsi perintah.
     *
     * @var string
     */
    protected $description = 'Hapus file media di storage/app/public yang tidak lagi digunakan/dirujuk oleh database';

    /**
     * Eksekusi perintah.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $disk = Storage::disk('public');
        $publicPath = $disk->path('');

        if (!is_dir($publicPath)) {
            $this->error('Folder storage public tidak ditemukan.');
            return 1;
        }

        $this->info('Mengambil data tabel dari database...');
        $tables = [];
        $dbName = DB::connection()->getDatabaseName();
        try {
            $results = DB::select('SHOW TABLES');
            $key = 'Tables_in_' . $dbName;
            foreach ($results as $row) {
                // Konversi row object ke array jika perlu atau akses properti secara dinamis
                $rowArray = (array) $row;
                $tables[] = reset($rowArray); // Mengambil value pertama dari array row
            }
        } catch (\Exception $e) {
            $this->error('Gagal mengambil daftar tabel: ' . $e->getMessage());
            return 1;
        }

        $excludedTables = ['migrations', 'sessions', 'jobs', 'failed_jobs', 'cache', 'cache_locks', 'password_reset_tokens'];
        $tables = array_diff($tables, $excludedTables);

        $this->info('Memindai seluruh file di storage/app/public...');
        $finder = new Finder();
        $finder->files()->in($publicPath);

        // Abaikan file sistem bawaan
        $finder->exclude(['.git', '.svn']);
        $finder->notName('.gitignore');
        $finder->notName('.htaccess');

        $files = iterator_to_array($finder);
        $totalFiles = count($files);
        $this->info("Menemukan {$totalFiles} file di storage.");

        $this->info('Mengambil data referensi dari database ke memori...');
        $allDbValues = [];
        foreach ($tables as $table) {
            $columns = Schema::getColumnListing($table);
            if (empty($columns)) {
                continue;
            }

            try {
                // Ambil seluruh data dari kolom-kolom tabel ini
                $rows = DB::table($table)->select($columns)->get();
                foreach ($rows as $row) {
                    foreach ($columns as $column) {
                        $val = $row->$column;
                        if (is_string($val) && !empty($val)) {
                            $allDbValues[] = $val;
                        }
                    }
                }
            } catch (\Exception $e) {
                // Abaikan jika ada tabel/kolom yang gagal dibaca
            }
        }

        // Hapus duplikat untuk mempercepat pencarian memori
        $allDbValues = array_unique($allDbValues);
        $this->info('Data referensi database berhasil dimuat. Mulai memindai file...');

        $orphanedFiles = [];
        $totalSize = 0;

        $bar = $this->output->createProgressBar($totalFiles);
        $bar->start();

        foreach ($files as $file) {
            $absolutePath = $file->getRealPath();
            // Ambil path relatif terhadap disk 'public'
            $relativePath = str_replace(
                [realpath($publicPath) . DIRECTORY_SEPARATOR, realpath($publicPath) . '/'],
                '',
                $absolutePath
            );
            // Normalisasi backslash menjadi slash biasa
            $relativePath = str_replace('\\', '/', $relativePath);

            // Cek apakah file ini dirujuk di DB
            $isReferenced = false;

            foreach ($allDbValues as $dbValue) {
                if (str_contains($dbValue, $relativePath)) {
                    $isReferenced = true;
                    break;
                }
            }

            if (!$isReferenced) {
                $orphanedFiles[] = [
                    'relative' => $relativePath,
                    'absolute' => $absolutePath,
                    'size' => $file->getSize()
                ];
                $totalSize += $file->getSize();
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        // Memindai file terjemahan yang yatim piatu
        $this->info('Memindai file terjemahan di resources/lang...');
        $langDirs = [resource_path('lang/id'), resource_path('lang/en')];
        $orphanedLangs = [];

        foreach ($langDirs as $dir) {
            if (is_dir($dir)) {
                $langFinder = new Finder();
                $langFinder->files()->in($dir)->name('home_*.php');
                foreach ($langFinder as $file) {
                    $filename = $file->getFilename();
                    if (preg_match('/home_(\d+)\.php/', $filename, $matches)) {
                        $featureId = $matches[1];
                        // Cek apakah Feature dengan ID ini ada dan bertipe 'home'
                        $featureExists = DB::table('features')
                            ->where('id', $featureId)
                            ->where('page_type', 'home')
                            ->exists();

                        if (!$featureExists) {
                            $orphanedLangs[] = [
                                'relative' => str_replace(resource_path(''), 'resources', $file->getRealPath()),
                                'absolute' => $file->getRealPath(),
                                'size' => $file->getSize()
                            ];
                        }
                    }
                }
            }
        }

        $orphanedCount = count($orphanedFiles);
        $orphanedLangCount = count($orphanedLangs);

        if ($orphanedCount === 0 && $orphanedLangCount === 0) {
            $this->info('Hebat! Tidak ditemukan file media atau file terjemahan yatim piatu.');
            return 0;
        }

        if ($orphanedCount > 0) {
            $formattedSize = number_format($totalSize / (1024 * 1024), 2) . ' MB';
            $this->warn("Menemukan {$orphanedCount} file media tidak terpakai dengan total ukuran {$formattedSize}:");
            foreach ($orphanedFiles as $orphaned) {
                $this->line("- {$orphaned['relative']} (" . number_format($orphaned['size'] / 1024, 1) . " KB)");
            }
        }

        if ($orphanedLangCount > 0) {
            $this->warn("Menemukan {$orphanedLangCount} file terjemahan tidak terpakai:");
            foreach ($orphanedLangs as $orphaned) {
                $this->line("- {$orphaned['relative']} (" . number_format($orphaned['size'] / 1024, 1) . " KB)");
            }
        }

        if ($dryRun) {
            $this->info('[DRY RUN] Tidak ada file yang dihapus.');
            return 0;
        }

        $confirmMsg = 'Apakah Anda yakin ingin menghapus semua file tidak terpakai ini?';
        if ($this->confirm($confirmMsg, true)) {
            $deletedCount = 0;
            foreach ($orphanedFiles as $orphaned) {
                if (@unlink($orphaned['absolute'])) {
                    $deletedCount++;
                }
            }
            if ($orphanedCount > 0) {
                $this->info("Berhasil menghapus {$deletedCount} dari {$orphanedCount} file media tidak terpakai.");
            }

            $deletedLangCount = 0;
            foreach ($orphanedLangs as $orphaned) {
                if (@unlink($orphaned['absolute'])) {
                    $deletedLangCount++;
                }
            }
            if ($orphanedLangCount > 0) {
                $this->info("Berhasil menghapus {$deletedLangCount} dari {$orphanedLangCount} file terjemahan tidak terpakai.");
            }
        } else {
            $this->info('Penghapusan dibatalkan.');
        }

        return 0;
    }
}
