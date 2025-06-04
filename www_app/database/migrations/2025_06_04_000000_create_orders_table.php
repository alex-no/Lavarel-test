<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained('users')->onDelete('restrict')->onUpdate('cascade')->comment('User who created the order');
            $table->string('order_id', 64)->unique()->comment('Public order ID for external use');
            $table->decimal('amount', 10, 2)->comment('Payment amount');
            $table->string('currency', 3)->default('UAH')->comment('Currency code');
            $table->enum('payment_status', ['pending', 'success', 'fail', 'cancel', 'refund', 'expired'])
                ->default('pending')
                ->comment('Payment status');
            $table->string('description', 255)->nullable()->comment('Order description');

            $table->timestamp('paid_at')->nullable()->comment('Payment timestamp');
            $table->timestamps(); // created_at, updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
