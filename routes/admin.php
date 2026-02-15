<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ReferralController;
// Admin Route
/*Route::group(['prefix'=>'admin','middleware' => ['auth','dashboard']], function() {
    Route::match(['get','post'],'/',function (){
        $module = ucfirst(htmlspecialchars('Dashboard'));
        $controller = ucfirst(htmlspecialchars($module));
        $class = "\\Modules\\$module\\Admin\\";
        $action = 'index';
        if(class_exists($class.$controller.'Controller') && method_exists($class.$controller.'Controller',$action)){
            return App::call($class.$controller.'Controller@'.$action,[]);
        }
        abort(404);
    });
    Route::match(['get','post'],'/module/{module}/{controller?}/{action?}/{param1?}/{param2?}/{param3?}',function ($module,$controller = '',$action = '',$param1 = '',$param2 = '',$param3 = ''){
        $module = ucfirst(htmlspecialchars($module));
        $controller = ucfirst(htmlspecialchars($controller));
        $class = "\\Modules\\$module\\Admin\\";
        if(!class_exists($class.$controller.'Controller')){
            $param3 = $param2;
            $param2 = $param1;
            $param1 = $action;
            $action = $controller;
            $controller = $module;
        }
        $action = $action ? $action : 'index';
        if(class_exists($class.$controller.'Controller') && method_exists($class.$controller.'Controller',$action)){
            $p = array_values(array_filter([$param1,$param2,$param3]));
            return App::call($class.$controller.'Controller@'.$action,$p);
//            return App::make($class.$controller.'Controller')->callAction($action,$p);
        }
        abort(404);
    });
});*/

Route::group([
    'prefix'=>'admin',
    'as'=>'admin.',
    'middleware' => ['auth','dashboard']
], function() {
    Route::get('referral', [ReferralController::class, 'index'])->name('referral.index');

    Route::group([
        'prefix'=>'virtuard360',
        'as' => 'virtuard360.'
    ], function() {
        Route::get('/', 'Admin\Virtuard360Controller@index')->name('index');
        Route::get('show/{id}', 'Admin\Virtuard360Controller@show')->name('show');
        Route::get('create', 'Admin\Virtuard360Controller@create')->name('create');
        Route::get('edit', 'Admin\Virtuard360Controller@edit')->name('edit');
        Route::post('store', 'Admin\Virtuard360Controller@store')->name('store');
        Route::get('/{id}/setstatus', 'Admin\Virtuard360Controller@setstatus')->name('setstatus');
        Route::put('/{id}', 'Admin\Virtuard360Controller@update')->name('update');
    });

    Route::group([
        'prefix'=>'compressimage',
        'as' => 'compressimage.'
    ], function() {
        Route::get('/', 'Admin\CompressImageController@index')->name('index');
    });

    // Puzzle AR Admin
    Route::group([
        'prefix' => 'puzzle',
        'as' => 'puzzle.'
    ], function() {
        Route::group([
            'prefix' => 'config',
            'as' => 'config.'
        ], function() {
            Route::get('/', [\App\Http\Controllers\Admin\PuzzleAdminController::class, 'index'])->name('index');
            Route::post('/', [\App\Http\Controllers\Admin\PuzzleAdminController::class, 'store'])->name('store');
        });
    });
});
