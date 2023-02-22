<?php

namespace NextDeveloper\Generator\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AbstractService
{
    public static function getColumns($model) {
        return DB::select( DB::raw("SHOW COLUMNS FROM " . $model));
    }

    public static function writeToFile($file, $content, $fileType = 'php') {
        switch ($fileType) {
            case 'php':
                $content = '<?php' . PHP_EOL . PHP_EOL . $content;
                break;
            case 'json':
                break;
        }

        file_put_contents(base_path($file), $content);
    }
}