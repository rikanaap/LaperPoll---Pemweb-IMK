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
        Schema::create('resep_filters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resep_id')->constrained('reseps')->cascadeOnDelete();
            $table->foreignId('filters_id')->constrained('filters')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resep_filters');
    }
};
