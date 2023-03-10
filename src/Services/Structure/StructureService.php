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

    public static function generateComposerFile($namespace, $module, $root) {
        $content = self::generateComposer($namespace, $module);

        self::writeToFile($root . '/composer.json', $content, 'json');

        return true;
    }

    public static function generateServiceProvider($namespace, $module) {
        $render = view('Generator::templates/serviceprovider', [
            'namespace'     =>  $namespace,
            'module'        =>  $module
        ])->render();

        return $render;
    }

    public static function generateServiceProviderFile($rootPath, $namespace, $module): bool {
        $content = self::generateServiceProvider($namespace, $module);

        self::writeToFile($rootPath . '/src/' . ucfirst($module) . 'ServiceProvider.php', $content);

        return true;
    }

    public static function generateAbstractServiceProvider($namespace, $module) {
        $render = view('Generator::templates/abstractprovider', [
            'namespace'     =>  $namespace,
            'module'        =>  $module
        ])->render();

        return $render;
    }

    public static function generateAbstractServiceProviderFile($rootPath, $namespace, $module): bool {
        $content = self::generateAbstractServiceProvider($namespace, $module);

        self::writeToFile($rootPath . '/src/AbstractServiceProvider.php', $content);

        return true;
    }

    public static function generateApiRoutes() {
        $render = view('Generator::templates/http/apiroutes', [
        ])->render();

        return $render;
    }

    public static function generateApiRoutesFile($rootPath): bool {
        $content = self::generateApiRoutes();

        self::writeToFile($rootPath . '/src/Http/api.routes.php', $content);

        return true;
    }

    public static function generateConfig() {
        $render = view('Generator::templates/configs/config', [
        ])->render();

        return $render;
    }

    public static function generateModelBindingConfig() {
        $render = view('Generator::templates/configs/config', [
        ])->render();

        return $render;
    }

    public static function generateRelationConfig() {
        $render = view('Generator::templates/configs/config', [
        ])->render();

        return $render;
    }

    public static function generateConfigurationFiles($rootPath, $module): bool {
        self::writeToFile($rootPath . '/config/' . strtolower($module) . '.php', self::generateConfig());
        self::writeToFile($rootPath . '/config/model-binding.php', self::generateModelBindingConfig());
        self::writeToFile($rootPath . '/config/relation.php', self::generateRelationConfig());

        return false;
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

    private static function createDirectory($directory) : ?bool {
        try {
            mkdir($directory, 0777, true);
        } catch (\ErrorException $exception) {
            //  We are not throwing exception here because the user may forget
            //  to add a new directory while generating it and may need to
            //  regenerate again.

            //  @TODO: Maybe later we can create a warning.
            if($exception->getMessage() == 'mkdir(): File exists')
                return false;
        }

        return true;
    }
}