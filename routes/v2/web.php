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
Route::get('/checkout/confirm/{code}', [\App\Http\Controllers\v2\Home\CheckoutController::class, 'confirmPlan'])->name('checkout.confirm');

// listing detail (v2)
Route::get('/hotel/{slug}', [\App\Http\Controllers\v2\Home\ListingDetailController::class, 'hotel'])->name('hotel.detail');
Route::get('/space/{slug}', [\App\Http\Controllers\v2\Home\ListingDetailController::class, 'space'])->name('space.detail');
Route::get('/business/{slug}', [\App\Http\Controllers\v2\Home\ListingDetailController::class, 'business'])->name('business.detail');

// enquiry (v2)
Route::post('/enquiry/send', [\App\Http\Controllers\v2\Home\ListingDetailController::class, 'sendEnquiry'])->name('enquiry.send');

// vendor (v2)
Route::group([
    'prefix' => 'vendor',
    'as' => 'vendor2.',
    'middleware' => 'auth',
], function () {
    // dashboard analytics
    Route::get('/dashboard', [\App\Http\Controllers\v2\Vendor\VendorDashboardController::class, 'index'])->name('dashboard');

    // notifications
    Route::get('/notification', [\App\Http\Controllers\v2\Vendor\VendorNotificationController::class, 'index'])->name('notification');
    Route::post('/notification/mark-read', [\App\Http\Controllers\v2\Vendor\VendorNotificationController::class, 'markAsRead'])->name('notification.markRead');
    Route::post('/notification/mark-all-read', [\App\Http\Controllers\v2\Vendor\VendorNotificationController::class, 'markAllAsRead'])->name('notification.markAllRead');

    // profile settings & verification
    Route::get('/profile', [\App\Http\Controllers\v2\Vendor\ProfileSettingController::class, 'index'])->name('profile.index');
    Route::post('/profile', [\App\Http\Controllers\v2\Vendor\ProfileSettingController::class, 'update'])->name('profile.update');
    Route::post('/profile/password', [\App\Http\Controllers\v2\Vendor\ProfileSettingController::class, 'changePassword'])->name('profile.password');
    Route::post('/profile/verify', [\App\Http\Controllers\v2\Vendor\ProfileSettingController::class, 'verifyAccount'])->name('profile.verify');
    Route::get('/profile/preview', [\App\Http\Controllers\v2\Vendor\ProfileSettingController::class, 'previewProfile'])->name('profile.preview');

    // booking history
    Route::get('/booking-history', [\App\Http\Controllers\v2\Vendor\VendorBookingController::class, 'index'])->name('booking.history');
    Route::get('/booking-history/{id}', [\App\Http\Controllers\v2\Vendor\VendorBookingController::class, 'detail'])->name('booking.detail');
    Route::get('/booking-history/{id}/invoice', [\App\Http\Controllers\v2\Vendor\VendorBookingController::class, 'invoice'])->name('booking.invoice');

    // booking report
    Route::get('/booking-report', [\App\Http\Controllers\v2\Vendor\VendorBookingController::class, 'reportIndex'])->name('booking.report');
    Route::get('/booking-report/{id}', [\App\Http\Controllers\v2\Vendor\VendorBookingController::class, 'reportDetail'])->name('booking.report.detail');

    // enquiry report
    Route::get('/enquiry-report', [\App\Http\Controllers\v2\Vendor\VendorEnquiryController::class, 'index'])->name('enquiry.report');
    Route::get('/enquiry-report/{id}/reply', [\App\Http\Controllers\v2\Vendor\VendorEnquiryController::class, 'reply'])->name('enquiry.reply');
    Route::post('/enquiry-report/{id}/reply', [\App\Http\Controllers\v2\Vendor\VendorEnquiryController::class, 'storeReply'])->name('enquiry.reply.store');

    // messages
    Route::get('/messages', [\App\Http\Controllers\v2\Vendor\VendorMessageController::class, 'index'])->name('messages.index');

    // payouts
    Route::get('/payouts', [\App\Http\Controllers\v2\Vendor\VendorPayoutController::class, 'index'])->name('payout.index');
    Route::post('/payouts/setup', [\App\Http\Controllers\v2\Vendor\VendorPayoutController::class, 'setupAccount'])->name('payout.setup');
    Route::post('/payouts/request', [\App\Http\Controllers\v2\Vendor\VendorPayoutController::class, 'requestPayout'])->name('payout.request');

    // my plans
    Route::get('/my-plans', [\App\Http\Controllers\v2\Vendor\VendorPlanController::class, 'index'])->name('my-plans.index');

    // wishlist
    Route::get('/wishlist', [\App\Http\Controllers\v2\Vendor\VendorWishlistController::class, 'index'])->name('wishlist.index');

    // referral
    Route::get('/referral', [\App\Http\Controllers\v2\Vendor\VendorReferralController::class, 'index'])->name('referral.index');

    // business
    Route::group(['prefix' => 'business', 'as' => 'business.'], function () {
        Route::get('/', [\App\Http\Controllers\v2\Vendor\VendorBusinessController::class, 'index'])->name('index');
        Route::get('/add', [\App\Http\Controllers\v2\Vendor\VendorBusinessController::class, 'create'])->name('add');
        Route::get('/edit/{id}', [\App\Http\Controllers\v2\Vendor\VendorBusinessController::class, 'edit'])->name('edit');
        Route::get('/show/{id}', [\App\Http\Controllers\v2\Vendor\VendorBusinessController::class, 'show'])->name('show');
        Route::get('/delete/{id}', [\App\Http\Controllers\v2\Vendor\VendorBusinessController::class, 'delete'])->name('delete');
        Route::get('/update-status/{id}', [\App\Http\Controllers\v2\Vendor\VendorBusinessController::class, 'updateStatus'])->name('updateStatus');
        Route::post('/store/{id?}', [\App\Http\Controllers\v2\Vendor\VendorBusinessController::class, 'store'])->name('store');
    });

    // property
    Route::group(['prefix' => 'space', 'as' => 'property.'], function () {
        Route::get('/', [\App\Http\Controllers\v2\Vendor\VendorPropertyController::class, 'index'])->name('index');
        Route::get('/add', [\App\Http\Controllers\v2\Vendor\VendorPropertyController::class, 'create'])->name('add');
        Route::get('/edit/{id}', [\App\Http\Controllers\v2\Vendor\VendorPropertyController::class, 'edit'])->name('edit');
        Route::get('/show/{id}', [\App\Http\Controllers\v2\Vendor\VendorPropertyController::class, 'show'])->name('show');
        Route::get('/delete/{id}', [\App\Http\Controllers\v2\Vendor\VendorPropertyController::class, 'delete'])->name('delete');
        Route::get('/update-status/{id}', [\App\Http\Controllers\v2\Vendor\VendorPropertyController::class, 'updateStatus'])->name('updateStatus');
        Route::post('/store/{id?}', [\App\Http\Controllers\v2\Vendor\VendorPropertyController::class, 'store'])->name('store');
    });

    // accommodation
    Route::group(['prefix' => 'hotel', 'as' => 'accommodation.'], function () {
        Route::get('/', [\App\Http\Controllers\v2\Vendor\VendorAccommodationController::class, 'index'])->name('index');
        Route::get('/add', [\App\Http\Controllers\v2\Vendor\VendorAccommodationController::class, 'create'])->name('add');
        Route::get('/edit/{id}', [\App\Http\Controllers\v2\Vendor\VendorAccommodationController::class, 'edit'])->name('edit');
        Route::get('/show/{id}', [\App\Http\Controllers\v2\Vendor\VendorAccommodationController::class, 'show'])->name('show');
        Route::get('/delete/{id}', [\App\Http\Controllers\v2\Vendor\VendorAccommodationController::class, 'delete'])->name('delete');
        Route::get('/update-status/{id}', [\App\Http\Controllers\v2\Vendor\VendorAccommodationController::class, 'updateStatus'])->name('updateStatus');
        Route::post('/store/{id?}', [\App\Http\Controllers\v2\Vendor\VendorAccommodationController::class, 'store'])->name('store');
    });

    // virtuard 360
    Route::group(['prefix' => 'virtuard360', 'as' => 'virtuard360.'], function () {
        Route::get('/', [\App\Http\Controllers\v2\Vendor\VendorVirtuardController::class, 'index'])->name('index');
        Route::get('/add', [\App\Http\Controllers\v2\Vendor\VendorVirtuardController::class, 'add'])->name('add');
        Route::get('/edit', [\App\Http\Controllers\v2\Vendor\VendorVirtuardController::class, 'edit'])->name('edit');
        Route::get('/show/{id}', [\App\Http\Controllers\v2\Vendor\VendorVirtuardController::class, 'show'])->name('show');
        Route::get('/delete/{id}', [\App\Http\Controllers\v2\Vendor\VendorVirtuardController::class, 'delete'])->name('delete');
        Route::get('/update-status/{id}', [\App\Http\Controllers\v2\Vendor\VendorVirtuardController::class, 'updateStatus'])->name('updateStatus');
        Route::post('/store', [\App\Http\Controllers\v2\Vendor\VendorVirtuardController::class, 'store'])->name('store');
        Route::post('/store-image', [\App\Http\Controllers\v2\Vendor\VendorVirtuardController::class, 'storeImage'])->name('storeImage');
    });
});