<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Add these fields if they don't exist
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('customer')->after('email');
            }
            if (!Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('role');
            }
            if (!Schema::hasColumn('users', 'avatar')) {
                $table->string('avatar')->nullable()->after('is_active');
            }
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->after('avatar');
            }
            if (!Schema::hasColumn('users', 'company')) {
                $table->string('company')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('users', 'job_title')) {
                $table->string('job_title')->nullable()->after('company');
            }
            if (!Schema::hasColumn('users', 'bio')) {
                $table->text('bio')->nullable()->after('job_title');
            }
            if (!Schema::hasColumn('users', 'address')) {
                $table->string('address')->nullable()->after('bio');
            }
            if (!Schema::hasColumn('users', 'city')) {
                $table->string('city')->nullable()->after('address');
            }
            if (!Schema::hasColumn('users', 'state')) {
                $table->string('state')->nullable()->after('city');
            }
            if (!Schema::hasColumn('users', 'zip_code')) {
                $table->string('zip_code')->nullable()->after('state');
            }
            if (!Schema::hasColumn('users', 'country')) {
                $table->string('country')->nullable()->after('zip_code');
            }
            if (!Schema::hasColumn('users', 'language')) {
                $table->string('language')->default('en')->after('country');
            }
            if (!Schema::hasColumn('users', 'timezone')) {
                $table->string('timezone')->default('America/New_York')->after('language');
            }
            if (!Schema::hasColumn('users', 'marketing_emails')) {
                $table->boolean('marketing_emails')->default(true)->after('timezone');
            }
            if (!Schema::hasColumn('users', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable()->after('updated_at');
            }
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = [
                'role', 'is_active', 'avatar', 'phone', 'company', 'job_title',
                'bio', 'address', 'city', 'state', 'zip_code', 'country',
                'language', 'timezone', 'marketing_emails', 'last_login_at'
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};