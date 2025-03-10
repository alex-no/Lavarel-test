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
        Schema::create('language', function (Blueprint $table) {
            $table->string('code', 2)->primary(); // Используем code как Primary Key
            $table->string('short_name', 3);  
            $table->string('full_name', 32);  
            $table->boolean('is_enabled')->default(true);
            $table->tinyInteger('order');
            // $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('language');
    }
};
