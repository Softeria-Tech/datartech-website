<?php

namespace App\Libs;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class MpesaGateway{

    // M-PESA Payment Gateway
    public $mpesaApiUrl = 'https://sandbox.safaricom.co.ke/';
    public $mpesaEnv = 'sandbox'; // or 'production'

    public function __construct()
    {
        $this->mpesaEnv = env('MPESA_ENVIRONMENT', 'sandbox');
        $this->mpesaApiUrl = $this->mpesaEnv === 'production'
            ? 'https://api.safaricom.co.ke/'
            : 'https://sandbox.safaricom.co.ke/';        
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

    public function stkPush($phone, $amount, $orderNumber)
    {
        $phone = $this->formatMpesaPhone($phone);

        $partnerName = env('SOFTERIA_WALLET_SECRET', 'datartech2021');
        $baseUrl = env('SOFTERIA_WALLET_URL', 'https://billing.softeriatech.com/api/v1/');
        $url = $baseUrl . 'partner/checkout';

        $callbackUrl = route('mpesa.callback',['order'=>$orderNumber]);//
        //$callbackUrl = 'https://6e7d-102-217-127-98.ngrok-free.app/api/mpesa/callback?order=' . $this->order->order_number;

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $partnerName,
                'Content-Type' => 'application/json',
            ])->post($url, [
                'phone' => $phone,
                'amount' => round($amount),
                'callback' => $callbackUrl,
                'username' => $partnerName,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'transaction_id' => $data['transaction_id'] ?? null,
                    'message' => 'STK Push sent successfully',
                ];
            }else{
                Log::error('Softeria Wallet STK Push Failed', ['response' => $response->body(), 'error' => $response->error()]);
                throw new Exception('Failed to initiate M-PESA payment: ' . $response->error() ?? 'Unknown error');
            }
        } catch (Exception $e) {
            Log::error('Softeria Wallet STK Push Failed', ['response' => '', 'error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'Failed to initiate M-PESA payment',
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

}