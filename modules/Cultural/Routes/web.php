<?php
use \Illuminate\Support\Facades\Route;

Route::group(['prefix'=>env('CULTURAL_ROUTE_PREFIX','cultural')],function(){
    Route::get('/','CulturalController@index')->name('cultural.search'); // Search
    Route::get('/{slug}','CulturalController@detail')->name('cultural.detail');// Detail
});

Route::group(['prefix'=>'user/'.env('CULTURAL_ROUTE_PREFIX','cultural'),'middleware' => ['auth','verified']],function(){
    Route::get('/','VendorCulturalController@indexCultural')->name('cultural.vendor.index');
    Route::get('/create','VendorCulturalController@createCultural')->name('cultural.vendor.create');
    Route::get('/edit/{id}','VendorCulturalController@editCultural')->name('cultural.vendor.edit');
    Route::get('/del/{id}','VendorCulturalController@deleteCultural')->name('cultural.vendor.delete');
    Route::post('/store/{id}','VendorCulturalController@store')->name('cultural.vendor.store');
    Route::get('bulkEdit/{id}','VendorCulturalController@bulkEditCultural')->name("cultural.vendor.bulk_edit");
    Route::get('/booking-report/bulkEdit/{id}','VendorCulturalController@bookingReportBulkEdit')->name("cultural.vendor.booking_report.bulk_edit");
    Route::get('/recovery','VendorCulturalController@recovery')->name('cultural.vendor.recovery');
    Route::get('/restore/{id}','VendorCulturalController@restore')->name('cultural.vendor.restore');
});

Route::group(['prefix'=>'user/'.env('CULTURAL_ROUTE_PREFIX','cultural')],function(){
    Route::group(['prefix'=>'availability'],function(){
        Route::get('/','AvailabilityController@index')->name('cultural.vendor.availability.index');
        Route::get('/loadDates','AvailabilityController@loadDates')->name('cultural.vendor.availability.loadDates');
        Route::post('/store','AvailabilityController@store')->name('cultural.vendor.availability.store');
    });
});
