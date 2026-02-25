<?php

namespace App\Livewire\Checkout;

use App\Models\MembershipPackage;
use App\Models\Order;
use App\Models\Subscription;
use Illuminate\Support\Facades\Auth;

class MembershipCheckout extends MpesaApi
{
    public $package;
    public $billingCycle;
    public $order;
    public $orderNumber;
    
    // Payment Methods
    public $paymentMethod = 'mpesa';
    public $mpesaPhone = '';
    public $mpesaProvider = 'safaricom';
   
    
    // STK Push Timer
    public $timerSeconds = 60;
    public $showTimer = false;
    
    // Price Calculation
    public $subtotal = 0;
    public $tax = 0;
    public $total = 0;
    public $taxRate = 0; 

    public $transactionId = '';
    
    protected $listeners = [
        'checkPaymentStatus' => 'checkPaymentStatus',
        'paymentCompleted' => 'paymentCompleted',
    ];

    public $existingOrder = null;

    public function mount($packageSlug, $billingCycle, $order = null)
    {
        $this->package = MembershipPackage::where('slug', $packageSlug)
            ->where('is_active', true)
            ->firstOrFail();
            
        $this->billingCycle = $billingCycle;
        
        // Check if we're resuming an existing order
        if ($order) {
            $this->orderNumber = $order;
            if($this->loadOrder()){
                return;
            }
            
        }
        
        $this->calculatePrices();
        $this->createOrder();
        $this->initiateGateway();
    }

    public function calculatePrices()
    {
        $priceField = "price_{$this->billingCycle}";
        $discountField = "discount_price_{$this->billingCycle}";
        
        $basePrice = $this->package->$priceField;
        
        // Apply discount if available
        $hasDiscount = $this->package->discount_percentage > 0 && 
                      $this->package->discount_ends_at && 
                      now()->lt($this->package->discount_ends_at);
                      
        $finalPrice = $hasDiscount && $this->package->$discountField 
            ? $this->package->$discountField 
            : $basePrice;
        
        $this->subtotal = $finalPrice;
        $this->tax = round($finalPrice * ($this->taxRate / 100), 2);
        $this->total = $this->subtotal + $this->tax;
    }

    public function loadOrder()
    {
        $this->existingOrder = Order::where('order_number', $this->orderNumber)
                ->where('user_id', Auth::id())
                ->whereIn('payment_status', ['pending', 'pending_verification'])
                ->first();

        if(!$this->existingOrder){
            return false;
        }
                
        $this->order = $this->existingOrder;
        $this->orderNumber = $this->existingOrder->order_number;
        $this->subtotal = $this->existingOrder->subtotal;
        $this->tax = $this->existingOrder->tax;
        $this->total = $this->existingOrder->total;
        
        // Pre-fill phone if available
        if (isset($this->existingOrder->order_data['mpesa_phone'])) {
            $this->mpesaPhone = $this->existingOrder->order_data['mpesa_phone'];
        }
        $this->transactionId = $this->existingOrder->reference;
        
        // Check if already paid
        if ($this->order->payment_status === 'paid') {
            return $this->redirectToSuccess($this->order->order_number);
        }
        
        if ($this->order->payment_status === 'pending_verification') {            
            $this->paymentStep = 'verify_manual';
        }else{
            $this->order->payment_status = 'pending';
            $this->order->save();
        }
        
        return true;
    }

    public function createOrder()
    {
        $this->order = Order::firstOrCreate([
            'user_id' => Auth::id(),
            'resource_id' => null,
            'order_status' => 'processing',
        ],[
            'order_number' => 'MEM-' . strtoupper(uniqid()),
            'user_id' => Auth::id(),
            'subtotal' => $this->subtotal,
            'tax' => $this->tax,
            'total' => $this->total,
            'payment_method' => 'mpesa',
            'payment_status' => 'pending',
            'order_status' => 'processing',
            'total_items' => 1,
            'order_data' => [
                'package_id' => $this->package->id,
                'package_name' => $this->package->name,
                'billing_cycle' => $this->billingCycle,
                'trial_days' => $this->package->trial_days,
                'features' => $this->package->features,
            ],
        ]);
        
        $this->orderNumber = $this->order->order_number;

        // Pre-fill phone if available
        if (isset($this->order->order_data['mpesa_phone'])) {            
            $this->mpesaPhone = $this->order->order_data['mpesa_phone'];
        }

        $this->transactionId = $this->order->reference;

        if ($this->order->payment_status === 'pending_verification') {            
            $this->paymentStep = 'verify_manual';
        }else{
            $this->order->payment_status = 'pending';
            $this->order->save();
        }
    }
    
