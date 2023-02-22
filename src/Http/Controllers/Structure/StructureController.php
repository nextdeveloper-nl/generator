<?php

namespace NextDeveloper\Generator\Http\Controllers\Structure;

use NextDeveloper\Generator\Common\AbstractController;
use NextDeveloper\Generator\Services\Services\ServiceService;
use NextDeveloper\Generator\Services\Structure\StructureService;

class StructureController extends AbstractController
{
    public function index() {
        /*
         * FOR TESTING PURPOSES.
         * Will be converted to a request
         */
        $namespace = 'NextDeveloper';
        $moduleName = 'Commons';
        $rootPath = '../' . $namespace . '/' . $moduleName;
        $model = 'accounts';


        StructureService::generateStructure('NextDeveloper', $moduleName, $rootPath);
        StructureService::generateComposerFile('NextDeveloper', $moduleName, $rootPath);
        StructureService::generateServiceProviderFile($rootPath, $namespace, $moduleName, $model);

        return $this->withCompleted();
    }
}