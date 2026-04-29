<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ResourceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $user = $request->user();
        if(!$user){
            $user = Auth::user();
        }
        
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
            'file_url' =>  $this->getFileUrl(),
            'preview_url' => $this->preview_file_path ? url('/storage/' . $this->preview_file_path) : null,
            'external_url' => $this->external_url,
            
            // Membership packages
            'membership_packages' => MembershipPackageResource::collection($this->whenLoaded('membershipPackages')),
        ];
    }
    
    private function canUserAccess($user): bool
    {        
        if (!$user) {
            return false;
        }

        if (!$this->requires_subscription && $this->price==0) {
            return true;
        }
        
        if ($this->orders()->where('user_id', $user->id)->where('payment_status', 'paid')->exists()) {
            return true;
        }

        $isSubscribed = $user->subscriptions()->active()->exists();
        if ($isSubscribed) {
            return true;
        }
        
        
        return $user->orders()->where('resource_id', $this->id)->where('payment_status', 'paid')->exists();;
    }
    
    private function getFileUrl(): ?string
    {
        if ($this->delivery_type === 'url' && $this->external_url) {
            return $this->external_url;
        }
        
        if (in_array($this->delivery_type, ['upload']) && $this->file_path) {
            return Storage::url($this->file_path);
        }
        
        return null;
    }
}