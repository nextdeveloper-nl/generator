<?php

namespace NextDeveloper\Generator\Services\Events;

use Illuminate\Support\Str;
use NextDeveloper\Generator\Exceptions\TemplateNotFoundException;
use NextDeveloper\Generator\Services\AbstractService;
use NextDeveloper\Generator\Services\Structure\StructureService;

class EventsService extends AbstractService
{
    /**
     * @throws TemplateNotFoundException
     */
    public static function generate($namespace, $module, $model, $event) {
        $columns = self::getColumns($model);

        $render = view('Generator::templates/events/event', [
            'namespace'     =>  $namespace,
            'module'        =>  $module,
            'model'         =>  ucfirst(Str::camel(Str::singular($model))),
            'event'         =>  $event
        ])->render();

        return $render;
    }

    public static function generateFiles($rootPath, $namespace, $module, $model, $forceOverwrite) : bool{
        $events = config('generator.action-events.events');

        $modelName = Str::camel($model);
        $modelName = Str::ucfirst($modelName);
        $modelName = Str::singular($modelName);

        foreach ($events as $event) {
            $event = $modelName . '' . Str::ucfirst($event) . 'Event';
            $content = self::generate($namespace, $module, $model, $event);

            StructureService::createEventFolderForModel($rootPath, $model);

            $file = $rootPath . '/src/Events/' . $modelName . '/' . $event . '.php';
            if(!file_exists(base_path($file)) || $forceOverwrite) {
                self::writeToFile($forceOverwrite, $file, $content);
            }
        }

        return true;
    }
}