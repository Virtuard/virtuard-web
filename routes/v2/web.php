<?php

// V2 Auth Routes
require 'auth.php';

// V2 Pages
Route::get('terms-and-conditions', [\App\Http\Controllers\v2\Home\PageController::class, 'termsAndConditions'])->name('page.terms');
Route::get('privacy-policy', [\App\Http\Controllers\v2\Home\PageController::class, 'privacyPolicy'])->name('page.privacy');

Route::get('/', [\App\Http\Controllers\v2\Home\HomeController::class, 'index']);

// explore (v2)
Route::group([
    'prefix' => 'explore',
    'as' => 'explore.',
], function () {
    Route::get('/', [\App\Http\Controllers\v2\Home\ExploreController::class, 'index'])->name('index');
    Route::post('search', [\App\Http\Controllers\v2\Home\ExploreController::class, 'search'])->name('search');
    Route::post('map', [\App\Http\Controllers\v2\Home\ExploreController::class, 'mapData'])->name('map');
});

// post (v2)
Route::get('/post', [\App\Http\Controllers\v2\Home\PostController::class, 'index'])->name('post.index');
Route::post('/post', [\App\Http\Controllers\v2\Home\PostController::class, 'store'])->name('post.store')->middleware('auth');

// member (v2)
Route::get('/member', [\App\Http\Controllers\v2\Home\MemberController::class, 'index'])->name('member.index');
Route::get('/member/{userName}', [\App\Http\Controllers\v2\Home\MemberController::class, 'show'])->name('member.show');

// checkout (v2)
Route::get('/checkout/{planId}', [\App\Http\Controllers\v2\Home\CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout/{planId}', [\App\Http\Controllers\v2\Home\CheckoutController::class, 'process'])->name('checkout.process');