<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 lg:py-12">
    
    {{-- Hero Section --}}
    <div class="bg-gradient-to-r from-primary-600 to-primary-800 rounded-2xl p-6 sm:p-8 lg:p-12 mb-8 text-white">
        <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold mb-3">
            Explore Our Resource Library
        </h1>
        <p class="text-primary-100 text-sm sm:text-base lg:text-lg max-w-2xl">
            Discover a wealth of knowledge with our carefully curated collection of resources.
            Find exactly what you need with our intelligent search and category filters.
        </p>
    </div>

    {{-- Mobile Filter Toggle --}}
    <div class="lg:hidden mb-4">
        <button 
            wire:click="$toggle('showMobileFilters')"
            class="w-full flex items-center justify-between px-4 py-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm"
        >
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linecap="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
                <span class="font-medium text-gray-700 dark:text-gray-300">Filters & Categories</span>
                @if($selectedParentCategory || $search || $showSubscriptionOnly)
                    <span class="px-2 py-0.5 text-xs bg-primary-100 dark:bg-primary-900 text-primary-700 dark:text-primary-300 rounded-full">
                        Active
                    </span>
                @endif
            </div>
            <svg class="w-5 h-5 text-gray-500 transition-transform duration-200 {{ $showMobileFilters ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linecap="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>
    </div>

    {{-- Main Content Grid --}}
    <div class="flex flex-col lg:flex-row gap-6 lg:gap-8">
        
        {{-- Sidebar Filters --}}
        <div class="lg:w-80 flex-shrink-0">
            <div class="{{ $showMobileFilters ? 'block' : 'hidden' }} lg:block">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden sticky top-24">
                    
                    {{-- Search Section --}}
                    <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Search Resources
                        </label>
                        <div class="relative">
                            <input
                                type="text"
                                wire:model.live.debounce.300ms="search"
                                placeholder="Title, author, keywords..."
                                class="w-full px-4 py-2.5 pl-10 text-sm bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                            >
                            <svg class="absolute left-3 top-3 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linecap="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </div>

                    {{-- Category Filters --}}
                    <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linecap="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                            </svg>
                            Categories
                        </h3>
                        
                        {{-- Parent Category --}}
                        <div class="mb-4">
                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">
                                Main Category
                            </label>
                            <select 
                                wire:model.live="selectedParentCategory"
                                class="w-full px-3 py-2 text-sm bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                            >
                                <option value="">All Main Categories</option>
                                @foreach($parentCategories as $category)
                                    <option value="{{ $category->id }}">
                                        {{ $category->name }} ({{ $category->resources_count }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        {{-- Sub Category --}}
                        @if($selectedParentCategory && count($subCategories) > 0)
                            <div class="mb-4">
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">
                                    Sub Category
                                </label>
                                <select 
                                    wire:model.live="selectedSubCategory"
                                    class="w-full px-3 py-2 text-sm bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                >
                                    <option value="">All Sub Categories</option>
                                    @foreach($subCategories as $category)
                                        <option value="{{ $category->id }}">
                                            {{ $category->name }} ({{ $category->resources_count }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        
                        {{-- Grand Category --}}
                        @if($selectedSubCategory && count($grandCategories) > 0)
                            <div class="mb-3">
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">
                                    Grand Category
                                </label>
                                <select 
                                    wire:model.live="selectedGrandCategory"
                                    class="w-full px-3 py-2 text-sm bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                >
                                    <option value="">All Grand Categories</option>
                                    @foreach($grandCategories as $category)
                                        <option value="{{ $category->id }}">
                                            {{ $category->name }} ({{ $category->resources_count }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        
                        {{-- Category Path Display --}}
                        @if($selectedParentCategory)
                            <div class="mt-3 p-2 bg-gray-50 dark:bg-gray-900 rounded-lg text-xs">
                                <span class="text-gray-500">Selected:</span>
                                <span class="text-gray-700 dark:text-gray-300 font-medium">
                                    @php
                                        $path = [];
                                        $parent = \App\Models\Category::find($selectedParentCategory);
                                        if($parent) $path[] = $parent->name;
                                        if($selectedSubCategory) {
                                            $sub = \App\Models\Category::find($selectedSubCategory);
                                            if($sub) $path[] = $sub->name;
                                        }
                                        if($selectedGrandCategory) {
                                            $grand = \App\Models\Category::find($selectedGrandCategory);
                                            if($grand) $path[] = $grand->name;
                                        }
                                    @endphp
                                    {{ implode(' > ', $path) }}
                                </span>
                            </div>
                        @endif
                    </div>

                    {{-- Additional Filters --}}
                    <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Additional Filters</h3>
                        
                        {{-- Subscription Toggle --}}
                        <label class="flex items-center justify-between cursor-pointer py-2">
                            <span class="text-sm text-gray-700 dark:text-gray-300">Subscribers Only</span>
                            <input 
                                type="checkbox" 
                                wire:model.live="showSubscriptionOnly"
                                class="rounded border-gray-300 text-primary-600 shadow-sm focus:ring-primary-500"
                            >
                        </label>
                    </div>

                    {{-- Sort Options --}}
                    <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Sort By</h3>
                        <select 
                            wire:model.live="sortBy"
                            class="w-full px-3 py-2 text-sm bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-primary-500"
                        >
                            <option value="latest">Latest First</option>
                            <option value="oldest">Oldest First</option>
                            <option value="price_low">Price: Low to High</option>
                            <option value="price_high">Price: High to Low</option>
                            <option value="popular">Most Downloaded</option>
                            <option value="title">Title A-Z</option>
                        </select>
                    </div>

                    {{-- Clear Filters --}}
                    <div class="p-4">
                        <button 
                            wire:click="clearFilters"
                            class="w-full px-4 py-2 text-sm text-gray-600 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition flex items-center justify-center gap-2"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linecap="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            Clear All Filters
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Resources Grid --}}
        <div class="flex-1">
            {{-- Active Filters Bar --}}
            @if($selectedParentCategory || $search || $showSubscriptionOnly)
                <div class="mb-4 p-3 bg-gray-50 dark:bg-gray-800/50 rounded-lg flex flex-wrap items-center gap-2">
                    <span class="text-xs text-gray-500">Active filters:</span>
                    @if($search)
                        <span class="inline-flex items-center gap-1 px-2 py-1 bg-primary-100 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300 text-xs rounded-full">
                            Search: {{ $search }}
                            <button wire:click="$set('search', '')" class="hover:text-primary-900">×</button>
                        </span>
                    @endif
                    @if($selectedParentCategory)
                        @php
                            $parentName = \App\Models\Category::find($selectedParentCategory)?->name;
                        @endphp
                        <span class="inline-flex items-center gap-1 px-2 py-1 bg-primary-100 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300 text-xs rounded-full">
                            {{ $parentName }}
                            @if($selectedSubCategory)
                                > {{ \App\Models\Category::find($selectedSubCategory)?->name }}
                            @endif
                            @if($selectedGrandCategory)
                                > {{ \App\Models\Category::find($selectedGrandCategory)?->name }}
                            @endif
                            <button wire:click="clearFilters" class="hover:text-primary-900">×</button>
                        </span>
                    @endif
                    @if($showSubscriptionOnly)
                        <span class="inline-flex items-center gap-1 px-2 py-1 bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 text-xs rounded-full">
                            Subscribers Only
                            <button wire:click="$set('showSubscriptionOnly', false)" class="hover:text-purple-900">×</button>
                        </span>
                    @endif
                </div>
            @endif

            {{-- Results Count --}}
            <div class="mb-4 flex items-center justify-between">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Showing <span class="font-semibold text-gray-900 dark:text-white">{{ $resources->firstItem() ?? 0 }}</span> 
                    to <span class="font-semibold text-gray-900 dark:text-white">{{ $resources->lastItem() ?? 0 }}</span> 
                    of <span class="font-semibold text-gray-900 dark:text-white">{{ $resources->total() }}</span> resources
                </p>
            </div>

            {{-- Resources Grid --}}
            <div class="grid grid-cols-1 xs:grid-cols-2 sm:grid-cols-3 xl:grid-cols-3 gap-4 sm:gap-6">
                @forelse($resources as $resource)
                    <x-library.resource-card :resource="$resource" />
                @empty
                    <div class="col-span-full py-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linecap="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No resources found</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Try adjusting your search or filters.
                        </p>
                        <button 
                            wire:click="clearFilters"
                            class="mt-4 px-4 py-2 text-sm text-primary-600 hover:text-primary-700 font-medium"
                        >
                            Clear all filters →
                        </button>
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            <div class="mt-8">
                {{ $resources->links() }}
            </div>
        </div>
    </div>

    @include('livewire.library.resource_models')

    @include('frontend.layouts.partials.loading-indicator')
</div>