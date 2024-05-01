<?php
use \Illuminate\Support\Facades\Route;
Route::get('/','CulturalController@index')->name('cultural.admin.index');
Route::get('/create','CulturalController@create')->name('cultural.admin.create');
Route::get('/edit/{id}','CulturalController@edit')->name('cultural.admin.edit');
Route::post('/store/{id}','CulturalController@store')->name('cultural.admin.store');
Route::post('/bulkEdit','CulturalController@bulkEdit')->name('cultural.admin.bulkEdit');
Route::get('/recovery','CulturalController@recovery')->name('cultural.admin.recovery');
Route::get('/getForSelect2','CulturalController@getForSelect2')->name('cultural.admin.getForSelect2');
Route::get('/getForSelect2','CulturalController@getForSelect2')->name('cultural.admin.getForSelect2');

Route::get('/category','CategoryController@index')->name('cultural.admin.category.index');
Route::get('/category/edit/{id}','CategoryController@edit')->name('cultural.admin.category.edit');
Route::post('/category/store/{id}','CategoryController@store')->name('cultural.admin.category.store');
Route::get('/category/getForSelect2','CategoryController@getForSelect2')->name('cultural.admin.category.category.getForSelect2');
Route::post('/category/bulkEdit','CategoryController@bulkEdit')->name('cultural.admin.category.bulkEdit');

Route::group(['prefix'=>'attribute'],function (){
    Route::get('/','AttributeController@index')->name('cultural.admin.attribute.index');
    Route::get('edit/{id}','AttributeController@edit')->name('cultural.admin.attribute.edit');
    Route::post('store/{id}','AttributeController@store')->name('cultural.admin.attribute.store');
    Route::post('/editAttrBulk','AttributeController@editAttrBulk')->name('cultural.admin.attribute.editAttrBulk');

    Route::get('terms/{id}','AttributeController@terms')->name('cultural.admin.attribute.term.index');
    Route::get('term_edit/{id}','AttributeController@term_edit')->name('cultural.admin.attribute.term.edit');
    Route::post('term_store','AttributeController@term_store')->name('cultural.admin.attribute.term.store');
    Route::post('/editTermBulk','AttributeController@editTermBulk')->name('cultural.admin.attribute.term.editTermBulk');

    Route::get('getForSelect2','AttributeController@getForSelect2')->name('cultural.admin.attribute.term.getForSelect2');
});

Route::group(['prefix'=>'availability'],function(){
    Route::get('/','AvailabilityController@index')->name('cultural.admin.availability.index');
    Route::get('/loadDates','AvailabilityController@loadDates')->name('cultural.admin.availability.loadDates');
    Route::post('/store','AvailabilityController@store')->name('cultural.admin.availability.store');
});
