<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\MembershipPackage;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use RoundingMode;

class Subscription extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'membership_package_id',
        'order_id',
        'name',
        'type',
        'plan',
        'price',
        'quantity',
        'trial_ends_at',
        'starts_at',
        'ends_at',
        'cancelled_at',
        'next_billing_at',
        'downloads_used',
        'download_limit',
        'metadata',
    ];

    protected $casts = [
        'trial_ends_at' => 'datetime',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'next_billing_at' => 'datetime',
        'price' => 'decimal:2',
    ];

    public $tracker_start_date;
    public $tracker_end_date;

    protected $appends = [
        'download_usage',
        'tracker_start_date',
        'tracker_end_date'
    ];

    /**
     * Get the user that owns the subscription.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the membership package.
     */
    public function membershipPackage(): BelongsTo
    {
        return $this->belongsTo(MembershipPackage::class);
    }

    /**
     * Check if subscription is active.
     */
    public function isActive(): bool
    {
        if ($this->cancelled_at) {
            return false;
        }

        if ($this->ends_at && $this->ends_at->isPast()) {
            return false;
        }

        return true;
    }

    /**
     * Check if subscription is on trial.
     */
    public function onTrial(): bool
    {
        return $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }

    /**
     * Check if subscription is cancelled.
     */
    public function isCancelled(): bool
    {
        return !is_null($this->cancelled_at);
    }

    /**
     * Get remaining downloads.
     */
    public function remainingDownloads(): ?int
    {
        if (!$this->download_limit) {
            return null; // Unlimited
        }

        return max(0, $this->download_limit - $this->download_usage);
    }

    /**
     * Check if can download.
     */
    public function canDownload(): bool
    {
        if (!$this->isActive()) {
            return false;
        }

        if ($this->download_limit && $this->download_usage >= $this->download_limit) {
            return false;
        }

        return true;
    }

    /**
     * Scope active subscriptions.
     */
    public function scopeActive($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('ends_at')
                ->orWhere('ends_at', '>', now());
        })->whereNull('cancelled_at');
    }

    /**
     * Scope for membership type.
     */
    public function scopeMembership($query)
    {
        return $query->where('type', 'membership');
    }

    public function getDownloadUsageAttribute()
    {
        //Log::info('getDownloadUsageAttribute');
        $today = Carbon::today();
        $startOfSubscription = $this->starts_at;

        // 1. Calculate how many full months have passed since the start
        $monthsElapsed = $startOfSubscription->diffInMonths($today);

        // 2. Define the start of the "Current Billing Month"
        // e.g., If started Jan 15, and today is March 20, 
        // this becomes Jan 15 + 2 months = March 15.
        $currentMonthStart = $startOfSubscription->copy()->addMonths($monthsElapsed);

        // 3. Define the end of this billing cycle (one month after the current start)
        $currentMonthEnd = $currentMonthStart->copy()->addMonth()->subDay();

        $this->tracker_start_date = $currentMonthStart;
        $this->tracker_end_date = $currentMonthEnd->addDay();

        return DownloadTracker::query()
            ->where('user_id', $this->user_id)
            ->whereBetween('date', [$currentMonthStart, $currentMonthEnd])
            ->sum('downloads'); // Added sum() assuming you want the total count
    }

    public function getTrackerStartDateAttribute()
    {
        if (empty($this->tracker_start_date)) {
            $this->getDownloadUsageAttribute();
        }

        return $this->tracker_start_date;
    }

    public function getTrackerEndDateAttribute()
    {
        Log::info('getTrackerEndDateAttribute');
        if (empty($this->tracker_end_date)) {
            $this->getDownloadUsageAttribute();
        }

        return $this->tracker_end_date;
    }
}