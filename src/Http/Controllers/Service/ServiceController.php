<?php

namespace NextDeveloper\Generator\Http\Controllers\Service;

use NextDeveloper\Commons\Http\Controllers\AbstractController;
use NextDeveloper\Generator\Services\Services\ServiceService;

class ServiceController extends AbstractController
{
    public function index() {
        /*
         * FOR TESTING PURPOSES.
         * Will be converted to a request
         */
        $moduleName = 'Commons';
        $rootPath = '../NextDeveloper/' . $moduleName;

        ServiceService::generateFile($rootPath, 'NextDeveloper', $moduleName,'accounts');
        ServiceService::generateAbstractFile($rootPath, 'NextDeveloper', $moduleName,'accounts');

        return $this->withCompleted();
    }
}
