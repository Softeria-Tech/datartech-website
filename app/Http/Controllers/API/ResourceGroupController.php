<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\ResourceGroupResource;
use App\Http\Resources\ResourceResource;
use App\Models\ResourceGroup;
use App\Models\Resource;
use Illuminate\Http\Request;

class ResourceGroupController extends Controller
{
    /**
     * Get all resource groups (with optional filtering by depth)
     */
    public function index(Request $request)
    {
        $query = ResourceGroup::with(['children.children.children.children']);

        // Filter by depth level
        if ($request->has('depth')) {
            $depth = (int) $request->depth;
            $query->atDepth($depth);
        }
        
        // Filter by parent group
        if ($request->has('parent_id')) {
            if ($request->parent_id === 'null' || $request->parent_id === null) {
                $query->root();
            } else {
                $query->where('parent_id', $request->parent_id);
            }
        } else {
            // Default to root groups
            $query->root();
        }

        // Only show active groups
        if ($request->has('active_only') && $request->active_only) {
            $query->where('is_active', true);
        }

        $groups = $query->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return ResourceGroupResource::collection($groups);
    }

    /**
     * Get complete hierarchical tree of all groups
     */
    public function tree(Request $request)
    {
        $groups = ResourceGroup::with(['children' => function ($query) {
            $query->orderBy('sort_order');
        }])
        ->root()
        ->orderBy('sort_order')
        ->get();

        return ResourceGroupResource::collection($groups);
    }

