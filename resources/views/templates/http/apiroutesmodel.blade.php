@if($prefix != '/')
Route::prefix('{{ strtolower($prefix) }}')->group(function () {
@endif
        Route::get('/', '{{ $controller }}\{{ $controller }}Controller@index');

@php
    if($traits) {
        foreach ($traits as $trait) {
            echo 'Route::get(\'{' . str_replace('-', '_', strtolower($model)) . '}/' . $trait['suffix'] . ' \', \'' . $controller . '\\' . $controller . 'Controller@' . $trait['get_method'] . '\');' . PHP_EOL;
            echo 'Route::post(\'{' . str_replace('-', '_', strtolower($model)) . '}/' . $trait['suffix'] . ' \', \'' . $controller . '\\' . $controller . 'Controller@' . $trait['post_method'] . '\');';
            echo PHP_EOL;
        }
    }
@endphp

        Route::get('/{@php echo str_replace('-', '_', strtolower($model))@endphp}/{subObjects}', '{{ $controller }}\{{ $controller }}Controller@relatedObjects');
        Route::get('/{@php echo str_replace('-', '_', strtolower($model))@endphp}', '{{ $controller }}\{{ $controller }}Controller@show');

        Route::post('/', '{{ $controller }}\{{ $controller }}Controller@store');
        Route::patch('/{@php echo str_replace('-', '_', strtolower($model))@endphp}', '{{ $controller }}\{{ $controller }}Controller@update');
        Route::delete('/{@php echo str_replace('-', '_', strtolower($model))@endphp}', '{{ $controller }}\{{ $controller }}Controller@destroy');
@if($prefix != '/')
    });
@endif

// EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
