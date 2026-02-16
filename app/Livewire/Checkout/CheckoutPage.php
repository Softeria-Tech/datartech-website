<?php

namespace App\Livewire\Checkout;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class CheckoutPage extends MpesaApi
{
    public $order;
    public $orderNumber;
    
    // Payment Methods
    public $paymentMethod = 'mpesa';
    public $mpesaPhone = '';
    public $mpesaProvider = 'safaricom'; // safaricom, airtel, telkom
    
    // Payment Status
    public $processing = false;
    public $paymentStep = 'method'; // method, processing, success, failed
    public $errorMessage = '';
    public $transactionId = '';
    
    
    protected $listeners = [
        'checkPaymentStatus' => 'checkPaymentStatus',
        'paymentCompleted' => 'paymentCompleted',
    ];

    public function mount($order = null)
    {
        if ($order) {
            $this->orderNumber = $order;
            $this->loadOrder();
        }
        $this->initiateGateway();
    }

    public function loadOrder()
    {
        $this->order = Order::with(['resource', 'user'])
            ->where('order_number', $this->orderNumber)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // Check if already paid
        if ($this->order->payment_status === 'paid') {
            return redirect()->route('checkout.success', $this->order->order_number);
        }
        
        // Pre-fill phone if available
        if (isset($this->order->order_data['mpesa_phone'])) {            
            $this->mpesaPhone = $this->order->order_data['mpesa_phone'];
        }
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
        
        // Continue checking
        if ($this->paymentStep === 'processing' && $this->timerSeconds > 0) {
            //$this->dispatch('checkPaymentStatus')->delay(5000);
        }
    }

    public function paymentCompleted()
    {
        $this->paymentStep = 'success';
        $this->processing = false;
        $this->showTimer = false;
        
        // Create download access
        $this->order->user->downloads()->firstOrCreate([
            'order_id' => $this->order->id,
            'resource_id' => $this->order->resource_id,
        ],[
            'resource_id' => $this->order->resource_id,
            'order_id' => $this->order->id,
            'access_type' => 'one_time_purchase',
            'amount_paid' => $this->order->total,
            'downloaded_at' => null,
            'download_count' => 0,
        ]);

        // Redirect to success page after 3 seconds
        $this->redirectToSuccess($this->order->order_number);
    }

    public function redirectToSuccess($order)
    {
        return redirect()->route('checkout.success', $order);
    }

    public function manualConfirmPayment()
    {
        $this->validate([
            'transactionId' => 'required|string',
        ]);

        $this->paymentStep = 'verify_manual';
        $this->processing = false;
        $this->showTimer = false;

        // In production, verify this transaction ID with M-PESA API
        $this->order->update([
            'paid_at' => now(),
            'order_status' => 'processing',
            'payment_status' => 'pending_verification',
            'reference' => $this->transactionId,
        ]);

        return redirect()->route('orders.show', $this->order->order_number);
    }

    public function cancelCheckout()
    {
        return redirect()->route('library.resource.detail', $this->order->resource->slug);
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
        return view('livewire.checkout.checkout-page')
            ->layout('frontend.layouts.library-app');
    }

    public function getPaymentDescription()
    {
        return 'Purchase: ' . $this->order->resource->title;
    }
}