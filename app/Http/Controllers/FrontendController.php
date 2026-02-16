<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Service;
use Illuminate\Http\Request;
use App\Models\ServiceCategory;

class FrontendController extends Controller
{
    public function index()
    {

        $random_categories = ServiceCategory::with('services')->inRandomOrder()->take(4)->get();

        $service_categories = ServiceCategory::with('services')->get();

        $all_services = $service_categories->flatMap->services;

        $chunks = $all_services->chunk(ceil($all_services->count() / 3));


        return view('frontend.index', [
            'random_categories' => $random_categories,
            'service_categories' => $service_categories,
            'chunk1' => $chunks->get(0) ?? collect(),
            'chunk2' => $chunks->get(1) ?? collect(),
            'chunk3' => $chunks->get(2) ?? collect(),
        ]);
    }

    public function serviceDetails($service_id)
    {
        $services = Service::with('serviceCategory')->where('service_category_id', $service_id)->get();

        $service_categories = ServiceCategory::where('id', '!=', $service_id)->get();

        $service_category = ServiceCategory::where('id', $service_id)->first();

        return view('frontend.services.service-details', compact('services', 'service_category', 'service_categories'));
    }
    public function aboutUs()
    {

        return view('frontend.about-us');
    }
    public function contactUs()
    {

        return view('frontend.contact-us');
    }

    public function shop()
    {

        $product_categories = ProductCategory::with('products')->get();

        $products = Product::with('productCategory')->paginate(6);


        return view('frontend.shop', compact('product_categories', 'products'));
    }

    public function productDetails($product_id)
    {
        $product_details = Product::with('productCategory')->findOrFail($product_id);

        return view('frontend.product-details', compact('product_details'));
    }
}
