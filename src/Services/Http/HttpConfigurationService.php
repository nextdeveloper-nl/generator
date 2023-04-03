<?php

namespace NextDeveloper\Generator\Services\Http;

use Illuminate\Support\Str;
use NextDeveloper\Generator\Exceptions\TemplateNotFoundException;
use NextDeveloper\Generator\Services\AbstractService;

class HttpConfigurationService extends AbstractService
{
    /**
     * @throws TemplateNotFoundException
     */
    public static function generate($namespace, $module, $model) {
        $columns = self::getColumns($model);

        $render = view('Generator::templates/configs/modelbindingcontent', [
            'namespace'          =>  $namespace,
            'module'             =>  $module,
            'model'              =>  ucfirst(Str::camel(Str::singular($model))),
            'columns'            =>  $columns
        ])->render();

        return $render;
    }

    public static function appendToModelBinding($rootPath, $namespace, $module, $model, $forceOverwrite) : bool{
        $content = self::generate($namespace, $module, $model);
        $rootPath .= '/config/model-binding.php';

        return self::appendToFile($rootPath, $content, $forceOverwrite);
    }
}