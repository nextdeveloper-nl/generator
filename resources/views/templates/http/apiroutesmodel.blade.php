Route::prefix('{{ strtolower($model) }}')->group(function () {
        Route::get('/', '{{ $model }}\{{ $model }}Controller@index');
        Route::get('/{@php echo strtolower($model)@endphp}', '{{ $model }}\{{ $model }}Controller@show');
        Route::post('/', '{{ $model }}\{{ $model }}Controller@store');
        Route::put('/{@php echo strtolower($model)@endphp}', '{{ $model }}\{{ $model }}Controller@update');
        Route::delete('/{@php echo strtolower($model)@endphp}', '{{ $model }}\{{ $model }}Controller@destroy');
    });

//!APPENDHERE