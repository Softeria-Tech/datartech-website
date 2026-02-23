<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class MembershipPackage extends Model
{
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * @return bool|Carbon
     */
    public function calculateEndDate($amount, $startDate=false)
    {
        if(!$startDate){
            $startDate = Carbon::now();
        }
        if($amount==$this->price_monthly){
            return [
                'ends_at' =>$startDate->copy()->addMonth(),
                'next_billing_at' =>$startDate->copy()->addMonth(),
                'billing_cycle' => 'monthly',
            ];
        }
        if($amount==$this->price_yearly){
            return [
                'ends_at' =>$startDate->copy()->addYear(),
                'next_billing_at' =>$startDate->copy()->addYear(),
                'billing_cycle' => 'yearly',
            ];
        }
        if($amount==$this->price_quarterly){
            return [
                'ends_at' =>$startDate->copy()->addMonths(3),
                'next_billing_at' =>$startDate->copy()->addMonths(3),
                'billing_cycle' => 'quarterly',
            ];
        }
        if($amount==$this->price_lifetime){
            return [
                'ends_at' =>$startDate->copy()->addCentury(),
                'next_billing_at' =>null,
                'billing_cycle' => 'lifetime',
            ];
        }
        Log::warning("Amount Not Recognized:::" . $amount);
        return false;
    }
}
