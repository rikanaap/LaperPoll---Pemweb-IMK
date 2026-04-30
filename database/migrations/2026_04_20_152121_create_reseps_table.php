<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reseps', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->string('title');
            $table->text('description')->nullable();

            $table->time('cook_duration');

            $table->integer('calorie')->nullable();

            $table->float('current_star')->default(0);
            $table->integer('views_count')->default(0);

            $table->string('thumbnail')->nullable();

            $table->foreignId('main_filter_id')
                  ->nullable()
                  ->constrained('filters')
                  ->nullOnDelete();

            $table->boolean('is_published')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reseps');
    }
};