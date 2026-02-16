<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
     public function products()
    {
        return $this->hasMany(Product::class, 'product_category_id');
    }
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
