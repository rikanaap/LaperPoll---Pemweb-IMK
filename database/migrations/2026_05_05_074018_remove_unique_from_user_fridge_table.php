<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_fridge', function (Blueprint $table) {
            // 1. Drop foreign keys dulu
            $table->dropForeign(['user_id']);
            $table->dropForeign(['bahan_id']);

            // 2. Sekarang baru bisa drop unique
            $table->dropUnique(['user_id', 'bahan_id']);

            // 3. Buat ulang foreign keys
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('bahan_id')->references('id')->on('bahans')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('user_fridge', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['bahan_id']);

            $table->unique(['user_id', 'bahan_id']);

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('bahan_id')->references('id')->on('bahans')->cascadeOnDelete();
        });
    }
};