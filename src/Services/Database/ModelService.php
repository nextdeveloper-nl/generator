<?php

namespace NextDeveloper\Generator\Services\Database;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use NextDeveloper\Generator\Services\AbstractService;
use NextDeveloper\Generator\Exceptions\TemplateNotFoundException;

class ModelService extends AbstractService
{
    
    /**
     * @throws TemplateNotFoundException
     */
    public static function generate($namespace, $module, $model) {
        $columns = self::getColumns($model);
        $casts = self::generateCastsArray($columns);
        $dates = self::generateDatesArray($columns);
        $fullTextFields = self::generateFullTextFieldsArray($model);
        $tabAmount = 2;

        $hasTimestamps = false;

        foreach ($columns as $column) {
            if($column->Field == 'created_at') {
                $hasTimestamps = true;
                break;
            }
        }

        $render = view('Generator::templates/database/model', [
            'namespace'         =>  $namespace,
            'module'            =>  $module,
            'has_created'       =>  self::hasColumn('created_at', $model),
            'has_updated'       =>  self::hasColumn('updated_at', $model),
            'has_deleted'       =>  self::hasColumn('deleted_at', $model),
            'model'             =>  ucfirst(Str::singular($model)),
            'casts'             =>  self::objectArrayToString($casts,$tabAmount),
            'dates'             =>  self::arrayToString($dates),
            'fullTextFields'    =>  self::arrayToString($fullTextFields),
            'perPage'           =>  config('generator.pagination.perPage'),
            'hasTimestamps'     =>  $hasTimestamps
        ])->render();

        return $render;
    }

    public static function generateFile($rootPath, $namespace, $module, $model) : bool{
        $content = self::generate($namespace, $module, $model);

        self::writeToFile($rootPath . '/src/Database/Models/' . ucfirst(Str::singular($model)) . '.php', $content);

        return true;
    }

    public static function generateCastsArray($columns) {
        $casts = [];

        foreach ($columns as $column) {
            $columnType = self::cleanColumnType($column->Type); 
            switch ($columnType) {
                case 'boolean':
                case 'tinyint':
                    $casts[$column->Field] = 'boolean';
                    break;
                case 'decimal':
                case 'float':
                case 'double':
                case 'real':
                    $casts[$column->Field] = 'double';
                    break;
                case 'int':
                case 'integer':
                case 'bigint':
                case 'mediumint':
                case 'smallint':
                    $casts[$column->Field] = 'integer';
                    break;
                case 'date':
                case 'datetime':
                case 'timestamp':
                case 'immutable_date':
                case 'immutable_datetime':
                    $casts[$column->Field] = 'datetime';
                    break;
                case 'text':
                case 'mediumtext':
                case 'longtext':
                case 'varchar':
                case 'char':
                    $casts[$column->Field] = 'string';
                    break;
            }
            
        }

        return $casts;
    }

    public static function generateDatesArray($columns) {
        $dates = [];

        foreach ($columns as $column) {
            $columnType = self::cleanColumnType($column->Type);
            switch ($columnType) {
                case 'date':
                case 'datetime':
                case 'timestamp':
                case 'immutable_date':
                case 'immutable_datetime':
                    $dates[] = $column->Field;
                    break;
            }
        }

        return $dates;
    }

    public static function generateFullTextFieldsArray($model) {
        $fullTextFields = [];

        $indexes = DB::select(DB::raw("SHOW INDEX FROM ".$model." WHERE Index_type = 'FULLTEXT'"));
            foreach ($indexes as $index) {
                $fullTextFields[] = $index->Column_name;
            }
        return $fullTextFields;
    }
}