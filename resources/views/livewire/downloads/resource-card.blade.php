<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow duration-300">
    <div class="relative">
        @if($resource->thumbnail)
            <img src="{{ asset('storage/' . $resource->thumbnail) }}" 
                 alt="{{ $resource->name }}"
                 class="w-full h-48 object-cover">
        @else
            <div class="w-full h-48 bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center">
                <svg class="w-16 h-16 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
            </div>
        @endif
        
        @if($downloadCount > 0)
            <span class="absolute top-2 right-2 bg-green-500 text-white text-xs px-2 py-1 rounded-full">
                Downloaded {{ $downloadCount }} {{ Str::plural('time', $downloadCount) }}
            </span>
        @endif
    </div>

    <div class="p-4">
        <h3 class="font-semibold text-lg mb-2">{{ $resource->name }}</h3>
        <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ $resource->description }}</p>
        
        @if($lastDownloaded)
            <p class="text-xs text-gray-500 mb-3">
                Last downloaded: {{ $lastDownloaded->diffForHumans() }}
            </p>
        @endif

        <div class="flex items-center justify-between">
            <span class="text-sm text-gray-500">
                {{ $resource->downloads_count ?? 0 }} total downloads
            </span>
            
            <button wire:click="download" 
                    class="inline-flex items-center px-3 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                </svg>
                Download
            </button>
        </div>
    </div>
</div>