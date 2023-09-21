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
    public static function generate($namespace, $module, $model, $event)
    {
        $columns = self::getColumns($model);

        $modelWithoutModule = self::getModelName($model, $module);
        $eventWithoutModule = Str::remove(Str::ucfirst(strtolower(Str::singular($module))), $event);

        $render = view('Generator::templates/events/event', [
            'namespace' => $namespace,
            'module' => $module,
            'model' => $modelWithoutModule,
            'event' => $eventWithoutModule
        ])->render();

        return $render;
    }

    public static function generateFiles($rootPath, $namespace, $module, $model, $forceOverwrite): bool
    {
        $events = config('generator.action-events.events');

        $modelName = Str::camel($model);
        $modelName = Str::ucfirst($modelName);

        foreach ($events as $event) {
            $event = $modelName . '' . Str::ucfirst($event) . 'Event';
            $content = self::generate($namespace, $module, $model, $event);

            StructureService::createEventFolderForModel($rootPath, $model, $module);

            $modelWithoutModule = self::getModelName($model, $module);

            $singularModule = Str::singular($module);

            if (ctype_upper($module)) {
                //  If all letters are upper case, this means that this is short version of something.
                //  That is why we dont make it singular.
                $singularModule = $module;
            }

            $eventWithoutModule = Str::remove(Str::ucfirst(strtolower($singularModule)), $event);

            $file = $rootPath . '/src/Events/' . $modelWithoutModule . '/' . $eventWithoutModule . '.php';
            if (!file_exists(base_path($file)) || $forceOverwrite) {
                self::writeToFile($forceOverwrite, $file, $content);
            }
        }

        return true;
    }
}