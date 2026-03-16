<?php

namespace App\Livewire\Library;

use Livewire\WithPagination;
use App\Models\ResourceGroup;
use App\Models\Resource;

class GroupPage extends ResourcesPage
{
    use WithPagination;

    public $group;
    public $slug;
    public $subGroups;
    
    // Filters
    public $search = '';
    public $sortBy = 'latest';
    public $selectedSubGroup = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'sortBy' => ['except' => 'latest'],
        'selectedSubGroup' => ['except' => ''],
    ];

    public function mount($slug='')
    {
        $this->slug = $slug;
        $this->loadGroup();
    }

    public function loadGroup()
    {
        $this->group = ResourceGroup::with(['children', 'parent'])
            ->where('slug', $this->slug)
            ->where('is_active', true)
            ->firstOrFail();

        $this->subGroups = $this->group->children;
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedSelectedSubGroup()
    {
        $this->resetPage();
    }

    public function updatedSortBy()
    {
        $this->resetPage();
    }

    public function getGroupResourcesCount()
    {
        $groupIds = $this->group->getAllDescendantIds();
        return Resource::whereIn('group_id', $groupIds)
            ->where('is_published', true)
            ->count();
    }

    public function render()
    {
        // Get all descendant group IDs including current group
        $groupIds = $this->group->getAllDescendantIds();

        $query = Resource::with(['category'])
            ->whereIn('group_id', $groupIds)
            ->where('is_published', true);

        // Filter by sub-group
        if ($this->selectedSubGroup) {
            $query->where('group_id', $this->selectedSubGroup);
        }

        // Search filter
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%')
                  ->orWhere('author', 'like', '%' . $this->search . '%');
            });
        }

        // Sorting
        switch ($this->sortBy) {
            case 'latest':
                $query->latest();
                break;
            case 'oldest':
                $query->oldest();
                break;
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'popular':
                $query->orderBy('download_count', 'desc');
                break;
            case 'title':
                $query->orderBy('title', 'asc');
                break;
            default:
                $query->latest();
                break;
        }

        $resources = $query->paginate(12);

        return view('livewire.library.group-page', [
            'resources' => $resources,
            'totalResources' => $this->getGroupResourcesCount(),
            'breadcrumbs' => $this->getBreadcrumbs(),
        ])->layout('frontend.layouts.library-app');
    }

    protected function getBreadcrumbs()
    {
        $breadcrumbs = [];
        $current = $this->group;
        
        while ($current) {
            $breadcrumbs[] = [
                'name' => $current->name,
                'url' => route('library.group', $current->slug),
            ];
            $current = $current->parent;
        }
        
        return array_reverse($breadcrumbs);
    }
}