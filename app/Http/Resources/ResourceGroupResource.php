<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ResourceGroupResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'parent_id' => $this->parent_id,
            'sort_order' => $this->sort_order,
            'is_active' => $this->is_active,
            'cover_image' => $this->cover_image ? url('/storage/' . $this->cover_image) : null,
            'depth' => $this->depth,
            'level_name' => $this->level_name,
            'full_path' => $this->full_path,
            'is_parent' => $this->isParent(),
            'is_sub_group' => $this->isSubGroup(),
            'is_grand_group' => $this->isGrandGroup(),
            'children' => ResourceGroupResource::collection($this->whenLoaded('children')),
            'resources_count' => $this->resources()->count(),
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
        ];
    }
}