<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\UserDownload;
use App\Models\Order;
use App\Models\MembershipPackage;
use App\Models\ResourceGroup;
use Illuminate\Support\Facades\Storage;

class Resource extends Model
{    
    protected $fillable = [
        'title',
        'slug',
        'description',
        'short_description',
        'category_id',
        'group_id',
        'bulk_upload_id', // Add this if you have it
        'language',
        'author',
        'publisher',
        'published_date',
        'version',
        'page_count',
        'isbn',
        'price',
        'discount_price',
        'discount_ends_at',
        'requires_subscription',
        'delivery_type',
        'file_path',
        'preview_file_path',
        'external_url',
        'file_name',
        'file_size',
        'thumbnail',
        'cover_image',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'tags',
        'is_published',
        'featured',
        'sort_order',
        'download_count'
    ];

    protected $casts = [
        'published_date' => 'datetime',
        'discount_ends_at' => 'datetime',
        'is_published' => 'boolean',
        'featured' => 'boolean',
        'requires_subscription' => 'boolean',
        'price' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'page_count' => 'integer',
        'sort_order' => 'integer',
        'download_count' => 'integer',
        'deleted_at' => 'datetime',
    ];

    static $delivery_type_upload = 'upload';
    static $delivery_type_url = 'url';
    static $delivery_type_both = 'both';

    public function downloads()
    {
        return $this->hasMany(UserDownload::class);
    }

    public function membershipPackages()
    {
        return $this->belongsToMany(MembershipPackage::class, 'membership_resource', 'resource_id', 'membership_package_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function group()
    {
        return $this->belongsTo(ResourceGroup::class);
    }
    
    public function bulkUpload()
    {
        return $this->belongsTo(BulkUpload::class, 'bulk_upload_id');
    }

    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeInGroup($query, $groupId)
    {
        return $query->where('group_id', $groupId);
    }

    public function getFormattedFileSizeAttribute()
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }
    
    /**
     * Delete all associated files from storage
     */
    public function deleteFiles(): void
    {
        $disk = Storage::disk('public');
        
        // Delete main file
        if ($this->file_path && $disk->exists($this->file_path)) {
            $disk->delete($this->file_path);
        }
        
        // Delete preview file
        if ($this->preview_file_path && $disk->exists($this->preview_file_path)) {
            $disk->delete($this->preview_file_path);
        }
        
        // Delete thumbnail
        if ($this->thumbnail && $disk->exists($this->thumbnail)) {
            $disk->delete($this->thumbnail);
        }
        
        // Delete cover image
        if ($this->cover_image && $disk->exists($this->cover_image)) {
            $disk->delete($this->cover_image);
        }
        
        // Delete any directories that might be empty
        $this->cleanupEmptyDirectories();
    }
    
    /**
     * Clean up empty directories after file deletion
     */
    protected function cleanupEmptyDirectories(): void
    {
        $disk = Storage::disk('public');
        
        $directories = [
            dirname($this->file_path),
            dirname($this->preview_file_path),
            dirname($this->thumbnail),
            dirname($this->cover_image),
        ];
        
        foreach (array_unique($directories) as $directory) {
            if ($directory && $directory !== '.' && $directory !== 'resources') {
                $files = $disk->files($directory);
                $directories = $disk->directories($directory);
                
                if (empty($files) && empty($directories)) {
                    $disk->deleteDirectory($directory);
                }
            }
        }
    }
    
    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();
        
        static::deleting(function ($resource) {
            $resource->deleteFiles();
        });        
    }

    public function getFinalPrice(): float
    {
        if ($this->discount_price && (!$this->discount_ends_at || $this->discount_ends_at->isFuture())) {
            return (float) $this->discount_price;
        }
        
        return (float) ($this->price ?? 0);
    }

    public function canUserAccess($user = null): bool
    {
        if (!$this->requires_subscription && ($this->price == 0 || $this->price === null)) {
            return true;
        }
        
        if (!$user) {
            return false;
        }
        
        // Check if purchased
        if ($this->orders()->where('user_id', $user->id)->where('payment_status', 'paid')->exists()) {
            return true;
        }
        
        // Check subscription
        $subscription = $user->activeSubscription()->first();
        if ($subscription && $subscription->membershipPackage) {
            $allowedCategories = $subscription->membershipPackage->allowed_categories;
            if (empty($allowedCategories) || in_array($this->category_id, $allowedCategories)) {
                return true;
            }
        }
        
        return false;
    }
}