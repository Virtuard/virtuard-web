<?php
use \Illuminate\Support\Facades\Route;
Route::get('/','ArtController@index')->name('art.admin.index');
Route::get('/create','ArtController@create')->name('art.admin.create');
Route::get('/edit/{id}','ArtController@edit')->name('art.admin.edit');
Route::post('/store/{id}','ArtController@store')->name('art.admin.store');
Route::post('/bulkEdit','ArtController@bulkEdit')->name('art.admin.bulkEdit');
Route::get('/recovery','ArtController@recovery')->name('art.admin.recovery');
Route::get('/getForSelect2','ArtController@getForSelect2')->name('art.admin.getForSelect2');
Route::get('/getForSelect2','ArtController@getForSelect2')->name('art.admin.getForSelect2');

Route::get('/category','CategoryController@index')->name('art.admin.category.index');
Route::get('/category/edit/{id}','CategoryController@edit')->name('art.admin.category.edit');
Route::post('/category/store/{id}','CategoryController@store')->name('art.admin.category.store');
Route::get('/category/getForSelect2','CategoryController@getForSelect2')->name('art.admin.category.category.getForSelect2');
Route::post('/category/bulkEdit','CategoryController@bulkEdit')->name('art.admin.category.bulkEdit');

Route::group(['prefix'=>'attribute'],function (){
    Route::get('/','AttributeController@index')->name('art.admin.attribute.index');
    Route::get('edit/{id}','AttributeController@edit')->name('art.admin.attribute.edit');
    Route::post('store/{id}','AttributeController@store')->name('art.admin.attribute.store');
    Route::post('/editAttrBulk','AttributeController@editAttrBulk')->name('art.admin.attribute.editAttrBulk');

    Route::get('terms/{id}','AttributeController@terms')->name('art.admin.attribute.term.index');
    Route::get('term_edit/{id}','AttributeController@term_edit')->name('art.admin.attribute.term.edit');
    Route::post('term_store','AttributeController@term_store')->name('art.admin.attribute.term.store');
    Route::post('/editTermBulk','AttributeController@editTermBulk')->name('art.admin.attribute.term.editTermBulk');

    Route::get('getForSelect2','AttributeController@getForSelect2')->name('art.admin.attribute.term.getForSelect2');
});

Route::group(['prefix'=>'availability'],function(){
    Route::get('/','AvailabilityController@index')->name('art.admin.availability.index');
    Route::get('/loadDates','AvailabilityController@loadDates')->name('art.admin.availability.loadDates');
    Route::post('/store','AvailabilityController@store')->name('art.admin.availability.store');
});
