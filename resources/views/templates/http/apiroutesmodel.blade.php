@if($prefix != '/')
Route::prefix('{{ strtolower($prefix) }}')->group(function () {
@endif
        Route::get('/', '{{ $controller }}\{{ $controller }}Controller@index');
        Route::get('/{@php echo str_replace('-', '_', strtolower($model))@endphp}', '{{ $controller }}\{{ $controller }}Controller@show');
        Route::post('/', '{{ $controller }}\{{ $controller }}Controller@store');
        Route::put('/{@php echo str_replace('-', '_', strtolower($model))@endphp}', '{{ $controller }}\{{ $controller }}Controller@update');
        Route::delete('/{@php echo str_replace('-', '_', strtolower($model))@endphp}', '{{ $controller }}\{{ $controller }}Controller@destroy');
@if($prefix != '/')
    });
@endif

// EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE