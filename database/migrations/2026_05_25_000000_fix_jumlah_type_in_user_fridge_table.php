<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            // SQLite: skip change operation
            return;
        }

        Schema::table('user_fridge', function (Blueprint $table) {
            $table->integer('jumlah')->nullable(false)->default(0)->change();
        });
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        Schema::table('user_fridge', function (Blueprint $table) {
            $table->string('jumlah', 255)->nullable(false)->change();
        });
    }
};
