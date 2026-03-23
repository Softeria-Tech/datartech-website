<?php

namespace App\Livewire\Library;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Resource;
use App\Models\Category;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ResourcesPage extends Component
{
    use WithPagination, WithFileUploads;

    // Filters
    public $search = '';
    public $selectedParentCategory = null;
    public $selectedSubCategory = null;
    public $selectedGrandCategory = null;
    public $sortBy = 'latest';
    public $showSubscriptionOnly = false;
    
    // Category hierarchy data
    public $parentCategories = [];
    public $subCategories = [];
    public $grandCategories = [];
    
    // Preview modal
    public $showPreviewModal = false;
    public $previewResource = null;
    public $previewFileUrl = null;
    
    // Quick view
    public $showQuickView = false;
    public $quickViewResource = null;
    
    // Purchase flow
    public $showPurchaseModal = false;
    public $purchaseResource = null;
    public $purchaseQuantity = 1;
    public $agreeTerms = false;
    
    // Mobile filter state
    public $showMobileFilters = false;
    
    protected $queryString = [
        'search' => ['except' => ''],
        'selectedParentCategory' => ['except' => ''],
        'selectedSubCategory' => ['except' => ''],
        'selectedGrandCategory' => ['except' => ''],
        'sortBy' => ['except' => 'latest'],
    ];

    public function mount($slug = '')
    {
        // Load parent categories
        $this->loadParentCategories();
    }

    public function loadParentCategories()
    {
        $this->parentCategories = Category::whereNull('parent_id')
            ->whereHas('resources', fn($q) => $q->where('is_published', true))
            ->withCount(['resources' => fn($q) => $q->where('is_published', true)])
            ->orderBy('sort_order')
            ->get();
    }

    public function updatedSelectedParentCategory()
    {
        // Reset sub and grand categories when parent changes
        $this->selectedSubCategory = null;
        $this->selectedGrandCategory = null;
        $this->subCategories = [];
        $this->grandCategories = [];
        
        if ($this->selectedParentCategory) {
            // Load subcategories
            $this->subCategories = Category::where('parent_id', $this->selectedParentCategory)
                ->whereHas('resources', fn($q) => $q->where('is_published', true))
                ->withCount(['resources' => fn($q) => $q->where('is_published', true)])
                ->orderBy('sort_order')
                ->get();
        }
        
        $this->resetPage();
    }

    public function updatedSelectedSubCategory()
    {
        // Reset grand category when sub changes
        $this->selectedGrandCategory = null;
        $this->grandCategories = [];
        
        if ($this->selectedSubCategory) {
            // Load grand categories
            $this->grandCategories = Category::where('parent_id', $this->selectedSubCategory)
                ->whereHas('resources', fn($q) => $q->where('is_published', true))
                ->withCount(['resources' => fn($q) => $q->where('is_published', true)])
                ->orderBy('sort_order')
                ->get();
        }
        
        $this->resetPage();
    }

    public function updatedSelectedGrandCategory()
    {
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedSortBy()
    {
        $this->resetPage();
    }

    public function updatedShowSubscriptionOnly()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->selectedParentCategory = null;
        $this->selectedSubCategory = null;
        $this->selectedGrandCategory = null;
        $this->search = '';
        $this->sortBy = 'latest';
        $this->showSubscriptionOnly = false;
        $this->subCategories = [];
        $this->grandCategories = [];
        $this->resetPage();
    }

    // Preview Resource
    public function previewResourceItem($slug)
    {
        $this->previewResource = Resource::where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();
        
        // Get preview file URL
        if ($this->previewResource->preview_file_path) {
            $this->previewFileUrl = Storage::url($this->previewResource->preview_file_path);
        }
        
        $this->showPreviewModal = true;
    }

    // Quick View
    public function quickView($slug)
    {
        $this->quickViewResource = Resource::with('category')
            ->where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();
        
        $this->showQuickView = true;
    }

    // Purchase Modal
    public function initiatePurchase($slug)
    {
        if (!Auth::check()) {
            session()->flash('message', 'Please login to purchase resources.');
            return redirect()->route('login', ['redirect' => route('library.resources')]);
        }

        $this->purchaseResource = Resource::where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();
        
        $this->purchaseQuantity = 1;
        $this->agreeTerms = false;
        $this->showPurchaseModal = true;
    }

    // Process Purchase
    public function processPurchase()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if (!$this->agreeTerms) {
            $this->addError('agreeTerms', 'You must agree to the terms and conditions.');
            return;
        }

        $resource = $this->purchaseResource;
        
        // Calculate totals
        $subtotal = $resource->price * $this->purchaseQuantity;
        $tax = $subtotal * 0.0;
        $total = $subtotal + $tax;

        // Create order
        $order = Order::firstOrCreate([
            'user_id' => Auth::id(),
            'resource_id' => $resource->id,
            'order_status' => 'processing',
        ],[
            'order_number' => 'ORD-' . strtoupper(uniqid()),
            'user_id' => Auth::id(),
            'resource_id' => $resource->id,
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $total,
            'payment_method' => 'mpesa',
            'payment_status' => 'pending',
            'order_status' => 'processing',
            'total_items' => $this->purchaseQuantity,
            'order_data' => [
                'resource_title' => $resource->title,
                'resource_price' => $resource->price,
                'quantity' => $this->purchaseQuantity,
                'purchased_at' => now()->toDateTimeString(),
            ],
        ]);

        // Close modal and redirect to checkout
        $this->showPurchaseModal = false;
        
        return redirect()->route('checkout', ['order' => $order->order_number]);
    }

    // Check if user already purchased
    public function userHasPurchased($resourceId)
    {
        if (!Auth::check()) return false;

        if ($this->userHasSubscription()) {
            return true;
        }

        $resource = Resource::find($resourceId);
        if($resource->price==0){
            return true;
        }
        
        return Auth::user()->orders()->where('resource_id', $resourceId)->where('payment_status', 'paid')->exists();
    }

    // Check if user has active subscription
    public function userHasSubscription()
    {
        if (!Auth::check()) return false;
        
        return Auth::user()->subscriptions()->active()->exists();
    }

    public function render()
    {
        $query = Resource::query()
            ->with(['category'])
            ->where('is_published', true);

        // Search filter
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%')
                  ->orWhere('author', 'like', '%' . $this->search . '%')
                  ->orWhere('tags', 'like', '%' . $this->search . '%');
            });
        }

        // Category filtering - use the deepest selected category
        $selectedCategoryId = $this->selectedGrandCategory ?? $this->selectedSubCategory ?? $this->selectedParentCategory;
        
        if ($selectedCategoryId) {
            // Get all descendant category IDs for comprehensive filtering
            $category = Category::find($selectedCategoryId);
            if ($category) {
                $categoryIds = $category->getAllDescendantIds();
                $query->whereIn('category_id', $categoryIds);
            }
        }

        // Subscription only filter
        if ($this->showSubscriptionOnly) {
            $query->where('requires_subscription', true);
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

        return view('livewire.library.resources-page', [
            'resources' => $resources,
            'isAuthenticated' => Auth::check(),
            'user' => Auth::user(),
        ])->layout('frontend.layouts.library-app');
    }
}