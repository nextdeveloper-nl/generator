<?php

namespace NextDeveloper\Generator\Http\Controllers\Structure;

use NextDeveloper\Generator\Common\AbstractController;
use NextDeveloper\Generator\Services\Structure\StructureService;

class StructureController extends AbstractController
{
    public function index() {
        /*
         * FOR TESTING PURPOSES.
         * Will be converted to a request
         */
        $moduleName = 'Commons';
        $rootPath = '../NextDeveloper/' . $moduleName;

        $result = StructureService::generateStructure($moduleName, $rootPath);

        if($result) {
            return $this->withCompleted();
        }
    }
}