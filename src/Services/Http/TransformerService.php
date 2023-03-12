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
            'model'              =>  ucfirst(Str::singular($model)),
            'columns'            =>  $columns,
            'returnData'         =>  self::buildData($columns, $model)
        ])->render();

        return $render;
    }

    public static function generateFile($rootPath, $namespace, $module, $model) : bool{
        $content = self::generate($namespace, $module, $model);

        self::writeToFile($rootPath . '/src/Http/Transformers/' . ucfirst(Str::singular($model)) . 'Transformer.php', $content);

        return true;
    }

    private static function buildData($columns, $model) {
        $data = [];

        if(self::isColumnExists('id_ref', $columns)) {
            $data[] =   [
                'field'     =>  'id',
                'return'    =>  'id_ref'
            ];
        } else {
            $data[] =   [
                'field'     =>  'id',
                'return'    =>  'id'
            ];
        }

        foreach ($columns as $column) {
            if($column->Field == 'id' || $column->Field == 'id_ref')
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