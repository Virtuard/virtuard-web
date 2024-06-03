<?php
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
use Illuminate\Support\Facades\Route;

// Vendor Manage Natural
Route::group(['prefix'=>'user/'.config('natural.natural_route_prefix'),'middleware' => ['auth','verified']],function(){
    Route::get('/','ManageNaturalController@manageNatural')->name('natural.vendor.index');
    Route::get('/create','ManageNaturalController@createNatural')->name('natural.vendor.create');
    Route::get('/edit/{id}','ManageNaturalController@editNatural')->name('natural.vendor.edit');
    Route::get('/del/{id}','ManageNaturalController@deleteNatural')->name('natural.vendor.delete');
    Route::post('/store/{id}','ManageNaturalController@store')->name('natural.vendor.store');
    Route::get('bulkEdit/{id}','ManageNaturalController@bulkEditNatural')->name("natural.vendor.bulk_edit");
    Route::get('clone/{id}','ManageNaturalController@cloneNatural')->name("natural.vendor.clone");
    Route::get('/booking-report/bulkEdit/{id}','ManageNaturalController@bookingReportBulkEdit')->name("natural.vendor.booking_report.bulk_edit");
    Route::get('/recovery','ManageNaturalController@recovery')->name('natural.vendor.recovery');
    Route::get('/restore/{id}','ManageNaturalController@restore')->name('natural.vendor.restore');
});
Route::group(['prefix'=>'user/'.config('natural.natural_route_prefix')],function(){
    Route::group(['prefix'=>'availability'],function(){
        Route::get('/','AvailabilityController@index')->name('natural.vendor.availability.index');
        Route::get('/loadDates','AvailabilityController@loadDates')->name('natural.vendor.availability.loadDates');
        Route::post('/store','AvailabilityController@store')->name('natural.vendor.availability.store');
    });
});
// Natural
Route::group(['prefix'=>config('natural.natural_route_prefix')],function(){
    Route::get('/','\Modules\Natural\Controllers\NaturalController@index')->name('natural.search'); // Search
    Route::get('/{slug}','\Modules\Natural\Controllers\NaturalController@detail')->name('natural.detail');// Detail
});
