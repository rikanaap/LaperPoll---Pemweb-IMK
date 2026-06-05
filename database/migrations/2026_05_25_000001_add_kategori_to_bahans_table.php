<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Tambah kolom 'kategori' ke tabel bahans.
     * Kolom ini sudah ada di DB aktual tapi tidak ada di migration awal.
     * Dibutuhkan untuk pengelompokan bahan di Nota Belanja.
     * Pakai raw SQL agar aman dari strict mode XAMPP/MySQL.
     */
    public function up(): void
    {
        // Cek dulu apakah kolom sudah ada (idempotent — aman dijalankan ulang)
        $columns = DB::select("SHOW COLUMNS FROM bahans LIKE 'kategori'");
        if (empty($columns)) {
            DB::statement("SET SESSION sql_mode = ''");
            DB::statement("ALTER TABLE bahans ADD COLUMN kategori VARCHAR(50) NULL DEFAULT NULL AFTER nama");
            DB::statement("SET SESSION sql_mode = 'STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION'");
        }
    }

    public function down(): void
    {
        $columns = DB::select("SHOW COLUMNS FROM bahans LIKE 'kategori'");
        if (!empty($columns)) {
            DB::statement("ALTER TABLE bahans DROP COLUMN kategori");
        }
    }
};