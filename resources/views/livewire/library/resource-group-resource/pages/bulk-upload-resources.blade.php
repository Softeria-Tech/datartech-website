<x-filament-panels::page>
    <x-filament-panels::form wire:submit="processUpload">
        {{ $this->form }}
        
        <div class="flex justify-end mt-6">
            <x-filament::button type="submit" color="success" icon="heroicon-o-cloud-arrow-up" :disabled="$isProcessing">
                Save Uploads
            </x-filament::button>
        </div>
    </x-filament-panels::form>
</x-filament-panels::page>