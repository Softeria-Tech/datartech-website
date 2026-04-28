<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'short_description' => $this->short_description,
            'parent_id' => $this->parent_id,
            'sort_order' => $this->sort_order,
            'is_visible' => $this->is_visible,
            'is_featured' => $this->is_featured,
            'thumbnail' => $this->thumbnail ? url('/storage/' . $this->thumbnail) : null,
            'cover_image' => $this->cover_image ? url('/storage/' . $this->cover_image) : null,
            'icon' => $this->icon,
            'depth' => $this->depth,
            'has_children' => $this->hasChildren(),
            'children' => CategoryResource::collection($this->whenLoaded('children')),
            'path' => $this->path,
            'full_path' => $this->full_path,
            'resources_count' => $this->resources()->count(),
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description
        ];
    }
}