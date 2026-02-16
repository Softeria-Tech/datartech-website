<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('resource_id')->nullable()->constrained();
            
            // Amounts
            $table->decimal('subtotal', 10, 2);
            $table->decimal('tax', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            
            // Payment
            $table->string('payment_method')->nullable(); // mpesa, etc.
            $table->text('reference')->nullable(); // Transaction ID from gateway
            $table->enum('payment_status', ['pending', 'pending_verification', 'paid', 'failed', 'refunded'])->default('pending');
            $table->timestamp('paid_at')->nullable();
            
            // Status
            $table->enum('order_status', ['processing', 'completed', 'cancelled'])->default('processing');
            
            // Items summary
            $table->integer('total_items')->default(0);
            $table->text('order_data')->nullable(); // Store full cart snapshot
            
            $table->timestamps();
            
            $table->index(['user_id', 'payment_status']);
            $table->index('order_number');
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
};