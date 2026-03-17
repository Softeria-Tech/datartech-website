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

    {{-- Group Header --}}
    <div class="bg-gradient-to-r from-primary-600 to-primary-800 rounded-xl p-8 mb-8 dark:text-white">
        <h1 class="text-3xl font-bold mb-2">{{ $group->name }}</h1>
        @if($group->description)
            <p class="text-primary-100 mb-4 max-w-2xl">{{ $group->description }}</p>
        @endif
        <div class="flex items-center gap-4 text-sm text-primary-100">
            <span class="flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linecap="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                {{ $totalResources }} total resources
            </span>
        </div>
    </div>

    {{-- Sub-Groups --}}
    @if($subGroups->isNotEmpty())
        <div class="mb-8">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Sub-Groups</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($subGroups as $subGroup)
                    <a href="{{ route('library.group', $subGroup->slug) }}" 
                    class="flex items-start gap-3 p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 hover:border-primary-300 dark:hover:border-primary-600 hover:shadow-md transition-all duration-200">
                        
                        {{-- Thumbnail Image --}}
                        <div class="flex-shrink-0 w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-lg overflow-hidden">
                            <img src="{{ $subGroup->cover_image ? Storage::url($subGroup->cover_image) : '/assets/frontend/images/folder-groups.png' }}" 
                                alt="{{ $subGroup->name }}"
                                class="w-full h-full object-cover">
                        </div>
                        
                        {{-- Content --}}
                        <div class="flex-1 min-w-0">
                            <h3 class="font-medium text-gray-900 dark:text-white truncate">
                                {{ $subGroup->name }}
                            </h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                {{ $subGroup->resources()->count() }} resources
                            </p>
                            
                            {{-- Optional: Show if has children --}}
                            @if($subGroup->children->isNotEmpty())
                                <span class="inline-block mt-2 text-xs px-2 py-0.5 bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400 rounded-full">
                                    {{ $subGroup->children->count() }} sub-groups
                                </span>
                            @endif
                        </div>
                        
                        {{-- Optional Arrow Icon --}}
                        <svg class="flex-shrink-0 w-5 h-5 text-gray-400 group-hover:text-primary-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linecap="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
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
                    placeholder="Search in this group..."
                    class="w-full px-4 py-2 pl-10 text-gray-900 dark:text-white bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500"
                >
                <svg class="absolute left-3 top-2.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linecap="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
        </div>

        <div class="flex gap-3 w-full sm:w-auto">
            @if($subGroups->isNotEmpty())
                <select
                    wire:model.live="selectedSubGroup"
                    class="px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-primary-500"
                >
                    <option value="">All Sub-Groups</option>
                    @foreach($subGroups as $subGroup)
                        <option value="{{ $subGroup->id }}">{{ $subGroup->name }}</option>
                    @endforeach
                </select>
            @endif

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
                Try adjusting your search or filters.
            </p>
        </div>
    @endif
</div>