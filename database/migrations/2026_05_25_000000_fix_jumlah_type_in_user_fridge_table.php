<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_fridge', function (Blueprint $table) {
            $table->integer('jumlah')->nullable(false)->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('user_fridge', function (Blueprint $table) {
            $table->string('jumlah', 255)->nullable(false);
        });
    }
};
