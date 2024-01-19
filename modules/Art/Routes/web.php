<?php
use \Illuminate\Support\Facades\Route;

Route::group(['prefix'=>env('ART_ROUTE_PREFIX','art')],function(){
    Route::get('/','ArtController@index')->name('art.search'); // Search
    Route::get('/{slug}','ArtController@detail')->name('art.detail');// Detail
});

Route::group(['prefix'=>'user/'.env('ART_ROUTE_PREFIX','art'),'middleware' => ['auth','verified']],function(){
    Route::get('/','VendorArtController@indexArt')->name('art.vendor.index');
    Route::get('/create','VendorArtController@createArt')->name('art.vendor.create');
    Route::get('/edit/{id}','VendorArtController@editArt')->name('art.vendor.edit');
    Route::get('/del/{id}','VendorArtController@deleteArt')->name('art.vendor.delete');
    Route::post('/store/{id}','VendorArtController@store')->name('art.vendor.store');
    Route::get('bulkEdit/{id}','VendorArtController@bulkEditArt')->name("art.vendor.bulk_edit");
    Route::get('/booking-report/bulkEdit/{id}','VendorArtController@bookingReportBulkEdit')->name("art.vendor.booking_report.bulk_edit");
    Route::get('/recovery','VendorArtController@recovery')->name('art.vendor.recovery');
    Route::get('/restore/{id}','VendorArtController@restore')->name('art.vendor.restore');
});

Route::group(['prefix'=>'user/'.env('ART_ROUTE_PREFIX','art')],function(){
    Route::group(['prefix'=>'availability'],function(){
        Route::get('/','AvailabilityController@index')->name('art.vendor.availability.index');
        Route::get('/loadDates','AvailabilityController@loadDates')->name('art.vendor.availability.loadDates');
        Route::post('/store','AvailabilityController@store')->name('art.vendor.availability.store');
    });
});
