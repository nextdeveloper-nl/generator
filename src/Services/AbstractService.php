<?php

namespace NextDeveloper\Generator\Services;

use Illuminate\Support\Str;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class AbstractService
{
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

        if($forceOverwrite || self::checkForSameContent($file,$content)){ // If the content is the same, generate the file again
            file_put_contents(base_path($file), $content);
        }       
        
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
            //dd("different content",$file,$content,$existingContent);
            return false;
        }
    }

    public static function readFile($file) {
        return file_get_contents(base_path($file));
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

    public static function backupModule($sourcePath, $backupPath, $moduleName) {
        // Get an iterator for all the files in the source directory
        $iterator = new RecursiveDirectoryIterator(base_path($sourcePath), RecursiveDirectoryIterator::SKIP_DOTS);

        // Loop through each file and copy it to the backup directory
        foreach (new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator::SELF_FIRST) as $file) {
            // Skip the file if it's a directory or in the excluded folders
            if ($file->isDir() || preg_match('/(^|\/)(\.git|backup)\//', $file->getPathname()) || Str::contains($file->getPathname(), 'backup') || Str::contains($file->getPathname(), '.git')) {
                continue;
            }


            // Get the file contents and write them to the backup file
            $contents = File::get($file->getPathname());
            
            $relativePath = Str::after($file->getPathname(), $moduleName);
            $backupFilePath = $backupPath.$relativePath;
            $backupFilePath = base_path($backupFilePath);

            $backupDirectory = dirname($backupFilePath);

            if (!File::exists($backupDirectory)) {
                File::makeDirectory($backupDirectory, 0755, true, true);
            }

            File::put($backupFilePath, $contents);
        }
    }

}