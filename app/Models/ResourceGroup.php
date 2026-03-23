<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResourceGroup extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'parent_id',
        'sort_order',
        'is_active',
        'cover_image',
        'meta_title',
        'meta_description'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer'
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ResourceGroup::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(ResourceGroup::class, 'parent_id')->orderBy('sort_order');
    }

    public function resources(): HasMany
    {
        return $this->hasMany(Resource::class, 'group_id');
    }

    public function getFullPathAttribute(): string
    {
        if ($this->parent) {
            return $this->parent->full_path . '/' . $this->name;
        }
        return $this->name;
    }

    public function getAllDescendantIds(): array
    {
        $ids = [$this->id];
        foreach ($this->children as $child) {
            $ids = array_merge($ids, $child->getAllDescendantIds());
        }
        return $ids;
    }
}