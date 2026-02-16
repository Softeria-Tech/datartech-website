<?php
// app/Livewire/Downloads/ResourceCard.php

namespace App\Livewire\Downloads;

use Livewire\Component;
use App\Models\Resource;
use Illuminate\Support\Facades\Auth;

class ResourceCard extends Component
{
    public Resource $resource;
    public $downloadCount = 0;
    public $lastDownloaded = null;

    public function mount(Resource $resource)
    {
        $download = $resource->downloads()
            ->where('user_id', Auth::id())
            ->latest('downloaded_at')
            ->first();

        if ($download) {
            $this->downloadCount = $download->download_count;
            $this->lastDownloaded = $download->downloaded_at;
        }
    }

    public function render()
    {
        return view('livewire.downloads.resource-card');
    }
}