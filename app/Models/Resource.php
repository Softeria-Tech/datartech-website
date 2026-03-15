<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\UserDownload;
use App\Models\Order;
use App\Models\MembershipPackage;
use App\Models\ResourceGroup;

class Resource extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'description',
        'short_description',
        'category_id',
        'group_id',
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
        'download_count' => 'integer'
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
}