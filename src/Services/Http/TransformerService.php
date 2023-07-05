<?php

namespace NextDeveloper\Generator\Services\Http;

use Illuminate\Support\Str;
use NextDeveloper\Generator\Exceptions\TemplateNotFoundException;
use NextDeveloper\Generator\Services\AbstractService;

class TransformerService extends AbstractService
{
    /**
     * @throws TemplateNotFoundException
     */
    public static function generate($namespace, $module, $model) {
        $columns = self::getColumns($model);

        $render = view('Generator::templates/http/transformer', [
            'namespace'          =>  $namespace,
            'module'             =>  $module,
            'model'              =>  ucfirst(Str::camel(Str::singular($model))),
            'columns'            =>  $columns,
            'returnData'         =>  self::buildData($columns, $model)
        ])->render();

        return $render;
    }

    public static function generateFile($rootPath, $namespace, $module, $model, $forceOverwrite) : bool{
        $content = self::generate($namespace, $module, $model);

        self::writeToFile($forceOverwrite, $rootPath . '/src/Http/Transformers/' . ucfirst(Str::camel(Str::singular($model))) . 'Transformer.php', $content);

        return true;
    }

    private static function buildData($columns, $model) {
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


}