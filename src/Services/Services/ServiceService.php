<?php

namespace NextDeveloper\Generator\Services\Services;

use Illuminate\Support\Str;
use NextDeveloper\Generator\Exceptions\TemplateNotFoundException;
use NextDeveloper\Generator\Services\AbstractService;
use NextDeveloper\Generator\Services\Database\ModelService;

class ServiceService extends AbstractService
{
    /**
     * @throws TemplateNotFoundException
     */
    public static function generate($namespace, $module, $model) {
        $columns = self::getColumns($model);

        $modelWithoutModule = self::getModelName($model, $module);

        $render = view('Generator::templates/services/service', [
            'namespace'     =>  $namespace,
            'module'        =>  $module,
            'model'         =>  $modelWithoutModule
        ])->render();

        return $render;
    }

    public static function generateAbstract($namespace, $module, $model) {
        $columns = self::getColumns($model);

        $events = config('generator.action-events.events');

        $hasUserId = false;
        $hasAccountId = false;

        $idFields = ModelService::getIdFields($namespace, $module, $model);

        $modelWithoutModule = self::getModelName($model, $module);

        $render = view('Generator::templates/services/abstract', [
            'namespace'     =>  $namespace,
            'module'        =>  $module,
            'createData'         =>  self::buildData($columns, $model),
            'idFields'      =>  $idFields,
            'model'      =>  $modelWithoutModule
        ])->render();

        return $render;
    }

    public static function generateAbstractFile($rootPath, $namespace, $module, $model, $forceOverwrite): bool {
        $content = self::generateAbstract($namespace, $module, $model);

        $modelWithoutModule = self::getModelName($model, $module);

        $file = $rootPath . '/src/Services/AbstractServices/Abstract' . $modelWithoutModule . 'Service.php';

        if(!file_exists(base_path($file))){
            self::writeToFile($forceOverwrite, $file, $content);
        }

        return true;
    }

    public static function generateFile($rootPath, $namespace, $module, $model, $forceOverwrite) : bool{
        $content = self::generate($namespace, $module, $model);

        $modelWithoutModule = self::getModelName($model, $module);

        $file = $rootPath . '/src/Services/' . $modelWithoutModule . 'Service.php';
        if(!file_exists(base_path($file)) || $forceOverwrite) {
            self::writeToFile($forceOverwrite, $file, $content);
        }

        return true;
    }

    private static function buildData($columns, $model) {
        $data = [];

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
                        'return'    =>  "\$data['" . $column->Field . "']"
                    ];
                    break;
            }
        }

        return $data;
    }
}