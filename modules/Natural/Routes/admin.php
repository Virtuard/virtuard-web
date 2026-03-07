<?php
use Illuminate\Support\Facades\Route;

Route::get('/','NaturalController@index')->name('natural.admin.index');
Route::get('/create','NaturalController@create')->name('natural.admin.create');
Route::get('/edit/{id}','NaturalController@edit')->name('natural.admin.edit');
Route::post('/store/{id}','NaturalController@store')->name('natural.admin.store');
Route::get('/getForSelect2','NaturalController@getForSelect2')->name('natural.admin.getForSelect2');
Route::post('/bulkEdit','NaturalController@bulkEdit')->name('natural.admin.bulkEdit');
Route::get('/recovery','NaturalController@recovery')->name('natural.admin.recovery');
Route::get('/getForSelect2','NaturalController@getForSelect2')->name('natural.admin.getForSelect2');

Route::get('/category','CategoryController@index')->name('natural.admin.category.index');
Route::get('/category/edit/{id}','CategoryController@edit')->name('natural.admin.category.edit');
Route::post('/category/store/{id}','CategoryController@store')->name('natural.admin.category.store');
Route::get('/category/getForSelect2','CategoryController@getForSelect2')->name('natural.admin.category.category.getForSelect2');
Route::post('/category/bulkEdit','CategoryController@bulkEdit')->name('natural.admin.category.bulkEdit');

Route::group(['prefix'=>'attribute'],function(){
    Route::get('/','AttributeController@index')->name('natural.admin.attribute.index');
    Route::get('/edit/{id}','AttributeController@edit')->name('natural.admin.attribute.edit');
    Route::post('/store/{id}','AttributeController@store')->name('natural.admin.attribute.store');
    Route::post('/editAttrBulk','AttributeController@editAttrBulk')->name('natural.admin.attribute.editAttrBulk');


    Route::get('/terms/{attr_id}','AttributeController@terms')->name('natural.admin.attribute.term.index');
    Route::get('/term_edit/{id}','AttributeController@term_edit')->name('natural.admin.attribute.term.edit');
    Route::post('/term_store/{id}','AttributeController@term_store')->name('natural.admin.attribute.term.store');
    Route::post('/editTermBulk','AttributeController@editTermBulk')->name('natural.admin.attribute.term.editTermBulk');
});


Route::group(['prefix'=>'availability'],function(){
    Route::get('/','AvailabilityController@index')->name('natural.admin.availability.index');
    Route::get('/loadDates','AvailabilityController@loadDates')->name('natural.admin.availability.loadDates');
    Route::post('/store','AvailabilityController@store')->name('natural.admin.availability.store');
});


Route::get('/booking','BookingController@index')->name('natural.admin.booking.index');
