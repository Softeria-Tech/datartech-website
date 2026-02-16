<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Main subscriptions table
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            
            // Customer relationship
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('membership_package_id')->nullable()->constrained()->onDelete('set null');
            
            // Stripe/Cashier fields
            $table->string('order_id')->unique()->nullable();
            
            // Subscription details
            $table->string('name'); // e.g., 'Premium Monthly', 'Basic Yearly'
            $table->string('type')->default('membership'); // membership, addon, etc.
            $table->string('plan')->nullable(); // monthly, yearly, quarterly, lifetime
            $table->decimal('price', 10, 2);
            $table->integer('quantity')->default(1);
            
            // Dates
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable(); // When subscription expires/cancels
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('next_billing_at')->nullable();
            
            // Usage tracking
            $table->integer('downloads_used')->default(0);
            $table->integer('download_limit')->nullable(); // Override package limit
            $table->text('metadata')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
        });
        
    }

    public function down(){
        Schema::dropIfExists('subscriptions');
    }
};