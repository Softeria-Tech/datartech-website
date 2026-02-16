<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('membership_packages', function (Blueprint $table) {
            $table->id();
            
            // Package Details
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->text('features')->nullable(); // JSON array of features
            
            // Pricing
            $table->decimal('price_monthly', 10, 2)->nullable();
            $table->decimal('price_yearly', 10, 2)->nullable();
            $table->decimal('price_quarterly', 10, 2)->nullable();
            $table->decimal('price_lifetime', 10, 2)->nullable();
            
            // Discounts
            $table->decimal('discount_percentage', 5, 2)->nullable();
            $table->decimal('discount_price_monthly', 10, 2)->nullable();
            $table->decimal('discount_price_yearly', 10, 2)->nullable();
            $table->timestamp('discount_ends_at')->nullable();
            
            // Subscription Settings
            $table->integer('duration_days')->nullable(); // For fixed-term subscriptions
            $table->integer('trial_days')->default(0);
            $table->integer('download_limit_per_month')->nullable(); // null = unlimited
            $table->integer('download_limit_per_day')->nullable();
            $table->boolean('has_premium_only_access')->default(false); // Exclusive resources
            
            // Access Control
            $table->json('allowed_categories')->nullable(); // Restrict to specific categories
            $table->boolean('allows_early_access')->default(false); // Get resources before public
            
            // Billing
            $table->boolean('is_popular')->default(false); // Highlighted package
            $table->integer('sort_order')->default(0);
            
            // Status
            $table->boolean('is_active')->default(true);
                        
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['is_active', 'is_popular']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('membership_packages');
    }
};