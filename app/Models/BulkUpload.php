<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class BulkUpload extends Model
{
    
    protected $fillable = [
        'category_id',
        'group_id',
        'original_category_id',
        'original_group_id',
        'uploaded_by',
        'total_files',
        'successful_uploads',
        'failed_uploads',
        'metadata',
        'completed_at',
        'status',
        'price'
    ];
    
    protected $casts = [
        'metadata' => 'array',
        'completed_at' => 'datetime',
        'total_files' => 'integer',
        'successful_uploads' => 'integer',
        'failed_uploads' => 'integer',
    ];
    
    public function resources(): HasMany
    {
        return $this->hasMany(Resource::class, 'bulk_upload_id');
    }
    
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
    
    public function originalCategory(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'original_category_id');
    }
    
    public function group(): BelongsTo
    {
        return $this->belongsTo(ResourceGroup::class, 'group_id');
    }
    
    public function originalGroup(): BelongsTo
    {
        return $this->belongsTo(ResourceGroup::class, 'original_group_id');
    }
    
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
    
    public function updateResourcesCategory($newCategoryId)
    {
        $this->original_category_id = $this->category_id;
        $this->category_id = $newCategoryId;
        
        $this->resources()->update(['category_id' => $newCategoryId]);
        
        $this->save();
        
        return $this;
    }
    
    public function updateResourcesGroup($newGroupId)
    {
        $this->original_group_id = $this->group_id;
        $this->group_id = $newGroupId;
        
        $this->resources()->update(['group_id' => $newGroupId]);
        
        $this->save();
        
        return $this;
    }

    public function updateResourcesPrice($newPrice)
    {
        $this->price = $newPrice;
        
        $this->resources()->update(['price' => $newPrice]);
        
        $this->save();
        
        return $this;
    }
    
    public function deleteAllResources()
    {
        foreach ($this->resources as $resource) {
            if ($resource->file_path && Storage::disk('public')->exists($resource->file_path)) {
                Storage::disk('public')->delete($resource->file_path);
            }
            
            if ($resource->preview_file_path && Storage::disk('public')->exists($resource->preview_file_path)) {
                Storage::disk('public')->delete($resource->preview_file_path);
            }
            
            if ($resource->thumbnail && Storage::disk('public')->exists($resource->thumbnail)) {
                Storage::disk('public')->delete($resource->thumbnail);
            }
            
            if ($resource->cover_image && Storage::disk('public')->exists($resource->cover_image)) {
                Storage::disk('public')->delete($resource->cover_image);
            }
            
            $resource->delete();
        }
        
        return $this;
    }
    
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
    
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
    
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }
    
    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }
    
    protected static function boot()
    {
        parent::boot();
        
        static::deleting(function ($bulkUpload) {
            $bulkUpload->deleteAllResources();
        });
    }
}