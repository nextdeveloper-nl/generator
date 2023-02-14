<?php

namespace NextDeveloper\Generator\Services\Models;

use NextDeveloper\Generator\Services\AbstractService;

class GeneratorService extends AbstractService
{
    public static function generateModel($module, $model) {



        $render = view('templates.model')->render();

        return $render;
    }
}