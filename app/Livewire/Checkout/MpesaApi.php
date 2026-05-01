<?php

namespace App\Livewire\Checkout;

use App\Libs\MpesaGateway;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

abstract class MpesaApi extends Component
{
     
    // Payment Status
    public $processing = false;
    public $paymentStep = 'method'; // method, processing, success, failed
    public $errorMessage = '';
    public $transactionId = '';

    // STK Push Timer
    public $timerSeconds = 60;
    public $showTimer = false;
    
    public $showStkPushForm = true;

    /**
     * MpesaGateway
     */
    protected $mpesaGateway;
    

    public function initiateGateway()
    {
        $this->mpesaGateway = new MpesaGateway();
        if ($this->mpesaEnv === 'sandbox' && in_array(strtolower(env('APP_ENV')), ['production', 'staging', 'live'])) {
            $this->showStkPushForm = false;
        }
    }

     public function initiateMpesaPayment()
    {
        request()->validate([
            'mpesaPhone' => 'required|regex:/^[0-9]{10,12}$/',
        ], [
            'mpesaPhone.required' => 'Please enter your M-PESA phone number',
            'mpesaPhone.regex' => 'Please enter a valid phone number (e.g., 0712345678)',
        ]);

        $this->processing = true;
        $this->paymentStep = 'processing';
        $this->errorMessage = '';

        $user = auth()->user();
        $user->phone = $this->mpesaPhone;
        $user->save();

        $phone = $this->mpesaGateway->formatMpesaPhone($this->mpesaPhone);
        
        try {
            $response = $this->mpesaGateway->stkPush($phone, $this->order->total,$this->order->order_number);
            Log::info('M-PESA STK Push Response'. json_encode(['response' => $response, 'phone' => $phone, 'amount' => $this->order->total]));
            if ($response['success']) {
                $this->order->update([
                    'reference' => $response['transaction_id'],
                    'payment_method' => 'mpesa',
                    'order_data' => array_merge($this->order->order_data ?? [], [
                        'mpesa_phone' => $phone,
                        'mpesa_provider' => $this->mpesaProvider,
                    ]),
                ]);
                
                $this->transactionId = $response['transaction_id'];
                $this->startPaymentTimer();
                $this->dispatch('checkPaymentStatus')->self();
            } else {
                throw new \Exception($response['message'] ?? 'M-PESA request failed');
            }
        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
            $this->paymentStep = 'failed';
            $this->processing = false;
            Log::error('M-PESA Api Error Details', [
                'phone' => $phone,
                'amount' => $this->order->total,
                'order_id' => $this->order->id,
                'error' => $e->getMessage(),
            ]);
            Log::emergency('M-PESA Api Exception', ['exception' => $e]);
        }
    }


    public function startPaymentTimer()
    {
        $this->showTimer = true;
        $this->timerSeconds = 60;
        
        $this->dispatch('startTimer');
    }

    abstract public function getPaymentDescription();
}