<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('langkah_bahan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('langkah_id')->constrained('langkah_reseps')->cascadeOnDelete();
            $table->foreignId('resep_bahan_id')->constrained('resep_bahan')->cascadeOnDelete();
            $table->integer('gram_total');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('langkah_bahan');
    }
};
