<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6">
        <h3 class="text-lg font-semibold mb-4">Download Statistics</h3>
        
        <div class="space-y-4">
            <div class="flex justify-between items-center pb-2 border-b border-gray-200">
                <span class="text-gray-600">Total Downloads</span>
                <span class="font-semibold text-lg">{{ $stats['total'] }}</span>
            </div>
            
            <div class="flex justify-between items-center pb-2 border-b border-gray-200">
                <span class="text-gray-600">This Month</span>
                <span class="font-semibold text-lg">{{ $stats['this_month'] }}</span>
            </div>
            
            <div class="flex justify-between items-center pb-2 border-b border-gray-200">
                <span class="text-gray-600">Membership Downloads</span>
                <span class="font-semibold text-lg">{{ $stats['membership'] }}</span>
            </div>
            
            <div class="flex justify-between items-center pb-2 border-b border-gray-200">
                <span class="text-gray-600">Purchased Downloads</span>
                <span class="font-semibold text-lg">{{ $stats['purchased'] }}</span>
            </div>
            
            @if($subscription && $subscription->download_limit)
                <div class="mt-4 pt-2">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm text-gray-600">Monthly Download Limit</span>
                        <span class="text-sm font-medium">
                            {{ $stats['this_month'] }}/{{ $subscription->download_limit }}
                        </span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                        @php
                            $percentage = min(100, ($stats['this_month'] / $subscription->download_limit) * 100);
                        @endphp
                        <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $percentage }}%"></div>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">
                        @if($stats['remaining_downloads'] !== null)
                            You have {{ $stats['remaining_downloads'] }} downloads remaining this month.
                        @else
                            Unlimited downloads with your membership.
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>