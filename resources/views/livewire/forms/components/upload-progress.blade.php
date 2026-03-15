@php
    $progress = $getState() ?? 0;
@endphp

<div class="mt-4 p-4 bg-gray-50 rounded-lg">
    <div class="flex justify-between mb-2">
        <span class="text-sm font-medium text-gray-700">Upload Progress</span>
        <span class="text-sm font-medium text-gray-700">{{ $progress }}%</span>
    </div>
    
    <div class="w-full bg-gray-200 rounded-full h-2.5">
        <div class="bg-primary-600 h-2.5 rounded-full transition-all duration-300" style="width: {{ $progress }}%"></div>
    </div>
    
    <p class="text-xs text-gray-500 mt-2">
        Processing files... Please don't close this window.
    </p>
</div>