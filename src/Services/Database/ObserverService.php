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

        $render = view('Generator::templates/database/observer', [
            'namespace'     =>  $namespace,
            'module'        =>  $module,
            'model'         =>  ucfirst(Str::camel(Str::singular($model)))
        ])->render();

        return $render;
    }

    public static function generateFile($rootPath, $namespace, $module, $model, $forceOverwrite) : bool{
        $content = self::generate($namespace, $module, $model);

        self::writeToFile($forceOverwrite, $rootPath . '/src/Database/Observers/' . ucfirst(Str::camel(Str::singular($model))) . 'Observer.php', $content);

        return true;
    }
}