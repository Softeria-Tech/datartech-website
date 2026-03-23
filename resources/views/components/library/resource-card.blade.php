<div wire:key="{{ $resource->id }}" class="group bg-white dark:bg-gray-800 rounded-xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-200 dark:border-gray-700 hover:border-primary-300 dark:hover:border-primary-600">

 {{-- Category Badge Enhancement --}}
    <div class="absolute top-3 left-3 z-10">
        @if($resource->category)
            <span class="px-2 py-1 bg-black/60 backdrop-blur-sm text-white text-xs font-medium rounded-lg">
                {{ $resource->category->name }}
            </span>
        @endif
    </div>

    <div class="relative h-48 overflow-hidden bg-gray-100 dark:bg-gray-900">
        @if ($resource->thumbnail)
            <img src="{{ Storage::url($resource->thumbnail) }}" alt="{{ $resource->title }}"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
        @else
            <div class="w-full h-full flex items-center justify-center">
                <img src="{{asset('assets/frontend/images/default-resource.png')}}" alt=""
                class="w-full h-full  object-cover group-hover:scale-105 transition-transform duration-300">
            </div>
        @endif

        {{-- Badges --}}
        <div class="absolute top-3 left-3 flex flex-col gap-2">
            @if ($resource->requires_subscription)
                <span
                    class="px-2 py-1 bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200 text-xs font-medium rounded-full">
                    🔒 Subscribers
                </span>
            @endif
            @if ($resource->discount_price && $resource->discount_ends_at?->isFuture())
                <span
                    class="px-2 py-1 bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 text-xs font-medium rounded-full">
                    -{{ round((($resource->price - $resource->discount_price) / $resource->price) * 100) }}%
                </span>
            @endif
        </div>

        {{-- Quick View Button --}}
        <button wire:click="quickView('{{ $resource->slug }}')"
            class="absolute top-3 right-3 p-2 bg-white dark:bg-gray-800 rounded-lg shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 hover:bg-gray-100 dark:hover:bg-gray-700">
            <svg class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linecap="round" stroke-width="2"
                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linecap="round" stroke-width="2"
                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
        </button>
    </div>

    {{-- Resource Details --}}
    <div class="p-5">
        <div class="flex items-start justify-between mb-2">
            <div>
                @if ($resource->category)
                    <span class="text-xs text-primary-600 dark:text-primary-400 font-medium">
                        {{ $resource->category->name }}
                    </span>
                @endif
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white line-clamp-2">
                    <a href="{{ route('library.resource.detail', $resource->slug) }}"
                        class="hover:text-primary-600 dark:hover:text-primary-400">
                        {{ $resource->title }}
                    </a>
                </h3>
            </div>
        </div>

        @if ($resource->author)
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                By {{ $resource->author }}
            </p>
        @endif

        <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2 mb-4">
            {!! $resource->short_description ?? Str::limit($resource->description, 100) !!}
        </p>

        <div class="flex items-center justify-between">
            <div>
            @if($this->userHasPurchased($resource->id))
                <a href="{{ route('library.resource.detail', $resource->slug) }}" class="p-2 text-green-600 dark:text-green-400 hover:bg-green-50 dark:hover:bg-green-900/20 rounded-lg transition">
                   Download
                </a>
            @else
                @if ($resource->price == 0)
                    <span class="text-2xl font-bold text-green-600 dark:text-green-400">
                        Free
                    </span>
                @elseif($resource->discount_price && $resource->discount_ends_at?->isFuture())
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
            @endif
            </div>

            <div class="flex gap-2">
                @if ($resource->price > 0)
                    <button wire:click="previewResourceItem('{{ $resource->slug }}')"
                        class="p-2 text-gray-600 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linecap="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linecap="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </button>
                @endif

                @auth
                    @if ($this->userHasPurchased($resource->id))
                        <a href="{{ route('library.resource.detail', $resource->slug) }}"
                            class="p-2 text-green-600 dark:text-green-400 hover:bg-green-50 dark:hover:bg-green-900/20 rounded-lg transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linecap="round" stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                        </a>
                    @else
                        <a href="{{ route('library.resource.detail', $resource->slug) }}"class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition">
                            Buy
                        </a>
                    @endif
                @else
                    <a href="{{ route('login', ['redirect' => route('library.resources')]) }}"
                        class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-lg transition">
                        {{ $resource->price > 0 ? 'Buy' : 'Download' }}
                    </a>
                @endauth
            </div>
        </div>
    </div>
</div>
