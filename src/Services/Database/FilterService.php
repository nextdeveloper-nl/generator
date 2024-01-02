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
        $modelWithoutModule = self::getModelName($model, $module);

        $columns = self::getColumns($model);
        $filterTextFields = self::generateFilterTextFields($columns);
        $filterNumberFields = self::generateFilterNumberFields($columns);
        $filterBooleanFields = self::generateFilterBooleanFields($columns);
        $filterDateFields = self::generateFilterDateFields($columns);
        $idRefFields = ModelService::getIdFields($namespace, $module, $model);

        $render = view('Generator::templates/database/filter', [
            'namespace'           =>  $namespace,
            'module'              =>  $module,
            'model'               =>  $modelWithoutModule,
            'columns'             =>  $columns,
            'filterTextFields'    =>  $filterTextFields,
            'filterNumberFields'  =>  $filterNumberFields,
            'filterBooleanFields' =>  $filterBooleanFields,
            'filterDateFields'    =>  $filterDateFields,
            'idRefFields'         =>  $idRefFields
        ])->render();

        return $render;
    }

    public static function generateFile($rootPath, $namespace, $module, $model, $forceOverwrite) : bool{
        $content = self::generate($namespace, $module, $model);

        $modelWithoutModule = self::getModelName($model, $module);

        self::writeToFile($forceOverwrite, $rootPath . '/src/Database/Filters/' . $modelWithoutModule . 'QueryFilter.php', $content);

        return true;
    }

    public static function isIdField($fieldName) {
        $idFields = ['id', 'uuid'];
        return (in_array($fieldName, $idFields) || substr($fieldName, -3) === '_id');
    }

    public static function generateFilterTextFields($columns) {
        $filterTextFields = [];

        foreach ($columns as $column) {
            $columnType = self::cleanColumnType($column->Type);
            $columnField = $column->Field;

            if(!self::isIdField($columnField)){
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
            
        }
        
        return $filterTextFields;
    }

    public static function generateFilterNumberFields($columns) {
        $filterNumberFields = [];

        foreach ($columns as $column) {
            $columnType = self::cleanColumnType($column->Type);
            $columnField = $column->Field;
            
            if(!self::isIdField($columnField)){
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
                    case 'tinyint':
                        if(!self::isBooleanField($column->Field)){
                            $filterNumberFields[] = $column->Field;
                            break;
                        }
                } 
            }
                   
        }
        return $filterNumberFields;
    }

    public static function generateFilterBooleanFields($columns) {
        $filterBooleanFields = [];

        foreach ($columns as $column) {
            $columnType = self::cleanColumnType($column->Type);
            $columnField = $column->Field;

            if(!self::isIdField($columnField)){
                switch ($columnType) {
                    case 'tinyint':
                    case 'bool':
                    case 'boolean':
                        if(self::isBooleanField($column->Field)){
                            $filterBooleanFields[] = $column->Field;
                            break;
                        }
                } 
            }
                   
        }
        return $filterBooleanFields;
    }

    public static function generateFilterDateFields($columns) {
        $filterDateFields = [];

        foreach ($columns as $column) {
            $columnType = self::cleanColumnType($column->Type);
            $columnField = $column->Field;

            if(!self::isIdField($columnField)){
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
        }
        return $filterDateFields;
    }

    public static function generateIdRefFields($columns) {
        $idRefFields = [];
        $disgardedFields = ['id', 'uuid'];

        foreach ($columns as $column) {
            $columnField = $column->Field;
            
            if(!in_array($columnField, $disgardedFields) && self::isIdField($columnField)){
                $idRefFields[] = $column->Field;             
            }     
        }

        return $idRefFields;
    }
}