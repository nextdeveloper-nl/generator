<?php

namespace NextDeveloper\Generator\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use NextDeveloper\Generator\Services\AbstractService;
use NextDeveloper\Generator\Common\AbstractController;
use NextDeveloper\Generator\Services\Database\TableService;
use NextDeveloper\Generator\Services\Events\EventsService;
use NextDeveloper\Generator\Services\Database\ModelService;
use NextDeveloper\Generator\Services\Http\ApiRoutesService;
use NextDeveloper\Generator\Services\Test\ModelTestService;
use NextDeveloper\Generator\Services\Database\FilterService;
use NextDeveloper\Generator\Services\Events\HandlersService;
use NextDeveloper\Generator\Services\Http\ControllerService;
use NextDeveloper\Generator\Services\Http\HttpConfigurationService;
use NextDeveloper\Generator\Services\Http\RequestService;
use NextDeveloper\Generator\Services\Http\TransformerService;
use NextDeveloper\Generator\Services\Services\ServiceService;
use NextDeveloper\Generator\Services\Database\ObserverService;
use NextDeveloper\Generator\Services\Structure\StructureService;

class AllController extends AbstractController
{
    public function index(Request $request) {

        $tarBackup = $request->query('tarBackup');

        $modules = config('generator.modules');

        $forceOverwrite = false;

        if($request->get('forceOverwrite') == 1) {
            $forceOverwrite = true;
        }

        foreach ($modules as $module) {
            dump($module);
            if(!$module['generate'])
                continue;

            $namespace = $module['namespace'];
            $moduleName = $module['name'];
            $rootPath = '../' . $namespace . '/' . $moduleName;

            AbstractService::backupModule($rootPath, $namespace, $moduleName, true);
            StructureService::generateStructure($rootPath);

            Log::info('[Generator] Generating composer file');
            StructureService::generateComposerFile($namespace, $moduleName, $rootPath, $forceOverwrite);
            Log::info('[Generator] Generating service provider');
            StructureService::generateServiceProviderFile($rootPath, $namespace, $moduleName, $forceOverwrite);
            Log::info('[Generator] Generating api routes file');
            StructureService::generateApiRoutesFile($rootPath, $namespace, $moduleName, $forceOverwrite);
            Log::info('[Generator] Generating configuration files');
            StructureService::generateConfigurationFiles($rootPath, $moduleName, $forceOverwrite);

            ControllerService::generateAbstractFile($rootPath, $namespace, $moduleName, $forceOverwrite);

            $modelsArray = [];

            if(Str::contains($module['tables'], '*')) {
                $modelsArray = TableService::getTables($module['tables']);
            } else {
                if($module['tables'] == '') {
                    $modelsArray = [];
                } else {
                    $modelsArray = explode(',', $request->query('models'));
                }
            }

            foreach ($modelsArray as $model) {
                $this->generateModels($rootPath, $namespace, $moduleName, $model, $forceOverwrite);
            }

            foreach ($modelsArray as $model) {
                $this->generateModelRelations($rootPath, $namespace, $moduleName, $model, $forceOverwrite);
            }

            if(Str::contains($module['views'], '*')) {
                $viewsArray = TableService::getViews($module['views']);
            } else {
                if($module['views'] == '') {
                    $viewsArray = [];
                } else {
                    $viewsArray = explode(',', $request->query('views'));
                }
            }

            foreach ($viewsArray as $model) {
                $this->generateViews($rootPath, $namespace, $moduleName, $model, $forceOverwrite);
            }
        }

        return $this->withCompleted();
    }

    private function generateModels($rootPath, $namespace, $moduleName, $model, $forceOverwrite) {
        StructureService::createEventFolderForModel($rootPath, $model, $moduleName);

        ServiceService::generateFile($rootPath, $namespace, $moduleName, $model, $forceOverwrite);

        // Abstract file should always be overwritten, so sending true directly!
        ServiceService::generateAbstractFile($rootPath, $namespace, $moduleName, $model, true);

        EventsService::generateFiles($rootPath, $namespace, $moduleName, $model, $forceOverwrite);
        HandlersService::generateFiles($rootPath, $namespace, $moduleName, $model, $forceOverwrite);

        ModelService::generateFile($rootPath, $namespace, $moduleName, $model, $forceOverwrite);
        ModelTestService::generateTraitFile($rootPath, $namespace, $moduleName, $model, $forceOverwrite);
        ModelTestService::generateFile($rootPath, $namespace, $moduleName, $model, $forceOverwrite);

        ObserverService::generateFile($rootPath, $namespace, $moduleName, $model, $forceOverwrite);
        FilterService::generateFile($rootPath, $namespace, $moduleName, $model, $forceOverwrite);

        RequestService::generateFile($rootPath, $namespace, $moduleName, $model, $forceOverwrite);

        ControllerService::generateFile($rootPath, $namespace, $moduleName, $model, $forceOverwrite);
        ApiRoutesService::appendToRoutes($rootPath, $namespace, $moduleName, $model, $forceOverwrite);

        //HttpConfigurationService::appendToModelBinding($rootPath, $namespace, $moduleName, $model, $forceOverwrite);
        //  Not writing over the file
        TransformerService::generateFile($rootPath, $namespace, $moduleName, $model, $forceOverwrite);
        TransformerService::generateAbstractFile($rootPath, $namespace, $moduleName, $model, $forceOverwrite);
    }

    private function generateModelRelations($rootPath, $namespace, $moduleName, $model, $forceOverwrite) {
        ModelService::generateOneToManyRelations($rootPath, $namespace, $moduleName, $model, $forceOverwrite);
    }

    private function generateViews($rootPath, $namespace, $moduleName, $model, $forceOverwrite) {
        ModelService::generateFile($rootPath, $namespace, $moduleName, $model, $forceOverwrite);
        //  We will remove this because view do not need to have observer
        ObserverService::generateFile($rootPath, $namespace, $moduleName, $model, $forceOverwrite);

        FilterService::generateFile($rootPath, $namespace, $moduleName, $model, $forceOverwrite);

        ControllerService::generateFile($rootPath, $namespace, $moduleName, $model, $forceOverwrite);
        ApiRoutesService::appendToRoutes($rootPath, $namespace, $moduleName, $model, $forceOverwrite);

        //HttpConfigurationService::appendToModelBinding($rootPath, $namespace, $moduleName, $model, $forceOverwrite);
        //  Not writing over the file
        TransformerService::generateFile($rootPath, $namespace, $moduleName, $model, $forceOverwrite);
        TransformerService::generateAbstractFile($rootPath, $namespace, $moduleName, $model, $forceOverwrite);
    }
}
