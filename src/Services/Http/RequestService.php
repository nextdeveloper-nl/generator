<?php

namespace NextDeveloper\Generator\Services\Http;

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
        $rules = self::generateRulesArray($columns, ($requestType == 'Update'));
        $tabAmount = 3;

        $render = view('Generator::templates/http/request', [
            'namespace'         =>  $namespace,
            'module'            =>  $module,
            'model'             =>  ucfirst(Str::camel(Str::singular($model))),
            'requestType'       =>  $requestType,
            'rules'             =>  self::objectArrayToString($rules, $tabAmount),
            'perPage'           =>  config('generator.pagination.perPage')
        ])->render();

        return $render;
    }

    public static function generateFile($rootPath, $namespace, $module, $model, $forceOverwrite) : bool{
        self::createModelFolderForRequest($rootPath, $model);
        $contentCreateRequest = self::generate($namespace, $module, $model, 'Create');
        $contentUpdateRequest = self::generate($namespace, $module, $model, 'Update');
    
        self::writeToFile($forceOverwrite, $rootPath . '/src/Http/Requests/'.ucfirst(Str::camel(Str::singular($model))).'/'. ucfirst(Str::camel(Str::singular($model))) . 'CreateRequest.php', $contentCreateRequest);
        self::writeToFile($forceOverwrite, $rootPath . '/src/Http/Requests/'.ucfirst(Str::camel(Str::singular($model))).'/'. ucfirst(Str::camel(Str::singular($model))) . 'UpdateRequest.php', $contentUpdateRequest);

        return true;
    }

    public static function generateRulesArray($columns, $isUpdate = false) {
        $rules = [];
        $discardedFields = ['created_at', 'deleted_at', 'updated_at', 'id', 'id_ref'];

        foreach ($columns as $column) {
            $columnType = self::cleanColumnType($column->Type); 
            $columnDefaultValue = $column->Default;
            $nullable = $column->Null === 'YES';

            if($isUpdate)
                $required = 'nullable|';
            else
                $required = $nullable ? 'nullable|' : 'required|';

            $fieldName = $column->Field;

            $type = $column->Type;

            if (!in_array($fieldName, $discardedFields) && stripos($fieldName, 'id_ref') === false){
                $rules[$fieldName] = '';
                if($columnDefaultValue == null){
                    $rules[$fieldName] = $required;
                }

                switch ($columnType) {
                    case 'boolean':
                    case 'tinyint':
                        $rules[$fieldName] .= 'boolean|';
                        break;
                    case 'decimal':
                    case 'float':
                    case 'double':
                    case 'real':
                        $rules[$fieldName] .= 'numeric|';
                        break;
                    case 'int':
                    case 'integer':
                    case 'bigint':
                    case 'mediumint':
                    case 'smallint':
                        $rules[$fieldName] .= 'integer|';
                        break;
                    case 'date':
                    case 'datetime':
                    case 'timestamp':
                    case 'immutable_date':
                    case 'immutable_datetime':
                        $rules[$fieldName] .= 'date|';
                        break;
                    case 'text':
                    case 'mediumtext':
                    case 'longtext':
                    case 'varchar':
                    case 'char':
                        $lengthRegex = '/\((?<max>\d+)\)/';
                        preg_match($lengthRegex, $type, $matches);

                        $rules[$fieldName] .= 'string|'; // Is this necessary when we have the max rule?
                        if (isset($matches['max'])) {
                            $rules[$fieldName] .= 'max:' . $matches['max'].'|';
                        }
                }

                if (Str::endsWith($rules[$fieldName], '|')) {
                    $rules[$fieldName] = substr($rules[$fieldName], 0, -1);
                }
            }
            
        }

        return $rules;
    }

    public static function createModelFolderForRequest($root, $model) {
        $folder = ucfirst(Str::camel(Str::singular($model)));
        self::createDirectory(base_path($root . '/src/Http/Requests/' . $folder));
    }
}