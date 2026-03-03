<?php

use App\Models\DownloadTracker;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $yesterday=Carbon::yesterday()->format('Y-m-d');
        $users = User::all();
        foreach ($users as $user) {
            foreach ($user->downloads as $download) {

                if(!$download->resource_id){
                    continue;
                }

                $tracker = DownloadTracker::firstOrCreate([
                    'user_id' => $user->id,
                    'date' => $yesterday
                ]);

                $tracker->downloads +=$download->download_count;
                if($tracker->downloads==0){
                    $tracker->downloads=1;                
                }
                $resources = $tracker->resources;
                $resources[]=$download->resource_id;
                $tracker->resources = array_unique(array_values($resources));

                $tracker->save();
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
