<?php
use \Illuminate\Support\Facades\Route;

Route::group(['prefix'=>env('NATURAL_ROUTE_PREFIX','natural')],function(){
    Route::get('/','NaturalController@index')->name('natural.search'); // Search
    Route::get('/{slug}','NaturalController@detail')->name('natural.detail');// Detail
});

Route::group(['prefix'=>'user/'.env('NATURAL_ROUTE_PREFIX','natural'),'middleware' => ['auth','verified']],function(){
    Route::get('/','VendorNaturalController@indexNatural')->name('natural.vendor.index');
    Route::get('/create','VendorNaturalController@createNatural')->name('natural.vendor.create');
    Route::get('/edit/{id}','VendorNaturalController@editNatural')->name('natural.vendor.edit');
    Route::get('/del/{id}','VendorNaturalController@deleteNatural')->name('natural.vendor.delete');
    Route::post('/store/{id}','VendorNaturalController@store')->name('natural.vendor.store');
    Route::get('bulkEdit/{id}','VendorNaturalController@bulkEditNatural')->name("natural.vendor.bulk_edit");
    Route::get('/booking-report/bulkEdit/{id}','VendorNaturalController@bookingReportBulkEdit')->name("natural.vendor.booking_report.bulk_edit");
    Route::get('/recovery','VendorNaturalController@recovery')->name('natural.vendor.recovery');
    Route::get('/restore/{id}','VendorNaturalController@restore')->name('natural.vendor.restore');
});

Route::group(['prefix'=>'user/'.env('NATURAL_ROUTE_PREFIX','natural')],function(){
    Route::group(['prefix'=>'availability'],function(){
        Route::get('/','AvailabilityController@index')->name('natural.vendor.availability.index');
        Route::get('/loadDates','AvailabilityController@loadDates')->name('natural.vendor.availability.loadDates');
        Route::post('/store','AvailabilityController@store')->name('natural.vendor.availability.store');
    });
});
