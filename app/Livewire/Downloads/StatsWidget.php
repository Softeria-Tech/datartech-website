<?php

namespace App\Livewire\Downloads;

use Livewire\Component;
use App\Models\UserDownload;
use App\Models\Subscription;
use Illuminate\Support\Facades\Auth;

class StatsWidget extends Component
{
    public $stats = [];
    public $subscription = null;

    protected $listeners = ['refreshStats' => 'loadStats'];

    public function mount()
    {
        $this->loadStats();
    }

    public function loadStats()
    {
        $userId = Auth::id();
        
        $this->subscription = Subscription::where('user_id', $userId)
            ->active()
            ->first();

        $downloads = UserDownload::where('user_id', $userId);

        $this->stats = [
            'total' => $downloads->count(),
            'this_month' => (clone $downloads)
                ->whereMonth('downloaded_at', now()->month)
                ->whereYear('downloaded_at', now()->year)
                ->count(),
            'membership' => (clone $downloads)
                ->where('access_type', 'membership')
                ->count(),
            'purchased' => (clone $downloads)
                ->where('access_type', 'purchase')
                ->count(),
            'remaining_downloads' => $this->subscription ? 
                $this->subscription->remainingDownloads() : null,
        ];
    }

    public function render()
    {
        return view('livewire.downloads.stats-widget');
    }
}