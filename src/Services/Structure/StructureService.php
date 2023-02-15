<?php

namespace NextDeveloper\Generator\Services\Structure;

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
    public static function generateStructure($moduleName, $root): bool {
        $structure = config('generator.structure');

        $paths = self::generateDirectories($structure, '');

        foreach ($paths as $path) {
            try {
                mkdir(base_path($root . $path), 0777, true);
            } catch (\ErrorException $exception) {
                //  We are not throwing exception here because the user may forget
                //  to add a new directory while generating it and may need to
                //  regenerate again.

                //  @TODO: Maybe later we can create a warning.
                if($exception->getMessage() == 'mkdir(): File exists')
                    continue;
            }
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