<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    
    {{-- Breadcrumb --}}
    <nav class="flex mb-8 text-sm">
        <ol class="flex items-center space-x-2">
            <li>
                <a href="/" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                    Home
                </a>
            </li>
            <li class="text-gray-400">/</li>
            <li>
                <a href="{{ route('library.resources') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                    Resources
                </a>
            </li>
            <li class="text-gray-400">/</li>
            @if($resource->category)
                <li>
                    <a href="{{ route('library.resources', ['selectedCategory' => $resource->category_id]) }}" 
                       class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                        {{ $resource->category->name }}
                    </a>
                </li>
                <li class="text-gray-400">/</li>
            @endif
            <li class="text-gray-900 dark:text-white font-medium truncate max-w-xs">
                {{ $resource->title }}
            </li>
        </ol>
    </nav>

    {{-- Main Content --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        {{-- Left Column - Resource Details --}}
        <div class="lg:col-span-2 space-y-6">
            
            {{-- Title & Meta --}}
            <div>
                <div class="flex items-center gap-3 mb-3">
                    @if($resource->category)
                        <span class="px-3 py-1 bg-primary-100 dark:bg-primary-900 text-primary-800 dark:text-primary-200 text-xs font-medium rounded-full">
                            {{ $resource->category->name }}
                        </span>
                    @endif
                    @if($resource->requires_subscription)
                        <span class="px-3 py-1 bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200 text-xs font-medium rounded-full">
                            🔒 Subscribers Only
                        </span>
                    @endif
                    @if($resource->featured)
                        <span class="px-3 py-1 bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200 text-xs font-medium rounded-full">
                            ⭐ Featured
                        </span>
                    @endif
                </div>
                
                <h1 class="text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                    {{ $resource->title }}
                </h1>
                
                <div class="flex flex-wrap items-center gap-4 text-sm text-gray-600 dark:text-gray-400">
                    @if($resource->author)
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linecap="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            By {{ $resource->author }}
                        </div>
                    @endif
                    
                    @if($resource->publisher)
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linecap="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l14-7 14 7z" />
                            </svg>
                            {{ $resource->publisher }}
                        </div>
                    @endif
                    
                    @if($resource->published_date)
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linecap="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Published {{ $resource->published_date->format('M d, Y') }}
                        </div>
                    @endif
                    
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linecap="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        {{ number_format($resource->download_count) }} downloads
                    </div>
                </div>
            </div>

            {{-- Preview Card --}}
            <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="p-3 bg-white dark:bg-gray-700 rounded-lg shadow-sm">
                            @php
                                $icon = $fileTypeIcon;
                                $color = $fileTypeColor;
                            @endphp
                            <x-filament::icon :icon="$icon" class="w-8 h-8 text-{{ $color }}-600 dark:text-{{ $color }}-400" />
                        </div>
                        <div>
                            <h3 class="font-medium text-gray-900 dark:text-white">
                                {{ $resource->file_name ?? 'Digital Document' }}
                            </h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                @if($resource->file_size)
                                    {{ \App\Filament\Resources\ResourceResource::formatBytes($resource->file_size) }} •
                                @endif
                                {{ strtoupper(pathinfo($resource->file_path ?? $resource->external_url, PATHINFO_EXTENSION) ?? 'PDF') }}
                            </p>
                        </div>
                    </div>
                    <button wire:click="previewResource" 
                            class="px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linecap="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linecap="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        Free Preview
                    </button>
                </div>
            </div>

            {{-- Tabs --}}
            <div class="border-b border-gray-200 dark:border-gray-700">
                <nav class="flex gap-8">
                    <button wire:click="$set('activeTab', 'description')"
                            class="pb-4 px-1 text-sm font-medium border-b-2 transition
                                   {{ $activeTab === 'description' 
                                      ? 'border-primary-600 text-primary-600 dark:text-primary-400' 
                                      : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300' }}">
                        Description
                    </button>
                    <button wire:click="$set('activeTab', 'details')"
                            class="pb-4 px-1 text-sm font-medium border-b-2 transition
                                   {{ $activeTab === 'details' 
                                      ? 'border-primary-600 text-primary-600 dark:text-primary-400' 
                                      : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300' }}">
                        Details
                    </button>
                </nav>
            </div>

            {{-- Tab Content --}}
            <div class="py-4">
                @if($activeTab === 'description')
                    <div class="prose dark:prose-invert max-w-none">
                        {!! $resource->description !!}
                    </div>
                @else
                    <div class="grid grid-cols-2 gap-4">
                        @if($resource->isbn)
                            <div class="p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                <p class="text-xs text-gray-500 dark:text-gray-400">ISBN</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $resource->isbn }}</p>
                            </div>
                        @endif
                        @if($resource->page_count)
                            <div class="p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                <p class="text-xs text-gray-500 dark:text-gray-400">Pages</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ number_format($resource->page_count) }}</p>
                            </div>
                        @endif
                        @if($resource->language)
                            <div class="p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                <p class="text-xs text-gray-500 dark:text-gray-400">Language</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ ucfirst($resource->language) }}</p>
                            </div>
                        @endif
                        @if($resource->version)
                            <div class="p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                <p class="text-xs text-gray-500 dark:text-gray-400">Version</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $resource->version }}</p>
                            </div>
                        @endif
                        @if($resource->tags)
                            <div class="col-span-2 p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">Tags</p>
                                <div class="flex flex-wrap gap-2">
                                    @foreach(explode(',', $resource->tags) as $tag)
                                        <span class="px-2 py-1 bg-white dark:bg-gray-700 text-xs text-gray-700 dark:text-gray-300 rounded-full">
                                            {{ trim($tag) }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        {{-- Right Column - Purchase Card --}}
        <div class="lg:col-span-1">
            <div class="sticky top-24 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
                
                {{-- Price --}}
                <div class="text-center pb-6 border-b border-gray-200 dark:border-gray-700">
                    @if ($resource->price==0)
                        <span class="text-3xl font-bold text-green-600 dark:text-green-400">Free</span>
                    @elseif($resource->discount_price && $resource->discount_ends_at?->isFuture())
                        <div class="flex items-center justify-center gap-2 mb-2">
                            <span class="text-3xl font-bold text-gray-900 dark:text-white">
                                Ksh{{ number_format($resource->discount_price, 0) }}
                            </span>
                            <span class="text-lg text-gray-500 line-through">
                                Ksh{{ number_format($resource->price, 0) }}
                            </span>
                        </div>
                        @if($resource->discount_ends_at)
                            <p class="text-xs text-red-600 dark:text-red-400">
                                ⏰ Price ends {{ $resource->discount_ends_at->diffForHumans() }}
                            </p>
                        @endif
                    @else
                        <span class="text-3xl font-bold text-gray-900 dark:text-white">
                            Ksh{{ number_format($resource->price, 0) }}
                        </span>
                    @endif
                </div>

                {{-- Actions --}}
                <div class="pt-6 space-y-4">
                    @auth
                        @if($hasPurchased)
                            {{-- Already Purchased --}}
                            <div class="space-y-3">
                                <div class="flex items-center justify-center gap-2 text-green-600 dark:text-green-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linecap="round" stroke-width="2" d="M9 12l2 2 4-5m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span class="font-medium">You own this resource</span>
                                </div>
                                <a wire:click="markDownloaded" href="{{ $downloadUrl }}" download
                                   class="block w-full px-4 py-3 bg-green-600 hover:bg-green-700 text-white text-center font-medium rounded-lg transition">
                                    Download Now
                                </a>
                            </div>
                        @else
                            {{-- Not Purchased --}}
                            @if($resource->requires_subscription)
                                @if($userHasSubscription ?? false)
                                    <div class="space-y-3">
                                        <div class="flex items-center justify-center gap-2 text-purple-600 dark:text-purple-400">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linecap="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                            </svg>
                                            <span class="font-medium">Active Subscription</span>
                                        </div>
                                        <button wire:click="initiatePurchase"
                                                class="block w-full px-4 py-3 bg-primary-600 hover:bg-primary-700 text-white text-center font-medium rounded-lg transition">
                                            Download with Subscription
                                        </button>
                                    </div>
                                @else
                                    <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-4 text-center">
                                        <span class="text-sm text-purple-800 dark:text-purple-200">
                                            🔒 This resource is for subscribers only
                                        </span>
                                        <a href="{{ route('membership.plans') }}" 
                                           class="block mt-2 text-sm text-purple-600 dark:text-purple-400 hover:underline">
                                            View Subscription Plans →
                                        </a>
                                    </div>
                                @endif
                            @else
                                <button wire:click="initiatePurchase"
                                        class="w-full px-4 py-3 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition flex items-center justify-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linecap="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    Buy
                                </button>
                            @endif
                        @endif
                    @else
                        {{-- Not Logged In --}}
                        <div class="space-y-3">
                            <a href="{{ route('login', ['redirect' => route('library.resource.detail', $resource->slug)]) }}"
                               class="block w-full px-4 py-3 bg-primary-600 hover:bg-primary-700 text-white text-center font-medium rounded-lg transition">
                                Purchase
                            </a>
                            <p class="text-xs text-center text-gray-500 dark:text-gray-400">
                                Already have an account? <a href="{{ route('login', ['redirect' => route('library.resource.detail', $resource->slug)]) }}" class="text-primary-600 hover:underline">Sign in</a>
                            </p>
                        </div>
                    @endauth
                </div>

                {{-- Features --}}
                <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700 space-y-3">
                    <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linecap="round" stroke-width="2" d="M9 12l2 2 4-5m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Instant download after purchase</span>
                    </div>
                    <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linecap="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Lifetime access</span>
                    </div>
                    <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linecap="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                        <span>Secure payment via M-Pesa</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Related Resources --}}
    @if($relatedResources->isNotEmpty())
        <div class="mt-16">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">
                You might also like
            </h2>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($relatedResources as $related)
                    <div class="group bg-white dark:bg-gray-800 rounded-xl shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden border border-gray-200 dark:border-gray-700">
                        <div class="relative h-40 bg-gray-100 dark:bg-gray-900">
                            @if($related->thumbnail)
                                <img src="{{ Storage::url($related->thumbnail) }}" 
                                     alt="{{ $related->title }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linecap="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                            @endif
                        </div>
                        
                        <div class="p-4">
                            <h3 class="font-medium text-gray-900 dark:text-white line-clamp-2">
                                <a href="{{ route('library.resource.detail', $related->slug) }}" 
                                   class="hover:text-primary-600 dark:hover:text-primary-400">
                                    {{ $related->title }}
                                </a>
                            </h3>
                            
                            @if($related->author)
                                <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                                    By {{ $related->author }}
                                </p>
                            @endif
                            
                            <div class="mt-3 flex items-center justify-between">
                                <span class="text-lg font-bold text-gray-900 dark:text-white">
                                    Ksh{{ number_format($related->price, 0) }}
                                </span>
                                <a href="{{ route('library.resource.detail', $related->slug) }}"
                                   class="text-primary-600 hover:text-primary-700 text-sm font-medium">
                                    View →
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Preview Modal --}}
    @if($showPreviewModal && $resource)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" 
                     wire:click="$set('showPreviewModal', false)"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                    
                    {{-- Modal Header --}}
                    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ $resource->title }} - Preview
                            </h3>
                            <button wire:click="$set('showPreviewModal', false)"
                                    class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linecap="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Preview Content --}}
                    <div class="px-6 py-4 max-h-[70vh] overflow-y-auto">
                        @if($previewFileUrl)
                            @php
                                $extension = pathinfo($resource->file_path ?? $resource->preview_file_path, PATHINFO_EXTENSION);
                            @endphp

                            @if(in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                                <img src="{{ $previewFileUrl }}" 
                                     alt="{{ $resource->title }}"
                                     class="max-w-full h-auto mx-auto rounded-lg shadow-lg">
                            
                            @elseif($extension === 'pdf')
                                <iframe src="{{$previewFileUrl}}"
                                        style="width:100%; height:600px;"
                                        frameborder="0"
                                        class="rounded-lg">
                                </iframe>
                            
                            @elseif(in_array($extension, ['doc', 'docx']))
                                <iframe src="https://view.officeapps.live.com/op/embed.aspx?src={{ urlencode($previewFileUrl) }}"
                                        style="width:100%; height:600px;"
                                        frameborder="0"
                                        class="rounded-lg">
                                </iframe>
                            
                            @else
                                <div class="text-center py-8">
                                    <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linecap="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <p class="mt-4 text-gray-600 dark:text-gray-400">
                                        Preview not available for this file type.
                                    </p>
                                </div>
                            @endif
                        @else
                            <div class="prose dark:prose-invert max-w-none">
                                {!! $resource->description !!}
                            </div>
                        @endif
                    </div>

                    {{-- Modal Footer --}}
                    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 border-t border-gray-200 dark:border-gray-600">
                        <div class="flex items-center justify-between">
                            <div>
                                @if($resource->discount_price && $resource->discount_ends_at?->isFuture())
                                    <span class="text-2xl font-bold text-gray-900 dark:text-white">
                                        Ksh{{ number_format($resource->discount_price, 0) }}
                                    </span>
                                    <span class="ml-2 text-sm text-gray-500 line-through">
                                        Ksh{{ number_format($resource->price, 0) }}
                                    </span>
                                @else
                                    <span class="text-2xl font-bold text-gray-900 dark:text-white">
                                        Ksh{{ number_format($resource->price, 0) }}
                                    </span>
                                @endif
                            </div>
                            <div class="flex gap-3">
                                <button wire:click="$set('showPreviewModal', false)"
                                        class="px-4 py-2 bg-gray-200 dark:bg-gray-600 hover:bg-gray-300 dark:hover:bg-gray-500 text-gray-800 dark:text-white text-sm font-medium rounded-lg transition">
                                    Close
                                </button>
                                
                                @auth
                                    @if(!$hasPurchased && !$resource->requires_subscription)
                                        <button wire:click="initiatePurchase"
                                                wire:click="$set('showPreviewModal', false)"
                                                class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition">
                                            Purchase Now
                                        </button>
                                    @endif
                                @else
                                    <a href="{{ route('login', ['redirect' => route('library.resource.detail', $resource->slug)]) }}"
                                       class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition">
                                        Purchase
                                    </a>
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Purchase Modal --}}
    @if($showPurchaseModal && $resource)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity"
                     wire:click="$set('showPurchaseModal', false)"></div>

                <div class="relative bg-white dark:bg-gray-800 rounded-lg max-w-md w-full p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                            Complete Your Purchase
                        </h3>
                        <button wire:click="$set('showPurchaseModal', false)"
                                class="text-gray-400 hover:text-gray-500">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linecap="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="mb-6">
                        <div class="flex gap-4 mb-4">
                            @if($resource->thumbnail)
                                <img src="{{ Storage::url($resource->thumbnail) }}" 
                                     alt="{{ $resource->title }}"
                                     class="w-20 h-20 object-cover rounded-lg">
                            @endif
                            <div>
                                <h4 class="font-medium text-gray-900 dark:text-white">
                                    {{ $resource->title }}
                                </h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ $resource->author }}
                                </p>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-400">Price</span>
                                <span class="font-medium text-gray-900 dark:text-white">
                                    Ksh{{ number_format($resource->price, 0) }}
                                </span>
                            </div>

                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-400">Quantity</span>
                                <div class="flex items-center gap-2">
                                    <button wire:click="$set('purchaseQuantity', max(1, $purchaseQuantity - 1))"
                                            class="p-1 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linecap="round" stroke-width="2" d="M20 12H4" />
                                        </svg>
                                    </button>
                                    <span class="w-8 text-center font-medium">{{ $purchaseQuantity }}</span>
                                    <button wire:click="$set('purchaseQuantity', $purchaseQuantity + 1)"
                                            class="p-1 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linecap="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <div class="border-t border-gray-200 dark:border-gray-700 pt-3">
                                <div class="flex justify-between font-medium">
                                    <span>Total</span>
                                    <span class="text-lg text-primary-600 dark:text-primary-400">
                                        Ksh{{ number_format($resource->price * $purchaseQuantity, 0) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="flex items-center">
                            <input type="checkbox" 
                                   wire:model="agreeTerms"
                                   class="rounded border-gray-300 text-primary-600 shadow-sm focus:ring-primary-500">
                            <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">
                                I agree to the <a href="/terms" class="text-primary-600 hover:text-primary-700">Terms of Service</a>
                            </span>
                        </label>
                        @error('agreeTerms') 
                            <span class="text-sm text-red-600">{{ $message }}</span>
                        @enderror
                    </div>

                    <button wire:click="processPurchase"
                            wire:loading.attr="disabled"
                            class="w-full px-4 py-3 bg-primary-600 hover:bg-primary-700 disabled:bg-primary-400 text-white font-medium rounded-lg transition">
                        <span wire:loading.remove>Proceed to Checkout</span>
                        <span wire:loading>Processing...</span>
                    </button>

                    <p class="mt-3 text-xs text-center text-gray-500 dark:text-gray-400">
                        Secure payment processed via M-Pesa
                    </p>
                </div>
            </div>
        </div>
    @endif
</div>