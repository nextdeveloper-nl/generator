<?php

namespace NextDeveloper\Generator\Services\Http;

use Illuminate\Support\Str;
use NextDeveloper\Generator\Exceptions\TemplateNotFoundException;
use NextDeveloper\Generator\Services\AbstractService;

class ControllerService extends AbstractService
{
    /**
     * @throws TemplateNotFoundException
     */
    public static function generate($namespace, $module, $model) {
        $columns = self::getColumns($model);

        $render = view('Generator::templates/http/controller', [
            'namespace'          =>  $namespace,
            'module'             =>  $module,
            'model'              =>  ucfirst(Str::camel(Str::singular($model))),
            'columns'            =>  $columns
        ])->render();

        return $render;
    }

    public static function generateFile($rootPath, $namespace, $module, $model, $forceOverwrite) : bool{
        self::createModelFolderForController($rootPath, ucfirst(Str::camel(Str::singular($model))));
        $content = self::generate($namespace, $module, $model);

        self::writeToFile($forceOverwrite, $rootPath . '/src/Http/Controllers/' . ucfirst(Str::camel(Str::singular($model))) . '/' . ucfirst(Str::camel(Str::singular($model))) . 'Controller.php', $content);

        return true;
    }

    public static function createModelFolderForController($root, $model) {
        $folder = ucfirst(Str::camel(Str::singular($model)));
        self::createDirectory(base_path($root . '/src/Http/Controllers/' . $folder));
    }
}