<?php

use App\Models\DownloadTracker;
use App\Models\MembershipPackage;
use App\Models\Resource;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

if (!function_exists('trackDownload')) {
    function trackDownload($rid)
    {
        if (!Auth::check()) {
            //Log::info('User not logged in');
            return;
        }

        $resource = Resource::find($rid);
        if (!$resource) {
            Log::info("No resource found with Id: $rid");
            return;
        }
        $user = Auth::user();

        $tracker = DownloadTracker::firstOrCreate([
            'user_id' => $user->id,
            'date' => Carbon::today()->format('Y-m-d')
        ]);

        $currentResources = $tracker->resources ?? [];
        $currentResources[] = $rid;
        $tracker->resources = $currentResources;

        $tracker->downloads++;
        $tracker->save();

        $active = Auth::user()->activeSubscription()->first();
        if ($active) {
            $active->downloads_used++;
            $active->save();
        }
    }
}

if (!function_exists('hasHitDownloadLimit')) {
    function hasHitDownloadLimit($rid)
    {
        if (!Auth::check()) {
            //Log::info('User not logged in');
            return false;
        }

        $resource = Resource::find($rid);
        if (!$resource) {
            Log::info("No resource found with Id: $rid");
            return false;
        }

        $active = Auth::user()->activeSubscription()->first();

        if ($resource->requires_subscription && !$active) {
            return "<a href='" . route('membership.plans') . "'>Subscribe to unlock</a>";
        }


        if (!$active) {
            return false;
        }

        $sub_limit = $active->download_limit;
        $package = $active->membershipPackage;

        $todayTracker = DownloadTracker::firstWhere([
            'user_id' => auth()->id(),
            'date' => Carbon::today()->format('Y-m-d')
        ]);

        if ($package->download_limit_per_day && $todayTracker && $package->download_limit_per_day <= $todayTracker->downloads) {
            $msg = '<a href="' . route('membership.plans') . '">Daily Download Limit Exceeded<br>Upgrade to enjoy more downloads</a>';
            Log::info($msg, ['daily limit' => $package->download_limit_per_day, 'today downloads' => $todayTracker->downloads]);
            return $msg;
        }

        if ($sub_limit && !$active->canDownload()) {
            $msg = 'Download Limit Exceeded';
            Log::info($msg, ['sub limit' => $sub_limit, 'downloads' => $active->download_usage]);
            return $msg;
        }

        /*if($package->download_limit_per_month && $package->download_limit_per_month <= $periodTrack){
            $msg = 'Download Limit Exceeded';
            Log::info($msg,['Package limit'=>$package->download_limit_per_month,'downloads'=>$periodTrack]);
            return $msg;
        }*/

        return false;
    }
}

if (!function_exists('getTrialPackage')) {
    function getTrialPackage()
    {
        return MembershipPackage::getTrialPackage();
    }
}

if (!function_exists('optimizePhone')) {
    function optimizePhone($phone)
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (str_starts_with($phone, '0')) {
            $phone = '254' . substr($phone, 1);
        }

        if (strlen($phone) === 9 && (str_starts_with($phone, '7') || str_starts_with($phone, '1'))) {
            $phone =  '254' . $phone;
        }
        if (substr($phone, 0, 3) == '254') {
            $phone = '0' . substr($phone, 3);
        }
        return $phone;
    }
}
