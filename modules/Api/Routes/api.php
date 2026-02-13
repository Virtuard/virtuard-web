<?php

use Illuminate\Http\Request;
use \Illuminate\Support\Facades\Route;
use Modules\Api\Controllers\ChatController;
use Modules\Api\Controllers\FollowController;
use Modules\Api\Controllers\Hotel\ManageHotelController as ApiManageHotelController;
use Modules\Api\Controllers\Space\ManageSpaceController as ApiManageSpaceController;
use Modules\Api\Controllers\Business\ManageBusinessController as ApiManageBusinessController;   
use Modules\Api\Controllers\ListingController;
use Modules\Api\Controllers\MessagesController;
use Modules\Api\Controllers\PostController as ControllersPostController;
use Modules\Api\Controllers\ProfileController;
use Modules\Api\Controllers\RecentlyController;
use Modules\Api\Controllers\ReferralController;
use Modules\Api\Controllers\SearchController;
use Modules\Api\Controllers\StoryController;
use Modules\Business\Controllers\ManageBusinessController;
use Modules\Hotel\Controllers\VendorController;
use Modules\Space\Controllers\ManageSpaceController;
use Modules\User\Controllers\MessagesController as ControllersMessagesController;
use Modules\Api\Controllers\MemberController;
use Modules\Api\Controllers\MapController;
use Modules\Api\Controllers\AttributeController;
use Modules\Api\Controllers\Panorama\ManagePanoramaController;
use Modules\Api\Controllers\Auth\ResetPasswordController;
use Modules\Api\Controllers\Auth\GoogleLoginController;
use Modules\Api\Controllers\HomePageController;
use Modules\Api\Controllers\UserGameProgressController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
/* Config */
Route::get('configs','BookingController@getConfigs')->name('api.get_configs');

/* Home Page */
Route::get('home-page/service-counts', [HomePageController::class, 'getServiceCounts']);

/* Service */
Route::get('services','SearchController@searchServices')->name('api.service-search');
Route::get('{type}/search','SearchController@search')->name('api.search2');
Route::get('/search-by-author/{type?}', [SearchController::class, 'searchByAuthor']);
Route::get('{type}/detail/{id}','SearchController@detail')->name('api.detail');
Route::get('{type}/availability/{id}','SearchController@checkAvailability')->name('api.service.check_availability');
Route::get('boat/availability-booking/{id}','SearchController@checkBoatAvailability')->name('api.service.checkBoatAvailability');
Route::get('{type}/ipanorama/{id}','SearchController@getIpanorama')->name('api.getPanorama');


Route::get('/panoramaView', [SearchController::class, 'panoramaView']);


Route::get('{type}/filters','SearchController@getFilters')->name('api.service.filter');
Route::get('{type}/form-search','SearchController@getFormSearch')->name('api.service.form');

Route::group(['middleware' => 'api'],function(){
    Route::post('{type}/write-review/{id}','ReviewController@writeReview')->name('api.service.write_review');
    Route::get('messages/iframe/{id?}', [ChatController::class, 'iframe']);
    Route::get('messages/search', [ChatController::class, 'search']);
    Route::get('messages/contacts', [ChatController::class, 'getContacts']);
    Route::get('messages/fetch/{id}', [ChatController::class, 'idFetchData']);
    Route::get('messages/detail', [ChatController::class, 'fetch']);
    Route::post('messages/send', [ChatController::class, 'send']);
    // Route::post('booking/form','BookingController@bookingMidtrans')->name('api.booking.form');

});

Route::middleware(['auth:sanctum'])->post('booking/form', 'BookingController@bookingMidtrans')->name('api.booking.form');



/* Layout HomePage */
Route::get('home-page','BookingController@getHomeLayout')->name('api.get_home_layout');
Route::get('recently-services', [RecentlyController::class, 'getRecentlyServices']);

/* Register - Login */
Route::group(['middleware' => 'api', 'prefix' => 'auth'], function ($router) {
    Route::post('login', 'AuthController@login')->middleware(['throttle:login']);
    Route::post('register', 'AuthController@register');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::get('me', 'AuthController@me');
    Route::post('me', 'AuthController@updateUser');
    Route::post('change-password', 'AuthController@changePassword');
    Route::get('check-email-availability', 'AuthController@checkEmailAvailability');
    // Google Login
    Route::post('google/login', [GoogleLoginController::class, 'googleLogin']);
});

// OTP Reset Password
Route::group(['middleware' => 'api', 'prefix' => 'auth/otp'], function ($router) {
    Route::post('forgot-password', [ResetPasswordController::class, 'forgotPassword']);
    Route::post('forgot-password/verify', [ResetPasswordController::class, 'verifyOtp']);
    Route::post('reset-password', [ResetPasswordController::class, 'resetPassword']);
});


