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
    public static function generateObserver($namespace, $module, $model) {
        $columns = self::getColumns($model);

        $render = view('Generator::templates/database/observer', [
            'namespace'     =>  $namespace,
            'module'        =>  $module,
            'model'         =>  ucfirst(Str::singular($model))
        ])->render();

        return $render;
    }

    public static function generateFile($rootPath, $namespace, $module, $model) : bool{
        $content = self::generateObserver($namespace, $module, $model);

        self::writeToFile($rootPath . '/src/Database/Observers/' . ucfirst(Str::singular($model)) . 'Observer.php', $content);

        return true;
    }
}