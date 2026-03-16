<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    {{-- Hero Section --}}
    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">
            Resource Library
        </h1>
        <p class="text-lg text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
            Browse our collection of educational resources, e-books, and documents
        </p>
    </div>

    {{-- Groups Section --}}
    @if($groups->isNotEmpty())
        <div class="mb-12">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 flex items-center">
                <svg class="w-6 h-6 mr-2 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linecap="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                Resource Groups
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach($groups as $group)
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

    {{-- Categories Section --}}
    @if($categories->isNotEmpty())
        <div class="mb-12">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 flex items-center">
                <svg class="w-6 h-6 mr-2 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linecap="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l5 5a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-5-5A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                </svg>
                Categories
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
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

    {{-- Featured Resources --}}
    @if($featuredResources->isNotEmpty())
        <div class="mb-12">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 flex items-center">
                <svg class="w-6 h-6 mr-2 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                </svg>
                Featured Resources
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($featuredResources as $resource)
                    <x-library.resource-card :resource="$resource" />
                @endforeach
            </div>
        </div>
    @endif

    {{-- Quick Search Bar --}}
    <div class="mt-12 text-center">
        <a href="{{ route('library.resources') }}" 
           class="inline-flex items-center px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linecap="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            Browse All Resources
        </a>
    </div>


    @include('livewire.library.resource_models')
</div>