/* User */
Route::group(['middleware' => 'api','prefix' => 'user' ], function ($router) {
    Route::get('booking-history', 'UserController@getBookingHistory')->name("api.user.booking_history");
    Route::post('/wishlist','UserController@handleWishList')->name("api.user.wishList.handle");
    Route::get('/wishlist','UserController@indexWishlist')->name("api.user.wishList.index");
    Route::post('/permanently_delete','UserController@permanentlyDelete')->name("api.user.permanently.delete");

});

/* Location */
Route::get('locations','LocationController@search')->name('api.location.search');
Route::get('location/{id}','LocationController@detail')->name('api.location.detail');

// Route::post('send-message', [MessagesController::class, 'sendMessage']);
// Route::post('read-messages', [MessagesController::class, 'readMessages']);

// Route::middleware('auth:sanctum')->group(function () {
//     Route::get('/chats', [ChatController::class, 'index']); // Daftar chat
//     Route::get('/chats/{id}', [ChatController::class, 'show']); // Detail chat
//     Route::post('/chats', [ChatController::class, 'store']); // Kirim chat
// });

// Booking
Route::group(['prefix'=>config('booking.booking_route_prefix')],function(){
    Route::post('/addToCart','BookingController@addToCart')->name("api.booking.add_to_cart");
    Route::post('/addEnquiry','BookingController@addEnquiry')->name("api.booking.add_enquiry");
    Route::post('/doCheckout','BookingController@doCheckout')->name('api.booking.doCheckout');
    Route::get('/confirm/{gateway}','BookingController@confirmPayment');
    Route::get('/cancel/{gateway}','BookingController@cancelPayment');
    Route::get('/{code}','BookingController@detail');
    Route::get('/{code}/thankyou','BookingController@thankyou')->name('booking.thankyou');
    Route::get('/{code}/checkout','BookingController@checkout');
    Route::get('/{code}/check-status','BookingController@checkStatusCheckout');
});

// Gateways
Route::get('/gateways','BookingController@getGatewaysForApi');

// News
Route::get('news','NewsController@search')->name('api.news.search');
Route::get('news/category','NewsController@category')->name('api.news.category');
Route::get('news/{id}','NewsController@detail')->name('api.news.detail');


//post
Route::get('/profile-user/profile/{id_or_slug}', [ProfileController::class, 'profile']);
Route::get('/profile-user/{id_or_slug}/reviews', [ProfileController::class, 'allReviews']);
Route::get('/profile-user/{id_or_slug}/services', [ProfileController::class, 'allServices']);

/* Media */
Route::group(['prefix'=>'media','middleware' => 'auth:sanctum'],function(){
    Route::post('/upload','MediaController@store')->name("api.media.store");
    Route::get('/lists','MediaController@getLists')->name("api.media.lists");
});


// Route::group(['prefix'=>'listing','middleware' => ['auth:sanctum'],],function(){
//     Route::post('/hotels/{id?}', [ListingController::class, 'store']);
// });

Route::middleware('auth:sanctum')->post('/listing-hotels', [ListingController::class, 'store'])->name('api.listing.store');
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/hotel/store/{id?}', [VendorController::class, 'storeApi']);
    Route::post('/space/store/{id?}', [ManageSpaceController::class, 'storeApi']);
    Route::post('/business/store/{id?}', [ManageBusinessController::class, 'storeApi']);
    Route::get('/search-author/{type?}', [SearchController::class, 'searchByIdToken']);
    Route::delete('/del-hotel/{id}', [ListingController::class, 'destroyHotels']);
    Route::delete('/del-space/{id}', [ListingController::class, 'destroySpaces']);
    Route::delete('/del-business/{id}', [ListingController::class, 'destroyBussiness']);
});

/* Post */
Route::group(['prefix' => 'post', 'middleware' => ['auth:sanctum'],], function () {
    Route::get('/', 'PostController@index');
    Route::post('/','PostController@store');
    Route::post('/{id}/comment','PostController@storeComment');
    Route::get('/{id}/comments','PostController@getComments');
    Route::put('/{id}/like','PostController@likeOrUnlikePost');
    Route::delete('/{id}','PostController@deletePost');
    Route::delete('/comment/{id}', 'PostController@deleteComment');
    
});

Route::post('map/mobile-search', [MapController::class, 'searchMapExplorerMobile']);
Route::get('map/explore', [MapController::class, 'explore']);

