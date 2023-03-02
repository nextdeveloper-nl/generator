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

        $render = view('Generator::templates/http/apiroutescommon', [
            'namespace'          =>  $namespace,
            'module'             =>  $module,
            'model'              =>  ucfirst(Str::singular($model)),
            'columns'            =>  $columns
        ])->render();

        return $render;
    }

    public static function appendToRoutes($rootPath, $namespace, $module, $model) : bool{
        $content = self::generate($namespace, $module, $model);

        //self::writeToFile($rootPath . '/src/Http/Controllers/api.routes.php', $content);

        return true;
    }
}