<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('resources', function (Blueprint $table) {
            $table->id();
            
            // Basic Information
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->text('short_description')->nullable();
            
            // Pricing
            $table->decimal('price', 10, 2)->default(0.00);
            $table->decimal('discount_price', 10, 2)->nullable();
            $table->timestamp('discount_ends_at')->nullable();
            
            // File Information
            $table->string('file_path')->nullable();
            $table->string('preview_file_path')->nullable();
            $table->string('external_url')->nullable();
            $table->enum('delivery_type', ['upload', 'url', 'both'])->default('upload');
            
            // File Details
            $table->string('file_name')->nullable();
            $table->string('file_size')->nullable();
            $table->integer('download_count')->default(0);
            
            // Resource Metadata
            
            $table->string('tags')->nullable();
            $table->string('author')->nullable();
            $table->string('publisher')->nullable();
            $table->date('published_date')->nullable();
            $table->string('isbn')->nullable();
            $table->integer('page_count')->nullable();
            $table->string('language')->default('en');
            $table->string('version')->nullable();
            
            // Status & Visibility
            $table->boolean('is_published')->default(false);
            $table->boolean('featured')->default(false);
            $table->boolean('requires_subscription')->default(false);
            $table->integer('sort_order')->default(0);
            
            // SEO
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            
            // Thumbnails & Media
            $table->string('thumbnail')->nullable();
            $table->string('cover_image')->nullable();

            $table->foreignId('category_id')
                ->nullable()
                ->constrained('categories')
                ->onDelete('set null')
                ->after('tags');
            
            // Timestamps
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for faster queries
            $table->index(['is_published', 'featured']);
            $table->index('price');
        });
    }

    public function down()
    {
        Schema::dropIfExists('resources');
    }
};
