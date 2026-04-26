<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'membership_package_id' => $this->membership_package_id,
            'package' => new MembershipPackageResource($this->whenLoaded('membershipPackage')),
            'name' => $this->name,
            'type' => $this->type,
            'plan' => $this->plan,
            'price' => $this->price,
            'quantity' => $this->quantity,
            'trial_ends_at' => $this->trial_ends_at,
            'starts_at' => $this->starts_at,
            'ends_at' => $this->ends_at,
            'cancelled_at' => $this->cancelled_at,
            'next_billing_at' => $this->next_billing_at,
            'downloads_used' => $this->downloads_used,
            'download_limit' => $this->download_limit,
            'remaining_downloads' => $this->remainingDownloads(),
            'is_active' => $this->isActive(),
            'is_on_trial' => $this->onTrial(),
            'is_cancelled' => $this->isCancelled(),
            'download_usage' => $this->download_usage,
            'tracker_start_date' => $this->tracker_start_date,
            'tracker_end_date' => $this->tracker_end_date,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}