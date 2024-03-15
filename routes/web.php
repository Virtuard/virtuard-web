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
Route::get('/user/virtuard-360', 'VirtuardController@vendorVirtuardIndex')->name('user-virtuard');
Route::group([
    'middleware' => ['user_plan']
], function (){
Route::get('/user/add/virtuard-360', 'VirtuardController@vendorVirtuardAdd')->name('user-add-virtuard');
Route::post('/user/submission-virtuard-360', 'VirtuardController@submissionService')->name('submission-service');
Route::post('/user/add/new/virtuard-360', 'VirtuardController@addNewVirtuard360')->name('add-new-service');
Route::post('/user/add/newImage/virtuard-360', 'VirtuardController@addNewImageVirtuard360')->name('add-new-image-service');
Route::get('/user/add/virtuard-360/api/edit', 'VirtuardController@vendorVirtuardAjaxGetApi')->name('user-add-virtuard-api');
Route::post('/user/add/virtuard-360/api', 'VirtuardController@vendorVirtuardAddApi')->name('user-add-virtuard-api');
Route::post('/user/add/virtuard-360/apiSecond', 'VirtuardController@vendorVirtuardAddApiSecond')->name('user-add-virtuard-api-second');
Route::get('/user/edit/virtuard-360', 'VirtuardController@vendorVirtuardEdit')->name('user-edit-virtuard');
Route::post('/user/edit/virtuard-360/updateTour', 'VirtuardController@updateIsTourField')->name('user-update-tour');
Route::get('/user/delete/virtuard-360', 'VirtuardController@vendorVirtuardDelete')->name('user-delete-virtuard');   
});

// Story
Route::get('/user/get/story/api', 'StoryController@getStory')->name('get-story');
Route::post('/user/add/story/api', 'StoryController@addStory')->name('add-story');

Route::get('/admin/virtuard-360', 'VirtuardController@adminVirtuardIndex')->name('admin-virtuard');
Route::post('/admin/submission-virtuard-360', 'VirtuardController@validateService')->name('validate-service');

// Category
Route::get('/admin/add/category/product/{type}', 'CategoryController@index');
Route::post('/admin/add/category/product', 'CategoryController@store')->name('add.category');
Route::put('/admin/edit/category/product', 'CategoryController@update')->name('edit.category');
Route::post('/admin/delete/category/product', 'CategoryController@delete')->name('delete.new.category');

// Follow Boards
Route::get('/user/follow-boards', 'FollowBoardsController@index')->name('user-follow');
Route::post('/user/add/follow-boards', 'FollowBoardsController@store')->name('user.post.status');
Route::get('/user/like/{id}', 'FollowBoardsController@likePost')->name('user.post.like');
Route::post('/user/comment/{id}', 'FollowBoardsController@commentPost')->name('user.post.comment');

// Follow Members
Route::get('/user/follow-member', 'MemberController@index')->name('user-follow.member');
Route::post('/user/add/follow-member', 'MemberController@store')->name('user.add.follow.member');

// Social Login
Route::get('social-login/{provider}', 'Auth\LoginController@socialLogin');
Route::get('social-callback/{provider}', 'Auth\LoginController@socialCallBack');

// create
Route::get('/create', 'CreateController@index')->name('create');

// explore
Route::get('/explore', 'ExploreController@index')->name('explore.index');

// Logs
Route::get(config('admin.admin_route_prefix') . '/logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index')->middleware(['auth', 'dashboard', 'system_log_view'])->name('admin.logs');

Route::get('/install', 'InstallerController@redirectToRequirement')->name('LaravelInstaller::welcome');
Route::get('/install/environment', 'InstallerController@redirectToWizard')->name('LaravelInstaller::environment');
