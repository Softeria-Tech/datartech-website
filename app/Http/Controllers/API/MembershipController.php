<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\MembershipPackageResource;
use App\Http\Resources\SubscriptionResource;
use App\Models\MembershipPackage;
use App\Models\Order;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MembershipController extends Controller
{
    public function packages(Request $request)
    {
        $packages = MembershipPackage::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return MembershipPackageResource::collection($packages);
    }

    public function showPackage($id)
    {
        $package = MembershipPackage::where('is_active', true)
            ->findOrFail($id);

        return new MembershipPackageResource($package);
    }

    public function mySubscription(Request $request)
    {
        $subscription = $request->user()->activeSubscription()->first();

        if (!$subscription) {
            return response()->json([
                'message' => 'No active subscription found',
                'subscription' => null,
            ]);
        }

        return new SubscriptionResource($subscription);
    }

    public function subscribe(Request $request)
    {
        $request->validate([
            'package_id' => 'required|exists:membership_packages,id',
            'billing_cycle' => 'required|in:monthly,yearly,quarterly,lifetime',
        ]);

        $user = $request->user();
        $package = MembershipPackage::findOrFail($request->package_id);

        // Check if user already has active subscription
        $existingSubscription = $user->activeSubscription()->first();
        if ($existingSubscription) {
            return response()->json([
                'message' => 'You already have an active subscription. Please cancel it first.',
            ], 422);
        }

        // Check if user has used trial
        if ($package->isTrial() && MembershipPackage::userHasUsedTrial($user->id)) {
            return response()->json([
                'message' => 'You have already used your trial period.',
            ], 422);
        }

        // Calculate price based on billing cycle
        $price = $this->getPackagePrice($package, $request->billing_cycle);
        
        if ($price === null) {
            return response()->json([
                'message' => 'Invalid billing cycle selected.',
            ], 422);
        }

        // For paid subscriptions, initiate payment
        if ($price > 0) {
            // Here you would integrate with your payment gateway
            // Return payment intent or redirect to payment page
            return response()->json([
                'requires_payment' => true,
                'amount' => $price,
                'package' => new MembershipPackageResource($package),
                'payment_intent' => $this->createPaymentIntent($user, $package, $price, $request->billing_cycle),
            ]);
        }

        // Create free/trial subscription directly
        $subscription = $this->createSubscription($user, $package, $price, $request->billing_cycle);
        
        return response()->json([
            'message' => 'Subscription created successfully',
            'subscription' => new SubscriptionResource($subscription),
        ]);
    }

    public function cancelSubscription(Request $request)
    {
        $subscription = $request->user()
            ->subscriptions()
            ->active()
            ->first();

        if (!$subscription) {
            return response()->json([
                'message' => 'No active subscription found',
            ], 404);
        }

        $subscription->update([
            'cancelled_at' => now(),
        ]);

        return response()->json([
            'message' => 'Subscription cancelled successfully',
        ]);
    }

    private function getPackagePrice(MembershipPackage $package, string $cycle): ?float
    {
        return match($cycle) {
            'monthly' => $package->price_monthly,
            'yearly' => $package->price_yearly,
            'quarterly' => $package->price_quarterly,
            'lifetime' => $package->price_lifetime,
            default => null,
        };
    }

    private function createSubscription($user, $package, $price, $billingCycle)
    {
        $dates = $package->calculateEndDate($price);
        
        return Subscription::create([
            'user_id' => $user->id,
            'membership_package_id' => $package->id,
            'name' => $package->name,
            'type' => 'membership',
            'plan' => $billingCycle,
            'price' => $price,
            'quantity' => 1,
            'starts_at' => now(),
            'ends_at' => $dates['ends_at'],
            'next_billing_at' => $dates['next_billing_at'],
            'download_limit' => $package->download_limit_per_month,
            'downloads_used' => 0,
        ]);
    }

    private function createPaymentIntent($user, $package, $price, $billingCycle)
    {
        // Implement your payment gateway integration here
        // This is a placeholder - integrate with Stripe, PayPal, etc.
        
        return [
            'client_secret' => 'placeholder_secret',
            'amount' => $price,
            'currency' => 'KES',
        ];
    }
}