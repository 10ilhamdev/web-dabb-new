<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PDO;

class DumpDataOnly extends Command
{
    protected $signature = 'db:dump-data {filename?}';
    protected $description = 'Backup HANYA data (tanpa struktur tabel) untuk import ke database yang sudah ada tabelnya';

    public function handle(): int
    {
        $filename = $this->argument('filename') ?? 'dabb_data_only.sql';
        $path = base_path($filename);

        $host     = config('database.connections.mysql.host');
        $port     = config('database.connections.mysql.port') ?: '3306';
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');
        $database = config('database.connections.mysql.database');

        try {
            $pdo = new PDO(
                "mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4",
                $username,
                $password,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
            );
            // Pastikan MySQL mengembalikan string sebagai UTF-8, bukan binary
            $pdo->exec("SET NAMES utf8mb4");
        } catch (\Throwable $e) {
            $this->error("Gagal terhubung ke database: " . $e->getMessage());
            return Command::FAILURE;
        }

        $skipTables = [
            'migrations',
            'jobs',
            'job_batches',
            'failed_jobs',
            'sessions',
            'cache',
            'cache_locks',
            'password_reset_tokens',
            'page_views',
        ];

        $stmt = $pdo->query("SHOW TABLES");
        $allRows = $stmt->fetchAll(PDO::FETCH_NUM);
        $tables = array_filter(
            array_column($allRows, 0),
            fn($t) => !in_array($t, $skipTables)
        );

        if (empty($tables)) {
            $this->error('Tidak ada tabel untuk di-export.');
            return Command::FAILURE;
        }

        $this->info("Memulai export data ke {$filename}...");
        $this->line("Tabel yang di-skip: <fg=yellow>" . implode(', ', $skipTables) . "</>");
        $this->line("Total tabel target: " . count($tables));
        $this->newLine();

        $header = "-- =======================================================\n" .
                  "-- Data Only Backup\n" .
                  "-- Database: {$database}\n" .
                  "-- Host: {$host}\n" .
                  "-- Exported: " . now()->format('Y-m-d H:i:s') . "\n" .
                  "-- =======================================================\n\n" .
                  "SET FOREIGN_KEY_CHECKS = 0;\n\n";

        $fp = fopen($path, 'w');
        if (!$fp) {
            $this->error("Tidak dapat membuka file untuk ditulis: {$path}");
            return Command::FAILURE;
        }
        fwrite($fp, $header);

        $exported = 0;
        $skippedEmpty = 0;
        $errors = [];

        foreach ($tables as $table) {
            // Ambil metadata kolom: nama + tipe asli
            $metaStmt = $pdo->query("SHOW COLUMNS FROM `{$table}`");
            $columns = $metaStmt->fetchAll();

            // Tentukan kolom mana yang BINARY/BLOB (harus di-encode 0x)
            $binaryCols = [];
            foreach ($columns as $col) {
                $type = strtolower($col['Type'] ?? '');
                if (
                    strpos($type, 'blob') !== false
                    || $type === 'binary'
                    || strpos($type, 'varbinary') !== false
                ) {
                    $binaryCols[$col['Field']] = true;
                }
            }

            // Hitung row
            $countStmt = $pdo->query("SELECT COUNT(*) FROM `{$table}`");
            $count = (int) $countStmt->fetchColumn();

            if ($count === 0) {
                $this->line("  <fg=gray>[SKIP]</> {$table} (0 rows)");
                $skippedEmpty++;
                continue;
            }

            // Ambil semua data
            $dataStmt = $pdo->query("SELECT * FROM `{$table}`");
            $rows = $dataStmt->fetchAll();
            $cols = array_keys($rows[0] ?? []);

            fwrite($fp, "-- Table: {$table} ({$count} rows)\n");
            fwrite($fp, "DELETE FROM `{$table}`;\n");

            foreach ($rows as $row) {
                $colList = '`' . implode('`, `', $cols) . '`';
                $vals = [];

                foreach ($cols as $col) {
                    $val = $row[$col];

                    if ($val === null) {
                        $vals[] = 'NULL';
                    } elseif (isset($binaryCols[$col])) {
                        // Binary/BLOB: encode sebagai 0xhex
                        $vals[] = '0x' . bin2hex($val);
                    } elseif (is_int($val) || is_float($val)) {
                        // Numerik: tanpa kutip
                        $vals[] = $val;
                    } else {
                        // String/text/json/varchar: escape dan quote
                        $vals[] = "'" . $this->mysqlEscape((string) $val) . "'";
                    }
                }

                fwrite($fp, "INSERT INTO `{$table}` ({$colList}) VALUES (" . implode(', ', $vals) . ");\n");
            }

            fwrite($fp, "\n");

            $exported++;
            $this->line("  <fg=green>[OK]</> {$table} ({$count} rows)");
        }

        fwrite($fp, "SET FOREIGN_KEY_CHECKS = 1;\n");
        fclose($fp);

        $this->newLine();

        if (!empty($errors)) {
            $this->warn("Tabel yang gagal: " . implode(', ', $errors));
        }

        $size = filesize($path);
        $sizeFormatted = $size >= 1048576
            ? round($size / 1048576, 2) . ' MB'
            : round($size / 1024, 2) . ' KB';

        $this->info("Berhasil! <fg=cyan>{$filename}</> ({$sizeFormatted})");
        $this->line("Diexport: {$exported} tabel | Kosong: {$skippedEmpty} tabel | Gagal: " . count($errors) . " tabel");
        $this->newLine();
        $this->warn("Penting: File SQL ini berisi DELETE + INSERT. IMPORT ke database yang SUDAH punya tabel!");

        return Command::SUCCESS;
    }

    /**
     * Escape string untuk MySQL INSERT - aman untuk semua karakter termasuk NULL byte.
     *
     * U+2028 (Line Separator) dan U+2029 (Paragraph Separator) dikonversi ke
     * newline biasa terlebih dahulu agar tidak muncul literal di file SQL
     * dan tidak memicu peringatan "unusual line terminators" di editor.
     */
    private function mysqlEscape(string $value): string
    {
        // Konversi unusual line terminators ke newline biasa SEBELUM proses lain
        // U+2028 LINE SEPARATOR      = \xE2\x80\xA8 (UTF-8)
        // U+2029 PARAGRAPH SEPARATOR = \xE2\x80\xA9 (UTF-8)
        $value = str_replace("\xE2\x80\xA8", "\n", $value);
        $value = str_replace("\xE2\x80\xA9", "\n", $value);

        // Urutan penting: NULL bytes duluan, lalu backslash, lalu kutip
        $value = str_replace("\0", '\\0', $value);
        $value = str_replace('\\', '\\\\', $value);
        $value = str_replace("'", "''", $value);
        $value = str_replace("\n", '\\n', $value);
        $value = str_replace("\r", '\\r', $value);
        $value = str_replace("\t", '\\t', $value);
        $value = str_replace("\x1a", '\\Z', $value);
        return $value;
    }
}
