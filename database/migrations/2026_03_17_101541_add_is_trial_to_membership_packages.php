<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsTrialToMembershipPackages extends Migration
{
    public function up()
    {
        Schema::table('membership_packages', function (Blueprint $table) {
            $table->boolean('is_trial')->default(false)->after('is_popular');
        });
        
    }

    public function down()
    {
        Schema::table('membership_packages', function (Blueprint $table) {
            $table->dropColumn('is_trial');
        });
    }
}