<?php

namespace NextDeveloper\Generator\Services\Events;

use Illuminate\Support\Str;
use NextDeveloper\Generator\Exceptions\TemplateNotFoundException;
use NextDeveloper\Generator\Services\AbstractService;
use NextDeveloper\Generator\Services\Structure\StructureService;

class HandlersService extends AbstractService
{
    /**
     * @throws TemplateNotFoundException
     */
    public static function generate($namespace, $module, $model, $handler) {
        $render = view('Generator::templates/events/handlers', [
            'namespace'     =>  $namespace,
            'module'        =>  $module,
            'model'         =>  ucfirst(Str::camel(Str::singular($model))),
            'handler'       =>  $handler
        ])->render();

        return $render;
    }

    public static function generateFiles($rootPath, $namespace, $module, $model, $forceOverwrite) : bool{
        $handlers = config('generator.action-events.handlers');

        $modelName = Str::camel($model);
        $modelName = Str::ucfirst($modelName);
        $modelName = Str::singular($modelName);

        foreach ($handlers as $handler) {
            $handler = $modelName . '' . Str::ucfirst($handler) . 'Event';
            $content = self::generate($namespace, $module, $model, $handler);

            StructureService::createEventFolderForModel($rootPath, $model);

            $file = $rootPath . '/src/EventHandlers/' . $modelName . '/' . $handler . '.php';
            if(!file_exists(base_path($file)) || $forceOverwrite) {
                self::writeToFile($forceOverwrite, $file, $content);
            }
        }

        return true;
    }
}