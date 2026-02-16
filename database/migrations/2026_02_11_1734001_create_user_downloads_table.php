<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_downloads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('resource_id')->constrained()->onDelete('cascade');
            
            // How they accessed it
            $table->enum('access_type', ['one_time_purchase', 'subscription', 'free', 'admin_grant'])->default('one_time_purchase');
            $table->foreignId('membership_package_id')->nullable()->constrained();
            
            // Purchase details (if one-time)
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('cascade');
            $table->decimal('amount_paid', 10, 2)->nullable();
            
            // Download tracking
            $table->integer('download_count')->default(1);
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamp('downloaded_at');
            
            $table->timestamps();
            
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_downloads');
    }
};