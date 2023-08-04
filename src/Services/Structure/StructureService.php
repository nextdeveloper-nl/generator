<?php

namespace NextDeveloper\Generator\Services\Structure;

use Illuminate\Support\Str;
use NextDeveloper\Generator\Services\AbstractService;

class StructureService extends AbstractService
{
    /**
     * This function will create the structure as well as the required configuration files.
     *
     * @param $moduleName
     * @throws \Exception
     * @return void
     */
    public static function generateStructure($root): bool {
        $structure = config('generator.structure');

        $paths = self::generateDirectories($structure, '');

        foreach ($paths as $path) {
            self::createDirectory(base_path($root . $path));
        }

        return true;
    }

    public static function createEventFolderForModel($root, $model) {
        $folder = Str::camel($model);
        $folder = Str::ucfirst($folder);
        $folder = Str::singular($folder);

        self::createDirectory(base_path($root . '/src/Events/' . $folder));
        self::createDirectory(base_path($root . '/src/EventHandlers/' . $folder));
    }

    public static function generateComposer($namespace, $module) {
        $render = view('Generator::templates/composer', [
            'namespace'     =>  $namespace,
            'module'        =>  $module
        ])->render();

        return $render;
    }

    public static function generateComposerFile($namespace, $module, $root, $forceOverwrite) {
        $content = self::generateComposer($namespace, $module);

        self::writeToFile($forceOverwrite ,$root . '/composer.json', $content, 'json');

        return true;
    }

    public static function generateServiceProvider($namespace, $module) {
        $render = view('Generator::templates/serviceprovider', [
            'namespace'     =>  $namespace,
            'module'        =>  $module
        ])->render();

        return $render;
    }

    public static function generateServiceProviderFile($rootPath, $namespace, $module, $forceOverwrite): bool {
        $content = self::generateServiceProvider($namespace, $module);

        $file = $rootPath . '/src/' . ucfirst($module) . 'ServiceProvider.php';

        if(!file_exists(base_path($file))) {
            self::writeToFile($forceOverwrite, $file, $content);
        }

        return true;
    }

    public static function generateApiRoutes($moduleName) {
        $render = view('Generator::templates/http/apiroutescommon', [
            'module'    =>  $moduleName
        ])->render();

        return $render;
    }

    public static function generateApiRoutesFile($rootPath, $namespace, $moduleName, $forceOverwrite): bool {
        $content = self::generateApiRoutes($moduleName);

        self::writeToFile($forceOverwrite, $rootPath . '/src/Http/api.routes.php', $content);

        return true;
    }

    public static function generateConfig() {
        $render = view('Generator::templates/configs/config', [
        ])->render();

        return $render;
    }

    public static function generateModelBindingConfig() {
        $render = view('Generator::templates/configs/modelbinding', [
        ])->render();

        return $render;
    }

    public static function generateRelationConfig() {
        $render = view('Generator::templates/configs/config', [
        ])->render();

        return $render;
    }

    public static function generateConfigurationFiles($rootPath, $module, $forceOverwrite): bool {
        if(!file_exists(base_path($rootPath) . '/config/' . strtolower($module) . '.php')) {
            self::writeToFile($forceOverwrite, $rootPath . '/config/' . strtolower($module) . '.php', self::generateConfig());
        }

        if(!file_exists(base_path($rootPath) . '/config/model-binding.php')) {
            self::writeToFile($forceOverwrite, $rootPath . '/config/model-binding.php', self::generateModelBindingConfig());
        }

        if(!file_exists(base_path($rootPath) . '/config/relation.php')) {
            self::writeToFile($forceOverwrite, $rootPath . '/config/relation.php', self::generateRelationConfig());
        }

        return true;
    }

    private static function generateDirectories($directory, $currentPath, $paths = []) {
        foreach ($directory as $parent => $subDirectory) {
            if(is_array($subDirectory)) {
                $tempPath = $currentPath . '/' . $parent;
                $paths = array_merge($paths, self::generateDirectories($subDirectory, $tempPath));
            } else {
                $paths[] = $currentPath . '/' . $subDirectory;
            }
        }

        return $paths;
    }
}