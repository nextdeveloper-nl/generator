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

    public static function generateFilterTextFieldsMySQL($columns) {
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

    public static function generateFilterTextFieldsPostgresql($columns) {
        $filterTextFields = [];

        foreach ($columns as $column) {
            $columnType = self::cleanColumnType($column->data_type);
            $columnField = $column->data_type;

            if(!self::isIdField($columnField)){
                switch ($columnType) {
                    case 'text':
                    case 'mediumtext':
                    case 'longtext':
                    case 'varchar':
                    case 'char':
                        $filterTextFields[] = $column->column_name;
                        break;
                }
            }

        }

        return $filterTextFields;
    }

    public static function generateFilterTextFields($columns) {
        if(config('database.default') == 'mysql') {
            return self::generateFilterTextFieldsMySQL($columns);
        } else {
            return self::generateFilterTextFieldsPostgresql($columns);
        }
    }

    public static function generateFilterNumberFieldsMySQL($columns) {
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

    public static function generateFilterNumberFieldsPostgresql($columns) {
        $filterNumberFields = [];

        foreach ($columns as $column) {
            $columnType = self::cleanColumnType($column->data_type);
            $columnField = $column->column_name;

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
                        $filterNumberFields[] = $column->column_name;
                        break;
                    case 'tinyint':
                        if(!self::isBooleanField($column->column_name)){
                            $filterNumberFields[] = $column->column_name;
                            break;
                        }
                }
            }

        }
        return $filterNumberFields;
    }

    public static function generateFilterNumberFields($columns) {
        if(config('database.default') == 'mysql') {
            return self::generateFilterNumberFieldsMySQL($columns);
        } else {
            return self::generateFilterNumberFieldsPostgresql($columns);
        }
    }

    public static function generateFilterBooleanFieldsPostgresql($columns) {
        $filterBooleanFields = [];

        foreach ($columns as $column) {
            $columnType = self::cleanColumnType($column->data_type);
            $columnField = $column->column_name;

            if(!self::isIdField($columnField)){
                switch ($columnType) {
                    case 'tinyint':
                    case 'bool':
                    case 'boolean':
                        if(self::isBooleanField($column->column_name)){
                            $filterBooleanFields[] = $column->column_name;
                            break;
                        }
                }
            }

        }
        return $filterBooleanFields;
    }

    public static function generateFilterBooleanFields($columns) {
        if(config('database.default') == 'mysql') {
            return self::generateFilterBooleanFieldsMySQL($columns);
        } else {
            return self::generateFilterBooleanFieldsPostgresql($columns);
        }
    }

    public static function generateFilterBooleanFieldsMySQL($columns) {
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

    public static function generateFilterDateFieldsMySQL($columns)
    {
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

    public static function generateFilterDateFieldsPostgresql($columns)
    {
        $filterDateFields = [];

        foreach ($columns as $column) {
            $columnType = self::cleanColumnType($column->data_type);
            $columnField = $column->column_name;

            if(!self::isIdField($columnField)){
                switch ($columnType) {
                    case 'date':
                    case 'datetime':
                    case 'timestamp':
                    case 'immutable_date':
                    case 'immutable_datetime':
                    case 'timestamp with time zone':
                        $filterDateFields[] = $column->column_name;
                        break;
                }
            }
        }
        return $filterDateFields;
    }

    public static function generateFilterDateFields($columns) {
        if(config('database.default') == 'mysql') {
            return self::generateFilterDateFieldsMySQL($columns);
        } else {
            return self::generateFilterDateFieldsPostgresql($columns);
        }
    }

    public static function generateIdRefFieldsMySQL($columns) {
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

    public static function generateIdRefFieldsPostgresql($columns) {
        $idRefFields = [];
        $disgardedFields = ['id', 'uuid'];

        foreach ($columns as $column) {
            $columnField = $column->column_name;

            if(!in_array($columnField, $disgardedFields) && self::isIdField($columnField)){
                $idRefFields[] = $column->column_name;
            }
        }

        return $idRefFields;
    }

    public static function generateIdRefFields($columns) {
        if(config('database.default') == 'mysql') {
            return self::generateIdRefFieldsMySQL($columns);
        } else {
            return self::generateIdRefFieldsPostgresql($columns);
        }
    }
}
