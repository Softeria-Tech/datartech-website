<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use App\Models\User;

class SmsSendWidget extends Widget
{
    use WithPagination;
    
    protected static string $view = 'livewire.widgets.sms-send-widget';
    
    public $mobiles = '';
    public $message = '';
    public $senderName = '';
    public $isLoading = false;
    public $successMessage = '';
    public $errorMessage = '';
    
    // User selection properties
    public $userSelectionMode = 'manual'; // manual, filter, all
    public $selectedUsers = [];
    public $filterType = 'all'; // all, expired_subscriptions, pending_orders, new_users
    public $userSearch = '';
    public $totalSelectedCount = 0;
    
    // Pagination
    public $perPage = 10;
    protected $paginationTheme = 'tailwind';
    
    protected $rules = [
        'mobiles' => 'required_if:userSelectionMode,manual|string|min:10',
        'message' => 'required|string|min:1|max:160',
        'senderName' => 'required|string|max:11',
    ];
    
    public function mount()
    {
        $this->senderName = config('services.sms_pro.sender_name', '');
        $this->userSelectionMode = 'manual';
    }
    
    #[On('balance-refreshed')]
    public function onBalanceRefreshed()
    {
        $this->dispatch('$refresh');
    }
    
    public function updatedUserSelectionMode($value)
    {
        $this->reset(['mobiles', 'selectedUsers', 'totalSelectedCount']);
        $this->resetPage();
        
        if ($value === 'filter') {
            $this->loadFilteredUsers();
        }
    }
    
    public function updatedFilterType()
    {
        if ($this->userSelectionMode === 'filter') {
            $this->resetPage();
            $this->loadFilteredUsers();
        }
    }
    
    public function updatedUserSearch()
    {
        if ($this->userSelectionMode === 'filter') {
            $this->resetPage();
            $this->loadFilteredUsers();
        }
    }
    
    public function updatedPerPage()
    {
        $this->resetPage();
    }
    
    public function loadFilteredUsers()
    {
        // This method is now handled by the paginated query in the view
        // We just need to reset selection when filter changes
        $this->selectedUsers = [];
        $this->totalSelectedCount = 0;
    }
    
