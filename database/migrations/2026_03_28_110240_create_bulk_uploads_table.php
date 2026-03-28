<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bulk_uploads', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('group_id');

            $table->string('status')->default('pending');
            $table->decimal('price', 10, 2)->default(0.00);
            $table->unsignedBigInteger('uploaded_by')->nullable();
            $table->integer('total_files')->default(0);
            $table->integer('successful_uploads')->default(0);
            $table->integer('failed_uploads')->default(0);
            $table->json('metadata')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->unsignedBigInteger('original_category_id')->nullable();
            $table->unsignedBigInteger('original_group_id')->nullable();
            //$table->softDeletes();
            $table->timestamps();
            
            // Add foreign keys
            $table->foreign('uploaded_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('original_category_id')->references('id')->on('categories')->onDelete('set null');
            $table->foreign('original_group_id')->references('id')->on('resource_groups')->onDelete('set null');
        });

        if (!Schema::hasColumn('resources', 'bulk_upload_id')) {
            Schema::table('resources', function (Blueprint $table) {
                $table->foreignId('bulk_upload_id')
                    ->nullable()
                    ->after('group_id')
                    ->constrained('bulk_uploads')
                    ->onDelete('set null');
            });
        }
    }

    public function down(): void
    {
        Schema::table('bulk_uploads', function (Blueprint $table) {
            $table->dropForeign(['uploaded_by']);
            $table->dropForeign(['original_category_id']);
            $table->dropForeign(['original_group_id']);
            
            $table->dropColumn([
                'status',
                'uploaded_by',
                'total_files',
                'successful_uploads',
                'failed_uploads',
                'metadata',
                'completed_at',
                'original_category_id',
                'original_group_id',
                'deleted_at'
            ]);
        });

        Schema::table('resources', function (Blueprint $table) {
            $table->dropForeign(['bulk_upload_id']);
            $table->dropColumn('bulk_upload_id');
        });
    }
};