    public function checkPaymentStatus()
    {
        $this->order->refresh();
        
        if ($this->order->payment_status === 'paid') {
            $this->paymentCompleted();
            return;
        }

        if ($this->order->payment_status === 'failed') {
            $this->paymentStep = 'failed';
            $this->errorMessage = 'Payment failed. Please try again.';
            $this->processing = false;
            return;
        }

        $this->timerSeconds -= 5;
        
        if ($this->paymentStep === 'processing' && $this->timerSeconds > 0) {
            //$this->dispatch('checkPaymentStatus')->delay(5000);
        }
    }

    public function paymentCompleted()
    {
        // Create subscription
        $this->createSubscription();
        
        $this->paymentStep = 'success';
        $this->processing = false;
        $this->showTimer = false;
        
        // Redirect to success page after 3 seconds
        $this->redirectToSuccess($this->order->order_number);
    }

    public function createSubscription()
    {
        $startDate = now();
        $endDate = $this->calculateEndDate($startDate);
        $trialEndsAt = $this->package->trial_days > 0 
            ? now()->addDays($this->package->trial_days) 
            : null;

        $subscription = Subscription::create([
            'user_id' => Auth::id(),
            'membership_package_id' => $this->package->id,
            'order_id' => $this->order->id,
            'name' => $this->package->name,
            'type' => 'membership',
            'plan' => $this->billingCycle,
            'price' => $this->total,
            'quantity' => 1,
            'trial_ends_at' => $trialEndsAt,
            'starts_at' => $startDate,
            'ends_at' => $endDate,
            'next_billing_at' => $this->billingCycle !== 'lifetime' ? $endDate : null,
            'downloads_used' => 0,
            'download_limit' => $this->package->download_limit_per_month,
            'metadata' => json_encode([
                'package_details' => [
                    'name' => $this->package->name,
                    'billing_cycle' => $this->billingCycle,
                    'has_premium_only' => $this->package->has_premium_only_access,
                    'allows_early_access' => $this->package->allows_early_access,
                ],
            ]),
        ]);

        // Update order with subscription reference
        $this->order->update([
            'order_data' => array_merge($this->order->order_data ?? [], [
                'subscription_id' => $subscription->id,
            ]),
        ]);

        return $subscription;
    }

    public function calculateEndDate($startDate)
    {
        switch ($this->billingCycle) {
            case 'monthly':
                return $startDate->copy()->addMonth();
            case 'quarterly':
                return $startDate->copy()->addMonths(3);
            case 'yearly':
                return $startDate->copy()->addYear();
            case 'lifetime':
                return null; // No end date for lifetime
            default:
                return $startDate->copy()->addDays($this->package->duration_days ?? 30);
        }
    }

    public function redirectToSuccess($orderNumber)
    {
        return redirect()->route('checkout.membership.success', $orderNumber);
    }

    public function manualConfirmPayment()
    {
        $this->validate([
            'transactionId' => 'required|string',
        ]);

        $this->paymentStep = 'verify_manual';
        
        $this->order->update([
            'reference' => $this->transactionId,
            'paid_at' => now(),
            'payment_status' => 'pending_verification',
            'reference' => $this->transactionId,
            'order_status' => 'processing',
        ]);
        
        return redirect()->route('orders.show', $this->order->order_number);
    }

    public function cancelCheckout()
    {
        // Cancel the order
        $this->order->update([
            'order_status' => 'cancelled',
        ]);
        
        return redirect()->route('membership.plans');
    }

    public function retryPayment()
    {
        $this->paymentStep = 'method';
        $this->processing = false;
        $this->errorMessage = '';
        $this->mpesaPhone = '';
        $this->transactionId = '';
    }

    public function render()
    {
        return view('livewire.checkout.membership-checkout', [
            'package' => $this->package,
            'billingCycle' => $this->billingCycle,
            'savings' => $this->calculateSavings(),
        ])->layout('frontend.layouts.library-app');
    }

    public function calculateSavings()
    {
        if ($this->billingCycle === 'yearly' && $this->package->price_monthly) {
            $monthlyTotal = $this->package->price_monthly * 12;
            $savings = $monthlyTotal - $this->package->price_yearly;
            $percentage = round(($savings / $monthlyTotal) * 100);
            
            return [
                'amount' => $savings,
                'percentage' => $percentage,
            ];
        }
        
        return null;
    }

    public function getPaymentDescription()
    {
        return 'Membership: ' . $this->package->name . ' (' . ucfirst($this->billingCycle) . ')';
    }
}