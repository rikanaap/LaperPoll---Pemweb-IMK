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
        Schema::create('feedbacks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resep_id')->constrained('reseps')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->float('rating');
            $table->string('description');
            $table->timestamps(); //Ini ntanrya bakal ngebuat created_at sama updated_at
        });
         Schema::create('feedback_photos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('feedback_id')->constrained('feedbacks')->cascadeOnDelete();
            $table->string('path');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product');
    }
};
