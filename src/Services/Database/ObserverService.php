<?php

namespace NextDeveloper\Generator\Services\Database;

use Illuminate\Support\Str;
use NextDeveloper\Generator\Exceptions\TemplateNotFoundException;
use NextDeveloper\Generator\Services\AbstractService;

class ObserverService extends AbstractService
{
    /**
     * @throws TemplateNotFoundException
     */
    public static function generate($namespace, $module, $model) {
        $columns = self::getColumns($model);

        $modelWithoutModule = self::getModelName($model, $module);

        $render = view('Generator::templates/database/observer', [
            'namespace'     =>  $namespace,
            'module'        =>  $module,
            'model'         =>  $modelWithoutModule
        ])->render();

        return $render;
    }

    public static function generateFile($rootPath, $namespace, $module, $model, $forceOverwrite) : bool{
        $content = self::generate($namespace, $module, $model);

        $modelWithoutModule = self::getModelName($model, $module);

        $file = $rootPath . '/src/Database/Observers/' . $modelWithoutModule . 'Observer.php';
        if(!file_exists(base_path($file)) || $forceOverwrite) {
            self::writeToFile($forceOverwrite, $file, $content);
        }

        return true;
    }
}