<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MembershipPackageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'features' => $this->features,
            'price_monthly' => $this->price_monthly,
            'price_yearly' => $this->price_yearly,
            'price_quarterly' => $this->price_quarterly,
            'price_lifetime' => $this->price_lifetime,
            'discount_percentage' => $this->discount_percentage,
            'discount_price_monthly' => $this->discount_price_monthly,
            'discount_price_yearly' => $this->discount_price_yearly,
            'discount_ends_at' => $this->discount_ends_at,
            'has_discount' => $this->discount_percentage > 0 && (!$this->discount_ends_at || $this->discount_ends_at->isFuture()),
            'duration_days' => $this->duration_days,
            'trial_days' => $this->trial_days,
            'download_limit_per_month' => $this->download_limit_per_month,
            'download_limit_per_day' => $this->download_limit_per_day,
            'has_premium_only_access' => $this->has_premium_only_access,
            'allows_early_access' => $this->allows_early_access,
            'allowed_categories' => $this->allowed_categories,
            'is_popular' => $this->is_popular,
            'sort_order' => $this->sort_order,
            'is_active' => $this->is_active,
            'is_trial' => $this->isTrial(),
        ];
    }
}