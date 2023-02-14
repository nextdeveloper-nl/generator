<?php

namespace NextDeveloper\Generator\Services;

use Illuminate\Support\Facades\DB;

class AbstractService
{
    public static function getColumns($model) {
        return DB::select( DB::raw("SHOW COLUMNS FROM " . $model));
    }
}