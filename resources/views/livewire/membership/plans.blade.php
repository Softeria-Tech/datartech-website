
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        <!-- Billing Cycle Toggle -->
        <div class="flex justify-center mb-10">
            <div class="relative bg-gray-100 rounded-lg p-1 flex">
                <button wire:click="selectBillingCycle('monthly')"
                        class="relative px-6 py-2 text-sm font-medium rounded-md transition-all duration-200
                               {{ $selectedBillingCycle === 'monthly' 
                                  ? 'bg-white text-gray-900 shadow' 
                                  : 'text-gray-700 hover:text-gray-900' }}">
                    Monthly
                </button>
                <button wire:click="selectBillingCycle('quarterly')"
                        class="relative px-6 py-2 text-sm font-medium rounded-md transition-all duration-200
                               {{ $selectedBillingCycle === 'quarterly' 
                                  ? 'bg-white text-gray-900 shadow' 
                                  : 'text-gray-700 hover:text-gray-900' }}">
                    Quarterly
                </button>
                <button wire:click="selectBillingCycle('yearly')"
                        class="relative px-6 py-2 text-sm font-medium rounded-md transition-all duration-200
                               {{ $selectedBillingCycle === 'yearly' 
                                  ? 'bg-white text-gray-900 shadow' 
                                  : 'text-gray-700 hover:text-gray-900' }}">
                    Yearly
                    @if($featuredPlan && $this->getSavingsPercentage($featuredPlan))
                        <span class="absolute -top-2 -right-2 bg-green-500 text-white text-xs px-2 py-1 rounded-full">
                            Save {{ $this->getSavingsPercentage($featuredPlan) }}%
                        </span>
                    @endif
                </button>
                <button wire:click="selectBillingCycle('lifetime')"
                        class="relative px-6 py-2 text-sm font-medium rounded-md transition-all duration-200
                               {{ $selectedBillingCycle === 'lifetime' 
                                  ? 'bg-white text-gray-900 shadow' 
                                  : 'text-gray-700 hover:text-gray-900' }}">
                    Lifetime
                </button>
            </div>
        </div>

        <!-- Search Bar (Optional for many plans) -->
        @if($plans->count() > 3)
        <div class="max-w-md mx-auto mb-10">
            <div class="relative">
                <input type="text" 
                       wire:model.live.debounce.300ms="search" 
                       placeholder="Search plans..."
                       class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <svg class="absolute left-3 top-2.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
        </div>
        @endif

        <!-- Plans Grid -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @foreach($plans as $plan)
                @php
                    $priceData = $this->getPriceForCycle($plan);
                    $isPopular = $plan->is_popular;
                    $savings = $this->getSavingsPercentage($plan);
                    $isInCompare = in_array($plan->id, $comparePlans);
                @endphp

                <div wire:key="plan-{{ $plan->id }}" 
                     class="relative flex flex-col rounded-2xl border {{ $isPopular ? 'border-blue-500 shadow-xl' : 'border-gray-200' }} 
                            bg-white hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                    
                    <!-- Popular Badge -->
                    @if($isPopular)
                        <div class="absolute top-0 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
                            <span class="inline-flex px-4 py-1 text-xs font-semibold tracking-wide text-white bg-gradient-to-r from-blue-600 to-purple-600 rounded-full">
                                Most Popular
                            </span>
                        </div>
                    @endif

                    <!-- Compare Checkbox -->
                    <div class="absolute top-4 right-4">
                        <button wire:click="toggleCompare({{ $plan->id }})"
                                class="p-1 rounded-full hover:bg-gray-100 transition-colors">
                            <svg class="w-5 h-5 {{ $isInCompare ? 'text-blue-600' : 'text-gray-400' }}" 
                                 fill="{{ $isInCompare ? 'currentColor' : 'none' }}" 
                                 stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </button>
                    </div>

                    <div class="p-8">
                        <!-- Plan Name & Description -->
                        <h3 class="text-2xl font-bold text-gray-900">{{ $plan->name }}</h3>
                        <p class="mt-4 text-sm text-gray-500">{{ $plan->description }}</p>

                        <!-- Price -->
                        <div class="mt-6">
                            @if($priceData['original'])
                                <div class="flex items-baseline">
                                    <span class="text-5xl font-extrabold tracking-tight text-gray-900">
                                         {{ number_format($priceData['discounted'] ?? $priceData['original'], 0) }}
                                    </span><br>
                                    <span class="ml-1 text-sm font-medium text-gray-400">
                                        /{{ $selectedBillingCycle === 'lifetime' ? 'once' : $selectedBillingCycle }}
                                    </span>
                                </div>
                                
                                @if($priceData['discounted'])
                                    <div class="mt-2">
                                        <span class="text-sm text-gray-500 line-through">
                                            {{ number_format($priceData['original'], 0) }}
                                        </span>
                                        <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Save {{ $priceData['discount_percentage'] }}%
                                        </span>
                                        @if($priceData['discount_ends_at'])
                                            <p class="text-xs text-orange-600 mt-1">
                                                Offer ends {{ $priceData['discount_ends_at']->diffForHumans() }}
                                            </p>
                                        @endif
                                    </div>
                                @endif

                                @if($savings && $selectedBillingCycle === 'yearly' && !$priceData['discounted'])
                                    <p class="mt-2 text-sm text-green-600">
                                        Save {{ $savings }}% compared to monthly
                                    </p>
                                @endif
                            @else
                                <p class="text-2xl text-gray-400">Not available</p>
                            @endif
                        </div>

                        <!-- Trial Info -->
                        @if($plan->trial_days > 0)
                            <p class="mt-4 text-sm text-blue-600 font-medium">
                                {{ $plan->trial_days }}-day free trial
                            </p>
                        @endif

                        <!-- Key Features Preview -->
                        <ul class="mt-6 space-y-4">
                            @php
                                $features = is_array($plan->features) ? $plan->features : json_decode($plan->features, true) ?? [];
                                $previewFeatures = array_slice($features, 0, 3);
                            @endphp
                            
                            @foreach($previewFeatures as $feature)
                                <li class="flex items-start">
                                    <svg class="flex-shrink-0 h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="ml-3 text-sm text-gray-700">{{ $feature }}</span>
                                </li>
                            @endforeach
                            
                            @if(count($features) > 3)
                                <li class="text-sm text-gray-500">
                                    + {{ count($features) - 3 }} more features
                                </li>
                            @endif
                        </ul>

                        <!-- Action Buttons -->
                        <div class="mt-8 space-y-3">
                            @if($priceData['original'])
                                <button wire:click="subscribe({{ $plan->id }}, '{{ $selectedBillingCycle }}')"
                                        class="w-full flex items-center justify-center px-4 py-3 border border-transparent text-sm font-medium rounded-md 
                                               {{ $isPopular 
                                                  ? 'text-white bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700' 
                                                  : 'text-blue-700 bg-blue-100 hover:bg-blue-200' }} 
                                               transition-all duration-200">
                                    Get Started
                                    <svg class="ml-2 -mr-1 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                    </svg>
                                </button>
                            @else
                                <button disabled
                                        class="w-full flex items-center justify-center px-4 py-3 border border-gray-300 text-sm font-medium rounded-md text-gray-400 bg-gray-100 cursor-not-allowed">
                                    Coming Soon
                                </button>
                            @endif
                            
                            <button wire:click="viewDetails({{ $plan->id }})"
                                    class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                                View Details
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Compare Bar (when plans selected) -->
        @if(count($comparePlans) > 0)
            <div class="fixed bottom-6 left-1/2 transform -translate-x-1/2 z-50">
                <div class="bg-white rounded-lg shadow-xl border border-gray-200 p-4 flex items-center space-x-4">
                    <span class="text-sm text-gray-600">
                        {{ count($comparePlans) }} {{ Str::plural('plan', count($comparePlans)) }} selected
                    </span>
                    <button wire:click="$set('showCompareModal', true)"
                            class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700">
                        Compare Now
                    </button>
                    <button wire:click="loadComparePlans"
                            class="p-2 text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        @endif

        <!-- Comparison Modal -->
        @if($showCompareModal && count($comparePlans) > 0)
            <div class="fixed inset-0 overflow-y-auto z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="$set('showCompareModal', false)"></div>

                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                    <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                                        Compare Membership Plans
                                    </h3>
                                    
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead>
                                                <tr>
                                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Features
                                                    </th>
                                                    @foreach($comparePlans as $planId)
                                                        @php $plan = $plans->find($planId); @endphp
                                                        <th class="px-6 py-3 bg-gray-50 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                            {{ $plan->name }}
                                                            @if($plan->is_popular)
                                                                <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                                    Popular
                                                                </span>
                                                            @endif
                                                        </th>
                                                    @endforeach
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                <!-- Price Row -->
                                                <tr>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                        Price ({{ ucfirst($selectedBillingCycle) }})
                                                    </td>
                                                    @foreach($comparePlans as $planId)
                                                        @php 
                                                            $plan = $plans->find($planId);
                                                            $priceData = $this->getPriceForCycle($plan);
                                                        @endphp
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500">
                                                            @if($priceData['original'])
                                                                @if($priceData['discounted'])
                                                                    <span class="line-through text-gray-400">${{ number_format($priceData['original'], 0) }}</span>
                                                                    <br>
                                                                    <span class="font-bold text-green-600">${{ number_format($priceData['discounted'], 0) }}</span>
                                                                @else
                                                                    {{ number_format($priceData['original'], 0) }}
                                                                @endif
                                                            @else
                                                                -
                                                            @endif
                                                        </td>
                                                    @endforeach
                                                </tr>

                                                <!-- Trial Days -->
                                                <tr>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                        Trial Days
                                                    </td>
                                                    @foreach($comparePlans as $planId)
                                                        @php $plan = $plans->find($planId); @endphp
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500">
                                                            {{ $plan->trial_days ?: '-' }}
                                                        </td>
                                                    @endforeach
                                                </tr>

                                                <!-- Download Limits -->
                                                <tr>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                        Downloads per Month
                                                    </td>
                                                    @foreach($comparePlans as $planId)
                                                        @php $plan = $plans->find($planId); @endphp
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500">
                                                            {{ $plan->download_limit_per_month ?? 'Unlimited' }}
                                                        </td>
                                                    @endforeach
                                                </tr>

                                                <!-- Early Access -->
                                                <tr>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                        Early Access
                                                    </td>
                                                    @foreach($comparePlans as $planId)
                                                        @php $plan = $plans->find($planId); @endphp
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                                            @if($plan->allows_early_access)
                                                                <svg class="h-5 w-5 text-green-500 mx-auto" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                                </svg>
                                                            @else
                                                                <svg class="h-5 w-5 text-red-500 mx-auto" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                                                </svg>
                                                            @endif
                                                        </td>
                                                    @endforeach
                                                </tr>

                                                <!-- Premium Only Access -->
                                                <tr>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                        Premium Only Resources
                                                    </td>
                                                    @foreach($comparePlans as $planId)
                                                        @php $plan = $plans->find($planId); @endphp
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                                            @if($plan->has_premium_only_access)
                                                                <svg class="h-5 w-5 text-green-500 mx-auto" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                                </svg>
                                                            @else
                                                                <span class="text-gray-400">-</span>
                                                            @endif
                                                        </td>
                                                    @endforeach
                                                </tr>

                                                <!-- All Features -->
                                                @php
                                                    $allFeatures = collect($comparePlans)
                                                        ->flatMap(fn($id) => json_decode($plans->find($id)->features, true) ?? [])
                                                        ->unique()
                                                        ->values();
                                                @endphp

                                                @foreach($allFeatures as $feature)
                                                    <tr>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                            {{ $feature }}
                                                        </td>
                                                        @foreach($comparePlans as $planId)
                                                            @php 
                                                                $plan = $plans->find($planId);
                                                                $features = json_decode($plan->features, true) ?? [];
                                                            @endphp
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                                                @if(in_array($feature, $features))
                                                                    <svg class="h-5 w-5 text-green-500 mx-auto" fill="currentColor" viewBox="0 0 20 20">
                                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                                    </svg>
                                                                @else
                                                                    <span class="text-gray-400">-</span>
                                                                @endif
                                                            </td>
                                                        @endforeach
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="button" 
                                    wire:click="$set('showCompareModal', false)"
                                    class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:w-auto sm:text-sm">
                                Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- FAQ Section -->
        <div class="mt-20">
            <h2 class="text-3xl font-bold text-center text-gray-900 mb-10">Frequently Asked Questions</h2>
            
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Can I change plans later?</h3>
                    <p class="text-gray-600">Yes, you can upgrade or downgrade your plan at any time. Changes will be reflected in your next billing cycle.</p>
                </div>
                
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">What payment methods do you accept?</h3>
                    <p class="text-gray-600">We accept all major credit cards, PayPal, and bank transfers for annual plans.</p>
                </div>
                
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Is there a refund policy?</h3>
                    <p class="text-gray-600">Yes, we offer a 30-day money-back guarantee if you're not satisfied with your membership.</p>
                </div>
                
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">How do downloads work?</h3>
                    <p class="text-gray-600">You can download resources directly from our platform. Download limits reset monthly for membership plans.</p>
                </div>
                
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Can I cancel anytime?</h3>
                    <p class="text-gray-600">Yes, you can cancel your subscription at any time from your account dashboard.</p>
                </div>
                
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">What's included in early access?</h3>
                    <p class="text-gray-600">Early access members get new resources 7 days before they're released to the public.</p>
                </div>
            </div>
        </div>

        <!-- Call to Action -->
        @if(!Auth::check())
            <div class="mt-20 bg-gradient-to-r from-blue-600 to-purple-600 rounded-2xl shadow-xl overflow-hidden">
                <div class="px-6 py-12 sm:px-12 sm:py-16 lg:flex lg:items-center lg:justify-between">
                    <div>
                        <h2 class="text-3xl font-extrabold tracking-tight text-white sm:text-4xl">
                            <span class="block">Ready to get started?</span>
                            <span class="block text-blue-200">Join thousands of creative professionals.</span>
                        </h2>
                        <p class="mt-4 text-lg leading-6 text-blue-100">
                            Get instant access to our entire library of premium resources.
                        </p>
                    </div>
                    <div class="mt-8 flex lg:mt-0 lg:flex-shrink-0">
                        <div class="inline-flex rounded-md shadow">
                            <a href="{{ route('register') }}" 
                               class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-blue-600 bg-white hover:bg-gray-50">
                                Create Free Account
                            </a>
                        </div>
                        <div class="ml-3 inline-flex rounded-md shadow">
                            <a href="{{ route('login') }}" 
                               class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-800 bg-opacity-50 hover:bg-opacity-60">
                                Sign In
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Loading Indicator -->
        <div wire:loading.flex class="fixed inset-0 bg-gray-500 bg-opacity-25 items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6 shadow-xl">
                <svg class="animate-spin h-8 w-8 text-blue-600 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <p class="mt-2 text-sm text-gray-600">Loading...</p>
            </div>
        </div>
    </div>
</div>