    public function getFilteredUsersQuery()
    {
        $query = User::query()
            ->where('is_active', true)
            ->whereNotNull('phone')
            ->where('phone', '!=', '');
        
        // Apply filters based on filter type
        switch ($this->filterType) {
            case 'expired_subscriptions':
                $query->where(function($q) {
                    $q->whereHas('subscriptions', function ($subQuery) {
                        $subQuery->where('ends_at', '<', now())
                            ->whereNull('cancelled_at');
                    })->orWhereDoesntHave('subscriptions', function ($subQuery) {
                        $subQuery->whereNull('ends_at')
                            ->orWhere('ends_at', '>', now());
                    });
                });
                break;
                
            case 'pending_orders':
                $query->whereHas('orders', function ($q) {
                    $q->where('payment_status', 'pending');
                });
                break;
                
            case 'new_users':
                $query->where('created_at', '>=', now()->subDays(7));
                break;
                
            case 'all':
            default:
                // All active users with phone numbers
                break;
        }
        
        // Apply search if provided
        if (!empty($this->userSearch)) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->userSearch . '%')
                    ->orWhere('email', 'like', '%' . $this->userSearch . '%')
                    ->orWhere('phone', 'like', '%' . $this->userSearch . '%');
            });
        }
        
        return $query->orderBy('name');
    }
    
    public function getAvailableUsersProperty()
    {
        return $this->getFilteredUsersQuery()->paginate($this->perPage);
    }
    
    public function getTotalFilteredCountProperty()
    {
        return $this->getFilteredUsersQuery()->count();
    }
    
    public function selectAllFiltered()
    {
        // Get all user IDs from the filtered query (not just current page)
        $allUserIds = $this->getFilteredUsersQuery()->pluck('id')->toArray();
        $this->selectedUsers = $allUserIds;
        $this->totalSelectedCount = count($allUserIds);
        $this->dispatch('notify', message: "Selected {$this->totalSelectedCount} users", type: 'success');
    }
    
    public function clearSelection()
    {
        $this->selectedUsers = [];
        $this->totalSelectedCount = 0;
    }
    
    public function selectCurrentPage()
    {
        $currentPageUserIds = $this->getAvailableUsersProperty->pluck('id')->toArray();
        $this->selectedUsers = array_unique(array_merge($this->selectedUsers, $currentPageUserIds));
        $this->totalSelectedCount = count($this->selectedUsers);
        $this->dispatch('notify', message: "Added {$this->perPage} users to selection", type: 'success');
    }
    
    public function toggleUserSelection($userId)
    {
        if (in_array($userId, $this->selectedUsers)) {
            $this->selectedUsers = array_diff($this->selectedUsers, [$userId]);
        } else {
            $this->selectedUsers[] = $userId;
        }
        $this->totalSelectedCount = count($this->selectedUsers);
    }
    
    public function getSelectedUsersPhones()
    {
        if ($this->userSelectionMode === 'manual') {
            return $this->processMobileNumbers($this->mobiles);
        }
        
        if ($this->userSelectionMode === 'all') {
            $users = User::where('is_active', true)
                ->whereNotNull('phone')
                ->where('phone', '!=', '')
                ->get();
            return $users->pluck('phone')->toArray();
        }
        
        if ($this->userSelectionMode === 'filter') {
            $users = User::whereIn('id', $this->selectedUsers)
                ->whereNotNull('phone')
                ->where('phone', '!=', '')
                ->get();
            return $users->pluck('phone')->toArray();
        }
        
        return [];
    }
    
    public function sendSms()
    {
        if ($this->userSelectionMode === 'manual') {
            $this->validate(['mobiles' => 'required|string|min:10']);
        } elseif ($this->userSelectionMode === 'filter') {
            if (empty($this->selectedUsers)) {
                $this->errorMessage = 'Please select at least one user to send SMS.';
                return;
            }
        }
        
        $this->isLoading = true;
        $this->successMessage = '';
        $this->errorMessage = '';
        
        $mobileNumbers = $this->getSelectedUsersPhones();
        
        if (empty($mobileNumbers)) {
            $this->errorMessage = 'Please enter at least one valid phone number or select users with phone numbers.';
            $this->isLoading = false;
            return;
        }
        if(empty(trim($this->message))){
            $this->errorMessage = 'Message is Required';
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
                
                // Clear form based on mode
                if ($this->userSelectionMode === 'manual') {
                    $this->reset(['mobiles', 'message']);
                } else {
                    $this->reset(['message']);
                }
                
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
            'selection_mode' => $this->userSelectionMode,
            'filter_type' => $this->filterType,
            'timestamp' => now()
        ]);
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
        if (!$rate) return 0;
        
        if ($this->userSelectionMode === 'manual') {
            if (empty($this->mobiles)) return 0;
            $numbers = $this->processMobileNumbers($this->mobiles);
            $count = count($numbers);
        } elseif ($this->userSelectionMode === 'filter') {
            $count = $this->totalSelectedCount;
        } elseif ($this->userSelectionMode === 'all') {
            $count = User::where('is_active', true)
                ->whereNotNull('phone')
                ->where('phone', '!=', '')
                ->count();
        } else {
            return 0;
        }
        
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
    
    public function getFilterStatsProperty()
    {
        $stats = [];
        
        switch ($this->filterType) {
            case 'expired_subscriptions':
                $stats['description'] = 'Users with expired subscriptions';
                $stats['count'] = User::whereHas('subscriptions', function ($q) {
                    $q->where('ends_at', '<', now())
                        ->whereNull('cancelled_at');
                })->orWhereDoesntHave('subscriptions', function ($q) {
                    $q->whereNull('ends_at')
                        ->orWhere('ends_at', '>', now());
                })->where('is_active', true)
                ->whereNotNull('phone')
                ->where('phone', '!=', '')
                ->count();
                break;
                
            case 'pending_orders':
                $stats['description'] = 'Users with pending orders';
                $stats['count'] = User::whereHas('orders', function ($q) {
                    $q->where('payment_status', 'pending');
                })->where('is_active', true)
                ->whereNotNull('phone')
                ->where('phone', '!=', '')
                ->count();
                break;
                
            case 'new_users':
                $stats['description'] = 'Users joined in last 7 days';
                $stats['count'] = User::where('created_at', '>=', now()->subDays(7))
                    ->where('is_active', true)
                    ->whereNotNull('phone')
                    ->where('phone', '!=', '')
                    ->count();
                break;
                
            case 'all':
            default:
                $stats['description'] = 'All active users';
                $stats['count'] = User::where('is_active', true)
                    ->whereNotNull('phone')
                    ->where('phone', '!=', '')
                    ->count();
                break;
        }
        
        return $stats;
    }
}