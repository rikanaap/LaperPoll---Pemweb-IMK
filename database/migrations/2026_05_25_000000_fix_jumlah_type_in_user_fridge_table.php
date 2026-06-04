<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Disable strict mode sementara agar warning 1265 tidak jadi error
        DB::statement("SET SESSION sql_mode = ''");
        DB::statement('ALTER TABLE user_fridge MODIFY jumlah INT(11) NOT NULL DEFAULT 0');
        DB::statement("SET SESSION sql_mode = 'STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION'");
    }

    public function down(): void
    {
        DB::statement("SET SESSION sql_mode = ''");
        DB::statement('ALTER TABLE user_fridge MODIFY jumlah VARCHAR(255) NOT NULL');
        DB::statement("SET SESSION sql_mode = 'STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION'");
    }
};