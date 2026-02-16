<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'user_id',
        'resource_id',
        'subtotal',
        'tax',
        'total',
        'payment_method',
        'reference',
        'payment_status',
        'paid_at',
        'order_status',
        'total_items',
        'order_data',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
        'paid_at' => 'datetime',
        'order_data' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function resource(): BelongsTo
    {
        return $this->belongsTo(Resource::class);
    }

    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    public function isPending(): bool
    {
        return $this->payment_status === 'pending';
    }

    public function isCompleted(): bool
    {
        return $this->order_status === 'completed';
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = 'ORD-' . strtoupper(uniqid());
            }
            if (empty($order->total_items)) {
                $order->total_items = 1; // Single resource
            }
        });
    }

    /**
     * Get flattened order data for display
     */
    public function getFlattenedOrderDataAttribute(): array
    {
        if (empty($this->order_data)) {
            return [];
        }

        $flattened = [];
        
        foreach ($this->order_data as $key => $value) {
            $this->flattenArray($key, $value, $flattened);
        }
        
        return $flattened;
    }

    /**
     * Recursively flatten a nested array
     */
    private function flattenArray($prefix, $data, &$result)
    {
        if (is_array($data)) {
            if (array_keys($data) === range(0, count($data) - 1)) {
                $result[ucwords(str_replace('_', ' ', $prefix))] = implode(', ', $data);
            } else {
                foreach ($data as $key => $value) {
                    $newPrefix = $prefix . '.' . $key;
                    $this->flattenArray($newPrefix, $value, $result);
                }
            }
        } elseif (is_bool($data)) {
            $result[ucwords(str_replace('_', ' ', $prefix))] = $data ? 'Yes' : 'No';
        } elseif ($data === null) {
            $result[ucwords(str_replace('_', ' ', $prefix))] = 'Not set';
        } else {
            $result[ucwords(str_replace('_', ' ', $prefix))] = $data;
        }
    }

    public function getItemDisplayAttribute(): string
    {
        if ($this->resource && !empty($this->resource->title)) {
            return $this->resource->title;
        }
        
        if (!empty($this->order_data['package_name'])) {
            return $this->order_data['package_name'] . ' (Membership)';
        }
        
        return '—';
    }
}