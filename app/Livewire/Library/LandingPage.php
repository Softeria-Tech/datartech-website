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
    
    public function mount()
    {
        // Get top-level groups (parent_id = null)
        $this->groups = ResourceGroup::with(['children'])
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(function($group) {
                $group->resources_count = $this->getGroupResourcesCount($group);
                return $group;
            });

        // Get top-level categories
        $this->categories = Category::with(['children'])
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->get()
            ->map(function($category) {
                $category->resources_count = $this->getCategoryResourcesCount($category);
                return $category;
            });

        // Get some featured/popular resources for the landing page
        $this->featuredResources = \App\Models\Resource::with(['category', 'group'])
            ->where('is_published', true)
            ->where('featured', true)
            ->limit(6)
            ->get();

        $this->popularResources = \App\Models\Resource::with(['category', 'group'])
            ->where('is_published', true)
            ->orderBy('download_count', 'desc')
            ->limit(6)
            ->get();

        $this->recentResources = \App\Models\Resource::with(['category', 'group'])
            ->where('is_published', true)
            ->latest()
            ->limit(6)
            ->get();
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
        ])->layout('frontend.layouts.library-app');
    }
}