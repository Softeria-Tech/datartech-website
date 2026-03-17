<div class="min-h-screen bg-gradient-to-b from-gray-50 to-white dark:from-gray-900 dark:to-gray-800">
    
    {{-- Animated Background Elements --}}
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-primary-200 dark:bg-primary-900/20 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob"></div>
        <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-secondary-200 dark:bg-secondary-900/20 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob animation-delay-2000"></div>
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-80 h-80 bg-purple-200 dark:bg-purple-900/20 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob animation-delay-4000"></div>
    </div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        
        {{-- Hero Section with Floating Elements --}}
        <div class="relative mb-24">
            {{-- Floating Cards Decoration --}}
            <div class="absolute top-20 left-0 w-64 h-64 bg-white dark:bg-gray-800 rounded-2xl shadow-2xl rotate-6 opacity-20 hidden lg:block animate-float"></div>
            <div class="absolute bottom-0 right-0 w-48 h-48 bg-gradient-to-br from-primary-500 to-secondary-500 rounded-2xl shadow-2xl -rotate-12 opacity-20 hidden lg:block animate-float animation-delay-1000"></div>
            
            <div class="relative text-center max-w-4xl mx-auto">
                {{-- Badge --}}
                <div class="inline-flex items-center px-4 py-2 bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-full shadow-lg mb-8 border border-gray-200 dark:border-gray-700">
                    <span class="relative flex h-2 w-2 mr-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                    </span>
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">1,000+ resources available</span>
                </div>

                {{-- Main Heading with Gradient --}}
                <h1 class="text-6xl md:text-7xl font-bold mb-6">
                    <span class="bg-gradient-to-r from-primary-600 via-secondary-600 to-purple-600 bg-clip-text text-transparent bg-300% animate-gradient">
                        Discover. Learn. Grow.
                    </span>
                </h1>
                
                <p class="text-xl text-gray-600 dark:text-gray-400 mb-10 leading-relaxed max-w-2xl mx-auto">
                    Access thousands of premium educational resources, e-books, and documents<br> 
                    curated by experts to accelerate your learning journey
                </p>
                
                {{-- Search Bar with Glassmorphism --}}
                <div class="max-w-2xl mx-auto relative group">
                    <div class="absolute -inset-1 bg-gradient-to-r from-primary-600 via-secondary-600 to-purple-600 rounded-2xl blur-xl opacity-30 group-hover:opacity-50 transition duration-500 animate-gradient"></div>
                    <div class="relative glass-effect rounded-2xl shadow-2xl">
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="search"
                            placeholder="Search groups, categories, or resources..."
                            class="w-full px-8 py-5 pl-14 pr-40 text-gray-900 dark:text-white bg-white/90 dark:bg-gray-800/90 backdrop-blur-sm border-2 border-transparent rounded-2xl focus:border-primary-500 focus:ring-2 focus:ring-primary-500 text-lg transition-all duration-300"
                        >
                        <div class="absolute inset-y-0 left-0 flex items-center pl-5">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linecap="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                            <a href="{{ route('library.resources') }}" 
                               class="px-6 py-2.5 bg-gradient-to-r from-primary-600 to-primary-700 hover:from-primary-700 hover:to-primary-800 text-white text-sm font-semibold rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                                Advanced Search
                            </a>
                        </div>
                    </div>
                    
                    {{-- Search Suggestions --}}
                    @if(!empty($search))
                        <div class="absolute left-0 right-0 mb-2 mt-2 bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-200 dark:border-gray-700 overflow-hidden z-20 animate-slideDown">
                            <div class="p-3 text-sm text-gray-500 dark:text-gray-400 border-b border-gray-100 dark:border-gray-700">
                                Found {{ $groups->count() + $categories->count() }} results
                            </div>
                            <div class="max-h-96 overflow-y-auto">
                                @foreach($groups as $group)
                                    <a href="{{ route('library.group', $group->slug) }}" class="flex items-center px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                        <div class="w-8 h-8 bg-primary-100 dark:bg-primary-900/30 rounded-lg flex items-center justify-center mr-3">
                                            <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linecap="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                            </svg>
                                        </div>
                                        <div class="flex-1 text-left">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $group->name }}</div>
                                            <div class="text-xs text-gray-500">Group • {{ $group->resources_count }} resources</div>
                                        </div>
                                    </a>
                                @endforeach
                                @foreach($categories as $category)
                                    <a href="{{ route('library.category', $category->slug) }}" class="flex items-center px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                        <div class="w-8 h-8 bg-secondary-100 dark:bg-secondary-900/30 rounded-lg flex items-center justify-center mr-3">
                                            <svg class="w-4 h-4 text-secondary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linecap="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l5 5a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-5-5A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                            </svg>
                                        </div>
                                        <div class="flex-1 text-left">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $category->name }}</div>
                                            <div class="text-xs text-gray-500">Category • {{ $category->resources_count }} resources</div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

            </div>
        </div>

        {{-- Interactive Stats Bar --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-20 mt-2 mb-2">
            @php
                $stats = [
                    ['value' => number_format(App\Models\Resource::count()), 'label' => 'Resources', 'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253', 'color' => 'primary'],
                    ['value' => number_format(App\Models\Category::count()), 'label' => 'Categories', 'icon' => 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l5 5a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-5-5A1.994 1.994 0 013 12V7a4 4 0 014-4z', 'color' => 'secondary'],
                    ['value' => number_format(App\Models\ResourceGroup::count()), 'label' => 'Groups', 'icon' => 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10', 'color' => 'purple'],
                    ['value' => number_format(App\Models\User::count()), 'label' => 'Learners', 'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z', 'color' => 'green'],
                ];
            @endphp
            @foreach($stats as $stat)
                <div class="group relative bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-1 overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-r from-{{ $stat['color'] }}-500 to-{{ $stat['color'] }}-600 opacity-0 group-hover:opacity-10 transition-opacity duration-500"></div>
                    <div class="relative flex items-center space-x-4">
                        <div class="p-3 bg-{{ $stat['color'] }}-100 dark:bg-{{ $stat['color'] }}-900/30 rounded-xl group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-6 h-6 text-{{ $stat['color'] }}-600 dark:text-{{ $stat['color'] }}-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linecap="round" stroke-width="2" d="{{ $stat['icon'] }}" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stat['value'] }}</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $stat['label'] }}</div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Groups Section with Masonry Grid --}}
        @if($groups->isNotEmpty())
            <div class="mb-20">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <span class="text-sm font-semibold text-primary-600 dark:text-primary-400 uppercase tracking-wider">Collections</span>
                        <h2 class="text-3xl font-bold text-gray-900 dark:text-white mt-1">Resource Groups</h2>
                    </div>
                    @if($hasMoreGroups)
                        <button wire:click="showMoreGroups" 
                                class="group inline-flex items-center px-6 py-3 bg-white dark:bg-gray-800 rounded-full shadow-md hover:shadow-xl transition-all duration-300 border border-gray-200 dark:border-gray-700">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300 mr-2">View All ({{ $totalGroupsCount }})</span>
                            <svg class="w-4 h-4 text-gray-500 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linecap="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    @endif
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 auto-rows-fr">
                    @foreach($groups as $index => $group)
                        <a href="{{ route('library.group', $group->slug) }}" 
                       class="group block bg-white dark:bg-gray-800 rounded-xl shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden border border-gray-200 dark:border-gray-700 hover:border-primary-300 dark:hover:border-primary-600">
                        
                        <div class="h-40 bg-gray-100 dark:bg-gray-900">
                            <img src="{{ $group->cover_image?Storage::url($group->cover_image):'/assets/frontend/images/folder-groups.png' }}" 
                                    alt="{{ $group->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        </div>

                        <div class="p-5">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                                {{ $group->name }}
                            </h3>
                            
                            @if($group->description)
                                <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2 mb-3">
                                    {{ $group->description }}
                                </p>
                            @endif

                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $group->resources_count }} resources
                                </span>
                                
                                @if($group->children->isNotEmpty())
                                    <span class="text-xs px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-full">
                                        {{ $group->children->count() }} sub-groups
                                    </span>
                                @endif
                            </div>

                            @if($group->children->isNotEmpty())
                                <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-700">
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($group->children->take(3) as $child)
                                            <span class="text-xs px-2 py-1 bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-full">
                                                {{ $child->name }}
                                            </span>
                                        @endforeach
                                        @if($group->children->count() > 3)
                                            <span class="text-xs px-2 py-1 bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-full">
                                                +{{ $group->children->count() - 3 }} more
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Categories Section with Glass Cards --}}
        @if($categories->isNotEmpty())
            <div class="mb-20">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <span class="text-sm font-semibold text-secondary-600 dark:text-secondary-400 uppercase tracking-wider">Browse by</span>
                        <h2 class="text-3xl font-bold text-gray-900 dark:text-white mt-1">Categories</h2>
                    </div>
                    @if($hasMoreCategories)
                        <button wire:click="showMoreCategories" 
                                class="group inline-flex items-center px-6 py-3 bg-white dark:bg-gray-800 rounded-full shadow-md hover:shadow-xl transition-all duration-300 border border-gray-200 dark:border-gray-700">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300 mr-2">View All ({{ $totalCategoriesCount }})</span>
                            <svg class="w-4 h-4 text-gray-500 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linecap="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    @endif
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($categories as $category)
                        <a href="{{ route('library.category', $category->slug) }}" 
                       class="group block bg-white dark:bg-gray-800 rounded-xl shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden border border-gray-200 dark:border-gray-700 hover:border-primary-300 dark:hover:border-primary-600">
                        
                        <div class="p-5">
                            <div class="flex items-start justify-between mb-3">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                    {{ $category->name }}
                                </h3>
                                <span class="text-2xl opacity-20 group-hover:opacity-30 transition-opacity">
                                    📁
                                </span>
                            </div>
                            
                            @if($category->description)
                                <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2 mb-3">
                                    {{ $category->description }}
                                </p>
                            @endif

                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $category->resources_count }} resources
                                </span>
                                
                                @if($category->children->isNotEmpty())
                                    <span class="text-xs px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-full">
                                        {{ $category->children->count() }} sub-categories
                                    </span>
                                @endif
                            </div>

                            @if($category->children->isNotEmpty())
                                <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-700">
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($category->children->take(3) as $child)
                                            <span class="text-xs px-2 py-1 bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-full">
                                                {{ $child->name }}
                                            </span>
                                        @endforeach
                                        @if($category->children->count() > 3)
                                            <span class="text-xs px-2 py-1 bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-full">
                                                +{{ $category->children->count() - 3 }} more
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Featured Resources with Horizontal Scroll --}}
        @if($featuredResources->isNotEmpty())
            <div class="mb-20">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <span class="text-sm font-semibold text-yellow-600 dark:text-yellow-400 uppercase tracking-wider">Top Picks</span>
                        <h2 class="text-3xl font-bold text-gray-900 dark:text-white mt-1">Featured Resources</h2>
                    </div>
                    <a href="{{ route('library.resources', ['featured' => true]) }}" 
                       class="text-yellow-600 hover:text-yellow-700 dark:text-yellow-400 text-sm font-medium flex items-center gap-1">
                        View All
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linecap="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>

                <div class="relative">
                    <div class="flex space-x-6 overflow-x-auto pb-6 scrollbar-hide snap-x snap-mandatory">
                        @foreach($featuredResources as $resource)
                            <div class="flex-none w-72 snap-start" style="margin-right: 10px">
                                <x-library.resource-card :resource="$resource" featured />
                            </div>
                        @endforeach
                    </div>
                    
                    {{-- Gradient Fades --}}
                    <div class="absolute left-0 top-0 bottom-6 w-12 bg-gradient-to-r from-gray-50 to-transparent dark:from-gray-900 pointer-events-none"></div>
                    <div class="absolute right-0 top-0 bottom-6 w-12 bg-gradient-to-l from-gray-50 to-transparent dark:from-gray-900 pointer-events-none"></div>
                </div>
            </div>
        @endif

        {{-- Popular & Recent with Timeline Design --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-20">
            {{-- Popular Resources Timeline --}}
            @if($popularResources->isNotEmpty())
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center mb-6">
                        <div class="p-3 bg-orange-100 dark:bg-orange-900/30 rounded-xl mr-4">
                            <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linecap="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Trending Now</h3>
                    </div>
                    
                    <div class="space-y-4">
                        @foreach($popularResources as $index => $resource)
                            <a href="{{ route('library.resource.detail', $resource->slug) }}" 
                               class="group flex items-center p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 rounded-xl transition-all duration-300"
                               style="animation: slideInRight 0.3s ease-out {{ $index * 0.1 }}s both;">
                                <div class="relative">
                                    <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-xl overflow-hidden">
                                        @if($resource->thumbnail)
                                            <img src="{{ Storage::url($resource->thumbnail) }}" alt="" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center">
                                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linecap="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="absolute -top-2 -right-2 w-6 h-6 bg-orange-500 rounded-full flex items-center justify-center text-xs font-bold dark:text-white shadow-lg">
                                        #{{ $index + 1 }}
                                    </div>
                                </div>
                                <div class="ml-4 flex-1">
                                    <h4 class="text-base font-semibold text-gray-900 dark:text-white group-hover:text-orange-600 dark:group-hover:text-orange-400 transition-colors line-clamp-1">
                                        {{ $resource->title }}
                                    </h4>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                        {{ number_format($resource->download_count) }} downloads • {{ $resource->price > 0 ? 'Ksh'.number_format($resource->price) : 'Free' }}
                                    </p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Recent Resources Timeline --}}
            @if($recentResources->isNotEmpty())
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center mb-6">
                        <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-xl mr-4">
                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linecap="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Just Added</h3>
                    </div>
                    
                    <div class="relative">
                        {{-- Timeline Line --}}
                        <div class="absolute left-6 top-0 bottom-0 w-0.5 bg-gray-200 dark:bg-gray-700"></div>
                        
                        <div class="space-y-6">
                            @foreach($recentResources as $index => $resource)
                                <a href="{{ route('library.resource.detail', $resource->slug) }}" 
                                   class="group relative flex items-start pl-14"
                                   style="animation: slideInLeft 0.3s ease-out {{ $index * 0.1 }}s both;">
                                    {{-- Timeline Dot --}}
                                    <div class="absolute left-0 w-12 h-12 bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl shadow-lg flex items-center justify-center transform group-hover:scale-110 transition-transform duration-300">
                                        <svg class="w-6 h-6 dark:text-white " fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linecap="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                        </svg>
                                    </div>
                                    
                                    <div class="flex-1 bg-gray-50 dark:bg-gray-700/30 rounded-xl p-4 group-hover:shadow-lg transition-all duration-300">
                                        <h4 class="text-base font-semibold text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                            {{ $resource->title }}
                                        </h4>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                            Added {{ $resource->created_at->diffForHumans() }} • {{ $resource->price > 0 ? 'Ksh'.number_format($resource->price) : 'Free' }}
                                        </p>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- Newsletter/CTA Section with Parallax --}}
        <div class="relative mb-12">
            <div class="absolute inset-0 bg-gradient-to-r from-primary-600 via-secondary-600 to-purple-600 rounded-3xl blur-2xl opacity-30 animate-gradient"></div>
            <div class="relative bg-gradient-to-r from-primary-600 via-secondary-600 to-purple-600 rounded-3xl p-12 text-white overflow-hidden">
                {{-- Animated Background Pattern --}}
                <div class="absolute inset-0 opacity-10">
                    <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                        <pattern id="pattern-grid" x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse">
                            <rect x="0" y="0" width="1" height="20" fill="white" />
                            <rect x="0" y="0" width="20" height="1" fill="white" />
                        </pattern>
                        <rect x="0" y="0" width="100%" height="100%" fill="url(#pattern-grid)" />
                    </svg>
                </div>
                
                <div class="relative flex flex-col md:flex-row items-center justify-between gap-8">
                    <div class="text-center md:text-left">
                        <h3 class="text-3xl md:text-4xl font-bold mb-3">Ready to start learning?</h3>
                        <p class="text-xl">Join thousands of learners accessing premium resources daily</p>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="{{ route('library.resources') }}" 
                           class="px-8 py-4 bg-white text-primary-600 hover:bg-gray-100 font-semibold rounded-xl transition-all duration-300 shadow-xl hover:shadow-2xl transform hover:-translate-y-1 text-center">
                            Browse All Resources
                        </a>
                        <a href="{{ route('register') }}" 
                           class="px-8 py-4 bg-transparent border-2 border-white dark:text-white hover:bg-white/10 font-semibold rounded-xl transition-all duration-300 transform hover:-translate-y-1 text-center backdrop-blur-sm">
                            Create Free Account
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>


    @include('livewire.library.resource_models')
</div>