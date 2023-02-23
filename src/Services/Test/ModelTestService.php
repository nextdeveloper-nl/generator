<?php

namespace NextDeveloper\Generator\Services\Test;

use Illuminate\Support\Str;
use NextDeveloper\Generator\Exceptions\TemplateNotFoundException;
use NextDeveloper\Generator\Services\AbstractService;

class ModelTestService extends AbstractService
{
    public static function generate($namespace, $module, $model) {
        $columns = self::getColumns($model);

        $render = view('Generator::tests/test', [
            'namespace'     =>  $namespace,
            'module'        =>  $module,
            'model'         =>  ucfirst(Str::singular($model)),
            'columns'       =>  $columns
        ])->render();

        return $render;
    }

    public static function generateFile($rootPath, $namespace, $module, $model) : bool{
        $content = self::generate($namespace, $module, $model);

        self::writeToFile('tests/Unit/GeneratedModel' . Str::ucfirst(Str::singular($model)) . 'Test.php', $content);

        return true;
    }

    public static function generateTrait($namespace, $module, $model) {
        $columns = self::getColumns($model);

        $render = view('Generator::tests/trait', [
            'namespace'     =>  $namespace,
            'module'        =>  $module,
            'model'         =>  ucfirst(Str::singular($model)),
            'columns'       =>  $columns
        ])->render();

        return $render;
    }

    public static function generateTraitFile($rootPath, $namespace, $module, $model) : bool{
        $content = self::generateTrait($namespace, $module, $model);

        self::writeToFile($rootPath . '/tests/Database/Models/' . ucfirst(Str::singular($model)) . 'TestTraits.php', $content);

        return true;
    }
}