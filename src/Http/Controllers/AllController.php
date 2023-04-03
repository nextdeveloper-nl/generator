<?php

namespace NextDeveloper\Generator\Http\Controllers;

use NextDeveloper\Generator\Common\AbstractController;
use NextDeveloper\Generator\Services\Database\FilterService;
use NextDeveloper\Generator\Services\Database\ModelService;
use NextDeveloper\Generator\Services\Database\ObserverService;
use NextDeveloper\Generator\Services\Events\EventsService;
use NextDeveloper\Generator\Services\Events\HandlersService;
use NextDeveloper\Generator\Services\Http\ApiRoutesService;
use NextDeveloper\Generator\Services\Http\ControllerService;
use NextDeveloper\Generator\Services\Http\HttpConfigurationService;
use NextDeveloper\Generator\Services\Http\RequestService;
use NextDeveloper\Generator\Services\Http\TransformerService;
use NextDeveloper\Generator\Services\Services\ServiceService;
use NextDeveloper\Generator\Services\Structure\StructureService;
use NextDeveloper\Generator\Services\Test\ModelTestService;
use Psy\Util\Str;

class AllController extends AbstractController
{
    public function index() {
        /*
         * FOR TESTING PURPOSES.
         * Will be converted to a request
         */
        $namespace = 'NextDeveloper';
        $moduleName = 'Commons';
        $rootPath = '../' . $namespace . '/' . $moduleName;
        $model = 'countries';

        //dd(AccountService::getAll());

        StructureService::generateStructure($rootPath);
        StructureService::generateComposerFile($namespace, $moduleName, $rootPath);
        StructureService::generateServiceProviderFile($rootPath, $namespace, $moduleName);
        StructureService::generateApiRoutesFile($rootPath, $namespace, $moduleName);
        StructureService::generateConfigurationFiles($rootPath, $moduleName);
        StructureService::createEventFolderForModel($rootPath, $model);

        ServiceService::generateFile($rootPath, $namespace, $moduleName, $model);
        ServiceService::generateAbstractFile($rootPath, $namespace, $moduleName, $model);

        EventsService::generateFiles($rootPath, $namespace, $moduleName, $model);
        HandlersService::generateFiles($rootPath, $namespace, $moduleName, $model);

        ModelService::generateFile($rootPath, $namespace, $moduleName, $model);
        ModelTestService::generateTraitFile($rootPath, $namespace, $moduleName, $model);
        ModelTestService::generateFile($rootPath, $namespace, $moduleName, $model);
        
        ObserverService::generateFile($rootPath, $namespace, $moduleName, $model);
        FilterService::generateFile($rootPath, $namespace, $moduleName, $model);

        RequestService::generateFile($rootPath, $namespace, $moduleName, $model);

        ServiceService::generateFile($rootPath, $namespace, $moduleName, $model);
        ServiceService::generateAbstractFile($rootPath, $namespace, $moduleName, $model);

        ControllerService::generateFile($rootPath, $namespace, $moduleName, $model);
        ApiRoutesService::appendToRoutes($rootPath, $namespace, $moduleName, $model);
        HttpConfigurationService::appendToModelBinding($rootPath, $namespace, $moduleName, $model);

        TransformerService::generateFile($rootPath, $namespace, $moduleName, $model);

        return $this->withCompleted();
    }
}