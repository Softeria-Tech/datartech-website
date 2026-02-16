<?php

use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OrderController;
use App\Livewire\Checkout\CheckoutPage;
use App\Livewire\Checkout\MembershipCheckout;
use App\Livewire\Downloads\DownloadsIndex;
use Illuminate\Support\Facades\Artisan;
use App\Livewire\Library\ResourcesPage;
use App\Livewire\Library\ResourceDetailPage;
use App\Livewire\Membership\MembershipPlans;

Route::middleware('web')->group(function(){
    Route::get('/', [FrontendController::class, 'index'])->name('index');

    Route::get('service-details/{service_id}', [FrontendController::class, 'serviceDetails'])->name('service-details');
    Route::get('about-us', [FrontendController::class, 'aboutUs'])->name('about-us');
    Route::get('contact-us', [FrontendController::class, 'contactUs'])->name('contact-us');
    Route::get('shop', [FrontendController::class, 'shop'])->name('shop');
    Route::get('shop/product-details/{product_id}', [FrontendController::class, 'productDetails'])->name('product-details');
    Route::get('product-categories/{category_id}', [FrontendController::class, 'productCategories'])->name('product-categories');

    Route::get('resource/preview', [FrontendController::class, 'previewResource'])->name('resources.show');

    Route::get('/library', ResourcesPage::class)->name('library.resources');
    Route::get('/library/resource/{slug}', ResourceDetailPage::class)->name('library.resource.detail');
    Route::get('/membership/plans', MembershipPlans::class)->name('membership.plans');

    Route::middleware(['auth', 'verified'])->group(function(){

        Route::get('logout', [DashboardController::class, 'logout'])->name('logout');    
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');

        Route::get('/downloads', DownloadsIndex::class)->name('downloads.history');

        Route::get('/checkout/resume/{order}', [CheckoutController::class, 'resumeResourceCheckout'])->name('checkout.resume');    
        Route::get('/checkout/membership/resume/{order}', [CheckoutController::class, 'resumeMembershipCheckout'])->name('checkout.membership.resume');

        Route::get('/checkout/membership/{packageSlug}/{billingCycle}/{order?}', MembershipCheckout::class)->name('checkout.membership');   

        Route::get('/checkout/membership/success/{order}', [CheckoutController::class, 'membershipSuccess'])->name('checkout.membership.success');
        Route::get('/checkout/{order}', CheckoutPage::class) ->name('checkout');

        Route::get('/checkout/success/{order}', function ($order) {
            return view('livewire.checkout.success', compact('order'));
        })->name('checkout.success');

        Route::view('profile', 'profile')->name('profile');

        Route::middleware('admin')->group(function(){
            Route::view('notifications', 'notifications')->name('notifications');
            Route::view('sms', 'sms')->name('sms');
            Route::post('notify', [NotificationController::class, 'sendToAllUsers'])->name('notify');    
            Route::post('notifications', [NotificationController::class, 'sendToAllUsers'])->name('notifications.send');
            Route::get('/notifications/images', [NotificationController::class, 'listUploadedImages'])->name('notifications.images'); 
            Route::delete('/notifications/images/{filename}', [NotificationController::class, 'deleteImage'])->name('notifications.delete-image');
        });
       
    });

    require __DIR__ . '/auth.php';

    Route::get('artisan',function(){
        Artisan::call('config:clear');
        Artisan::call('view:clear');
        Artisan::call('cache:clear');
        Artisan::call('migrate');
        exec('composer dump-autoload -d ' . base_path(), $output, $returnValue);
        //exec('ln -s ../public_html public',$output, $returnValue);
        echo "Artisan commands executed successfully.\n<br>";
        echo json_encode($output);
    });
});