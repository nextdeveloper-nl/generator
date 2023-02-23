<?php

namespace NextDeveloper\Generator\Services\Services;

use Illuminate\Support\Str;
use NextDeveloper\Generator\Exceptions\TemplateNotFoundException;
use NextDeveloper\Generator\Services\AbstractService;

class ServiceService extends AbstractService
{
    /**
     * @throws TemplateNotFoundException
     */
    public static function generate($namespace, $module, $model) {
        $columns = self::getColumns($model);

        $render = view('Generator::templates/services/service', [
            'namespace'     =>  $namespace,
            'module'        =>  $module,
            'model'         =>  ucfirst(Str::singular($model))
        ])->render();

        return $render;
    }

    public static function generateAbstract($namespace, $module, $model) {
        $columns = self::getColumns($model);

        $render = view('Generator::templates/services/abstract', [
            'namespace'     =>  $namespace,
            'module'        =>  $module,
            'model'         =>  ucfirst(Str::singular($model))
        ])->render();

        return $render;
    }

    public static function generateAbstractFile($rootPath, $namespace, $module, $model): bool {
        $content = self::generateAbstract($namespace, $module, $model);

        self::writeToFile($rootPath . '/src/Services/AbstractServices/Abstract' . ucfirst(Str::singular($model)) . 'Service.php', $content);

        return true;
    }

    public static function generateFile($rootPath, $namespace, $module, $model) : bool{
        $content = self::generate($namespace, $module, $model);

        self::writeToFile($rootPath . '/src/Services/' . ucfirst(Str::singular($model)) . 'Service.php', $content);

        return true;
    }
}