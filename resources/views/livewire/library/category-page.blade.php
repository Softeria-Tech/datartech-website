<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    {{-- Breadcrumb --}}
    <nav class="flex mb-6 text-sm">
        <ol class="flex items-center space-x-2">
            <li>
                <a href="/" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                    Home
                </a>
            </li>
            <li class="text-gray-400">/</li>
            <li>
                <a href="{{ route('home') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                    Library
                </a>
            </li>
            @foreach($breadcrumbs as $crumb)
                <li class="text-gray-400">/</li>
                <li>
                    <a href="{{ $crumb['url'] }}" 
                       class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                        {{ $crumb['name'] }}
                    </a>
                </li>
            @endforeach
        </ol>
    </nav>

    {{-- Category Header --}}
    <div class="bg-gradient-to-r from-secondary-600 to-secondary-800 rounded-xl p-8 mb-8 dark:text-white">
        <h1 class="text-3xl font-bold mb-2">{{ $category->name }}</h1>
        @if($category->description)
            <p class="text-secondary-100 mb-4 max-w-2xl">{{ $category->description }}</p>
        @endif
        <div class="flex items-center gap-4 text-sm text-secondary-100">
            <span class="flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linecap="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l5 5a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-5-5A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                </svg>
                {{ $totalResources }} resources in this category
            </span>
        </div>
    </div>

    {{-- Sub-Categories --}}
    @if($subCategories->isNotEmpty())
        <div class="mb-8">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Sub-Categories</h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach($subCategories as $subCategory)
                    <a href="{{ route('library.category', $subCategory->slug) }}" 
                       class="block p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 hover:border-secondary-300 dark:hover:border-secondary-600 transition">
                        <h3 class="font-medium text-gray-900 dark:text-white">{{ $subCategory->name }}</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            {{ $subCategory->resources()->count() }} resources
                        </p>
                        @if($subCategory->children->isNotEmpty())
                            <span class="inline-block mt-2 text-xs px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-full">
                                {{ $subCategory->children->count() }} sub-categories
                            </span>
                        @endif
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Filter Bar --}}
    <div class="mb-6 flex flex-col sm:flex-row gap-4 items-center justify-between">
        <div class="flex-1 w-full sm:w-auto">
            <div class="relative">
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Search in this category..."
                    class="w-full px-4 py-2 pl-10 text-gray-900 dark:text-white bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-secondary-500"
                >
                <svg class="absolute left-3 top-2.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linecap="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
        </div>

        <div class="flex gap-3 w-full sm:w-auto">
            @if($subCategories->isNotEmpty())
                <select
                    wire:model.live="selectedSubCategory"
                    class="px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-secondary-500"
                >
                    <option value="">All Sub-Categories</option>
                    @foreach($subCategories as $subCategory)
                        <option value="{{ $subCategory->id }}">{{ $subCategory->name }}</option>
                    @endforeach
                </select>
            @endif

            <select
                wire:model.live="sortBy"
                class="px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-secondary-500"
            >
                <option value="latest">Latest</option>
                <option value="oldest">Oldest</option>
                <option value="price_low">Price: Low to High</option>
                <option value="price_high">Price: High to Low</option>
                <option value="popular">Most Downloaded</option>
                <option value="title">Title A-Z</option>
            </select>
        </div>
    </div>

    {{-- Quick Filters (like price ranges, etc.) --}}
    <div class="mb-6 flex flex-wrap gap-2">
        <button wire:click="$set('selectedSubCategory', null)" 
                class="px-3 py-1 text-xs font-medium rounded-full transition
                       {{ is_null($selectedSubCategory) 
                          ? 'bg-secondary-600 text-white' 
                          : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
            All
        </button>
        <button wire:click="$set('sortBy', 'price_low')" 
                class="px-3 py-1 text-xs font-medium rounded-full transition
                       {{ $sortBy === 'price_low' 
                          ? 'bg-secondary-600 text-white' 
                          : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
            Lowest Price
        </button>
        <button wire:click="$set('sortBy', 'popular')" 
                class="px-3 py-1 text-xs font-medium rounded-full transition
                       {{ $sortBy === 'popular' 
                          ? 'bg-secondary-600 text-white' 
                          : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
            Most Popular
        </button>
        <button wire:click="$set('sortBy', 'latest')" 
                class="px-3 py-1 text-xs font-medium rounded-full transition
                       {{ $sortBy === 'latest' 
                          ? 'bg-secondary-600 text-white' 
                          : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
            Newest
        </button>
    </div>

    {{-- Resources Grid --}}
    @if($resources->isNotEmpty())
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($resources as $resource)
                <x-library.resource-card :resource="$resource" />
            @endforeach
        </div>

        <div class="mt-8">
            {{ $resources->links() }}
        </div>
    @else
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linecap="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No resources found</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Try adjusting your search or browse other categories.
            </p>
            <a href="{{ route('home') }}" 
               class="mt-4 inline-flex items-center px-4 py-2 bg-secondary-600 hover:bg-secondary-700 text-white text-sm font-medium rounded-lg transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linecap="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Categories
            </a>
        </div>
    @endif

    {{-- Related Categories (optional) --}}
    @if($category->parent && $category->parent->children->count() > 1)
        <div class="mt-12 pt-8 border-t border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                Other categories in {{ $category->parent->name }}
            </h3>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach($category->parent->children->where('id', '!=', $category->id)->take(4) as $sibling)
                    <a href="{{ route('library.category', $sibling->slug) }}" 
                       class="block p-3 bg-gray-50 dark:bg-gray-800 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                        <h4 class="font-medium text-gray-900 dark:text-white text-sm">{{ $sibling->name }}</h4>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $sibling->resources()->count() }} resources
                        </p>
                    </a>
                @endforeach
            </div>
        </div>
    @endif
</div>