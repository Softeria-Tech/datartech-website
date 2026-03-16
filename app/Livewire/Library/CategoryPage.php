<?php

namespace App\Livewire\Library;

use Livewire\WithPagination;
use App\Models\Category;
use App\Models\Resource;

class CategoryPage extends ResourcesPage
{
    use WithPagination;

    public $category;
    public $slug;
    public $subCategories;
    
    // Filters
    public $search = '';
    public $sortBy = 'latest';
    public $selectedSubCategory = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'sortBy' => ['except' => 'latest'],
        'selectedSubCategory' => ['except' => ''],
    ];

    public function mount($slug = '')
    {
        $this->slug = $slug;
        $this->loadCategory();
    }

    public function loadCategory()
    {
        $this->category = Category::with(['children', 'parent'])
            ->where('slug', $this->slug)
            ->firstOrFail();

        $this->subCategories = $this->category->children;
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedSelectedSubCategory()
    {
        $this->resetPage();
    }

    public function updatedSortBy()
    {
        $this->resetPage();
    }

    public function getCategoryResourcesCount()
    {
        $categoryIds = $this->category->getAllDescendantIds();
        return Resource::whereIn('category_id', $categoryIds)
            ->where('is_published', true)
            ->count();
    }

    public function render()
    {
        // Get all descendant category IDs including current category
        $categoryIds = $this->category->getAllDescendantIds();

        $query = Resource::with(['category'])
            ->whereIn('category_id', $categoryIds)
            ->where('is_published', true);

        // Filter by sub-category
        if ($this->selectedSubCategory) {
            $query->where('category_id', $this->selectedSubCategory);
        }

        // Search filter
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%')
                  ->orWhere('author', 'like', '%' . $this->search . '%')
                  ->orWhere('tags', 'like', '%' . $this->search . '%');
            });
        }

        // Price range filter can be added here if needed
        // You can add more filters similar to ResourcesPage

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

        return view('livewire.library.category-page', [
            'resources' => $resources,
            'totalResources' => $this->getCategoryResourcesCount(),
            'breadcrumbs' => $this->getBreadcrumbs(),
        ])->layout('frontend.layouts.library-app');
    }

    protected function getBreadcrumbs()
    {
        $breadcrumbs = [];
        $current = $this->category;
        
        while ($current) {
            $breadcrumbs[] = [
                'name' => $current->name,
                'url' => route('library.category', $current->slug),
            ];
            $current = $current->parent;
        }
        
        return array_reverse($breadcrumbs);
    }
}