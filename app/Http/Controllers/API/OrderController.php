<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Libs\MpesaGateway;
use App\Models\MembershipPackage;
use App\Models\Order;
use App\Models\Resource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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

    public function create(Request $request)
    {
        $request->validate([
            'items' => 'required',
        ]);

        $items = $request->items;
        Log::info('Order Items', $items);

        $item = $items[0];

        $orderPrefix = "";
        $total = 0;
        $subtotal = 0;
        $tax = 0;
        $extras = [];
        $resource_id = null;


        if($item['type']=='resource'){
            $orderPrefix = 'RES';
            $resource = Resource::findOrFail($item['id']);
            $total = $resource->price;
            $subtotal = $resource->price;

            $extras =  [
                'resource_title' => $resource->title,
                'resource_price' => $resource->price,
                'quantity' => 1,
                'purchased_at' => now()->toDateTimeString(),
            ];
        }elseif($item['type']=='subscription'){
            $orderPrefix = 'MEM';
            $package = MembershipPackage::findOrFail($item['id']);
            $total = $package->price_monthly;
            $subtotal = $package->price_monthly;
            $billingCycle = $item['extra_data']['plane']??'monthly';

            
            if($billingCycle == 'quarterly'){
                $total = $package->price_quarterly;
                $subtotal = $package->price_quarterly;
            }else if($billingCycle == 'yearly'){
                $total = $package->price_yearly;
                $subtotal = $package->price_yearly;
            }else if($billingCycle == 'lifetime'){
                $total = $package->price_lifetime;
                $subtotal = $package->price_lifetime;
            }


            $extras = [
                'package_id' => $package->id,
                'package_name' => $package->name,
                'billing_cycle' => $billingCycle,
                'trial_days' => $package->trial_days,
                'features' => $package->features,
                'purchased_at' => now()->toDateTimeString(),
            ];
        }else{
            return response()->json([
                'message' => 'Invalid item type',
            ], 422);
        }

        $orderData = [
            'order_number' => $orderPrefix.'-' . strtoupper(uniqid()),
            'user_id' => Auth::id(),
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $total,
            'payment_method' => 'mpesa',
            'payment_status' => 'pending',
            'order_status' => 'processing',
            'total_items' => 1,
            'order_data' => $extras,
        ];
        $order = Order::firstOrCreate([
            'user_id' => Auth::id(),
            'resource_id' => $resource_id,
            'order_status' => 'processing',
        ],$orderData);

        $order->update($orderData);
        
        return response()->json([
            'message' => 'Order created successfully',
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'total' => $order->total,
            //'order' => new OrderResource($order),
        ]);
    }

    public function show(Request $request, $id)
    {
        $order = $request->user()
            ->orders()
            ->with('resource')
            ->findOrFail($id);

        return new OrderResource($order);
    }


    public function initiatePayment(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'phone_number' => 'required'
        ]);

        $order = Order::findOrFail($request->order_id);
        $mpesaGate = new MpesaGateway();
        
        $response = $mpesaGate->stkPush($request->phone_number, $order->total, $order->order_number);

        if (!$response['success']) {
            $order->update([
                'payment_status' => 'failed',
                'order_status' => 'cancelled',
            ]);
        }
        
        return response()->json($response);
    }

}