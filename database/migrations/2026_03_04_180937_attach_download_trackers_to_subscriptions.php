<?php

use App\Models\DownloadTracker;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $trackers = DownloadTracker::all();
        foreach ($trackers as $tracker) {
            $subscription = $tracker->user->activeSubscription()->first();
            if ($subscription) {
                $subscription->downloads_used += $tracker->downloads;
                $subscription->save();
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        
    }
};
