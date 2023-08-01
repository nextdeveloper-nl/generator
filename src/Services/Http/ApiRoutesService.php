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

        $controller = Str::camel($model);
        $controller = Str::ucfirst($controller);
        $controller = Str::singular($controller);

        if(Str::startsWith($model, $namespace)) {
            $model = substr($model, strlen($namespace) - 1);
        }

        if(Str::contains($model, '_')) {
            $model = str_replace('_', '-', $model);
        }

        $prefix = $model;
        $prefix = Str::lower($prefix);

        if(Str::startsWith($prefix, $module)) {
            $prefix = substr($prefix, strlen($module) + 1);
        }

        if(Str::startsWith($prefix, Str::lower(Str::singular($module)))) {
            $prefix = substr($prefix, strlen($module));
        }

        if(Str::startsWith($prefix, '-')) {
            $prefix = substr($prefix, 1);
        }

        $render = view('Generator::templates/http/apiroutesmodel', [
            'namespace'          =>  $namespace,
            'module'             =>  $module,
            'model'              =>  $model,
            'prefix'             => $prefix,
            'controller'        =>  $controller,
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