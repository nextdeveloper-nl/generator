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

        $render = view('Generator::templates/database/filter', [
            'namespace'          =>  $namespace,
            'module'             =>  $module,
            'model'              =>  ucfirst(Str::singular($model)),
            'columns'            =>  $columns,
            'filterTextFields'   =>  $filterTextFields,
            'filterNumberFields' =>  $filterNumberFields
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
            /*  The regular expression removes the character limits and what comes after the datatype.
                e.g: varchar(30) to varchar
                     decimal(13,4) to decimal
                     bigint unsigned to bigint
             */ 
            $type = preg_replace('/\(\s*\d+((\s*,\s*)\d+)?\s*\)|\s+[a-zA-Z]+/i', '', $column->Type);
            switch ($type) {
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
            /*  The regular expression removes the character limits and what comes after the datatype.
                e.g: varchar(30) to varchar
                     decimal(13,4) to decimal
                     bigint unsigned to bigint
             */ 
            $type = preg_replace('/\(\s*\d+((\s*,\s*)\d+)?\s*\)|\s+[a-zA-Z]+/i', '', $column->Type);
            switch ($type) {
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
}