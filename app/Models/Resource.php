<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\UserDownload;
use App\Models\Order;
use App\Models\MembershipPackage;

class Resource extends Model
{
    protected $casts = [
        'published_date'=>'datetime',
    ];

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

    public function ScopeFeatured($query){
        return $query->where('featured', true);
    }

    public function scopePublished($query){
        return $query->orderBy('is_published', true);
    }
}
