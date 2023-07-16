<?php

namespace NextDeveloper\Generator\Services\Http;

use Illuminate\Support\Str;
use NextDeveloper\Generator\Exceptions\TemplateNotFoundException;
use NextDeveloper\Generator\Services\AbstractService;

class ApiRoutesService extends AbstractService
{
    /**
     * @throws TemplateNotFoundException
     */
    public static function generate($namespace, $module, $model) {
        $columns = self::getColumns($model);

        if(Str::startsWith($model, $namespace)) {
            $model = substr($model, strlen($namespace) - 1);
        }

        if(Str::contains($model, '_')) {
            $model = str_replace('_', '-', $model);
        }

        $render = view('Generator::templates/http/apiroutesmodel', [
            'namespace'          =>  $namespace,
            'module'             =>  $module,
            'model'              =>  $model,
            'columns'            =>  $columns
        ])->render();

        return $render;
    }

    public static function appendToRoutes($rootPath, $namespace, $module, $model, $forceOverwrite) : bool{
        $content = self::generate($namespace, $module, $model);
        $rootPath .= '/src/Http/api.routes.php';

        return self::appendToFile($rootPath, $content, $forceOverwrite);
    }
}