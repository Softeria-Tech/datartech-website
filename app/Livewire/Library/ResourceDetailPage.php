<?php

namespace App\Livewire\Library;

use Livewire\Component;
use App\Models\Resource;
use App\Models\Category;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ResourceDetailPage extends Component
{
    public $resource;
    public $slug;
    
    // Related resources
    public $relatedResources;
    
    // Preview modal
    public $showPreviewModal = false;
    public $previewFileUrl = null;
    
    // Purchase flow
    public $showPurchaseModal = false;
    public $purchaseQuantity = 1;
    public $agreeTerms = false;
    
    // Reviews/Tabs
    public $activeTab = 'description';
    
    protected $listeners = ['refreshResource' => '$refresh'];

    public function mount($slug)
    {
        $this->slug = $slug;
        $this->loadResource();
    }

    public function loadResource()
    {
        $this->resource = Resource::with(['category', 'category.parent'])
            ->where('slug', $this->slug)
            ->where('is_published', true)
            ->firstOrFail();

        // Increment view count if you have one
        // $this->resource->increment('views');
        
        $this->loadRelatedResources();
    }

    public function loadRelatedResources()
    {
        // Get resources from same category
        $this->relatedResources = Resource::where('id', '!=', $this->resource->id)
            ->where('is_published', true)
            ->where(function($query) {
                $query->where('category_id', $this->resource->category_id)
                    ->orWhere('author', $this->resource->author);
            })
            ->limit(4)
            ->get();

        // If not enough related resources, get featured or recent ones
        if ($this->relatedResources->count() < 4) {
            $additional = Resource::where('id', '!=', $this->resource->id)
                ->where('is_published', true)
                ->whereNotIn('id', $this->relatedResources->pluck('id'))
                ->featured()
                ->latest()
                ->limit(4 - $this->relatedResources->count())
                ->get();
                
            $this->relatedResources = $this->relatedResources->concat($additional);
        }
    }

    public function previewResource()
    {
        // Get preview file URL
        if ($this->resource->preview_file_path) {
            $this->previewFileUrl = Storage::url($this->resource->preview_file_path);
        } elseif ($this->resource->file_path && $this->resource->delivery_type === 'upload') {
            $this->previewFileUrl = Storage::url($this->resource->file_path);
        }
        
        $this->showPreviewModal = true;
    }

    public function initiatePurchase()
    {
        if (!Auth::check()) {
            session()->flash('message', 'Please login to purchase this resource.');
            return redirect()->route('login', ['redirect' => route('library.resource.detail', $this->resource->slug)]);
        }

        $this->purchaseQuantity = 1;
        $this->agreeTerms = false;
        $this->showPurchaseModal = true;
    }

    public function processPurchase()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if (!$this->agreeTerms) {
            $this->addError('agreeTerms', 'You must agree to the terms and conditions.');
            return;
        }

        $resource = $this->resource;
        
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

    public function userHasPurchased()
    {
        if (!Auth::check()) return false;

        $isSubscribed = Auth::user()->subscriptions()->active()->exists();
        if ($isSubscribed) {
            return true;
        }

        if($this->resource->price==0){
            return true;
        }
        
        return Auth::user()->orders()->where('resource_id', $this->resource->id)->where('payment_status', 'paid')->exists();
    }

    public function getDownloadUrl()
    {
        if (!Auth::check()) return '#';
        
        $canDownload = Auth::user()->orders()->where('resource_id', $this->resource->id)->where('payment_status', 'paid')->exists();
            
        if ($canDownload && $this->resource->file_path) {
            return Storage::url($this->resource->file_path);
        }
        
        return '#';
    }

    public function getFileTypeIcon()
    {
        $extension = pathinfo($this->resource->file_path ?? $this->resource->external_url, PATHINFO_EXTENSION);
        
        return match(strtolower($extension)) {
            'pdf' => 'heroicon-o-document',
            'doc', 'docx' => 'heroicon-o-document-text',
            'xls', 'xlsx' => 'heroicon-o-table-cells',
            'jpg', 'jpeg', 'png', 'gif', 'webp' => 'heroicon-o-photo',
            'zip', 'rar' => 'heroicon-o-archive-box',
            default => 'heroicon-o-document',
        };
    }

    public function getFileTypeColor()
    {
        $extension = pathinfo($this->resource->file_path ?? $this->resource->external_url, PATHINFO_EXTENSION);
        
        return match(strtolower($extension)) {
            'pdf' => 'danger',
            'doc', 'docx' => 'primary',
            'xls', 'xlsx' => 'success',
            'jpg', 'jpeg', 'png', 'gif', 'webp' => 'warning',
            default => 'gray',
        };
    }

    public function markDownloaded()
    {
        if (!Auth::check()) return;

        $user = Auth::user();
        $resource = $this->resource;

        $resource->download_count++;
        $resource->save();

        $userDownload = $user->downloads()->where('resource_id', $resource->id)->first();
        if ($userDownload) {
            $userDownload->download_count++;
            $userDownload->downloaded_at = now();
            $userDownload->save();
        } else {
            $user->downloads()->create([
                'resource_id' => $resource->id,
                'download_count' => 1,
                'downloaded_at' => now(),
                'access_type' => $user->subscriptions()->active()->exists() ? 'subscription' : 'free',
            ]);
        }

    }

    public function render()
    {
        return view('livewire.library.resource-detail-page', [
            'isAuthenticated' => Auth::check(),
            'user' => Auth::user(),
            'hasPurchased' => $this->userHasPurchased(),
            'downloadUrl' => $this->getDownloadUrl(),
            'fileTypeIcon' => $this->getFileTypeIcon(),
            'fileTypeColor' => $this->getFileTypeColor(),
        ])->layout('frontend.layouts.library-app');
    }
}