    /**
     * Get specific group by ID or slug
     */
    public function show(Request $request, $id)
    {
        $group = ResourceGroup::where('id', $id)
            ->orWhere('slug', $id)
            ->with(['parent', 'children' => function ($query) {
                $query->orderBy('sort_order');
            }])
            ->firstOrFail();

        // Get resources in this group with pagination
        $resources = $group->resources()
            ->published()
            ->with(['category', 'membershipPackages'])
            ->orderBy('sort_order')
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'group' => new ResourceGroupResource($group),
            'resources' => ResourceResource::collection($resources),
            'meta' => [
                'current_page' => $resources->currentPage(),
                'last_page' => $resources->lastPage(),
                'per_page' => $resources->perPage(),
                'total' => $resources->total(),
                'total_resources' => $group->resources()->count(),
            ],
        ]);
    }

    /**
     * Get all resources in a group and its subgroups
     */
    public function allResources(Request $request, $id)
    {
        $group = ResourceGroup::where('id', $id)
            ->orWhere('slug', $id)
            ->firstOrFail();

        // Get all descendant group IDs
        $groupIds = $group->getAllDescendantIds();
        
        // Get resources from all these groups
        $resources = Resource::whereIn('group_id', $groupIds)
            ->published()
            ->with(['category', 'membershipPackages'])
            ->orderBy('sort_order')
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        return ResourceResource::collection($resources);
    }

    /**
     * Get subgroups of a specific group
     */
    public function subgroups(Request $request, $id)
    {
        $group = ResourceGroup::where('id', $id)
            ->orWhere('slug', $id)
            ->firstOrFail();

        $subgroups = $group->children()
            ->with(['children' => function ($query) {
                $query->orderBy('sort_order');
            }])
            ->orderBy('sort_order')
            ->get();

        return ResourceGroupResource::collection($subgroups);
    }

    /**
     * Get groups by depth level
     */
    public function byDepth(Request $request, $depth)
    {
        $depth = (int) $depth;
        
        $groups = ResourceGroup::atDepth($depth)
            ->with(['parent', 'children' => function ($query) {
                $query->orderBy('sort_order');
            }])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return ResourceGroupResource::collection($groups);
    }

    /**
     * Get groups hierarchy as nested array
     */
    public function nestedHierarchy(Request $request)
    {
        $this->buildNestedHierarchy();
        
        $hierarchy = ResourceGroup::root()
            ->orderBy('sort_order')
            ->get()
            ->map(function ($group) {
                return $this->formatNestedGroup($group);
            });

        return response()->json([
            'hierarchy' => $hierarchy,
        ]);
    }

    /**
     * Get breadcrumb path for a group
     */
    public function breadcrumb(Request $request, $id)
    {
        $group = ResourceGroup::where('id', $id)
            ->orWhere('slug', $id)
            ->firstOrFail();

        $ancestors = $group->getAncestors();
        
        $breadcrumb = collect($ancestors)->map(function ($ancestor) {
            return [
                'id' => $ancestor->id,
                'name' => $ancestor->name,
                'slug' => $ancestor->slug,
                'depth' => $ancestor->depth,
            ];
        })->push([
            'id' => $group->id,
            'name' => $group->name,
            'slug' => $group->slug,
            'depth' => $group->depth,
        ]);

        return response()->json([
            'breadcrumb' => $breadcrumb,
            'full_path' => $group->full_path,
        ]);
    }

    /**
     * Get statistics for all groups
     */
    public function statistics(Request $request)
    {
        $stats = [
            'total_groups' => ResourceGroup::count(),
            'parent_groups' => ResourceGroup::root()->count(),
            'sub_groups' => ResourceGroup::subGroups()->count(),
            'grand_groups' => ResourceGroup::grandGroups()->count(),
            'fourth_degree_groups' => ResourceGroup::fourthDegree()->count(),
            'active_groups' => ResourceGroup::where('is_active', true)->count(),
            'inactive_groups' => ResourceGroup::where('is_active', false)->count(),
            'groups_with_resources' => ResourceGroup::has('resources')->count(),
            'groups_without_resources' => ResourceGroup::doesntHave('resources')->count(),
            'total_resources_in_groups' => Resource::whereNotNull('group_id')->count(),
        ];

        // Get depth distribution
        $groups = ResourceGroup::all();
        $depthDistribution = [];
        foreach ($groups as $group) {
            $depth = $group->depth;
            $depthDistribution[$depth] = ($depthDistribution[$depth] ?? 0) + 1;
        }

        $stats['depth_distribution'] = $depthDistribution;

        // Get groups with most resources
        $stats['top_groups_by_resources'] = ResourceGroup::withCount('resources')
            ->orderBy('resources_count', 'desc')
            ->limit(10)
            ->get()
            ->map(fn($group) => [
                'id' => $group->id,
                'name' => $group->name,
                'resource_count' => $group->resources_count,
                'depth' => $group->depth,
            ]);

        return response()->json($stats);
    }

    /**
     * Search groups by name or description
     */
    public function search(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:2',
        ]);

        $query = $request->get('q');
        
        $groups = ResourceGroup::where('name', 'LIKE', "%{$query}%")
            ->orWhere('description', 'LIKE', "%{$query}%")
            ->orWhere('slug', 'LIKE', "%{$query}%")
            ->with(['parent', 'children'])
            ->orderByRaw("CASE WHEN name LIKE ? THEN 0 ELSE 1 END", ["{$query}%"])
            ->orderBy('name')
            ->limit($request->get('limit', 20))
            ->get();

        return ResourceGroupResource::collection($groups);
    }

    /**
     * Get sibling groups (same parent)
     */
    public function siblings(Request $request, $id)
    {
        $group = ResourceGroup::where('id', $id)
            ->orWhere('slug', $id)
            ->firstOrFail();

        $siblings = ResourceGroup::where('parent_id', $group->parent_id)
            ->where('id', '!=', $group->id)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return response()->json([
            'parent_id' => $group->parent_id,
            'current_group' => new ResourceGroupResource($group),
            'siblings' => ResourceGroupResource::collection($siblings),
        ]);
    }

    /**
     * Get group path from root to specified group
     */
    public function path(Request $request, $id)
    {
        $group = ResourceGroup::where('id', $id)
            ->orWhere('slug', $id)
            ->firstOrFail();

        $ancestors = $group->getAncestors();
        
        $path = collect($ancestors)->map(function ($ancestor) {
            return [
                'id' => $ancestor->id,
                'name' => $ancestor->name,
                'slug' => $ancestor->slug,
                'depth' => $ancestor->depth,
            ];
        })->push([
            'id' => $group->id,
            'name' => $group->name,
            'slug' => $group->slug,
            'depth' => $group->depth,
        ]);

        return response()->json([
            'path' => $path,
            'path_string' => $group->full_path,
        ]);
    }

    /**
     * Get popular groups (groups with most resources or most views)
     */
    public function popular(Request $request)
    {
        $groups = ResourceGroup::withCount('resources')
            ->where('is_active', true)
            ->orderBy('resources_count', 'desc')
            ->limit($request->get('limit', 10))
            ->get();

        return ResourceGroupResource::collection($groups);
    }

    /**
     * Get groups by level name (Parent, Sub, Grand, etc.)
     */
    public function byLevel(Request $request, $level)
    {
        $levelMap = [
            'parent' => 0,
            'sub' => 1,
            'grand' => 2,
            'fourth' => 3,
        ];

        $depth = $levelMap[strtolower($level)] ?? null;
        
        if ($depth === null) {
            return response()->json([
                'message' => 'Invalid level. Use: parent, sub, grand, fourth',
            ], 422);
        }

        $groups = ResourceGroup::atDepth($depth)
            ->with(['parent', 'children'])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return ResourceGroupResource::collection($groups);
    }

    /**
     * Get random groups (for discovery/explore sections)
     */
    public function random(Request $request)
    {
        $limit = $request->get('limit', 6);
        $depth = $request->get('depth', null);
        
        $query = ResourceGroup::where('is_active', true);
        
        if ($depth !== null) {
            $query->atDepth((int) $depth);
        }
        
        $groups = $query->inRandomOrder()
            ->limit($limit)
            ->get();

        return ResourceGroupResource::collection($groups);
    }

    /**
     * Get group count by depth
     */
    public function countByDepth(Request $request)
    {
        $groups = ResourceGroup::all();
        $countByDepth = [];
        
        foreach ($groups as $group) {
            $depth = $group->depth;
            $depthName = $this->getDepthName($depth);
            $countByDepth[$depthName] = [
                'depth' => $depth,
                'count' => ($countByDepth[$depthName]['count'] ?? 0) + 1,
                'name' => $depthName,
            ];
        }
        
        return response()->json(array_values($countByDepth));
    }

    /**
     * Validate if a group exists (useful for navigation checks)
     */
    public function exists(Request $request)
    {
        $request->validate([
            'slug' => 'required_without:id|string',
            'id' => 'required_without:slug|integer',
        ]);

        $exists = false;
        $group = null;
        
        if ($request->has('slug')) {
            $group = ResourceGroup::where('slug', $request->slug)->first();
        } elseif ($request->has('id')) {
            $group = ResourceGroup::find($request->id);
        }
        
        if ($group) {
            $exists = true;
        }
        
        return response()->json([
            'exists' => $exists,
            'group' => $group ? new ResourceGroupResource($group) : null,
        ]);
    }

    /**
     * Get featured groups (groups that have featured resources)
     */
    public function featured(Request $request)
    {
        $groups = ResourceGroup::whereHas('resources', function ($query) {
                $query->where('featured', true)
                    ->where('is_published', true);
            })
            ->withCount(['resources' => function ($query) {
                $query->where('featured', true);
            }])
            ->where('is_active', true)
            ->orderBy('resources_count', 'desc')
            ->limit($request->get('limit', 8))
            ->get();

        return ResourceGroupResource::collection($groups);
    }

    // ============ Helper Methods ============

    /**
     * Recursively format nested group hierarchy
     */
    private function formatNestedGroup($group)
    {
        $data = [
            'id' => $group->id,
            'name' => $group->name,
            'slug' => $group->slug,
            'description' => $group->description,
            'depth' => $group->depth,
            'level_name' => $group->level_name,
            'resource_count' => $group->resources()->count(),
            'is_active' => $group->is_active,
            'sort_order' => $group->sort_order,
            'cover_image' => $group->cover_image ? url('/storage/' . $group->cover_image) : null,
        ];
        
        if ($group->children && $group->children->count() > 0) {
            $data['children'] = $group->children->map(function ($child) {
                return $this->formatNestedGroup($child);
            });
        }
        
        return $data;
    }

    /**
     * Build nested hierarchy (could be cached)
     */
    private function buildNestedHierarchy()
    {
        // This can be cached for better performance
        // Cache::remember('resource_groups_hierarchy', 3600, function () {
        //     return ResourceGroup::with('children')->root()->get();
        // });
    }

    /**
     * Get readable name for depth level
     */
    private function getDepthName($depth)
    {
        return match($depth) {
            0 => 'Parent Groups',
            1 => 'Sub Groups',
            2 => 'Grand Groups',
            3 => '4th Degree Groups',
            default => $depth . 'th Level Groups'
        };
    }
}