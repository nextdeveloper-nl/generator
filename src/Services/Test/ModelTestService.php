<?php

namespace NextDeveloper\Generator\Services\Test;

use Illuminate\Support\Str;
use NextDeveloper\Generator\Services\AbstractService;
use NextDeveloper\Generator\Services\Database\FilterService;

class ModelTestService extends AbstractService
{
    public static function generate($namespace, $module, $model) {
        $columns = self::getColumns($model);

        $render = view('Generator::tests/test', [
            'namespace'     =>  $namespace,
            'module'        =>  $module,
            'model'         =>  ucfirst(Str::camel(Str::singular($model))),
            'columns'       =>  $columns
        ])->render();

        return $render;
    }

    public static function generateFile($rootPath, $namespace, $module, $model, $forceOverwrite) : bool{
        $content = self::generate($namespace, $module, $model);

        self::writeToFile($forceOverwrite, 'tests/Unit/GeneratedModel' . Str::ucfirst(Str::camel(Str::singular($model))) . 'Test.php', $content);

        return true;
    }

    public static function generateTrait($namespace, $module, $model) {
        $columns = self::getColumns($model);

        $render = view('Generator::tests/trait', [
            'namespace'     =>  $namespace,
            'module'        =>  $module,
            'model'         =>  ucfirst(Str::camel(Str::singular($model))),
            'columns'       =>  $columns,
            'filterTextFields'   =>  FilterService::generateFilterTextFields($columns),
            'filterNumberFields' =>  FilterService::generateFilterNumberFields($columns),
            'filterDateFields'   =>  FilterService::generateFilterDateFields($columns),
            'events'        =>  config('generator.action-events.events'),
            'handlers'      =>  config('generator.action-events.handlers')
        ])->render();

        return $render;
    }

    public static function generateTraitFile($rootPath, $namespace, $module, $model, $forceOverwrite) : bool{
        $content = self::generateTrait($namespace, $module, $model);

        self::writeToFile($forceOverwrite, $rootPath . '/tests/Database/Models/' . ucfirst(Str::camel(Str::singular($model))) . 'TestTraits.php', $content);

        return true;
    }
}