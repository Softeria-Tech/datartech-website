<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use App\Models\Resource;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'short_description',
        'parent_id',
        'sort_order',
        'is_visible',
        'is_featured',
        'thumbnail',
        'cover_image',
        'icon',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'resources_count',
        'settings',
    ];

    protected $casts = [
        'is_visible' => 'boolean',
        'is_featured' => 'boolean',
        'sort_order' => 'integer',
        'resources_count' => 'integer',
        'settings' => 'array',
    ];

    /**
     * Get the parent category.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Get the subcategories.
     */
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id')->orderBy('sort_order');
    }

    /**
     * Get all resources in this category.
     */
    public function resources(): HasMany
    {
        return $this->hasMany(Resource::class);
    }

    /**
     * Get all resources including subcategories.
     */
    public function allResources()
    {
        $categoryIds = $this->descendants()->pluck('id')->push($this->id);
        return Resource::whereIn('category_id', $categoryIds);
    }

    /**
     * Get all descendants (children, grandchildren, etc.).
     */
    public function descendants()
    {
        return $this->children()->with('descendants');
    }

    /**
     * Get all ancestors (parent, grandparent, etc.).
     */
    public function ancestors()
    {
        $ancestors = collect();
        $parent = $this->parent;
        
        while ($parent) {
            $ancestors->push($parent);
            $parent = $parent->parent;
        }
        
        return $ancestors;
    }

    /**
     * Get full path (breadcrumb).
     */
    public function getPathAttribute(): string
    {
        $names = $this->ancestors()->pluck('name')->reverse()->push($this->name);
        return $names->join(' / ');
    }

    /**
     * Get depth level (0 = root, 1 = child, 2 = grandchild, etc.).
     */
    public function getDepthAttribute(): int
    {
        return $this->ancestors()->count();
    }

    /**
     * Check if category has children.
     */
    public function hasChildren(): bool
    {
        return $this->children()->count() > 0;
    }

    /**
     * Check if category is root (no parent).
     */
    public function isRoot(): bool
    {
        return is_null($this->parent_id);
    }

    /**
     * Scope root categories.
     */
    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope visible categories.
     */
    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }

    /**
     * Scope featured categories.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Get all root categories with their children.
     */
    public static function getTree()
    {
        return self::with('children.children')
            ->root()
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });

        static::deleting(function ($category) {
            // Reassign or delete child categories
            if ($category->isRoot()) {
                $category->children()->update(['parent_id' => null]);
            }
        });
    }
}