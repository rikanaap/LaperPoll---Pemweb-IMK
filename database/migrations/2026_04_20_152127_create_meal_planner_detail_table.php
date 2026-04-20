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
        Schema::create('meal_planner_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meal_planner_id')->constrained('meal_planner')->cascadeOnDelete();
            $table->foreignId('resep_id')->constrained('reseps')->cascadeOnDelete();
            $table->enum('meal_time', ['SA','SI','MA']);
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meal_planner_detail');
    }
};
