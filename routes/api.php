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


// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);

// Public resource routes
Route::get('/resources', [ResourceController::class, 'index']);
Route::get('/resources/featured', [ResourceController::class, 'featured']);
Route::get('/resources/{id}', [ResourceController::class, 'show']);

Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/tree', [CategoryController::class, 'tree']);
Route::get('/categories/{slug}', [CategoryController::class, 'show']);
Route::get('/categories/{slug}/subcategories', [CategoryController::class, 'subcategories']);
Route::get('/categories/{slug}/resources', [CategoryController::class, 'resources']);

Route::get('/membership/packages', [MembershipController::class, 'packages']);
Route::get('/membership/packages/{id}', [MembershipController::class, 'showPackage']);

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
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::post('/orders/purchase', [OrderController::class, 'purchase']);
    Route::post('/orders/payment-callback', [OrderController::class, 'paymentCallback']);
    
    // Membership
    Route::get('/membership/my-subscription', [MembershipController::class, 'mySubscription']);
    Route::post('/membership/subscribe', [MembershipController::class, 'subscribe']);
    Route::post('/membership/cancel', [MembershipController::class, 'cancelSubscription']);
});