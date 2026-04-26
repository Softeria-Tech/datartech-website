<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_number' => $this->order_number,
            'user_id' => $this->user_id,
            'resource_id' => $this->resource_id,
            'resource' => new ResourceResource($this->whenLoaded('resource')),
            'subtotal' => $this->subtotal,
            'tax' => $this->tax,
            'total' => $this->total,
            'payment_method' => $this->payment_method,
            'reference' => $this->reference,
            'payment_status' => $this->payment_status,
            'order_status' => $this->order_status,
            'total_items' => $this->total_items,
            'paid_at' => $this->paid_at,
            'is_paid' => $this->isPaid(),
            'is_completed' => $this->isCompleted(),
            'item_display' => $this->item_display,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}