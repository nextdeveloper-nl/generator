<?php

namespace NextDeveloper\Generator\Services;

use Carbon\Carbon;
use Illuminate\Support\Str;
use PHP_CodeSniffer\Exceptions\DeepExitException;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class AbstractService
{
    public static function getModelName($model, $module) {
        $singularModule = Str::singular($module);

        if(ctype_upper($module)) {
            //  If all letters are upper case, this means that this is short version of something.
            //  That is why we dont make it singular.
            $singularModule = $module;
        }

        $singularModule = Str::ucfirst(strtolower($singularModule));
        $modelWithoutModule = ucfirst(Str::camel(Str::singular($model)));
        $modelWithoutModule = Str::remove($singularModule, $modelWithoutModule);
        $modelWithoutModule = Str::plural($modelWithoutModule);


        return $modelWithoutModule;
    }

    public static function getColumns($model) {
        return DB::select( DB::raw("SHOW COLUMNS FROM " . $model));
    }

    public static function hasColumn($column, $model) {
        $columns = self::getColumns($model);

        foreach ($columns as $col) {
            if($col->Field == $column)
                return true;
        }

        return false;
    }

    public static function getMaxKeyLength($array){
        $maxKeyLength = 0;

        foreach ($array as $key => $value) {
            $maxKeyLength = max($maxKeyLength, strlen($key));
        }

        return $maxKeyLength;
    }

    public static function cleanColumnType($columnType){
        /*  The regular expression removes the character limits and what comes after the datatype.
            e.g: varchar(30) to varchar
                decimal(13,4) to decimal
                bigint unsigned to bigint
        */ 
        $type = preg_replace('/\(\s*\d+((\s*,\s*)\d+)?\s*\)|\s+[a-zA-Z]+/i', '', $columnType);
        return $type;
    }

    public static function isColumnExists($column, $columns) : bool {
        foreach ($columns as $col) {
            if($col->Field == $column)
                return true;
        }

        return false;
    }

    public static function isBooleanField($field) : bool {
        /* This function checks whether  the field is a boolean field or not
           e.g: is_active will return true since the first three charachters are 'is_'*/
        return substr($field, 0, 3) === 'is_';
    }

    public static function objectArrayToString(?array $array, $tabAmount): string {
        if (!$array) {
            return '';
        }
    
        $result = '';
        $isFirstElement = true;
        $maxKeyLength = self::getMaxKeyLength($array);

        $tabs = Str::repeat("\t", $tabAmount);

        foreach ($array as $key => $value) {
            $key_padding = str_repeat(' ',  $maxKeyLength - strlen($key));
            if($isFirstElement == true){
                $result .= sprintf("'%s'%s => '%s',\n", $key, $key_padding, $value);
            }
            else{
                $result .= sprintf($tabs."'%s'%s => '%s',\n", $key, $key_padding, $value);
            }   
            $isFirstElement = false;  
        }

        return rtrim($result, "\n");
    }

    public static function arrayToString(?array $array): string{
        if (!$array) {
            return '';
        }
        
        $result = "";
        $isFirstElement = true;

        foreach ($array as $value) {
            if($isFirstElement == true){
                $result .= sprintf("'%s',\n", $value);  
            }
 
            else{
                $result .= sprintf("%s'%s',\n", "\t\t", $value);
            }    
            $isFirstElement = false; 

        }

        return rtrim($result,"\n");
    }

    public static function writeToFile($forceOverwrite, $file, $content, $fileType = 'php') {
        switch ($fileType) {
            case 'php':
                $content = '<?php' . PHP_EOL . PHP_EOL . $content;
                break;
            case 'json':
                break;
        }

        $content = htmlspecialchars_decode($content);

        if(!$forceOverwrite){
            $content = self::appendExistingContentAfterWarningMessage($file,$content);
        }

        $fileContent = self::replaceUsedLibraries($file, $content);

        file_put_contents(base_path($file), $fileContent);
    }

    public static function appendExistingContentAfterWarningMessage($file, $content) {
        if (file_exists(base_path($file))) {
            $existingContent = file_get_contents(base_path($file));
        } 
        else { // If the file does not exist, regenerate the whole file
            return $content;
        }

        $existingContent = htmlspecialchars_decode($existingContent);

        $warningString = "// EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE";
        $pos = strpos($existingContent, $warningString);

        // Get the portion of the file contents that comes after the warning string
        if ($pos !== false) {
            $afterWarningString = substr($existingContent, $pos + strlen($warningString));
            $afterWarningString = self::removeLastBracket($afterWarningString);

            $afterWarningString = $warningString . '\n\n' . $afterWarningString;

            $content = str_replace($warningString, $afterWarningString, $content);

            return $content;

        } else { // If the warning string is not found, regenerate the whole file
            return $content;
        }
    }

    public static function removeLastBracket($content) {
        $lines = explode(PHP_EOL, $content);

        for($i = count($lines) -1; $i >= 0; $i--) {
            if($lines[$i] == '});' || $lines[$i] == '}') {
                unset($lines[$i]);
                break;
            }
        }

        $content = implode(PHP_EOL, $lines);

        return $content;
    }

    public static function getContentAfterWarningSign($content){
        $warningString = "// EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE";
        $pos = strpos($content, $warningString);

        $pos = strlen($warningString) + $pos;

        $content = substr($content, $pos, -1);

        return $content;
    }

    public static function checkForSameContent($file, $content) {
        $existingContent = file_get_contents(base_path($file));
        $existingContent = htmlspecialchars_decode($existingContent);

        /* There is a special case for api.routes.php and model-binding.php
           There is a problem because we are creating these files initially and then checking for them with the updated version
        */
        if ($existingContent === $content || $file == '../NextDeveloper/Dummy/src/Http/api.routes.php' || $file == '../NextDeveloper/Dummy/config/model-binding.php') {
            return true;
        } else{
            dd("different content",$file,$content,$existingContent);
            return false;
        }
    }

    public static function readFile($file) {
        if(file_exists(base_path($file)))
            return file_get_contents(base_path($file));

        return '';
    }

    /**
     * Used to create directory which is not stated in structure array
     *
     * @param $directory
     * @return bool|null
     */
    public static function createDirectory($directory) : ?bool {
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

    public static function backupModule($sourcePath, $namespace, $moduleName, $tarBackup = true) {
        $appPath = base_path();
        $timestamp = Carbon::now()->format('YmdHis');
        $backupTo = $appPath . '/../' . $namespace . '/' . $moduleName . '.' . $timestamp . '.tar.gz';

        $command = 'cp ';

        if($tarBackup) {
            $command = 'tar -czf ' . $backupTo . ' ' . $appPath . '/' . $sourcePath;
        }
        $appPath = base_path();
        $timestamp = Carbon::now()->format('YmdHis');
        $backupTo = $appPath . '/../' . $namespace . '/' . $moduleName . '.' . $timestamp . '.tar.gz';
        $command = 'tar -czf ' . $backupTo . ' ' . $appPath . '/' . $sourcePath;

        $output = shell_exec($command);

        return true;
    }

    /**
     * Reads the used libraries in the file and moves them into the new content file
     *
     * @param $file
     * @param $content
     * @return string
     */
    public static function replaceUsedLibraries($file, $content) : string {
        $fileLibraries = self::findUsedLibraries(self::readFile($file));
        $contentLibraries = self::findUsedLibraries($content);

        $lines = explode(PHP_EOL, $content);

        $linesBeforeUse = [];
        $linesAfterUse = [];

        $isHit = false;

        foreach ($lines as $line) {
            $tmpLine = str_replace('\t', '', $line);
            $tmpLine = trim($tmpLine);

            if(Str::startsWith($tmpLine, 'use') && Str::contains($tmpLine, '\\')) {
                $isHit = true;
                continue;
            }

            if(!$isHit) $linesBeforeUse[] = $line;
            if($isHit) $linesAfterUse[] = $line;
        }

        $libraries = [];

        foreach ($fileLibraries as $fileLibrary) {
            if(!in_array(trim($fileLibrary), $libraries)) $libraries[] = $fileLibrary;
        }

        foreach ($contentLibraries as $contentLibrary) {
            if(!in_array(trim($contentLibrary), $libraries)) $libraries[] = $contentLibrary;
        }

        $fileContents = array_merge(
            $linesBeforeUse,
            $libraries,
            $linesAfterUse
        );


        $fileContents = implode(PHP_EOL, $fileContents);

        return $fileContents;
    }

    public static function findUsedLibraries($contents) : array {
        $lines = explode(PHP_EOL, $contents);

        $libraries = [];

        foreach ($lines as $line) {
            $line = str_replace('\t', '', $line);
            $line = trim($line);
            if(Str::startsWith($line, 'use') && Str::contains($line, '\\' )) {
                $libraries[] = $line;
            }
        }

        return $libraries;
    }

    /**
     * Returns true if the related method exists
     *
     * @param $file
     * @param $method
     * @return bool
     */
    public static function isMethodExists($file, $method) :bool {
        $contents = self::readFile($file);

        $methodLines = explode(PHP_EOL, $method);

        $methodName = '';

        foreach ($methodLines as $line) {
            if(Str::contains($line, 'function')) {
                $line = str_replace('public', '', $line);
                $line = str_replace('private', '', $line);
                $line = str_replace('static', '', $line);
                $line = str_replace('function', '', $line);

                $methodName = trim($line);
            }
        }

        $lines = explode(PHP_EOL, $contents);

        foreach ($lines as $line) {
            if(Str::contains($line, 'function')) {
                if(Str::contains($line, $method)) {
                    return true;
                }
            }
        }

        return false;
    }

    public static function appendToFile($rootPath, $content, $fileType = 'php') : bool{
        $fileContent = self::readFile($rootPath);

        if(!Str::contains($fileContent, $content)) {
            $fileContent = str_replace('// EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE', $content, $fileContent);
        }

        file_put_contents(base_path($rootPath), $fileContent);

        return true;
    }
}