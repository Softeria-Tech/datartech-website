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
            return $this->parent->full_path . ' / ' . $this->name;
        }
        return $this->name;
    }

    /**
     * Get the depth level of the group (0 = root/parent, 1 = sub, 2 = grand, 3 = 4th degree)
     */
    public function getDepthAttribute(): int
    {
        $depth = 0;
        $current = $this;
        
        while ($current->parent) {
            $depth++;
            $current = $current->parent;
        }
        
        return $depth;
    }

    /**
     * Get the level name based on depth
     */
    public function getLevelNameAttribute(): string
    {
        return match($this->depth) {
            0 => 'Parent Group',
            1 => 'Sub Group',
            2 => 'Grand Group',
            3 => '4th Degree Group',
            default => $this->depth . 'th Level Group'
        };
    }

    /**
     * Get the ancestor chain up to a specific level
     */
    public function getAncestors(): array
    {
        $ancestors = [];
        $current = $this;
        
        while ($current->parent) {
            array_unshift($ancestors, $current->parent);
            $current = $current->parent;
        }
        
        return $ancestors;
    }

    public function getAllDescendantIds(): array
    {
        $ids = [$this->id];
        foreach ($this->children as $child) {
            $ids = array_merge($ids, $child->getAllDescendantIds());
        }
        return $ids;
    }

    /**
     * Scope for root level groups (parent groups)
     */
    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope for sub groups (depth 1)
     */
    public function scopeSubGroups($query)
    {
        return $query->whereNotNull('parent_id')
            ->whereDoesntHave('parent.parent');
    }

    /**
     * Scope for grand groups (depth 2)
     */
    public function scopeGrandGroups($query)
    {
        return $query->whereHas('parent', function ($q) {
            $q->whereNotNull('parent_id')
                ->whereDoesntHave('parent.parent');
        });
    }

    /**
     * Scope for 4th degree groups (depth 3)
     */
    public function scopeFourthDegree($query)
    {
        return $query->whereHas('parent.parent', function ($q) {
            $q->whereNotNull('parent_id')
                ->whereDoesntHave('parent.parent');
        });
    }

    /**
     * Scope for groups at a specific depth level
     */
    public function scopeAtDepth($query, $depth)
    {
        if ($depth == 0) {
            return $query->whereNull('parent_id');
        }
        
        $query->whereNotNull('parent_id');
        
        for ($i = 1; $i < $depth; $i++) {
            $query->whereHas('parent', function ($q) use ($i, $depth) {
                if ($i == $depth - 1) {
                    // At the exact depth, ensure parent doesn't have further parents
                    if ($depth == 1) {
                        $q->whereNull('parent_id');
                    } else {
                        $q->whereHas('parent', function ($inner) use ($depth) {
                            // Build parent chain up to depth-2
                            $this->buildParentChain($inner, $depth - 2);
                        });
                    }
                } else {
                    $q->whereNotNull('parent_id');
                }
            });
        }
        
        return $query;
    }

    protected function buildParentChain($query, $levels)
    {
        if ($levels <= 0) {
            $query->whereNull('parent_id');
            return;
        }
        
        $query->whereHas('parent', function ($q) use ($levels) {
            $this->buildParentChain($q, $levels - 1);
        });
    }

    /**
     * Check if this is a parent group (root level)
     */
    public function isParent(): bool
    {
        return is_null($this->parent_id);
    }

    /**
     * Check if this is a sub group
     */
    public function isSubGroup(): bool
    {
        return !is_null($this->parent_id) && is_null($this->parent?->parent_id);
    }

    /**
     * Check if this is a grand group
     */
    public function isGrandGroup(): bool
    {
        return !is_null($this->parent_id) && 
               !is_null($this->parent?->parent_id) && 
               is_null($this->parent?->parent?->parent_id);
    }

    /**
     * Check if this is a 4th degree group
     */
    public function isFourthDegree(): bool
    {
        return !is_null($this->parent_id) && 
               !is_null($this->parent?->parent_id) && 
               !is_null($this->parent?->parent?->parent_id);
    }
}