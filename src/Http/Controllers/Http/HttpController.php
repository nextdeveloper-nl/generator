<?php

namespace NextDeveloper\Generator\Http\Controllers\Http;

use NextDeveloper\Commons\Http\Controllers\AbstractController;

class HttpController extends AbstractController
{
    public function index() {
        /*
         * FOR TESTING PURPOSES.
         * Will be converted to a request
         */
        $moduleName = 'Commons';
        $rootPath = '../NextDeveloper/' . $moduleName;

//        ModelService::generateFile($rootPath, 'NextDeveloper', $moduleName,'accounts');

        return $this->withCompleted();
    }
}
