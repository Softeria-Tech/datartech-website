<?php

use Illuminate\Support\Facades\Route;

Route::any("/",function(){
    return "ok";
});

Route::any('/mpesa/callback', [App\Http\Controllers\MpesaCallbackController::class, 'callback'])->name('mpesa.callback');    
Route::any('/mpesa/test-callback', [App\Http\Controllers\MpesaCallbackController::class, 'testCallback'])->name('mpesa.test');