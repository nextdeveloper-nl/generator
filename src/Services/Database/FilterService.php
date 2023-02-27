<?php

namespace NextDeveloper\Generator\Services\Database;

use Illuminate\Support\Str;
use NextDeveloper\Generator\Exceptions\TemplateNotFoundException;
use NextDeveloper\Generator\Services\AbstractService;

class FilterService extends AbstractService
{
    /**
     * @throws TemplateNotFoundException
     */
    public static function generate($namespace, $module, $model) {
        $columns = self::getColumns($model);
        $filterTextFields = self::generateFilterTextFields($columns);
        $filterNumberFields = self::generateFilterNumberFields($columns);
        $filterDateFields = self::generateFilterDateFields($columns);

        $render = view('Generator::templates/database/filter', [
            'namespace'          =>  $namespace,
            'module'             =>  $module,
            'model'              =>  ucfirst(Str::singular($model)),
            'columns'            =>  $columns,
            'filterTextFields'   =>  $filterTextFields,
            'filterNumberFields' =>  $filterNumberFields,
            'filterDateFields'   =>  $filterDateFields
        ])->render();

        return $render;
    }

    public static function generateFile($rootPath, $namespace, $module, $model) : bool{
        $content = self::generate($namespace, $module, $model);

        self::writeToFile($rootPath . '/src/Database/Filters/' . ucfirst(Str::singular($model)) . 'QueryFilter.php', $content);

        return true;
    }

    public static function generateFilterTextFields($columns) {
        $filterTextFields = [];

        foreach ($columns as $column) {
            $columnType = self::cleanColumnType($column->Type);
            switch ($columnType) {
                case 'text':
                case 'mediumtext':
                case 'longtext':
                case 'varchar':
                case 'char':
                    $filterTextFields[] = $column->Field;
                    break;
            }
            
        }
        
        return $filterTextFields;
    }

    public static function generateFilterNumberFields($columns) {
        $filterNumberFields = [];

        foreach ($columns as $column) {
            $columnType = self::cleanColumnType($column->Type);
            switch ($columnType) {
                case 'decimal':
                case 'float':
                case 'double':
                case 'real':
                case 'int':
                case 'integer':
                case 'bigint':
                case 'mediumint':
                case 'smallint':
                    $filterNumberFields[] = $column->Field;
                    break;
            }        
        }
        return $filterNumberFields;
    }

    public static function generateFilterDateFields($columns) {
        $filterDateFields = [];

        foreach ($columns as $column) {
            $columnType = self::cleanColumnType($column->Type);
            switch ($columnType) {
                case 'date':
                case 'datetime':
                case 'timestamp':
                case 'immutable_date':
                case 'immutable_datetime':
                    $filterDateFields[] = $column->Field;
                    break;
            }        
        }
        return $filterDateFields;
    }
}