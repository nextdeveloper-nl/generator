<?php

namespace NextDeveloper\Generator\Http\Controllers\Test;

use NextDeveloper\Commons\Http\Controllers\AbstractController;
use NextDeveloper\Generator\Services\Test\ModelTestService;

class TestController extends AbstractController
{
    public function index() {
        /*
         * FOR TESTING PURPOSES.
         * Will be converted to a request
         */
        $moduleName = 'Commons';
        $rootPath = '../NextDeveloper/' . $moduleName;

        $result = ModelTestService::generateFile($rootPath, 'NextDeveloper', $moduleName,'accounts');

        if($result) {
            return $this->withCompleted();
        }
    }
}
