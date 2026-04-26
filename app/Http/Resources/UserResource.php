<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'avatar' => $this->avatar ? url('/storage/' . $this->avatar) : null,
            'phone' => $this->phone,
            'company' => $this->company,
            'job_title' => $this->job_title,
            'bio' => $this->bio,
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'zip_code' => $this->zip_code,
            'country' => $this->country,
            'language' => $this->language,
            'timezone' => $this->timezone,
            'email_verified_at' => $this->email_verified_at,
            'created_at' => $this->created_at,
            'role' => $this->role,
            'is_active' => $this->is_active,
            'subscription' => new SubscriptionResource($this->whenLoaded('activeSubscription')),
        ];
    }
}