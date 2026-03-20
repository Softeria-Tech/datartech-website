<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;

class SmsSendWidget extends Widget
{
    protected static string $view = 'livewire.widgets.sms-send-widget';
    
    public $mobiles = '';
    public $message = '';
    public $senderName = '';
    public $isLoading = false;
    public $successMessage = '';
    public $errorMessage = '';
    
    protected $rules = [
        'mobiles' => 'required|string|min:10',
        'message' => 'required|string|min:1|max:160',
        'senderName' => 'required|string|max:11',
    ];
    
    public function mount()
    {
        $this->senderName = config('services.sms_pro.sender_name', '');
    }
    
    #[On('balance-refreshed')]
    public function onBalanceRefreshed()
    {
        // Refresh any data that depends on balance
        $this->dispatch('$refresh');
    }
    
    public function sendSms()
    {
        $this->validate();
        
        $this->isLoading = true;
        $this->successMessage = '';
        $this->errorMessage = '';
        
        $mobileNumbers = $this->processMobileNumbers($this->mobiles);
        
        if (empty($mobileNumbers)) {
            $this->errorMessage = 'Please enter at least one valid phone number.';
            $this->isLoading = false;
            return;
        }
        
        try {
            $response = Http::timeout(30)
                ->post(config('services.sms_pro.url') . '/send', [
                    'pro_api_key' => config('services.sms_pro.api_key'),
                    'sender_name' => $this->senderName,
                    'mobiles' => implode(',', $mobileNumbers),
                    'message' => $this->message,
                ]);
            
            if ($response->successful()) {
                $count = count($mobileNumbers);
                $rate = Cache::get('sms_rate', 0);
                $estimatedCost = $count * $rate;
                
                $this->successMessage = "✓ SMS sent successfully to {$count} recipient(s)!";
                
                // Log the SMS
                $this->logSmsSent($mobileNumbers, $this->message, $estimatedCost);
                
                // Update stats
                $this->updateStats($count, $estimatedCost);
                
                // Refresh balance widget
                $this->dispatch('refresh-balance')->to(SmsBalanceWidget::class);
                
                // Clear form
                $this->reset(['mobiles', 'message']);
                
                session()->flash('sms_sent', $this->successMessage);
            } else {
                $this->handleApiError($response);
            }
        } catch (\Exception $e) {
            $this->errorMessage = 'Failed to send SMS. Please check your connection and try again.';
            Log::error('SMS Send API Error: ' . $e->getMessage());
        } finally {
            $this->isLoading = false;
        }
    }
    
    protected function processMobileNumbers($input)
    {
        $numbers = preg_split('/[,\n;]+/', $input);
        $validNumbers = [];
        
        foreach ($numbers as $number) {
            $number = trim($number);
            if (!empty($number) && preg_match('/^[0-9]{9,14}$/', $number)) {
                $validNumbers[] = $number;
            }
        }
        
        return array_unique($validNumbers);
    }
    
    protected function handleApiError($response)
    {
        $statusCode = $response->status();
        $errorData = $response->json();
        
        if ($statusCode === 400 && isset($errorData['msg'])) {
            $this->errorMessage = $errorData['msg'];
            
            if (isset($errorData['credit'])) {
                $this->errorMessage .= " Current balance: {$errorData['credit']} units";
                Cache::put('sms_balance', (float) $errorData['credit'], now()->addMinutes(5));
                $this->dispatch('refresh-balance')->to(SmsBalanceWidget::class);
            }
        } elseif ($statusCode === 401) {
            $this->errorMessage = 'Invalid API key. Please check configuration.';
        } elseif ($statusCode === 429) {
            $this->errorMessage = 'Too many requests. Please try again later.';
        } else {
            $this->errorMessage = "API Error (Status: {$statusCode}). Please try again.";
        }
        
        Log::warning('SMS API Error', [
            'status' => $statusCode,
            'response' => $errorData
        ]);
    }
    
    protected function logSmsSent($numbers, $message, $cost)
    {
        Log::info('SMS Sent', [
            'numbers' => $numbers,
            'message' => $message,
            'cost' => $cost,
            'user_id' => auth()->id(),
            'timestamp' => now()
        ]);
        
        /* Optional: Store in database
        if (class_exists(\App\Models\SmsLog::class)) {
            \App\Models\SmsLog::create([
                'phone_numbers' => implode(',', $numbers),
                'message' => $message,
                'recipient_count' => count($numbers),
                'cost' => $cost,
                'sender_name' => $this->senderName,
                'user_id' => auth()->id(),
                'status' => 'sent',
            ]);
        }*/
    }
    
    protected function updateStats($count, $cost)
    {
        $stats = Cache::get('sms_stats', [
            'total_sent' => 0,
            'total_cost' => 0,
            'last_sent' => null
        ]);
        
        $stats['total_sent'] += $count;
        $stats['total_cost'] += $cost;
        $stats['last_sent'] = now();
        
        Cache::put('sms_stats', $stats, now()->addDays(30));
    }
    
    public function getEstimatedCostProperty()
    {
        $rate = Cache::get('sms_rate');
        if (empty($this->mobiles) || !$rate) return 0;
        
        $numbers = $this->processMobileNumbers($this->mobiles);
        $count = count($numbers);
        
        return round($count * $rate, 2);
    }
    
    public function getMessageLengthProperty()
    {
        return strlen($this->message);
    }
    
    public function getMessageSegmentsProperty()
    {
        $length = strlen($this->message);
        if ($length <= 160) return 1;
        if ($length <= 306) return 2;
        return ceil($length / 153);
    }
}