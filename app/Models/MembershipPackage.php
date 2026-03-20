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
     * Check if this is a trial package
     */
    public function isTrial()
    {
        $isTrial= $this->is_trial || (
            (empty($this->price_monthly) || $this->price_monthly==0) && 
            (empty($this->price_yearly) ||  $this->price_yearly==0)&& 
            (empty($this->price_quarterly) || $this->price_quarterly==0) && 
            (empty($this->price_lifetime) || $this->price_lifetime==0));

        //Log::info("isTrial",['trial'=>$isTrial,'id'=>$this->id]);
        return $isTrial;
    }

    /**
     * @return bool|Carbon
     */
    public function calculateEndDate($amount, $startDate=false)
    {
        if(!$startDate){
            $startDate = Carbon::now();
        }

        // Handle trial packages - always return trial period
        if ($this->isTrial()) {
            return [
                'ends_at' => $startDate->copy()->addDays($this->trial_days ?: 14), // Default to 14 days if not set
                'next_billing_at' => null, // Trial doesn't auto-renew
                'billing_cycle' => 'trial',
            ];
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

    /**
     * Check if user has already used a trial
     */
    public static function userHasUsedTrial($userId)
    {
        $plan = self::getTrialPackage();
        if(!$plan) return false;

        return Subscription::active()
                ->where('user_id', $userId)
                ->where('membership_package_id', $plan->id)
                ->exists();
    }
    /**
     * Get the trial package (either by is_trial flag OR zero prices)
     */
    public static function getTrialPackage(): ?self
    {
        return self::where('is_active', true)
            ->where(function ($query) {
                $query->where('is_trial', true)
                    ->orWhere(function ($q) {
                        $q->where(fn($inner) => $inner->where('price_monthly', 0)->orWhereNull('price_monthly'))
                        ->where(fn($inner) => $inner->where('price_yearly', 0)->orWhereNull('price_yearly'))
                        ->where(fn($inner) => $inner->where('price_quarterly', 0)->orWhereNull('price_quarterly'))
                        ->where(fn($inner) => $inner->where('price_lifetime', 0)->orWhereNull('price_lifetime'));
                    });
            })
            ->first();
    }
}