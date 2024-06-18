<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/intro', 'LandingpageController@index');
Route::get('/', 'HomeController@index');
Route::get('/home', 'HomeController@index')->name('home');
Route::post('/install/check-db', 'HomeController@checkConnectDatabase');

// Virtuard 360
Route::group([
    'prefix' => '/user/virtuard-360',
    'as' => 'user.virtuard-360.',
    'middleware' => ['auth'],
], function (){
Route::get('/', 'VirtuardController@vendorVirtuardIndex')->name('index');
Route::post('/add/newImage', 'VirtuardController@addNewImageVirtuard360')->name('add-new-image-service');
Route::get('/add/api/edit', 'VirtuardController@vendorVirtuardAjaxGetApi')->name('add-virtuard-api');
Route::post('/add/api', 'VirtuardController@vendorVirtuardAddApi')->name('add-virtuard-api');
Route::post('/add/apiSecond', 'VirtuardController@vendorVirtuardAddApiSecond')->name('add-virtuard-api-second');
Route::post('/add/apiSecond', 'VirtuardController@vendorVirtuardAddApiSecond')->name('add-virtuard-api-second');
Route::get('bulkEdit/{id}', 'VirtuardController@bulkEdit')->name('bulk_edit');

Route::group([
    'middleware' => ['user_plan']
], function (){
Route::get('/add', 'VirtuardController@vendorVirtuardAdd')->name('add');
Route::post('/submission-virtuard-360', 'VirtuardController@submissionService')->name('submission-service');
Route::post('/add/new', 'VirtuardController@addNewVirtuard360')->name('add-new-service');
Route::get('/edit', 'VirtuardController@vendorVirtuardEdit')->name('edit');
Route::post('/edit/updateTour', 'VirtuardController@updateIsTourField')->name('update-tour');
Route::get('/{id}/delete', 'VirtuardController@vendorVirtuardDelete')->name('destroy');   
});
});

// Story
Route::get('/user/get/story/api', 'StoryController@getStory')->name('get-story');
Route::post('/user/add/story/api', 'StoryController@addStory')->name('add-story');

// Route::get('/admin/virtuard-360', 'VirtuardController@adminVirtuardIndex')->name('admin-virtuard');
// Route::post('/admin/submission-virtuard-360', 'VirtuardController@validateService')->name('validate-service');

// Category
Route::get('/admin/add/category/product/{type}', 'CategoryController@index');
Route::post('/admin/add/category/product', 'CategoryController@store')->name('add.category');
Route::put('/admin/edit/category/product', 'CategoryController@update')->name('edit.category');
Route::post('/admin/delete/category/product', 'CategoryController@delete')->name('delete.new.category');

// Follow Boards
Route::get('/user/follow-boards', 'FollowBoardsController@index')->name('user.board.index');
Route::post('/user/add/follow-boards', 'FollowBoardsController@store')->name('user.post.status');
Route::get('/user/like/{id}', 'FollowBoardsController@likePost')->name('user.post.like');
Route::post('/user/comment/{id}', 'FollowBoardsController@commentPost')->name('user.post.comment');

// Follow Members
Route::get('/user/member', 'MemberController@index')->name('member.index');
Route::post('/user/member', 'MemberController@store')->name('member.store');
Route::post('/user/followers', 'MemberController@follower')->name('member.follower');
Route::post('/user/following', 'MemberController@following')->name('member.following');

// Social Login
Route::get('social-login/{provider}', 'Auth\LoginController@socialLogin');
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
    Route::post('filter', 'ExploreController@filter')->name('filter');
});

// Logs
Route::get(config('admin.admin_route_prefix') . '/logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index')->middleware(['auth', 'dashboard', 'system_log_view'])->name('admin.logs');

Route::get('/install', 'InstallerController@redirectToRequirement')->name('LaravelInstaller::welcome');
Route::get('/install/environment', 'InstallerController@redirectToWizard')->name('LaravelInstaller::environment');
