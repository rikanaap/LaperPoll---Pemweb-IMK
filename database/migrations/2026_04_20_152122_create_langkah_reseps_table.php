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
        Schema::create('langkah_reseps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resep_id')->constrained('reseps')->cascadeOnDelete();
            $table->integer('step_order');
            $table->time('step_duration')->nullable();
            $table->text('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('langkah_reseps');
    }
};
