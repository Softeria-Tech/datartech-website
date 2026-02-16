<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('membership_resource', function (Blueprint $table) {
            $table->id();
            $table->foreignId('membership_package_id')->constrained()->onDelete('cascade');
            $table->foreignId('resource_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            // Prevent duplicates
            $table->unique(['membership_package_id', 'resource_id'], 'membership_resource_unique');
        });
    }

    public function down()
    {
        Schema::dropIfExists('membership_resource');
    }
};