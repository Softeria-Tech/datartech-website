<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\ResourceResource;
use App\Models\Resource;
use App\Models\ResourceGroup;
use App\Models\UserDownload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ResourceController extends Controller
{
    public function index(Request $request)
    {
        $query = Resource::published()
            ->with(['category', 'membershipPackages']);

        // Filter by category
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by group
        if ($request->has('group_id')) {
            $query->where('group_id', $request->group_id);
        }

        // Filter by featured
        if ($request->has('featured') && $request->featured) {
            $query->featured();
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%")
                    ->orWhere('author', 'LIKE', "%{$search}%");
            });
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $resources = $query->paginate($request->get('per_page', 15));

        return ResourceResource::collection($resources);
    }

    public function show(Request $request, $id)
    {
        $resource = Resource::where('id', $id)
            ->orWhere('slug', $id)
            ->with(['category', 'membershipPackages', 'group'])
            ->firstOrFail();

        // Increment view count or track
        // $resource->increment('view_count');

        return new ResourceResource($resource);
    }

    public function featured(Request $request)
    {
        $resources = Resource::featured()
            ->published()
            ->with(['category', 'membershipPackages'])
            ->limit($request->get('limit', 10))
            ->get();

        return ResourceResource::collection($resources);
    }

    public function download(Request $request, $id)
    {
        $user = $request->user();
        $resource = Resource::findOrFail($id);

        // Check if user can download
        if (!$this->canDownloadResource($user, $resource)) {
            return response()->json([
                'message' => 'You do not have permission to download this resource.',
            ], 403);
        }

        // Check download limits for subscription users
        if ($user && $user->activeSubscription) {
            $subscription = $user->activeSubscription()->first();
            
            if (!$subscription->canDownload()) {
                return response()->json([
                    'message' => 'You have reached your download limit for this period.',
                ], 429);
            }
        }

        // Get file path
        $filePath = $resource->file_path;
        
        if (!$filePath || !Storage::disk('public')->exists($filePath)) {
            return response()->json([
                'message' => 'File not found.',
            ], 404);
        }

        // Track download
        $this->trackDownload($user, $resource);

        // Return file download
        $fullPath = Storage::disk('public')->path($filePath);
        $fileName = $resource->file_name ?? $resource->slug . '.pdf';

        return response()->download($fullPath, $fileName, [
            'Content-Type' => 'application/octet-stream',
        ]);
    }

    private function canDownloadResource($user, Resource $resource): bool
    {
        // Free resource
        if (!$resource->requires_subscription && ($resource->price == 0 || $resource->price === null)) {
            return true;
        }

        if (!$user) {
            return false;
        }

        // Check if user purchased this resource
        $hasPurchased = $resource->orders()
            ->where('user_id', $user->id)
            ->where('payment_status', 'paid')
            ->exists();

        if ($hasPurchased) {
            return true;
        }

        // Check subscription access
        $subscription = $user->activeSubscription()->first();
        if ($subscription && $subscription->membershipPackage) {
            $allowedCategories = $subscription->membershipPackage->allowed_categories;
            if (empty($allowedCategories) || in_array($resource->category_id, $allowedCategories)) {
                return true;
            }
        }

        return false;
    }

    private function trackDownload($user, Resource $resource)
    {
        // Update resource download count
        $resource->increment('download_count');

        if (!$user) {
            return;
        }

        // Record user download
        UserDownload::create([
            'user_id' => $user->id,
            'resource_id' => $resource->id,
            'downloaded_at' => now(),
            'ip_address' => request()->ip(),
        ]);

        // Update subscription download usage
        $subscription = $user->activeSubscription()->first();
        if ($subscription) {
            $subscription->increment('downloads_used');
        }

        // Track in download tracker
        \App\Models\DownloadTracker::updateOrCreate(
            [
                'user_id' => $user->id,
                'date' => now()->toDateString(),
            ],
            [
                'downloads' => DB::raw('downloads + 1'),
            ]
        );
    }

    /**
     * Get resources by group ID
     */
    public function byGroup(Request $request, $groupId)
    {
        $group = ResourceGroup::findOrFail($groupId);
        
        // Get resources in this group
        $resources = $group->resources()
            ->published()
            ->with(['category', 'membershipPackages'])
            ->orderBy('sort_order')
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));
        
        return ResourceResource::collection($resources);
    }

    /**
     * Get resources by group slug
     */
    public function byGroupSlug(Request $request, $slug)
    {
        $group = ResourceGroup::where('slug', $slug)->firstOrFail();
        
        $resources = $group->resources()
            ->published()
            ->with(['category', 'membershipPackages'])
            ->orderBy('sort_order')
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));
        
        return ResourceResource::collection($resources);
    }

    /**
     * Get resources from a group and all its subgroups
     */
    public function byGroupWithDescendants(Request $request, $groupId)
    {
        $group = ResourceGroup::findOrFail($groupId);
        $groupIds = $group->getAllDescendantIds();
        
        $resources = Resource::whereIn('group_id', $groupIds)
            ->published()
            ->with(['category', 'membershipPackages'])
            ->orderBy('sort_order')
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));
        
        return ResourceResource::collection($resources);
    }
}