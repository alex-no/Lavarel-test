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
        Schema::create('development_plan', function (Blueprint $table) {
            $table->id();
            $table->integer('sort_order')->nullable();
            $table->enum('status', ['pending', 'in_progress', 'completed'])->default('pending');
            $table->string('feature_en', 255);
            $table->string('feature_uk', 255);
            $table->string('feature_ru', 255);
            $table->string('technology_en', 512);
            $table->string('technology_uk', 512);
            $table->string('technology_ru', 512);
            $table->text('result_en')->nullable();
            $table->text('result_uk')->nullable();
            $table->text('result_ru')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            // $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('development_plan');
    }
};
