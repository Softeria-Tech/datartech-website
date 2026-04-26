<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\Resource;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = $request->user()
            ->orders()
            ->with('resource')
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        return OrderResource::collection($orders);
    }

    public function show(Request $request, $id)
    {
        $order = $request->user()
            ->orders()
            ->with('resource')
            ->findOrFail($id);

        return new OrderResource($order);
    }

    public function purchase(Request $request)
    {
        $request->validate([
            'resource_id' => 'required|exists:resources,id',
            'payment_method' => 'required|string',
        ]);

        $user = $request->user();
        $resource = Resource::findOrFail($request->resource_id);

        // Check if already purchased
        $existingOrder = Order::where('user_id', $user->id)
            ->where('resource_id', $resource->id)
            ->where('payment_status', 'paid')
            ->exists();

        if ($existingOrder) {
            return response()->json([
                'message' => 'You have already purchased this resource.',
            ], 422);
        }

        // Check if resource is free
        $isFree = (!$resource->requires_subscription && ($resource->price == 0 || $resource->price === null));
        
        if ($isFree) {
            // Create free order
            $order = Order::create([
                'user_id' => $user->id,
                'resource_id' => $resource->id,
                'order_number' => $this->generateOrderNumber(),
                'subtotal' => 0,
                'tax' => 0,
                'total' => 0,
                'payment_method' => $request->payment_method,
                'payment_status' => 'paid',
                'order_status' => 'completed',
                'paid_at' => now(),
                'total_items' => 1,
            ]);

            return response()->json([
                'message' => 'Resource added to your library',
                'order' => new OrderResource($order),
            ]);
        }

        // For paid resources, initiate payment
        $order = Order::create([
            'user_id' => $user->id,
            'resource_id' => $resource->id,
            'order_number' => $this->generateOrderNumber(),
            'subtotal' => $resource->getFinalPrice(),
            'tax' => 0,
            'total' => $resource->getFinalPrice(),
            'payment_method' => $request->payment_method,
            'payment_status' => 'pending',
            'order_status' => 'pending',
            'total_items' => 1,
        ]);

        // Here you would integrate with your payment gateway
        // Return payment intent or redirect to payment page
        
        return response()->json([
            'requires_payment' => true,
            'order' => new OrderResource($order),
            'payment_intent' => $this->createPaymentIntent($order, $resource),
        ]);
    }

    public function paymentCallback(Request $request)
    {
        // Handle payment gateway callback/webhook
        // Update order status based on payment result
        
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'payment_status' => 'required|in:success,failed',
            'reference' => 'nullable|string',
        ]);

        $order = Order::findOrFail($request->order_id);
        
        if ($request->payment_status === 'success') {
            $order->update([
                'payment_status' => 'paid',
                'order_status' => 'completed',
                'paid_at' => now(),
                'reference' => $request->reference,
            ]);
            
            return response()->json([
                'message' => 'Payment successful',
                'order' => new OrderResource($order),
            ]);
        }
        
        $order->update([
            'payment_status' => 'failed',
            'order_status' => 'cancelled',
        ]);
        
        return response()->json([
            'message' => 'Payment failed',
            'order' => new OrderResource($order),
        ], 400);
    }

    private function generateOrderNumber(): string
    {
        return 'ORD-' . strtoupper(uniqid());
    }

    private function createPaymentIntent($order, $resource)
    {
        // Implement your payment gateway integration here
        return [
            'client_secret' => 'placeholder_secret',
            'amount' => $order->total,
            'currency' => 'KES',
        ];
    }
}