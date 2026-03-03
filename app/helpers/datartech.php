<?php

use App\Models\DownloadTracker;
use App\Models\Resource;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

if(!function_exists('trackDownload')){
    function trackDownload($rid){
        if(!Auth::check()){
            Log::info('User not logged in');
            return;
        }

        $resource = Resource::find($rid);
        if(!$resource){
            Log::info("No resource found with Id: $rid");
            return;
        }
        $user=Auth::user();

        $tracker = DownloadTracker::firstOrCreate([
            'user_id'=>$user->id,'date'=>Carbon::today()->format('Y-m-d')
        ]);

        $currentResources = $tracker->resources ?? [];
        $currentResources[] = $rid;
        $tracker->resources = $currentResources;

        $tracker->downloads++;
        $tracker->save();
    }
}

if(!function_exists('hasHitDownloadLimit')){
    function hasHitDownloadLimit($rid){
        if(!Auth::check()){
            Log::info('User not logged in');
            return false;
        }

        $resource = Resource::find($rid);
        if(!$resource){
            Log::info("No resource found with Id: $rid");
            return false;
        }

        $active = Auth::user()->activeSubscription()->first();

        if($resource->requires_subscription && !$active){
            return 'Subscribe to unlock';
        }

        
        if (!$active) {
            return false;
        }

        $sub_limit= $active->download_limit;
        $package=$active->membershipPackage;

        $todayTracker = DownloadTracker::firstWhere([
            'user_id'=>auth()->id(),'date'=>Carbon::today()->format('Y-m-d')
        ]);

        $periodTrack = DownloadTracker::query()
                ->where('user_id', auth()->id())
                ->whereBetween('date', [$active->starts_at, $active->ends_at])
                ->sum('downloads');

        if($package->download_limit_per_day && $todayTracker && $package->download_limit_per_day <= $todayTracker->downloads){
            $msg = '24-hour Download Limit Exceeded';
            Log::info($msg,['daily limit'=>$package->download_limit_per_day,'today downloads'=>$todayTracker->downloads]);
            return $msg;
        }

        if($sub_limit && $sub_limit <= $periodTrack){
            $msg = 'Download Limit Exceeded';
            Log::info($msg,['sub limit'=>$sub_limit,'downloads'=>$periodTrack]);
            return $msg;
        }

        if($package->download_limit_per_month && $package->download_limit_per_month <= $periodTrack){
            $msg = 'Download Limit Exceeded';
            Log::info($msg,['Package limit'=>$package->download_limit_per_month,'downloads'=>$periodTrack]);
            return $msg;
        }

        return false;
    }
}