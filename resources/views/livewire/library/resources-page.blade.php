
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    
    {{-- Search and Filters --}}
    <div class="mb-8 space-y-6">
        {{-- Search Bar --}}
        <div class="max-w-2xl mx-auto">
            <div class="relative">
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Search resources by title, author, keywords..."
                    class="w-full px-4 py-3 pl-12 pr-4 text-gray-900 dark:text-white bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                >
                <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linecap="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>
        </div>

        {{-- Filter Bar --}}
        <div class="flex flex-col lg:flex-row gap-4 items-start lg:items-center justify-between bg-gray-50 dark:bg-gray-800 p-4 rounded-lg">
            <div class="flex flex-wrap gap-3">
                {{-- Category Filter --}}
                <select
                    wire:model.live="selectedCategory"
                    class="px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-primary-500"
                >
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">
                            {{ str_repeat('— ', $category->depth) }} {{ $category->name }}
                            ({{ $category->resources_count }})
                        </option>
                    @endforeach
                </select>

                {{-- Type Filter --}}
                <select
                    wire:model.live="selectedType"
                    class="px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-primary-500"
                >
                    <option value="">All Types</option>
                    <option value="pdf">PDF Documents</option>
                    <option value="word">Word Documents</option>
                    <option value="excel">Excel Sheets</option>
                    <option value="ebook">E-Books</option>
                </select>

                {{-- Price Filter --}}
                <select
                    wire:model.live="selectedPrice"
                    class="px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-primary-500">
                    <option value="">All Prices</option>
                    <option value="free">Free</option>
                    <option value="under50">Under Ksh50</option>
                    <option value="50to100">Ksh50 - Ksh100</option>
                    <option value="100to250">Ksh100 - Ksh250</option>
                    <option value="over250">Over Ksh250</option>
                </select>

                {{-- Subscription Toggle --}}
                <label class="inline-flex items-center">
                    <input
                        type="checkbox"
                        wire:model.live="showSubscriptionOnly"
                        class="rounded border-gray-300 text-primary-600 shadow-sm focus:ring-primary-500"
                    >
                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                        Subscribers Only
                    </span>
                </label>
            </div>

            {{-- Sort --}}
            <div class="flex items-center gap-2">
                <span class="text-sm text-gray-600 dark:text-gray-400">Sort by:</span>
                <select
                    wire:model.live="sortBy"
                    class="px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-primary-500"
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
    </div>

    {{-- Resources Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
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
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div class="mt-8">
        {{ $resources->links() }}
    </div>

    @include('livewire.library.resource_models')

    @include('frontend.layouts.partials.loading-indicator')
</div>