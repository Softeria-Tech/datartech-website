<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    public function membershipSuccess($orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)
            ->where('user_id', Auth::id())
            ->with('user')
            ->firstOrFail();
            
        $subscription = Subscription::where('order_id', $order->id)->firstOrFail();
            
        return view('checkout.membership-success', compact('order', 'subscription'));
    }

    /**
     * Resume resource checkout for a pending order
     */
    public function resumeResourceCheckout($orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)
            ->where('user_id', Auth::id())
            ->where('payment_status', 'pending')
            ->with('resource')
            ->firstOrFail();

        // Check if order has a resource (not membership)
        if (!$order->resource) {
            return redirect()->route('orders.show', $orderNumber)
                ->with('error', 'Invalid order type for resource checkout.');
        }

        // Redirect to the existing checkout page
        return redirect()->route('checkout.page', $orderNumber);
    }

    /**
     * Resume membership checkout for a pending order
     */
    public function resumeMembershipCheckout($orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)
            ->where('user_id', Auth::id())
            ->whereIn('payment_status', ['pending', 'pending_verification'])
            ->first();
        
        if (!$order) {
            return redirect()->route('orders.show', $orderNumber)->with('error', 'Invalid order status for resuming checkout.');
        }

        // Check if order has membership data
        if (!isset($order->order_data['package_id'])) {
            return redirect()->route('orders.show', $orderNumber)->with('error', 'Invalid order type for membership checkout.');
        }

        $packageId = $order->order_data['package_id'];
        $billingCycle = $order->order_data['billing_cycle'] ?? 'monthly';

        // Get the package
        $package = \App\Models\MembershipPackage::find($packageId);
        
        if (!$package) {
            return redirect()->route('orders.show', $orderNumber)->with('error', 'Membership package not found.');
        }

        // Redirect to membership checkout with order parameter
        return redirect()->route('checkout.membership', [
            $package->slug,$billingCycle,$orderNumber
        ]);
    }
}
