@php
    $record = $record ?? $getRecord();
    $path = $preview? $record->file_path : $record->preview_file_path;
    $extension = pathinfo($path, PATHINFO_EXTENSION);
    $url = Storage::url($path);
@endphp


<div class="mt-2 border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
    @if(in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']))
        {{-- Image Preview --}}
        <div class="bg-gray-50 dark:bg-gray-800 p-4 text-center">
            <img 
                src="{{ $url }}" 
                alt="File preview" 
                class="max-w-full max-h-96 mx-auto object-contain rounded-lg shadow-sm"
                loading="lazy"
            >
            <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                <span class="font-medium">{{ strtoupper($extension) }}</span> image
            </div>
        </div>
    @elseif($extension === 'pdf')
        {{-- PDF Preview using Google Docs Viewer --}}
        <div class="bg-gray-50 dark:bg-gray-800 p-2">
            <iframe 
                src="{{$url}}" 
                style="width:100%; height:500px;" 
                frameborder="0"
                class="rounded-lg"
            ></iframe>
            <div class="mt-2 text-xs text-center text-gray-500 dark:text-gray-400">
                <span class="font-medium">PDF</span> document - 
                <a href="{{ $url }}" target="_blank" class="text-primary-600 hover:text-primary-900 dark:text-primary-400">
                    Open full screen
                </a>
            </div>
        </div>
    @elseif(in_array($extension, ['doc', 'docx']))
        {{-- Word Document Preview using Office Online --}}
        <div class="bg-gray-50 dark:bg-gray-800 p-4 text-center">
            <div class="flex flex-col items-center justify-center py-8">
                <x-filament::icon
                    icon="heroicon-o-document-text"
                    class="w-16 h-16 text-primary-600 dark:text-primary-400 mb-4"
                />
                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">
                    Microsoft Word Document
                </span>
                <span class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    {{ strtoupper($extension) }} file
                </span>
                <div class="mt-4 flex gap-3">
                    <a 
                        href="https://view.officeapps.live.com/op/embed.aspx?src={{ urlencode($url) }}"
                        target="_blank"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition"
                    >
                        <x-filament::icon icon="heroicon-o-eye" class="w-4 h-4 mr-2" />
                        Preview Online
                    </a>
                    <a 
                        href="{{ $url }}"
                        download
                        class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-lg transition"
                    >
                        <x-filament::icon icon="heroicon-o-arrow-down-tray" class="w-4 h-4 mr-2" />
                        Download
                    </a>
                </div>
            </div>
        </div>
    @else
        {{-- Generic File Preview --}}
        <div class="bg-gray-50 dark:bg-gray-800 p-4 text-center">
            <div class="flex flex-col items-center justify-center py-6">
                <x-filament::icon
                    icon="heroicon-o-document"
                    class="w-16 h-16 text-gray-500 dark:text-gray-400 mb-4"
                />
                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">
                    {{ strtoupper($extension) ?: 'Unknown' }} File
                </span>
                <span class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    {{ basename($path) }}
                </span>
                <div class="mt-4">
                    <a 
                        href="{{ $url }}"
                        download
                        class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition"
                    >
                        <x-filament::icon icon="heroicon-o-arrow-down-tray" class="w-4 h-4 mr-2" />
                        Download File
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>