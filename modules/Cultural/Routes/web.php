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
// Vendor Manage Cultural
Route::group(['prefix'=>'user/'.config('cultural.cultural_route_prefix'),'middleware' => ['auth','verified']],function(){
    Route::get('/','ManageCulturalController@manageCultural')->name('cultural.vendor.index');
    Route::get('/create','ManageCulturalController@createCultural')->name('cultural.vendor.create');
    Route::get('/edit/{id}','ManageCulturalController@editCultural')->name('cultural.vendor.edit');
    Route::get('/del/{id}','ManageCulturalController@deleteCultural')->name('cultural.vendor.delete');
    Route::post('/store/{id}','ManageCulturalController@store')->name('cultural.vendor.store');
    Route::get('bulkEdit/{id}','ManageCulturalController@bulkEditCultural')->name("cultural.vendor.bulk_edit");
    Route::get('clone/{id}','ManageCulturalController@cloneCultural')->name("cultural.vendor.clone");
    Route::get('/booking-report/bulkEdit/{id}','ManageCulturalController@bookingReportBulkEdit')->name("cultural.vendor.booking_report.bulk_edit");
    Route::get('/recovery','ManageCulturalController@recovery')->name('cultural.vendor.recovery');
    Route::get('/restore/{id}','ManageCulturalController@restore')->name('cultural.vendor.restore');
});
Route::group(['prefix'=>'user/'.config('cultural.cultural_route_prefix')],function(){
    Route::group(['prefix'=>'availability'],function(){
        Route::get('/','AvailabilityController@index')->name('cultural.vendor.availability.index');
        Route::get('/loadDates','AvailabilityController@loadDates')->name('cultural.vendor.availability.loadDates');
        Route::post('/store','AvailabilityController@store')->name('cultural.vendor.availability.store');
    });
});
// Cultural
Route::group(['prefix'=>config('cultural.cultural_route_prefix')],function(){
    Route::get('/','\Modules\Cultural\Controllers\CulturalController@index')->name('cultural.search'); // Search
    Route::get('/{slug}','\Modules\Cultural\Controllers\CulturalController@detail');// Detail
});
