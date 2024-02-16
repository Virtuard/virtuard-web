<?php

use \Illuminate\Support\Facades\Route;


Route::get('/','BusinessController@index')->name('business.admin.index');
Route::get('/create','BusinessController@create')->name('business.admin.create');
Route::get('/edit/{id}','BusinessController@edit')->name('business.admin.edit');
Route::post('/store/{id}','BusinessController@store')->name('business.admin.store');
Route::post('/bulkEdit','BusinessController@bulkEdit')->name('business.admin.bulkEdit');
Route::get('/recovery','BusinessController@recovery')->name('business.admin.recovery');
Route::get('/getForSelect2','BusinessController@getForSelect2')->name('business.admin.getForSelect2');
Route::get('/getForSelect2','BusinessController@getForSelect2')->name('business.admin.getForSelect2');


Route::group(['prefix'=>'attribute'],function (){
    Route::get('/','AttributeController@index')->name('business.admin.attribute.index');
    Route::get('edit/{id}','AttributeController@edit')->name('business.admin.attribute.edit');
    Route::post('store/{id}','AttributeController@store')->name('business.admin.attribute.store');
    Route::post('/editAttrBulk','AttributeController@editAttrBulk')->name('business.admin.attribute.editAttrBulk');


    Route::get('terms/{id}','AttributeController@terms')->name('business.admin.attribute.term.index');
    Route::get('term_edit/{id}','AttributeController@term_edit')->name('business.admin.attribute.term.edit');
    Route::post('term_store','AttributeController@term_store')->name('business.admin.attribute.term.store');
    Route::post('/editTermBulk','AttributeController@editTermBulk')->name('business.admin.attribute.term.editTermBulk');

    Route::get('getForSelect2','AttributeController@getForSelect2')->name('business.admin.attribute.term.getForSelect2');
});

Route::group(['prefix'=>'availability'],function(){
    Route::get('/','AvailabilityController@index')->name('business.admin.availability.index');
    Route::get('/loadDates','AvailabilityController@loadDates')->name('business.admin.availability.loadDates');
    Route::post('/store','AvailabilityController@store')->name('business.admin.availability.store');
});
