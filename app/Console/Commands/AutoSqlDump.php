<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class AutoSqlDump extends Command
{
    protected $signature = 'db:dump';
    protected $description = 'Dump database ke file SQL dan timpa file lama';

    public function handle(): int
    {
        $filename = 'dabb_backup.sql';
        $path     = base_path($filename);
        $tmpPath  = $path . '.tmp';

        $host     = config('database.connections.mysql.host');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');
        $database = config('database.connections.mysql.database');

        // Fallback host jika tidak di Docker
        if ($host === 'host.docker.internal') {
            $isDocker = file_exists('/.dockerenv') || env('LARAVEL_SAIL') === 1;
            if (!$isDocker) {
                $host = '127.0.0.1';
            }
        }

        $this->info("Memulai backup database ke $filename...");

        // Hapus tmp lama jika ada
        if (file_exists($tmpPath)) {
            unlink($tmpPath);
        }

        // Jalankan mysqldump dan tulis langsung ke .tmp agar file asli tidak tersentuh dulu
        $command = sprintf(
            'mysqldump --no-tablespaces --column-statistics=0 --skip-lock-tables ' .
            '-h %s -u %s --password=%s %s --result-file=%s 2>&1',
            escapeshellarg($host),
            escapeshellarg($username),
            escapeshellarg($password),
            escapeshellarg($database),
            escapeshellarg($tmpPath)
        );

        $cmdOutput = shell_exec($command);

        // Pastikan file tmp berhasil dibuat dan tidak kosong
        if (!file_exists($tmpPath) || filesize($tmpPath) === 0) {
            $this->error("Gagal membuat file backup!");
            if ($cmdOutput) {
                $this->error("mysqldump output: " . $cmdOutput);
            }
            return Command::FAILURE;
        }

        // Bersihkan unusual line terminators (U+2028 LS, U+2029 PS) dengan
        // chunk-based streaming agar tidak terkena PHP memory limit atau
        // PCRE JIT stack limit error pada file besar.
        $this->info("Membersihkan unusual line terminators...");
        $cleaned = $this->cleanLineTerminators($tmpPath, $path);

        // Hapus tmp setelah selesai
        if (file_exists($tmpPath)) {
            unlink($tmpPath);
        }

        if (!$cleaned) {
            $this->error("Gagal membersihkan file backup!");
            return Command::FAILURE;
        }

        $size          = filesize($path);
        $sizeFormatted = $size >= 1048576
            ? round($size / 1048576, 2) . ' MB'
            : round($size / 1024, 2) . ' KB';

        $this->info("Berhasil! File <fg=cyan>{$filename}</> ({$sizeFormatted}) telah diperbarui.");

        return Command::SUCCESS;
    }

    /**
     * Baca file input per-chunk, ganti karakter unusual line terminator
     * (CRLF, CR, U+2028 LS, U+2029 PS) menjadi LF, lalu tulis ke output.
     *
     * Pendekatan ini tidak pernah load seluruh file ke memory sehingga aman
     * untuk file berukuran puluhan hingga ratusan MB.
     */
    private function cleanLineTerminators(string $inputPath, string $outputPath): bool
    {
        $in = @fopen($inputPath, 'rb');
        if (!$in) {
            $this->error("Tidak bisa membuka file sumber: $inputPath");
            return false;
        }

        $out = @fopen($outputPath, 'wb');
        if (!$out) {
            fclose($in);
            $this->error("Tidak bisa membuka file tujuan: $outputPath");
            return false;
        }

        try {
            // Karakter yang akan diganti (dalam byte UTF-8):
            //   U+2028 LINE SEPARATOR      = E2 80 A8
            //   U+2029 PARAGRAPH SEPARATOR = E2 80 A9
            $search  = ["\r\n", "\r", "\xE2\x80\xA8", "\xE2\x80\xA9"];
            $replace = ["\n",   "\n", "\n",             "\n"];

            $buffer = '';

            while (!feof($in)) {
                $chunk = fread($in, 65536); // 64 KB per iterasi
                if ($chunk === false) {
                    break;
                }

                $buffer .= $chunk;

                // Simpan 3 byte terakhir buffer untuk menjaga integritas karakter
                // multi-byte UTF-8 yang mungkin terpotong di batas chunk.
                // U+2028/U+2029 masing-masing 3 byte dalam UTF-8.
                $safeLen = strlen($buffer) - 3;
                if ($safeLen > 0) {
                    $part   = substr($buffer, 0, $safeLen);
                    $part   = str_replace($search, $replace, $part);
                    fwrite($out, $part);
                    $buffer = substr($buffer, $safeLen);
                }
            }

            // Proses sisa buffer yang tidak sempat ditulis
            if ($buffer !== '') {
                $buffer = str_replace($search, $replace, $buffer);
                fwrite($out, $buffer);
            }

            fclose($in);
            fclose($out);

            return true;
        } catch (\Throwable $e) {
            @fclose($in);
            @fclose($out);
            $this->error("Exception saat membersihkan file: " . $e->getMessage());
            return false;
        }
    }
}
