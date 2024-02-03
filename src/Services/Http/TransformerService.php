<?php

namespace NextDeveloper\Generator\Services\Http;

use Illuminate\Support\Str;
use NextDeveloper\Generator\Exceptions\TemplateNotFoundException;
use NextDeveloper\Generator\Services\AbstractService;
use NextDeveloper\Generator\Services\Database\ModelService;

class TransformerService extends AbstractService
{
    public static function generateAbstract($namespace, $module, $model) {
        $columns = self::getColumns($model);

        $idFields = ModelService::getIdFields($namespace, $module, $model);

        $modelWithoutModule = self::getModelName($model, $module);

        $render = view('Generator::templates/http/abstracttransformer', [
            'namespace'          =>  $namespace,
            'module'             =>  $module,
            'model'              =>  $modelWithoutModule,
            'columns'            =>  $columns,
            'returnData'         =>  self::buildData($columns, $model),
            'idFields'           =>  $idFields
        ])->render();

        return $render;
    }

    public static function generateAbstractFile($rootPath, $namespace, $module, $model, $forceOverwrite) : bool{
        $content = self::generateAbstract($namespace, $module, $model);

        $fileAndfolder = self::getModelName($model, $module);

        self::writeToFile($forceOverwrite, $rootPath . '/src/Http/Transformers/AbstractTransformers/Abstract' . $fileAndfolder . 'Transformer.php', $content);

        return true;
    }

    /**
     * @throws TemplateNotFoundException
     */
    public static function generate($namespace, $module, $model) {
        $columns = self::getColumns($model);

        $idFields = ModelService::getIdFields($namespace, $module, $model);

        $modelWithoutModule = self::getModelName($model, $module);

        $render = view('Generator::templates/http/transformer', [
            'namespace'          =>  $namespace,
            'module'             =>  $module,
            'model'              =>  $modelWithoutModule,
            'columns'            =>  $columns,
            'returnData'         =>  self::buildData($columns, $model),
            'idFields'           =>  $idFields
        ])->render();

        return $render;
    }

    public static function generateFile($rootPath, $namespace, $module, $model, $forceOverwrite) : bool{
        $content = self::generate($namespace, $module, $model);

        $fileAndfolder = self::getModelName($model, $module);

        $file = $rootPath . '/src/Http/Transformers/' . $fileAndfolder . 'Transformer.php';

        if(!file_exists(base_path($file)) || $forceOverwrite) {
            self::writeToFile($forceOverwrite, $file, $content);
        }

        return true;
    }

    private static function buildDataPostgresql($columns, $model) {
        $data = [];

        if(self::isColumnExists('uuid', $columns)) {
            $data[] =   [
                'field'     =>  'id',
                'return'    =>  'uuid'
            ];
        } else {
            $data[] =   [
                'field'     =>  'id',
                'return'    =>  'id'
            ];
        }

        foreach ($columns as $column) {
            if($column->column_name == 'id' || $column->column_name == 'uuid')
                continue;

            switch ($column->data_type) {
                case 'tinyint':
                    $data[] =   [
                        'field'     =>  $column->column_name,
                        'return'    =>  $column->column_name . ' == 1 ? true : false'
                    ];
                    break;
                case 'timestamp':
                    $data[] =   [
                        'field'     =>  $column->column_name,
                        'return'    =>  $column->column_name . ' ? $model->' . $column->column_name . '->toIso8601String() : null'
                    ];
                    break;
                default:
                    $data[] =   [
                        'field'     =>  $column->column_name,
                        'return'    =>  $column->column_name
                    ];
                    break;
            }
        }

        return $data;
    }

    private static function buildDataMySQL($columns, $model) {
        $data = [];

        if(self::isColumnExists('uuid', $columns)) {
            $data[] =   [
                'field'     =>  'id',
                'return'    =>  'uuid'
            ];
        } else {
            $data[] =   [
                'field'     =>  'id',
                'return'    =>  'id'
            ];
        }

        foreach ($columns as $column) {
            if($column->Field == 'id' || $column->Field == 'uuid')
                continue;

            switch ($column->Type) {
                case 'tinyint':
                    $data[] =   [
                        'field'     =>  $column->Field,
                        'return'    =>  $column->Field . ' == 1 ? true : false'
                    ];
                    break;
                case 'timestamp':
                    $data[] =   [
                        'field'     =>  $column->Field,
                        'return'    =>  $column->Field . ' ? $model->' . $column->Field . '->toIso8601String() : null'
                    ];
                    break;
                default:
                    $data[] =   [
                        'field'     =>  $column->Field,
                        'return'    =>  $column->Field
                    ];
                    break;
            }
        }

        return $data;
    }

    private static function buildData($columns, $model) {
        if(config('database.default') == 'mysql') {
            return self::buildDataMySQL($columns, $model);
        } else {
            return self::buildDataPostgresql($columns, $model);
        }
    }
}
