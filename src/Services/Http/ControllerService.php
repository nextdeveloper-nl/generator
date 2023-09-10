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

        $modelWithoutModule = self::getModelName($model, $module);

        $render = view('Generator::templates/http/controller', [
            'namespace'          =>  $namespace,
            'module'             =>  $module,
            'model'              =>  $modelWithoutModule,
            'columns'            =>  $columns
        ])->render();

        return $render;
    }

    public static function generateFile($rootPath, $namespace, $module, $model, $forceOverwrite) : bool{
        self::createModelFolderForController($rootPath, ucfirst(Str::camel(Str::singular($model))), $module);
        $content = self::generate($namespace, $module, $model);

        $modelWithoutModule = self::getModelName($model, $module);

        $file = $rootPath . '/src/Http/Controllers/' . $modelWithoutModule . '/' . $modelWithoutModule . 'Controller.php';
        if(!file_exists(base_path($file)) || $forceOverwrite){
            self::writeToFile($forceOverwrite, $file, $content);
        }

        return true;
    }

    public static function createModelFolderForController($root, $model, $module) {
        $modelWithoutModule = self::getModelName($model, $module);

        self::createDirectory(base_path($root . '/src/Http/Controllers/' . $modelWithoutModule));
    }

    public static function generateAbstract($namespace, $module) {
        $render = view('Generator::templates/http/abstractcontroller', [
            'namespace'          =>  $namespace,
            'module'             =>  $module
        ])->render();

        return $render;
    }

    public static function generateAbstractFile($rootPath, $namespace, $module, $forceOverwrite) : bool{
        $content = self::generateAbstract($namespace, $module);

        $file = $rootPath . '/src/Http/Controllers/AbstractController.php';
        self::writeToFile($forceOverwrite, $file, $content);

        return true;
    }
}