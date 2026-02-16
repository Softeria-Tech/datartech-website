<?php

namespace App\Livewire\Library;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Resource;
use App\Models\Category;
use App\Models\Order;
use App\Models\UserDownload;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ResourcesPage extends Component
{
    use WithPagination, WithFileUploads;

    // Filters
    public $search = '';
    public $selectedCategory = null;
    public $selectedType = '';
    public $selectedPrice = '';
    public $sortBy = 'latest';
    public $showSubscriptionOnly = false;
    
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
    
    // Categories with counts
    public $categories;
    
    protected $queryString = [
        'search' => ['except' => ''],
        'selectedCategory' => ['except' => ''],
        'selectedType' => ['except' => ''],
        'sortBy' => ['except' => 'latest'],
    ];

    public function mount()
    {
        // Load categories with resource counts
        $this->loadCategories();
    }

    public function loadCategories()
    {
        $this->categories = Category::withCount('resources')
            ->whereHas('resources', fn($q) => $q->where('is_published', true))
            ->orderBy('sort_order')
            ->get();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedSelectedCategory()
    {
        $this->resetPage();
    }

    public function updatedSelectedType()
    {
        $this->resetPage();
    }

    public function updatedSortBy()
    {
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
        } elseif ($this->previewResource->file_path && $this->previewResource->delivery_type === 'upload') {
            // If no preview file, use the main file but we'll show limited preview
            $this->previewFileUrl = Storage::url($this->previewResource->file_path);
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
        $tax = $subtotal * 0.0; // Configure tax as needed
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
            'payment_method' => 'mpesa', // Default, can be changed
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
        
        return Auth::user()->orders()
            ->where('resource_id', $resourceId)
            ->where('payment_status', 'paid')
            ->exists();
    }

    // Check if user has active subscription
    public function userHasSubscription()
    {
        if (!Auth::check()) return false;
        
        return Auth::user()->subscriptions()
            ->active()
            ->exists();
    }

    // Get download URL for purchased resources
    public function getDownloadUrl($resourceId)
    {
        if (!Auth::check()) return '#';
        
        $canDownload = Auth::user()->orders()
            ->where('resource_id', $resourceId)
            ->where('payment_status', 'paid')
            ->exists();
            
        if ($canDownload) {
            $resource = Resource::find($resourceId);
            return $resource->file_path ? Storage::url($resource->file_path) : '#';
        }
        
        return '#';
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

        // Category filter
        if (!empty($this->selectedCategory)) {
            $query->where('category_id', $this->selectedCategory);
        }

        // Resource type filter (based on file type)
        if (!empty($this->selectedType)) {
            switch ($this->selectedType) {
                case 'pdf':
                    $query->where('file_path', 'like', '%.pdf');
                    break;
                case 'word':
                    $query->where(function($q) {
                        $q->where('file_path', 'like', '%.doc')
                          ->orWhere('file_path', 'like', '%.docx');
                    });
                    break;
                case 'excel':
                    $query->where(function($q) {
                        $q->where('file_path', 'like', '%.xls')
                          ->orWhere('file_path', 'like', '%.xlsx');
                    });
                    break;
                case 'ebook':
                    $query->where(function($q) {
                        $q->where('file_path', 'like', '%.epub')
                          ->orWhere('file_path', 'like', '%.mobi')
                          ->orWhere('isbn', '!=', '');
                    });
                    break;
            }
        }

        // Price filter
        if (!empty($this->selectedPrice)) {
            switch ($this->selectedPrice) {
                case 'free':
                    $query->where('price', 0);
                    break;
                case 'under10':
                    $query->where('price', '<', 10);
                    break;
                case '10to25':
                    $query->whereBetween('price', [10, 25]);
                    break;
                case '25to50':
                    $query->whereBetween('price', [25, 50]);
                    break;
                case 'over50':
                    $query->where('price', '>', 50);
                    break;
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
            'categories' => $this->categories,
            'isAuthenticated' => Auth::check(),
            'user' => Auth::user(),
        ])->layout('frontend.layouts.library-app');
    }
}