<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SmsBalanceWidget extends Widget
{
    protected static string $view = 'livewire.widgets.sms-balance-widget';
    
    public $balance = null;
    public $rate = null;
    public $lastUpdated = null;
    public $isLoading = false;
    
    public function mount()
    {
        $this->loadBalance();
    }
    
    public function loadBalance()
    {
        $this->isLoading = true;
        
        try {
            $response = Http::timeout(10)
                ->post(config('services.sms_pro.url') . '/units', [
                    'pro_api_key' => config('services.sms_pro.api_key'),
                ]);
            
            if ($response->successful()) {
                $data = $response->json();
                $this->balance = (float) $data['credit_balance'];
                $this->rate = (float) $data['rate'];
                $this->lastUpdated = now();
                
                // Cache for 5 minutes
                Cache::put('sms_balance', $this->balance, now()->addMinutes(5));
                Cache::put('sms_rate', $this->rate, now()->addMinutes(5));
                Cache::put('sms_balance_updated', $this->lastUpdated, now()->addMinutes(5));
            } else {
                $this->loadFromCache();
            }
        } catch (\Exception $e) {
            Log::error('SMS Balance Widget Error: ' . $e->getMessage());
            $this->loadFromCache();
        } finally {
            $this->isLoading = false;
        }
    }
    
    protected function loadFromCache()
    {
        $this->balance = Cache::get('sms_balance');
        $this->rate = Cache::get('sms_rate');
        $this->lastUpdated = Cache::get('sms_balance_updated');
    }
    
    public function refreshBalance()
    {
        $this->loadBalance();
        $this->dispatch('balance-refreshed');
    }
    
    public function getBalanceColorProperty()
    {
        if ($this->balance === null) return 'gray';
        if ($this->balance <= 0) return 'danger';
        if ($this->balance < 10) return 'warning';
        return 'success';
    }
    
    protected function getViewData(): array
    {
        return [
            'balance' => $this->balance,
            'rate' => $this->rate,
            'lastUpdated' => $this->lastUpdated,
            'isLoading' => $this->isLoading,
            'balanceColor' => $this->getBalanceColorProperty(),
        ];
    }
}