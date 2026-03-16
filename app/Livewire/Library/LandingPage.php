<?php

namespace App\Livewire\Library;

use App\Models\ResourceGroup;
use App\Models\Category;

class LandingPage extends ResourcesPage
{
    public $featuredResources;
    public $popularResources;
    public $recentResources;
    public $groups;
    public $categories;
    
    // Search
    public $search = '';
    public $showAllGroups = false;
    public $showAllCategories = false;
    
    // Limits
    protected $initialLimit = 15;
    protected $expandedLimit = 30;
    
    public function mount()
    {
        $this->loadGroups();
        $this->loadCategories();
        $this->loadFeaturedResources();
    }
    
    public function updatedSearch()
    {
        $this->showAllGroups = false;
        $this->showAllCategories = false;
        $this->loadGroups();
        $this->loadCategories();
    }
    
    public function loadGroups()
    {
        $query = ResourceGroup::with(['children'])
            ->whereNull('parent_id')
            ->where('is_active', true);
            
        // Apply search filter
        if (!empty($this->search)) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }
        
        $limit = $this->showAllGroups ? $this->expandedLimit : $this->initialLimit;
        
        $this->groups = $query->orderBy('sort_order')
            ->orderBy('name')
            ->limit($limit)
            ->get()
            ->map(function($group) {
                $group->resources_count = $this->getGroupResourcesCount($group);
                return $group;
            });
            
        $this->totalGroupsCount = $query->count();
    }
    
    public function loadCategories()
    {
        $query = Category::with(['children'])
            ->whereNull('parent_id');
            
        // Apply search filter
        if (!empty($this->search)) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }
        
        $limit = $this->showAllCategories ? $this->expandedLimit : $this->initialLimit;
        
        $this->categories = $query->orderBy('sort_order')
            ->orderBy('name')
            ->limit($limit)
            ->get()
            ->map(function($category) {
                $category->resources_count = $this->getCategoryResourcesCount($category);
                return $category;
            });
            
        $this->totalCategoriesCount = $query->count();
    }
    
    public function loadFeaturedResources()
    {
        $this->featuredResources = \App\Models\Resource::with(['category', 'group'])
            ->where('is_published', true)
            ->where('featured', true)
            ->inRandomOrder()
            ->limit(8)
            ->get();

        $this->popularResources = \App\Models\Resource::with(['category', 'group'])
            ->where('is_published', true)
            ->orderBy('download_count', 'desc')
            ->limit(4)
            ->get();

        $this->recentResources = \App\Models\Resource::with(['category', 'group'])
            ->where('is_published', true)
            ->latest()
            ->limit(4)
            ->get();
    }
    
    public function showMoreGroups()
    {
        $this->showAllGroups = true;
        $this->loadGroups();
    }
    
    public function showMoreCategories()
    {
        $this->showAllCategories = true;
        $this->loadCategories();
    }
    
    private function getGroupResourcesCount($group)
    {
        $groupIds = $group->getAllDescendantIds();
        return \App\Models\Resource::whereIn('group_id', $groupIds)
            ->where('is_published', true)
            ->count();
    }
    
    private function getCategoryResourcesCount($category)
    {
        $categoryIds = $category->getAllDescendantIds();
        return \App\Models\Resource::whereIn('category_id', $categoryIds)
            ->where('is_published', true)
            ->count();
    }
    
    public function render()
    {
        return view('livewire.library.landing-page', [
            'groups' => $this->groups,
            'categories' => $this->categories,
            'featuredResources' => $this->featuredResources,
            'popularResources' => $this->popularResources,
            'recentResources' => $this->recentResources,
            'hasMoreGroups' => !$this->showAllGroups && $this->totalGroupsCount > $this->initialLimit,
            'hasMoreCategories' => !$this->showAllCategories && $this->totalCategoriesCount > $this->initialLimit,
        ])->layout('frontend.layouts.library-app');
    }
}