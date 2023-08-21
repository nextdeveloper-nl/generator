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
        $tables = DB::select('show full tables where Table_Type = \'BASE TABLE\'');

        $tempTables = [];

        $prop = "Tables_in_".env('DB_DATABASE');

        if($search) {
            $search = Str::remove('*', $search);

            foreach ($tables as $table) {
                if(Str::startsWith($table->$prop, $search)) {
                    $tempTables[] = $table->$prop;
                }
            }
        } else {
            foreach ($tables as $table) {
                $tempTables[] = $table->$prop;
            }
        }

        return $tempTables;
    }

    public static function getViews($search = null) : array {
        $tables = DB::select('show full tables where Table_Type = \'VIEW\'');

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
            foreach ($tables as $table) {
                $tempTables[] = $table->$prop;
            }
        }

        return $tempTables;
    }
}