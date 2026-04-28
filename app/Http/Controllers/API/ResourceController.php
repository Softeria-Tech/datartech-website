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
        
        $allowedSortFields = ['title', 'price', 'created_at', 'updated_at', 'download_count', 'published_date'];
        if (in_array($sortBy, $allowedSortFields)) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $resources = $query->paginate($request->get('per_page', 20));

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
        $resource = Resource::findOrFail($id);

        return app('Http\Controllers\ResourceDownloadController')->download($request, $resource->slug);
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

    public function random(Request $request)
    {
        $limit = $request->get('limit', 10);
        
        $resources = Resource::where('is_published', true)
            ->inRandomOrder()
            ->limit($limit)
            ->get();
        
        return ResourceResource::collection($resources);
    }
}