<?php

namespace NextDeveloper\Generator\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use NextDeveloper\Generator\Services\AbstractService;
use NextDeveloper\Generator\Common\AbstractController;
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

        $namespace = $request->query('namespace');
        $moduleName = $request->query('moduleName');
        $rootPath = '../' . $namespace . '/' . $moduleName;

        $force = $request->query('force');
        $forceOverwrite = false;

        if($force === 'true')
            $forceOverwrite = true;

        $currentDateTime = Carbon::now();
        $dateString = $currentDateTime->format('Y_m_d--H_i_s');

        AbstractService::backupModule($rootPath,$rootPath.'/backup'.'/'.$dateString,$moduleName);
    
        StructureService::generateStructure($rootPath);
        StructureService::generateComposerFile($namespace, $moduleName, $rootPath, $forceOverwrite);
        StructureService::generateServiceProviderFile($rootPath, $namespace, $moduleName, $forceOverwrite);
        StructureService::generateApiRoutesFile($rootPath, $namespace, $moduleName, $forceOverwrite);
        StructureService::generateConfigurationFiles($rootPath, $moduleName, $forceOverwrite);

        $modelsArray = explode(',', $request->query('models'));

        foreach ($modelsArray as $model) {
            StructureService::createEventFolderForModel($rootPath, $model);

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
            HttpConfigurationService::appendToModelBinding($rootPath, $namespace, $moduleName, $model, $forceOverwrite);

            TransformerService::generateFile($rootPath, $namespace, $moduleName, $model, $forceOverwrite);
        }

        foreach ($modelsArray as $model) {
            ModelService::generateOneToManyRelations($rootPath, $namespace, $moduleName, $model, $forceOverwrite);
        }

        return $this->withCompleted();
    }
}