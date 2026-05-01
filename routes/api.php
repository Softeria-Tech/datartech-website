<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\MembershipController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\ResourceController;
use App\Http\Controllers\API\ResourceGroupController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/


Route::any("/",function(){
    return "ok";
});

Route::any('/mpesa/callback', [App\Http\Controllers\MpesaCallbackController::class, 'callback'])->name('mpesa.callback');    
Route::any('/mpesa/test-callback', [App\Http\Controllers\MpesaCallbackController::class, 'testCallback'])->name('mpesa.test');


Route::middleware('optional.sanctum')->group(function () {
    // Public routes
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);

    // Public resource routes
    Route::prefix('resources')->group(function () {
        Route::get('/', [ResourceController::class, 'index']);
        Route::get('/featured', [ResourceController::class, 'featured']);
        Route::get('/random', [ResourceController::class, 'random']);
        Route::get('/{id}', [ResourceController::class, 'show'])->where('id','[0-9]+');

        // Get resources by group
        Route::get('/group/{groupId}', [ResourceController::class, 'byGroup']);
        Route::get('/group-slug/{slug}', [ResourceController::class, 'byGroupSlug']);
        Route::get('/group-with-descendants/{groupId}', [ResourceController::class, 'byGroupWithDescendants']);
    });

    //Categories
    Route::prefix('categories')->group(function () {    
        Route::get('/', [CategoryController::class, 'index']);
        Route::get('/tree', [CategoryController::class, 'tree']);
        Route::get('/{slug}', [CategoryController::class, 'show']);
        Route::get('/{slug}/subcategories', [CategoryController::class, 'subcategories']);
        Route::get('/{slug}/resources', [CategoryController::class, 'resources']);
    });

    // Public membership routes

    Route::get('/membership/packages', [MembershipController::class, 'packages']);
    Route::get('/membership/packages/{id}', [MembershipController::class, 'showPackage']);

    // Resource Group routes (public - view only)
    Route::prefix('groups')->group(function () {
        // Basic CRUD operations (read-only)
        Route::get('/', [ResourceGroupController::class, 'index']);
        Route::get('/tree', [ResourceGroupController::class, 'tree']);
        Route::get('/stats', [ResourceGroupController::class, 'statistics']);
        Route::get('/search', [ResourceGroupController::class, 'search']);
        Route::get('/popular', [ResourceGroupController::class, 'popular']);
        Route::get('/featured', [ResourceGroupController::class, 'featured']);
        Route::get('/random', [ResourceGroupController::class, 'random']);
        Route::get('/by-depth/{depth}', [ResourceGroupController::class, 'byDepth']);
        Route::get('/by-level/{level}', [ResourceGroupController::class, 'byLevel']);
        Route::get('/count-by-depth', [ResourceGroupController::class, 'countByDepth']);
        Route::get('/check-exists', [ResourceGroupController::class, 'exists']);
        Route::get('/nested-hierarchy', [ResourceGroupController::class, 'nestedHierarchy']);
        
        // Specific group actions (order matters - place specific IDs after generic routes)
        Route::get('/{id}', [ResourceGroupController::class, 'show']);
        Route::get('/{id}/subgroups', [ResourceGroupController::class, 'subgroups']);
        Route::get('/{id}/resources', [ResourceGroupController::class, 'allResources']);
        Route::get('/{id}/breadcrumb', [ResourceGroupController::class, 'breadcrumb']);
        Route::get('/{id}/path', [ResourceGroupController::class, 'path']);
        Route::get('/{id}/siblings', [ResourceGroupController::class, 'siblings']);
    });
});
// Protected routes
Route::middleware(['auth:sanctum'])->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::put('/user/profile', [AuthController::class, 'updateProfile']);
    Route::put('/user/password', [AuthController::class, 'changePassword']);
    
    // Downloads
    Route::get('/resources/{id}/download', [ResourceController::class, 'download']);
    
    // Orders
    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'create']);
    Route::get('/orders/{id}', [OrderController::class, 'show'])->where('id','[0-9]+');
    Route::post('/orders/initiate-payment', [OrderController::class, 'initiatePayment']);
    
    // Membership
    Route::get('/membership/my-subscription', [MembershipController::class, 'mySubscription']);
    Route::post('/membership/subscribe', [MembershipController::class, 'subscribe']);
    Route::post('/membership/cancel', [MembershipController::class, 'cancelSubscription']);
});