<?php

namespace NextDeveloper\Generator\Services\Database;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use NextDeveloper\Generator\Services\AbstractService;
use NextDeveloper\Generator\Exceptions\TemplateNotFoundException;

class RequestService extends AbstractService
{
    
    /**
     * @throws TemplateNotFoundException
     */
    public static function generate($namespace, $module, $model, $requestType) {
        $columns = self::getColumns($model);
        $rules = self::generateRulesArray($columns);
        $tabAmount = 3;

        $render = view('Generator::templates/http/request', [
            'namespace'         =>  $namespace,
            'module'            =>  $module,
            'model'             =>  ucfirst(Str::singular($model)),
            'requestType'       =>  $requestType,
            'rules'             =>  self::objectArrayToString($rules, $tabAmount),
            'perPage'           =>  config('generator.pagination.perPage')
        ])->render();

        return $render;
    }

    public static function generateFile($rootPath, $namespace, $module, $model) : bool{
        self::createModelFolderForRequest($rootPath, $model);
        $contentCreateRequest = self::generate($namespace, $module, $model, 'Create');
        $contentUpdateRequest = self::generate($namespace, $module, $model, 'Update');
    
        self::writeToFile($rootPath . '/src/Http/Requests/'.ucfirst(Str::singular($model)).'/'. ucfirst(Str::singular($model)) . 'CreateRequest.php', $contentCreateRequest);
        self::writeToFile($rootPath . '/src/Http/Requests/'.ucfirst(Str::singular($model)).'/'. ucfirst(Str::singular($model)) . 'UpdateRequest.php', $contentUpdateRequest);

        return true;
    }

    public static function generateRulesArray($columns) {
        $rules = [];

        foreach ($columns as $column) {
            $columnType = self::cleanColumnType($column->Type); 
            $type = $column->Type;
            $fieldName = $column->Field;
            $nullable = $column->Null === 'YES';
            $required = $nullable ? 'nullable' : 'required';

            $rules[$fieldName] = $required;

            switch ($columnType) {
                case 'boolean':
                case 'tinyint':
                    $rules[$fieldName] .= '|boolean';
                    break;
                case 'decimal':
                case 'float':
                case 'double':
                case 'real':
                    $rules[$fieldName] .= '|numeric';
                    break;
                case 'int':
                case 'integer':
                case 'bigint':
                case 'mediumint':
                case 'smallint':
                    $rules[$fieldName] .= '|integer';
                    break;
                case 'date':
                case 'datetime':
                case 'timestamp':
                case 'immutable_date':
                case 'immutable_datetime':
                    $rules[$fieldName] .= '|date';
                    break;
                case 'text':
                case 'mediumtext':
                case 'longtext':
                case 'varchar':
                case 'char':
                    $lengthRegex = '/\((?<max>\d+)\)/';
                    preg_match($lengthRegex, $type, $matches);

                    $rules[$fieldName] .= '|string'; // Is this necessary when we have the max rule?
                    $rules[$fieldName] .= '|max:' . $matches['max'];
            }

            
            
        }

        return $rules;
    }

    public static function createModelFolderForRequest($root, $model) {
        $folder = ucfirst(Str::singular($model));
        self::createDirectory(base_path($root . '/src/Http/Requests/' . $folder));
    }

    private static function createDirectory($directory) : ?bool {
        try {
            mkdir($directory, 0777, true);
        } catch (\ErrorException $exception) {
            //  We are not throwing exception here because the user may forget
            //  to add a new directory while generating it and may need to
            //  regenerate again.

            //  @TODO: Maybe later we can create a warning.
            if($exception->getMessage() == 'mkdir(): File exists')
                return false;
        }

        return true;
    }
}