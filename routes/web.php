<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cookie;
use Modules\Booking\Controllers\BookingController;
use Modules\User\Controllers\PlanController;

/*
    |--------------------------------------------------------------------------
    | Web Routes
    |--------------------------------------------------------------------------
    |
    | Here is where you can register web routes for your application. These
    | routes are loaded by the RouteServiceProvider within a group which
    | contains the "web" middleware group. Now create something great!
    |
    */

// Route::get('/intro', 'LandingpageController@index');
Route::get('/', 'HomeController@index');
Route::get('/home', 'HomeController@index')->name('home');
Route::post('/install/check-db', 'HomeController@checkConnectDatabase');
Route::get('/need-reset-password', 'HomeController@needResetPassword');
Route::get('/need-confirm-email', 'HomeController@needConfirmEmail');

// Virtuard 360
Route::group([
    'prefix' => '/user/virtuard-360',
    'as' => 'user.virtuard-360.',
    'middleware' => ['auth'],
], function (){
Route::get('/', 'VirtuardController@vendorVirtuardIndex')->name('index');
Route::get('show/{id}', 'VirtuardController@show')->name('show');
Route::post('/add/newImage', 'VirtuardController@addNewImageVirtuard360')->name('add-new-image-service');
Route::get('/add/api/edit', 'VirtuardController@vendorVirtuardAjaxGetApi')->name('add-virtuard-api-edit');
Route::post('/add/api', 'VirtuardController@vendorVirtuardAddApi')->name('add-virtuard-api');
Route::post('/add/apiSecond', 'VirtuardController@vendorVirtuardAddApiSecond')->name('add-virtuard-api-second');
Route::get('bulkEdit/{id}', 'VirtuardController@bulkEdit')->name('bulk_edit');

Route::get('/edit', 'VirtuardController@vendorVirtuardEdit')->name('edit');
Route::post('/edit/updateTour', 'VirtuardController@updateIsTourField')->name('update-tour');
Route::get('/{id}/delete', 'VirtuardController@vendorVirtuardDelete')->name('destroy');   
Route::post('/add/new', 'VirtuardController@addNewVirtuard360')->name('add-new-service');
Route::group([
    'middleware' => ['user_ipanorama_plan']
], function (){
Route::get('/add', 'VirtuardController@vendorVirtuardAdd')->name('add');
});
});
Route::get('/panorama/preview', 'VirtuardController@previewIpanorama')->name('panorama.preview');
Route::get('/panorama/compress/{id}', 'VirtuardController@compressPanorama')->name('panorama.compress');

// Story
Route::get('/story', 'StoryController@list')->name('story.list');
Route::post('/story', 'StoryController@addStory')->name('story.store');
Route::delete('/story/{id}', 'StoryController@destroy')->name('story.destroy');

// Category
Route::get('/admin/add/category/product/{type}', 'CategoryController@index');
Route::post('/admin/add/category/product', 'CategoryController@store')->name('add.category');
Route::put('/admin/edit/category/product', 'CategoryController@update')->name('edit.category');
Route::post('/admin/delete/category/product', 'CategoryController@delete')->name('delete.new.category');

// post
Route::group([
    'prefix' => 'post',
    'as' => 'post.',
], function(){
Route::get('/', 'PostController@index')->name('index');
Route::post('/', 'PostController@store')->name('store');
Route::get('{id}/like', 'PostController@likePost')->name('like');
Route::post('{id}/comment', 'PostController@storeComment')->name('comment.store');
Route::delete('{id}', 'PostController@destroy')->name('destroy');
Route::delete('{id}/comment', 'PostController@destroyComment')->name('comment.destroy');
});

// member
Route::group([
    'prefix' => 'member',
    'as' => 'member.',
], function(){
Route::get('/', 'MemberController@index')->name('index');
Route::post('/', 'MemberController@store')->name('store');
Route::post('followers', 'MemberController@follower')->name('follower');
Route::post('following', 'MemberController@following')->name('following');
});

// Social Login
Route::get('social-login/{provider}/{affiliate_id?}', 'Auth\LoginController@socialLogin');
Route::get('social-callback/{provider}', 'Auth\LoginController@socialCallBack');

// create
Route::get('/create', 'CreateController@index')->name('create');

// explore
Route::group([
    'prefix' => 'explore',
    'as' => 'explore.',
], function(){
    Route::get('/', 'ExploreController@index')->name('index');
    Route::post('list', 'ExploreController@list')->name('list');
    Route::get('service/search', 'ExploreController@searchService')->name('service.search');
    // Route::post('service/search', 'ExploreController@searchService')->name('service.search');
    Route::post('map/search', 'ExploreController@searchMap')->name('map.search');
    Route::post('filter', 'ExploreController@filter')->name('filter');
});

// Logs
Route::get(config('admin.admin_route_prefix') . '/logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index')->middleware(['auth', 'dashboard', 'system_log_view'])->name('admin.logs');

Route::get('/install', 'InstallerController@redirectToRequirement')->name('LaravelInstaller::welcome');
Route::get('/install/environment', 'InstallerController@redirectToWizard')->name('LaravelInstaller::environment');


Route::get('/affiliate-{id_user}_{username}', function ($id_user, $username) {
    Cookie::queue('affiliate_id', $id_user, 43200);
    return redirect()->route('auth.register');
});

Route::post('midtrans/notification', [BookingController::class, 'handleNotification'])->name('midtrans.notification');
Route::post('midtrans/success/plan', [PlanController::class, 'handleSuccessPayment'])->name('midtrans.success.plan');

Route::get('thankyou/booking', [BookingController::class, 'thanyouController'])->name('booking.success.thankyou');

// Disable API Documentation in Production
if (app()->environment('local')) {
    Route::get('/api/documentation', '\L5Swagger\Http\Controllers\SwaggerController@api');
} else {
    Route::get('/api/documentation', function () {
        abort(404);
    });
}
