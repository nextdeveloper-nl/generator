<?php

Route::get('/', function () {
    return [
        'application'   => env('APP_NAME'),
        'health'        => 'ok',
    ];
});

Route::prefix('generator')->group(function () {
    Route::prefix('all')->group(function () {
        Route::get('/', 'AllController@index');
    });

    Route::prefix('structure')->group(function () {
        Route::get('/', 'Structure\StructureController@index');
    });

    Route::prefix('model')->group(function () {
        Route::get('/', 'Model\ModelController@index');
    });

    Route::prefix('service')->group(function () {
        Route::get('/', 'Service\ServiceController@index');
    });

    Route::prefix('tests')->group(function () {
        Route::get('/', 'Test\TestController@index');
    });
});