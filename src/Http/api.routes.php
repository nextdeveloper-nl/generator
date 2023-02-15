<?php

Route::get('/', function () {
    return [
        'application'   => env('APP_NAME'),
        'health'        => 'ok',
    ];
});

Route::prefix('generator')->group(function () {
    Route::prefix('structure')->group(function () {
        Route::get('/', 'Structure\StructureController@index');
    });
});