/* Member */
Route::group(['middleware' => 'api', 'prefix' => 'members'], function ($router) {
    Route::get('/', [MemberController::class, 'allMembers']);
    Route::get('/{id_or_slug}', [MemberController::class, 'detailMember']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/{id}/follow', [MemberController::class, 'follow']);
        Route::post('/{id}/unfollow', [MemberController::class, 'unfollow']);
    });
});

// Legacy route (keep for backward compatibility)
Route::get('/all-members', [MemberController::class, 'allMembers']);

/* Game Progress */
Route::group(['middleware' => ['api', 'auth:sanctum'], 'prefix' => 'game-progress'], function ($router) {
    Route::get('/', [UserGameProgressController::class, 'index']);
    Route::post('/', [UserGameProgressController::class, 'store']);
    Route::post('/add-score', [UserGameProgressController::class, 'addScore']);
    Route::post('/use-life', [UserGameProgressController::class, 'useLife']);
    Route::post('/add-play-time', [UserGameProgressController::class, 'addPlayTime']);
    Route::get('/players', [UserGameProgressController::class, 'players']);
    
    Route::post('/images/upload', [UserGameProgressController::class, 'uploadImage']);
    Route::get('/images', [UserGameProgressController::class, 'getImages']);
    Route::get('/images/{id}', [UserGameProgressController::class, 'getImage']);
    Route::delete('/images/{id}', [UserGameProgressController::class, 'deleteImage']);
});

Route::group(['prefix' => 'profile', 'middleware' => ['auth:sanctum'],], function () {
    Route::get('/', [ProfileController::class, 'getProfile']);
    Route::post('/', [ProfileController::class, 'updateProfile']);
    Route::post('/picture', [ProfileController::class, 'updatePicture']);
});

Route::group(['prefix' => 'referral', 'middleware' => ['auth:sanctum'],], function () {
    Route::get('/report', [ReferralController::class, 'getReports']);
});

/* User story */
Route::group(['prefix' => 'story', 'middleware' => ['auth:sanctum'],], function () {
    Route::post('/', [StoryController::class, 'createStory']);
    Route::get('/', [StoryController::class, 'getStories']);
    Route::delete('/{id}', [StoryController::class, 'deleteStory']);
    
});

// List of followers and followings
Route::group(['prefix' => 'user', 'middleware' => ['auth:sanctum'],], function () {
    Route::get('/{id}/followers', [FollowController::class, 'getFollowers']);
    Route::get('{id}/followings', [FollowController::class, 'getFollowings']);
});

Route::get('attributes', [AttributeController::class, 'index'])->middleware(['auth:sanctum']);

Route::get('user/vtour', [ManagePanoramaController::class, 'index']);
Route::get('user/vtour/{id}', [ManagePanoramaController::class, 'show']);

// User Group Access
Route::group([
    'prefix' => 'user',
    'middleware' => ['auth:sanctum'],
], function () {
    // Hotel
    Route::group([
        'prefix' => config('hotel.hotel_route_prefix'),
    ], function () {
        Route::get('/', [ApiManageHotelController::class, 'index']);
        Route::get('/{id}', [ApiManageHotelController::class, 'show']);
        Route::post('/', [ApiManageHotelController::class, 'store']);
        Route::put('/{id}', [ApiManageHotelController::class, 'update']);
        Route::delete('/{id}', [ApiManageHotelController::class, 'delete']);
    });

    // Space
    Route::group([
        'prefix' => config('space.space_route_prefix'),
    ], function () {
        Route::get('/', [ApiManageSpaceController::class, 'index']);
        Route::get('/{id}', [ApiManageSpaceController::class, 'show']);
        Route::post('/', [ApiManageSpaceController::class, 'store']);
        Route::put('/{id}', [ApiManageSpaceController::class, 'update']);
        Route::delete('/{id}', [ApiManageSpaceController::class, 'delete']);
    });

    // Business
    Route::group([
        'prefix' => config('business.business_route_prefix'),
    ], function () {
        Route::get('/', [ApiManageBusinessController::class, 'index']);
        Route::get('/{id}', [ApiManageBusinessController::class, 'show']);
        Route::post('/', [ApiManageBusinessController::class, 'store']);
        Route::put('/{id}', [ApiManageBusinessController::class, 'update']);
        Route::delete('/{id}', [ApiManageBusinessController::class, 'delete']);
    });

    // Vtour
    Route::group([
        'prefix' => 'vtour',
    ], function () {
        Route::post('/', [ManagePanoramaController::class, 'store']);
        Route::put('/{id}', [ManagePanoramaController::class, 'update']);
        Route::delete('/{id}', [ManagePanoramaController::class, 'delete']);
        Route::post('/{id}/add-image', [ManagePanoramaController::class, 'addImage']);
        Route::get('/get-files/{user_id}', [ManagePanoramaController::class, 'getFiles']);
    });
});
