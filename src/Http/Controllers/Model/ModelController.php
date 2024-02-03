<?php

namespace NextDeveloper\Generator\Http\Controllers\Model;

use NextDeveloper\Commons\Http\Controllers\AbstractController;
use NextDeveloper\Generator\Services\Database\FilterService;
use NextDeveloper\Generator\Services\Database\ModelService;
use NextDeveloper\Generator\Services\Database\ObserverService;

class ModelController extends AbstractController
{
    public function index() {
        /*
         * FOR TESTING PURPOSES.
         * Will be converted to a request
         */
        $moduleName = 'Commons';
        $rootPath = '../NextDeveloper/' . $moduleName;

        ModelService::generateFile($rootPath, 'NextDeveloper', $moduleName,'accounts');
        ObserverService::generateFile($rootPath, 'NextDeveloper', $moduleName,'accounts');
        FilterService::generateFile($rootPath, 'NextDeveloper', $moduleName,'accounts');

        return $this->withCompleted();
    }
}
