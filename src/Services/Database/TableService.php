<?php

namespace NextDeveloper\Generator\Services\Database;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use NextDeveloper\Generator\Services\AbstractService;
use NextDeveloper\Generator\Exceptions\TemplateNotFoundException;

class TableService extends AbstractService
{
    public static function getTables($search = null) :array {
        $tables = DB::select('SHOW TABLES');

        $tempTables = [];

        if($search) {
            $search = Str::remove('*', $search);

            $prop = "Tables_in_".env('DB_DATABASE');

            foreach ($tables as $table) {
                if(Str::startsWith($table->$prop, $search)) {
                    $tempTables[] = $table->$prop;
                }
            }
        } else {
            $tempTables = $tables;
        }

        return $tempTables;
    }
}