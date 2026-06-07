<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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
        Schema::table('bahans', function (Blueprint $table) {
            // Cek dulu apakah kolom sudah ada (idempotent)
            if (!Schema::hasColumn('bahans', 'kategori')) {
                $table->string('kategori', 50)->nullable()->after('nama');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bahans', function (Blueprint $table) {
            // Cek dulu apakah kolom ada sebelum drop
            if (Schema::hasColumn('bahans', 'kategori')) {
                $table->dropColumn('kategori');
            }
        });
    }
};
