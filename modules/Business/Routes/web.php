<?php
use \Illuminate\Support\Facades\Route;

Route::group(['prefix'=>config('business.business_route_prefix')],function(){
    Route::get('/','BusinessController@index')->name('business.search'); // Search
    Route::get('/{slug}','BusinessController@detail')->name('business.detail');// Detail
});


Route::group(['prefix'=>'user/'.config('business.business_route_prefix'),'middleware' => ['auth','verified']],function(){
    Route::get('/','ManageBusinessController@manageBusiness')->name('business.vendor.index');
    Route::get('/create','ManageBusinessController@createBusiness')->name('business.vendor.create');
    Route::get('/edit/{id}','ManageBusinessController@editBusiness')->name('business.vendor.edit');
    Route::get('/del/{id}','ManageBusinessController@deleteBusiness')->name('business.vendor.delete');
    Route::post('/store/{id}','ManageBusinessController@store')->name('business.vendor.store');
    Route::get('bulkEdit/{id}','ManageBusinessController@bulkEditBusiness')->name("business.vendor.bulk_edit");
    Route::get('/booking-report/bulkEdit/{id}','ManageBusinessController@bookingReportBulkEdit')->name("business.vendor.booking_report.bulk_edit");
	Route::get('clone/{id}','ManageBusinessController@cloneBusiness')->name("business.vendor.clone");
    Route::get('/recovery','ManageBusinessController@recovery')->name('business.vendor.recovery');
    Route::get('/restore/{id}','ManageBusinessController@restore')->name('business.vendor.restore');
});

Route::group(['prefix'=>'user/'.config('business.business_route_prefix')],function(){
    Route::group(['prefix'=>'availability'],function(){
        Route::get('/','AvailabilityController@index')->name('business.vendor.availability.index');
        Route::get('/loadDates','AvailabilityController@loadDates')->name('business.vendor.availability.loadDates');
        Route::post('/store','AvailabilityController@store')->name('business.vendor.availability.store');
    });
});
