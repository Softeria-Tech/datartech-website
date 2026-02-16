<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
     public function productCategory()
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
