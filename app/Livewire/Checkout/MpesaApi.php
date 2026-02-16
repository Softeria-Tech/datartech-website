<?php

namespace App\Livewire\Checkout;

use Illuminate\Support\Facades\Http;
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

    // M-PESA Payment Gateway
    public $mpesaApiUrl = 'https://sandbox.safaricom.co.ke/';
    public $mpesaEnv = 'sandbox'; // or 'production'
    public $showStkPushForm = true;

    public function initiateGateway()
    {
        $this->mpesaEnv = env('MPESA_ENVIRONMENT', 'sandbox');
        $this->mpesaApiUrl = $this->mpesaEnv === 'production'
            ? 'https://api.safaricom.co.ke/'
            : 'https://sandbox.safaricom.co.ke/';
        if ($this->mpesaEnv === 'sandbox' && in_array(strtolower(env('APP_ENV')), ['production', 'staging', 'live'])) {
            $this->showStkPushForm = false;
        }
    }

    public function initiateMpesaPayment()
    {
        $this->validate([
            'mpesaPhone' => 'required|regex:/^[0-9]{10,12}$/',
        ], [
            'mpesaPhone.required' => 'Please enter your M-PESA phone number',
            'mpesaPhone.regex' => 'Please enter a valid phone number (e.g., 0712345678)',
        ]);

        $this->processing = true;
        $this->paymentStep = 'processing';
        $this->errorMessage = '';

        $phone = $this->formatMpesaPhone($this->mpesaPhone);
        
        try {
            $response = $this->stkPush($phone, $this->order->total);
        
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

    public function formatMpesaPhone($phone)
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        if (substr($phone, 0, 1) === '0') {
            $phone = '254' . substr($phone, 1);
        } elseif (substr($phone, 0, 1) === '7') {
            $phone = '254' . $phone;
        }
        
        return $phone;
    }

    public function stkPush($phone, $amount)
    {
        $businessShortCode = env('MPESA_BUSINESS_SHORTCODE');
        $passkey = env('MPESA_PASSKEY');
        $timestamp = date('YmdHis');
        $password = base64_encode($businessShortCode . $passkey . $timestamp);
        //$callbackUrl = route('mpesa.callback');
        $callbackUrl = 'https://260f-102-217-127-105.ngrok-free.app/api/mpesa/callback';
        
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->getMpesaAccessToken(),
                'Content-Type' => 'application/json',
            ])->post('https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest', [
                'BusinessShortCode' => $businessShortCode,
                'Password' => $password,
                'Timestamp' => $timestamp,
                'TransactionType' => 'CustomerPayBillOnline',
                'Amount' => round($amount),
                'PartyA' => $phone,
                'PartyB' => $businessShortCode,
                'PhoneNumber' => $phone,
                'CallBackURL' => $callbackUrl,
                'AccountReference' => $this->order->order_number,
                'TransactionDesc' => $this->getPaymentDescription(),
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'transaction_id' => $data['CheckoutRequestID'] ?? null,
                    'message' => 'STK Push sent successfully',
                ];
            }

            Log::error('M-PESA STK Push Failed', ['response' => $response->json()]);
            return [
                'success' => false,
                'message' => 'Failed to initiate M-PESA payment',
            ];
        } catch (\Exception $e) {
            Log::error('M-PESA Exception', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'M-PESA service unavailable',
            ];
        }
    }

    public function getMpesaAccessToken()
    {
        $consumerKey = env('MPESA_CONSUMER_KEY');
        $consumerSecret = env('MPESA_CONSUMER_SECRET');
        
        $response = Http::withBasicAuth($consumerKey, $consumerSecret)
            ->get('https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials');
        
        return $response->json()['access_token'] ?? null;
    }


    public function startPaymentTimer()
    {
        $this->showTimer = true;
        $this->timerSeconds = 60;
        
        $this->dispatch('startTimer');
    }

    abstract public function getPaymentDescription();
}