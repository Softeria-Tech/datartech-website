<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ResourceResource;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::with('children')
            ->root()
            ->visible()
            ->orderBy('sort_order')
            ->get();

        return CategoryResource::collection($categories);
    }

    public function show(Request $request, $slug)
    {
        $category = Category::where('slug', $slug)
            ->with(['children' => function ($query) {
                $query->visible()->orderBy('sort_order');
            }])
            ->firstOrFail();

        // Load resources with pagination
        $resources = $category->resources()
            ->published()
            ->with(['category', 'membershipPackages'])
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'category' => new CategoryResource($category),
            'resources' => ResourceResource::collection($resources),
            'meta' => [
                'current_page' => $resources->currentPage(),
                'last_page' => $resources->lastPage(),
                'per_page' => $resources->perPage(),
                'total' => $resources->total(),
            ],
        ]);
    }

    public function tree(Request $request)
    {
        $tree = Category::getTree();
        
        return CategoryResource::collection($tree);
    }

    public function subcategories(Request $request, $slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        
        $subcategories = $category->children()
            ->visible()
            ->with('children')
            ->orderBy('sort_order')
            ->get();

        return CategoryResource::collection($subcategories);
    }

    public function resources(Request $request, $slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        
        $resources = $category->allResources()
            ->published()
            ->with(['category', 'membershipPackages'])
            ->orderBy('sort_order')
            ->paginate($request->get('per_page', 15));

        return ResourceResource::collection($resources);
    }
}