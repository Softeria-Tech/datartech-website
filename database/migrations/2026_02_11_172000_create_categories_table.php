<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            
            // Category Information
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->text('short_description')->nullable();
            
            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('categories')
                ->onDelete('cascade');
            
            $table->integer('sort_order')->default(0);
            $table->boolean('is_visible')->default(true);
            $table->boolean('is_featured')->default(false);
            
            $table->string('thumbnail')->nullable();
            $table->string('cover_image')->nullable();
            $table->string('icon')->nullable();
            
            // SEO
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            
            // Resource Count (cached)
            $table->integer('resources_count')->default(0);
            
            // Metadata
            $table->json('settings')->nullable(); 
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['parent_id', 'sort_order']);
            $table->index('is_visible');
            $table->index('slug');
        });
    }

    public function down()
    {
        Schema::dropIfExists('categories');
    }
};