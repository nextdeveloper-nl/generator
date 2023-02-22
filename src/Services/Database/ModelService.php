<?php

namespace NextDeveloper\Generator\Services\Database;

use Illuminate\Support\Str;
use NextDeveloper\Generator\Exceptions\TemplateNotFoundException;
use NextDeveloper\Generator\Services\AbstractService;

class ModelService extends AbstractService
{
    /**
     * @throws TemplateNotFoundException
     */
    public static function generateModel($namespace, $module, $model) {
        $columns = self::getColumns($model);

        $render = view('Generator::templates/database/model', [
            'namespace'     =>  $namespace,
            'module'        =>  $module,
            'model'         =>  ucfirst(Str::singular($model)),
            'perPage'       =>  config('generator.pagination.perPage')
        ])->render();

        return $render;
    }

    public static function generateFile($rootPath, $namespace, $module, $model) : bool{
        $content = self::generateModel($namespace, $module, $model);

        self::writeToFile($rootPath . '/src/Database/Models/' . ucfirst($model) . '.php', $content);

        return true;
    }
}