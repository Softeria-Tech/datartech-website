<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ResourceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $user = $request->user();
        $canAccess = $this->canUserAccess($user);
        
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'short_description' => $this->short_description,
            'category_id' => $this->category_id,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'group_id' => $this->group_id,
            'group' => new ResourceGroupResource($this->whenLoaded('group')),
            'language' => $this->language,
            'author' => $this->author,
            'publisher' => $this->publisher,
            'published_date' => $this->published_date,
            'version' => $this->version,
            'page_count' => $this->page_count,
            'isbn' => $this->isbn,
            'price' => $this->price,
            'discount_price' => $this->discount_price,
            'discount_ends_at' => $this->discount_ends_at,
            'has_discount' => $this->discount_price && (!$this->discount_ends_at || $this->discount_ends_at->isFuture()),
            'requires_subscription' => $this->requires_subscription,
            'delivery_type' => $this->delivery_type,
            'file_name' => $this->file_name,
            'file_size' => $this->file_size,
            'formatted_file_size' => $this->formatted_file_size,
            'thumbnail' => $this->thumbnail ? url('/storage/' . $this->thumbnail) : null,
            'cover_image' => $this->cover_image ? url('/storage/' . $this->cover_image) : null,
            'is_published' => $this->is_published,
            'featured' => $this->featured,
            'download_count' => $this->download_count,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            
            // Access related fields (conditional based on user access)
            'can_access' => $canAccess,
            'file_url' => $canAccess ? $this->getFileUrl() : null,
            'preview_url' => $this->preview_file_path ? url('/storage/' . $this->preview_file_path) : null,
            'external_url' => $this->external_url,
            
            // Membership packages
            'membership_packages' => MembershipPackageResource::collection($this->whenLoaded('membershipPackages')),
        ];
    }
    
    private function canUserAccess($user): bool
    {
        if (!$this->requires_subscription) {
            return true;
        }
        
        if (!$user) {
            return false;
        }
        
        // Check if user has purchased this resource
        if ($this->orders()->where('user_id', $user->id)->where('payment_status', 'paid')->exists()) {
            return true;
        }
        
        // Check if user has active subscription that includes this resource
        $subscription = $user->activeSubscription()->first();
        if ($subscription && $subscription->membershipPackage) {
            $allowedCategories = $subscription->membershipPackage->allowed_categories;
            if (empty($allowedCategories) || in_array($this->category_id, $allowedCategories)) {
                return true;
            }
        }
        
        return false;
    }
    
    private function getFileUrl(): ?string
    {
        if ($this->delivery_type === 'url' && $this->external_url) {
            return $this->external_url;
        }
        
        if (in_array($this->delivery_type, ['upload', 'both']) && $this->file_path) {
            return url('/api/resources/' . $this->id . '/download');
        }
        
        return null;
    }
}