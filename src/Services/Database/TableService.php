<?php

namespace NextDeveloper\Generator\Services\Database;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use NextDeveloper\Generator\Services\AbstractService;
use NextDeveloper\Generator\Exceptions\TemplateNotFoundException;

class TableService extends AbstractService
{
    public static function getTablesInMySQL($search = null) {
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

    public static function getTablesInPostgreSQL($search = null) {
        $tables = DB::select('SELECT table_name FROM information_schema.tables WHERE table_schema=\'public\' AND table_type=\'BASE TABLE\'');

        $tempTables = [];

        if($search) {
            $search = Str::remove('*', $search);

            foreach ($tables as $table) {
                if(Str::startsWith($table->table_name, $search)) {
                    $tempTables[] = $table->table_name;
                }
            }
        } else {
            foreach ($tables as $table) {
                $tempTables[] = $table->table_name;
            }
        }

        return $tempTables;
    }

    public static function getTables($search = null) :array {
        if(config('database.default') == 'mysql') {
            return self::getTablesInMySQL($search);
        } else {
            return self::getTablesInPostgreSQL($search);
        }

        return $tempTables;
    }

    public static function getViewsInMySQL($search = null) : array {
        $tables = DB::select('show full tables where Table_Type = \'VIEW\'');

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

    public static function getViewsInPostgreSQL($search = null) : array {
        $tables = DB::select('SELECT table_name FROM information_schema.tables WHERE table_schema=\'public\' AND table_type=\'VIEW\'');

        $tempTables = [];

        if($search) {
            $search = Str::remove('*', $search);

            foreach ($tables as $table) {
                if(Str::startsWith($table->table_name, $search)) {
                    $tempTables[] = $table->table_name;
                }
            }
        } else {
            foreach ($tables as $table) {
                $tempTables[] = $table->table_name;
            }
        }

        return $tempTables;
    }

    public static function getViews($search = null) : array {
        if(config('database.default') == 'mysql') {
            return self::getViewsInMySQL($search);
        } else {
            return self::getViewsInPostgreSQL($search);
        }
    }
}
