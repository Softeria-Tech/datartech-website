<?php

namespace App\Livewire\Downloads;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\UserDownload;
use App\Models\Resource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DownloadsIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $accessType = '';
    public $dateRange = '';
    public $sortField = 'downloaded_at';
    public $sortDirection = 'desc';
    public $perPage = 15;
    
    // For download modal
    public $showDownloadModal = false;
    public $selectedDownload = null;
    public $downloadUrl = '';
    
    protected $queryString = [
        'search' => ['except' => ''],
        'accessType' => ['except' => ''],
        'dateRange' => ['except' => ''],
        'sortField' => ['except' => 'downloaded_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedAccessType()
    {
        $this->resetPage();
    }

    public function updatedDateRange()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function prepareDownload($downloadId)
    {
        $this->selectedDownload = UserDownload::with('resource')
            ->where('user_id', Auth::id())
            ->findOrFail($downloadId);

        // Increment download count
        $this->selectedDownload->increment('download_count');
        
        // Generate download URL (you'll need to implement this based on your storage setup)
        $this->downloadUrl = route('library.resource.detail', $this->selectedDownload->resource->slug);
        
        $this->showDownloadModal = true;
    }

    public function closeModal()
    {
        $this->showDownloadModal = false;
        $this->selectedDownload = null;
        $this->downloadUrl = '';
    }

    public function getDownloadsProperty()
    {
        return UserDownload::query()
            ->where('user_id', Auth::id())
            ->with(['resource', 'order', 'membershipPackage'])
            ->when($this->search, function ($query) {
                $query->whereHas('resource', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->accessType, function ($query) {
                $query->where('access_type', $this->accessType);
            })
            ->when($this->dateRange, function ($query) {
                switch ($this->dateRange) {
                    case 'today':
                        $query->whereDate('downloaded_at', today());
                        break;
                    case 'week':
                        $query->whereBetween('downloaded_at', [now()->startOfWeek(), now()->endOfWeek()]);
                        break;
                    case 'month':
                        $query->whereMonth('downloaded_at', now()->month)
                              ->whereYear('downloaded_at', now()->year);
                        break;
                    case 'year':
                        $query->whereYear('downloaded_at', now()->year);
                        break;
                }
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);
    }

    public function render()
    {
        return view('livewire.downloads.index', [
            'downloads' => $this->downloads,
            'stats' => $this->getStats(),
            'recentActivities' => $this->getRecentActivities(),
        ])->layout('frontend.layouts.library-app');
    }

    private function getStats()
    {
        $userId = Auth::id();
        
        return [
            'total_downloads' => UserDownload::where('user_id', $userId)->count(),
            'unique_resources' => UserDownload::where('user_id', $userId)
                ->distinct('resource_id')
                ->count('resource_id'),
            'membership_downloads' => UserDownload::where('user_id', $userId)
                ->where('access_type', 'membership')
                ->count(),
            'purchased_downloads' => UserDownload::where('user_id', $userId)
                ->where('access_type', 'purchase')
                ->count(),
            'downloads_this_month' => UserDownload::where('user_id', $userId)
                ->whereMonth('downloaded_at', now()->month)
                ->whereYear('downloaded_at', now()->year)
                ->count(),
        ];
    }

    private function getRecentActivities()
    {
        return UserDownload::where('user_id', Auth::id())
            ->with('resource')
            ->latest('downloaded_at')
            ->limit(5)
            ->get()
            ->map(function ($download) {
                return [
                    'id' => $download->id,
                    'resource_name' => $download->resource->name,
                    'time_ago' => $download->downloaded_at->diffForHumans(),
                    'access_type' => $download->access_type,
                    'icon' => $this->getActivityIcon($download->access_type),
                ];
            });
    }

    private function getActivityIcon($type)
    {
        return $type === 'membership' 
            ? '<svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"></path></svg>'
            : '<svg class="w-5 h-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20"><path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"></path><path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd"></path></svg>';
    }
}