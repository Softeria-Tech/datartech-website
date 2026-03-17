<?php

namespace App\Livewire\Membership;

use Livewire\Component;
use App\Models\MembershipPackage;
use App\Models\Subscription;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class MembershipPlans extends Component
{
    public $selectedBillingCycle = 'monthly';
    public $showCompareModal = false;
    public $selectedPlan = null;
    public $comparePlans = [];
    public $search = '';
    public $sortBy = 'sort_order';
    public $sortDirection = 'asc';
    public $showInactive = false;
    
    protected $queryString = [
        'selectedBillingCycle' => ['except' => 'monthly'],
        'search' => ['except' => ''],
        'showInactive' => ['except' => false],
    ];

    public function mount()
    {
        $this->loadComparePlans();
    }

    public function selectBillingCycle($cycle)
    {
        $this->selectedBillingCycle = $cycle;
    }

    public function toggleCompare($planId)
    {
        if (in_array($planId, $this->comparePlans)) {
            $this->comparePlans = array_diff($this->comparePlans, [$planId]);
        } else {
            if (count($this->comparePlans) < 3) {
                $this->comparePlans[] = $planId;
            } else {
                session()->flash('warning', 'You can compare up to 3 plans at a time.');
            }
        }
    }

    public function loadComparePlans()
    {
        $this->comparePlans = [];
    }

    public function viewDetails($planId)
    {
        $this->selectedPlan = MembershipPackage::find($planId);
        $this->showCompareModal = true;
    }

    public function subscribe($planId, $billingCycle)
    {
        $plan = MembershipPackage::findOrFail($planId);
        $this->selectedPlan = $plan;

        if (!Auth::check()) {
            return redirect()->route('login', ['redirect' => route('membership.plans')]);
        }

        $existingSubscription = Subscription::where('user_id', Auth::id())
            ->active()
            ->first();

        if ($plan->isTrial()) {
            if (MembershipPackage::userHasUsedTrial(Auth::id())) {
                session()->flash('error', 'You have already used your trial. Please choose a paid plan to continue.');
                return;
            }

            if ($existingSubscription && $existingSubscription->membershipPackage->isTrial()) {
                session()->flash('warning', 'You already have an active subscription. Please cancel it before starting a trial.');
                return;
            }

            return redirect()->route('checkout.membership', [
                $plan->slug,
                'monthly'
            ]);
        }

        if ($existingSubscription && !$existingSubscription->membershipPackage->isTrial()) {
            Log::info("Subscribing to plan: $planId, billing cycle: $billingCycle, existing subscription: $existingSubscription->name");
            session()->flash('warning', 'You already have an active subscription. Please cancel it before purchasing a new one.');
            return;
        }

        return redirect()->route('checkout.membership', [
            $plan->slug,
            $billingCycle
        ]);
    }

    public function getPlansProperty()
    {
        return MembershipPackage::query()
            ->where('is_active', true)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->get();
    }

    public function getFeaturedPlanProperty()
    {
        return MembershipPackage::where('is_active', true)
            ->where('is_popular', true)
            ->first();
    }

    public function getPriceForCycle($plan)
    {
        // For trial packages, always show free
        if ($plan->isTrial()) {
            return [
                'original' => 0,
                'discounted' => null,
                'has_discount' => false,
                'discount_percentage' => null,
                'discount_ends_at' => null,
                'is_trial' => true,
                'trial_days' => $plan->trial_days ?: $plan->duration_days,
            ];
        }

        $prices = [
            'monthly' => $plan->price_monthly,
            'quarterly' => $plan->price_quarterly,
            'yearly' => $plan->price_yearly,
            'lifetime' => $plan->price_lifetime,
        ];

        $discountPrices = [
            'monthly' => $plan->discount_price_monthly,
            'quarterly' => $plan->price_quarterly,
            'yearly' => $plan->discount_price_yearly,
            'lifetime' => $plan->price_lifetime,
        ];

        $hasDiscount = $plan->discount_percentage > 0 && 
                      $plan->discount_ends_at && 
                      now()->lt($plan->discount_ends_at);

        $price = $prices[$this->selectedBillingCycle] ?? null;
        $discountPrice = $discountPrices[$this->selectedBillingCycle] ?? null;

        return [
            'original' => $price,
            'discounted' => $hasDiscount && $discountPrice ? $discountPrice : null,
            'has_discount' => $hasDiscount,
            'discount_percentage' => $plan->discount_percentage,
            'discount_ends_at' => $plan->discount_ends_at,
            'is_trial' => false,
        ];
    }

    public function getSavingsPercentage($plan)
    {
        if (!$plan->price_monthly || !$plan->price_yearly || $plan->isTrial()) {
            return null;
        }

        $monthlyTotal = $plan->price_monthly * 12;
        $yearlyTotal = $plan->price_yearly;
        
        if ($monthlyTotal > 0) {
            $savings = (($monthlyTotal - $yearlyTotal) / $monthlyTotal) * 100;
            return round($savings);
        }
        
        return null;
    }

    public function canUserAccessTrial()
    {
        if (!Auth::check()) {
            return true; // Non-logged in users can see trial
        }
        return !MembershipPackage::userHasUsedTrial(Auth::id());
    }

    public function render()
    {
        return view('livewire.membership.plans', [
            'plans' => $this->plans,
            'featuredPlan' => $this->featuredPlan,
            'userSubscription' => Auth::check() ? Subscription::where('user_id', Auth::id())->active()->first() : null,
            'hasUsedTrial' => Auth::check() ? ($this->selectedPlan && $this->selectedPlan->isTrial() && MembershipPackage::userHasUsedTrial(Auth::id())) : false,
        ])->layout('frontend.layouts.library-app');
    }
}