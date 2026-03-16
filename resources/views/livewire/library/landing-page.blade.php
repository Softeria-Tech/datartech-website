<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    {{-- Hero Section with Search --}}
    <div class="relative mb-16">
        {{-- Background Decoration --}}
        <div class="absolute inset-0 overflow-hidden rounded-3xl">
            <div class="absolute -top-40 -right-40 w-80 h-80 bg-primary-200 dark:bg-primary-900/20 rounded-full blur-3xl opacity-30"></div>
            <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-secondary-200 dark:bg-secondary-900/20 rounded-full blur-3xl opacity-30"></div>
        </div>
        
        <div class="relative text-center max-w-4xl mx-auto">
            <h1 class="text-5xl md:text-6xl font-bold bg-gradient-to-r from-primary-600 to-secondary-600 bg-clip-text text-transparent mb-6">
                Resource Library
            </h1>
            <p class="text-xl text-gray-600 dark:text-gray-400 mb-10 leading-relaxed">
                Discover thousands of educational resources, e-books, and documents<br> 
                to enhance your learning journey
            </p>
            
            {{-- Unified Search --}}
            <div class="max-w-2xl mx-auto">
                <div class="relative group">
                    <div class="absolute -inset-1 bg-gradient-to-r from-primary-600 to-secondary-600 rounded-2xl blur opacity-25 group-hover:opacity-40 transition duration-300"></div>
                    <div class="relative">
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="search"
                            placeholder="Search groups, categories, or resources..."
                            class="w-full px-6 py-4 pl-14 pr-36 text-gray-900 dark:text-white bg-white dark:bg-gray-800 border-2 border-transparent rounded-2xl focus:border-primary-500 focus:ring-2 focus:ring-primary-500 shadow-lg text-lg"
                        >
                        <div class="absolute inset-y-0 left-0 flex items-center pl-5">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linecap="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-2">
                            <a href="{{ route('library.resources') }}" 
                               class="px-4 py-2 bg-gradient-to-r from-primary-600 to-primary-700 hover:from-primary-700 hover:to-primary-800 dark:text-white text-sm font-medium rounded-xl transition-all duration-200 shadow-md hover:shadow-lg">
                                Advanced Search
                            </a>
                        </div>
                    </div>
                </div>
                
                {{-- Search Stats --}}
                @if(!empty($search))
                    <div class="mt-3 text-sm text-gray-600 dark:text-gray-400">
                        Found {{ $groups->count() + $categories->count() }} results
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Stats Bar --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-12">
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 text-center shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="text-3xl font-bold text-primary-600 dark:text-primary-400 mb-1">{{ number_format(App\Models\Resource::count()) }}+</div>
            <div class="text-sm text-gray-600 dark:text-gray-400">Total Resources</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 text-center shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="text-3xl font-bold text-secondary-600 dark:text-secondary-400 mb-1">{{ number_format(App\Models\Category::count()) }}</div>
            <div class="text-sm text-gray-600 dark:text-gray-400">Categories</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 text-center shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="text-3xl font-bold text-green-600 dark:text-green-400 mb-1">{{ number_format(App\Models\ResourceGroup::count()) }}</div>
            <div class="text-sm text-gray-600 dark:text-gray-400">Resource Groups</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 text-center shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="text-3xl font-bold text-purple-600 dark:text-purple-400 mb-1">{{ number_format(App\Models\User::whereHas('downloads')->count()) }}+</div>
            <div class="text-sm text-gray-600 dark:text-gray-400">Happy Learners</div>
        </div>
    </div>

    {{-- Groups Section --}}
    @if($groups->isNotEmpty())
        <div class="mb-16">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center">
                    <div class="p-2 bg-primary-100 dark:bg-primary-900/30 rounded-lg mr-3">
                        <svg class="w-6 h-6 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linecap="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                    Resource Groups
                </h2>
                @if($hasMoreGroups)
                    <button wire:click="showMoreGroups" 
                            class="text-primary-600 hover:text-primary-700 dark:text-primary-400 text-sm font-medium flex items-center gap-1 group">
                        Show More ({{ $totalGroupsCount - $initialLimit }} more)
                        <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linecap="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                @endif
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach($groups as $group)
                    <a href="{{ route('library.group', $group->slug) }}" 
                       class="group block bg-white dark:bg-gray-800 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-200 dark:border-gray-700 hover:border-primary-300 dark:hover:border-primary-600 transform hover:-translate-y-1">
                        
                        <div class="h-40 bg-gradient-to-br from-primary-500 to-primary-700 relative overflow-hidden">
                            <img src="{{ $group->cover_image ? Storage::url($group->cover_image) : '/assets/frontend/images/folder-groups.png' }}" 
                                 alt="{{ $group->name }}" 
                                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
                            @if($group->children->isNotEmpty())
                                <div class="absolute top-3 right-3">
                                    <span class="px-2 py-1 bg-white/90 backdrop-blur-sm text-primary-600 text-xs font-medium rounded-full shadow-lg">
                                        {{ $group->children->count() }} sub-groups
                                    </span>
                                </div>
                            @endif
                        </div>

                        <div class="p-5">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2 line-clamp-1 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
                                {{ $group->name }}
                            </h3>
                            
                            @if($group->description)
                                <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2 mb-3">
                                    {{ $group->description }}
                                </p>
                            @endif

                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                    <span class="text-primary-600 dark:text-primary-400 font-bold">{{ $group->resources_count }}</span> resources
                                </span>
                                
                                <span class="text-primary-600 group-hover:text-primary-700 text-sm font-medium flex items-center gap-1">
                                    Explore
                                    <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linecap="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Categories Section --}}
    @if($categories->isNotEmpty())
        <div class="mb-16">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center">
                    <div class="p-2 bg-secondary-100 dark:bg-secondary-900/30 rounded-lg mr-3">
                        <svg class="w-6 h-6 text-secondary-600 dark:text-secondary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linecap="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l5 5a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-5-5A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                    </div>
                    Categories
                </h2>
                @if($hasMoreCategories)
                    <button wire:click="showMoreCategories" 
                            class="text-secondary-600 hover:text-secondary-700 dark:text-secondary-400 text-sm font-medium flex items-center gap-1 group">
                        Show More ({{ $totalCategoriesCount - $initialLimit }} more)
                        <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linecap="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                @endif
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($categories as $category)
                    <a href="{{ route('library.category', $category->slug) }}" 
                       class="group block bg-white dark:bg-gray-800 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-200 dark:border-gray-700 hover:border-secondary-300 dark:hover:border-secondary-600 transform hover:-translate-y-1">
                        
                        <div class="p-6">
                            <div class="flex items-start justify-between mb-4">
                                <div class="p-3 bg-secondary-100 dark:bg-secondary-900/30 rounded-xl">
                                    <svg class="w-8 h-8 text-secondary-600 dark:text-secondary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linecap="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l5 5a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-5-5A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                    </svg>
                                </div>
                                @if($category->children->isNotEmpty())
                                    <span class="px-2 py-1 bg-secondary-100 dark:bg-secondary-900/50 text-secondary-600 dark:text-secondary-400 text-xs font-medium rounded-full">
                                        {{ $category->children->count() }} sub
                                    </span>
                                @endif
                            </div>
                            
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2 group-hover:text-secondary-600 dark:group-hover:text-secondary-400 transition-colors">
                                {{ $category->name }}
                            </h3>
                            
                            @if($category->description)
                                <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2 mb-4">
                                    {{ $category->description }}
                                </p>
                            @endif

                            <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                    <span class="text-secondary-600 dark:text-secondary-400 font-bold">{{ $category->resources_count }}</span> items
                                </span>
                                
                                <span class="text-secondary-600 group-hover:text-secondary-700 text-sm font-medium flex items-center gap-1">
                                    Browse
                                    <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linecap="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Featured Resources Carousel --}}
    @if($featuredResources->isNotEmpty())
        <div class="mb-16">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 flex items-center">
                <div class="p-2 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg mr-3">
                    <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                </div>
                Featured Resources
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($featuredResources as $resource)
                    <x-library.resource-card :resource="$resource" />
                @endforeach
            </div>
        </div>
    @endif

    {{-- Popular and Recent Resources --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">
        {{-- Popular Resources --}}
        @if($popularResources->isNotEmpty())
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linecap="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                    Most Popular
                </h3>
                <div class="space-y-4">
                    @foreach($popularResources as $resource)
                        <a href="{{ route('library.resource.detail', $resource->slug) }}" 
                           class="flex items-center gap-3 p-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 rounded-xl transition">
                            <div class="w-12 h-12 bg-gray-100 dark:bg-gray-700 rounded-lg overflow-hidden flex-shrink-0">
                                @if($resource->thumbnail)
                                    <img src="{{ Storage::url($resource->thumbnail) }}" alt="" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linecap="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $resource->title }}</h4>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ number_format($resource->download_count) }} downloads</p>
                            </div>
                            <span class="text-xs font-medium text-primary-600">{{ $resource->price > 0 ? 'Ksh'.number_format($resource->price) : 'Free' }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Recent Resources --}}
        @if($recentResources->isNotEmpty())
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linecap="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Recently Added
                </h3>
                <div class="space-y-4">
                    @foreach($recentResources as $resource)
                        <a href="{{ route('library.resource.detail', $resource->slug) }}" 
                           class="flex items-center gap-3 p-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 rounded-xl transition">
                            <div class="w-12 h-12 bg-gray-100 dark:bg-gray-700 rounded-lg overflow-hidden flex-shrink-0">
                                @if($resource->thumbnail)
                                    <img src="{{ Storage::url($resource->thumbnail) }}" alt="" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linecap="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $resource->title }}</h4>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Added {{ $resource->created_at->diffForHumans() }}</p>
                            </div>
                            <span class="text-xs font-medium text-primary-600">{{ $resource->price > 0 ? 'Ksh'.number_format($resource->price) : 'Free' }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    {{-- Newsletter/CTA Section --}}
    <div class="relative mt-16 mb-8">
        <div class="absolute inset-0 bg-gradient-to-r from-primary-600 to-secondary-600 rounded-3xl blur-xl opacity-20"></div>
        <div class="relative bg-gradient-to-r from-primary-600 to-secondary-600 rounded-3xl p-8 md:p-12 dark:text-white overflow-hidden">
            <div class="absolute inset-0 opacity-10">
                <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                    <pattern id="pattern-circles" x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse">
                        <circle cx="10" cy="10" r="2" fill="white" />
                    </pattern>
                    <rect x="0" y="0" width="100%" height="100%" fill="url(#pattern-circles)" />
                </svg>
            </div>
            
            <div class="relative flex flex-col md:flex-row items-center justify-between gap-6">
                <div>
                    <h3 class="text-2xl md:text-3xl font-bold mb-2">Can't find what you're looking for?</h3>
                    <p class="text-primary-100">Browse all resources or use advanced filters to narrow down your search</p>
                </div>
                <div class="flex gap-4">
                    <a href="{{ route('library.resources') }}" 
                       class="px-6 py-3 bg-white text-primary-600 hover:bg-primary-50 font-medium rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                        Browse All Resources
                    </a>
                    <a href="{{ route('contact-us') }}" 
                       class="px-6 py-3 bg-transparent border-2 border-white dark:text-white hover:bg-white/10 font-medium rounded-xl transition-all duration-200">
                        Request Resource
                    </a>
                </div>
            </div>
        </div>
    </div>

    @include('livewire.library.resource_models')
</div>