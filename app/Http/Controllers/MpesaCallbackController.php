<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\UserDownload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class MpesaCallbackController extends Controller
{
    /**
     * Handle M-PESA STK Push Callback
     */
    public function callback(Request $request)
    {
        // Log the entire callback for debugging
        Log::info('M-PESA Callback Received', $request->all());

        try {
            // Get the callback data
            $callbackData = $request->all();
            
            // Check if it's from STK Push
            if (isset($callbackData['Body']['stkCallback'])) {
                return $this->handleStkCallback($callbackData);
            }
            
            // Check if it's from C2B (if you're using that)
            if (isset($callbackData['TransactionType'])) {
                return $this->handleC2BCallback($callbackData);
            }

            return response()->json([
                'ResultCode' => 1,
                'ResultDesc' => 'Unknown callback type'
            ]);

        } catch (\Exception $e) {
            Log::error('M-PESA Callback Error: ' . $e->getMessage());
            
            return response()->json([
                'ResultCode' => 1,
                'ResultDesc' => 'Internal server error'
            ]);
        }
    }

    /**
     * Handle STK Push Callback
     */
    private function handleStkCallback($data)
    {
        $callback = $data['Body']['stkCallback'];
        
        $merchantRequestID = $callback['MerchantRequestID'];
        $checkoutRequestID = $callback['CheckoutRequestID'];
        $resultCode = $callback['ResultCode'];
        $resultDesc = $callback['ResultDesc'];

        // Log the STK callback
        Log::info('STK Callback', [
            'MerchantRequestID' => $merchantRequestID,
            'CheckoutRequestID' => $checkoutRequestID,
            'ResultCode' => $resultCode,
            'ResultDesc' => $resultDesc
        ]);

        // Find order by CheckoutRequestID (stored in reference field)
        $order = Order::where('reference', $checkoutRequestID)->first();

        if (!$order) {
            Log::error('Order not found for CheckoutRequestID: ' . $checkoutRequestID);
            
            return response()->json([
                'ResultCode' => 1,
                'ResultDesc' => 'Order not found'
            ]);
        }

        // Check if payment was successful (ResultCode 0 means success)
        if ($resultCode == 0) {
            // Get callback metadata
            $metadata = $callback['CallbackMetadata']['Item'] ?? [];
            
            $amount = null;
            $mpesaReceiptNumber = null;
            $transactionDate = null;
            $phoneNumber = null;

            // Extract metadata
            foreach ($metadata as $item) {
                switch ($item['Name']) {
                    case 'Amount':
                        $amount = $item['Value'];
                        break;
                    case 'MpesaReceiptNumber':
                        $mpesaReceiptNumber = $item['Value'];
                        break;
                    case 'TransactionDate':
                        $transactionDate = $item['Value'];
                        break;
                    case 'PhoneNumber':
                        $phoneNumber = $item['Value'];
                        break;
                }
            }

            // Update order as paid
            $order->update([
                'payment_status' => 'paid',
                'paid_at' => now(),
                'reference' => $mpesaReceiptNumber,
                'order_status' => 'completed',
                'order_data' => array_merge($order->order_data ?? [], [
                    'mpesa' => [
                        'amount' => $amount,
                        'receipt' => $mpesaReceiptNumber,
                        'transaction_date' => $transactionDate,
                        'phone' => $phoneNumber,
                        'checkout_request_id' => $checkoutRequestID,
                    ]
                ]),
            ]);

            // Create download access
            UserDownload::create([
                'user_id' => $order->user_id,
                'resource_id' => $order->resource_id,
                'order_id' => $order->id,
                'access_type' => 'one_time_purchase',
                'amount_paid' => $order->total,
                'downloaded_at' => null,
                'download_count' => 0,
            ]);

            Log::info('Order marked as paid', [
                'order_number' => $order->order_number,
                'receipt' => $mpesaReceiptNumber
            ]);

            // Send confirmation email
            $this->sendPaymentConfirmation($order, $mpesaReceiptNumber);

            return response()->json([
                'ResultCode' => 0,
                'ResultDesc' => 'Success'
            ]);

        } else {
            // Payment failed
            $order->update([
                'payment_status' => 'failed',
                'order_data' => array_merge($order->order_data ?? [], [
                    'mpesa_error' => [
                        'code' => $resultCode,
                        'description' => $resultDesc,
                        'checkout_request_id' => $checkoutRequestID,
                    ]
                ]),
            ]);

            Log::warning('M-PESA payment failed', [
                'order' => $order->order_number,
                'result_code' => $resultCode,
                'result_desc' => $resultDesc
            ]);

            return response()->json([
                'ResultCode' => $resultCode,
                'ResultDesc' => $resultDesc
            ]);
        }
    }

    /**
     * Handle C2B Callback (if using paybill)
     */
    private function handleC2BCallback($data)
    {
        $transactionType = $data['TransactionType'];
        $transID = $data['TransID'];
        $transTime = $data['TransTime'];
        $transAmount = $data['TransAmount'];
        $businessShortCode = $data['BusinessShortCode'];
        $billRefNumber = $data['BillRefNumber']; // This should be your order number
        $invoiceNumber = $data['InvoiceNumber'] ?? null;
        $orgAccountBalance = $data['OrgAccountBalance'] ?? null;
        $thirdPartyTransID = $data['ThirdPartyTransID'] ?? null;
        $msisdn = $data['MSISDN']; // Customer phone
        $firstName = $data['FirstName'] ?? '';
        $middleName = $data['MiddleName'] ?? '';
        $lastName = $data['LastName'] ?? '';

        Log::info('C2B Callback', [
            'TransID' => $transID,
            'BillRefNumber' => $billRefNumber,
            'Amount' => $transAmount
        ]);

        // Find order by order_number (from BillRefNumber)
        $order = Order::where('order_number', $billRefNumber)->first();

        if (!$order) {
            Log::error('Order not found for BillRefNumber: ' . $billRefNumber);
            
            return response()->json([
                'ResultCode' => 1,
                'ResultDesc' => 'Order not found'
            ]);
        }

        // Update order as paid
        $order->update([
            'payment_status' => 'paid',
            'paid_at' => now(),
            'reference' => $transID,
            'order_data' => array_merge($order->order_data ?? [], [
                'mpesa_c2b' => [
                    'transaction_id' => $transID,
                    'amount' => $transAmount,
                    'time' => $transTime,
                    'phone' => $msisdn,
                    'name' => trim($firstName . ' ' . $middleName . ' ' . $lastName),
                ]
            ]),
        ]);

        // Create download access
        UserDownload::create([
            'user_id' => $order->user_id,
            'resource_id' => $order->resource_id,
            'order_id' => $order->id,
            'access_type' => 'one_time_purchase',
            'amount_paid' => $order->total,
            'downloaded_at' => null,
            'download_count' => 0,
        ]);

        // Send confirmation
        $this->sendPaymentConfirmation($order, $transID);

        return response()->json([
            'ResultCode' => 0,
            'ResultDesc' => 'Success'
        ]);
    }

    /**
     * Send payment confirmation email
     */
    private function sendPaymentConfirmation($order, $receiptNumber)
    {
        try {
            // You can implement email sending here
            // Mail::to($order->user->email)->send(new PaymentConfirmation($order, $receiptNumber));
            
            Log::info('Payment confirmation would be sent', [
                'user' => $order->user->email,
                'order' => $order->order_number,
                'receipt' => $receiptNumber
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send payment confirmation: ' . $e->getMessage());
        }
    }

    /**
     * Test callback endpoint (for development only)
     */
    public function testCallback(Request $request)
    {
        // Simulate a successful STK Push callback
        $testData = [
            'Body' => [
                'stkCallback' => [
                    'MerchantRequestID' => 'TEST-' . uniqid(),
                    'CheckoutRequestID' => $request->input('checkout_id', 'ws_CO_ ' . date('YmdHis')),
                    'ResultCode' => 0,
                    'ResultDesc' => 'The service request is processed successfully.',
                    'CallbackMetadata' => [
                        'Item' => [
                            ['Name' => 'Amount', 'Value' => $request->input('amount', 100)],
                            ['Name' => 'MpesaReceiptNumber', 'Value' => 'TEST' . rand(100000, 999999)],
                            ['Name' => 'TransactionDate', 'Value' => date('YmdHis')],
                            ['Name' => 'PhoneNumber', 'Value' => $request->input('phone', '254712345678')],
                        ]
                    ]
                ]
            ]
        ];

        return $this->callback(new Request($testData));
    }
}