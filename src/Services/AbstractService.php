<?php

namespace NextDeveloper\Generator\Services;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AbstractService
{
    public static function getColumns($model) {
        return DB::select( DB::raw("SHOW COLUMNS FROM " . $model));
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

    public static function writeToFile($file, $content, $fileType = 'php') {
        switch ($fileType) {
            case 'php':
                $content = '<?php' . PHP_EOL . PHP_EOL . $content;
                break;
            case 'json':
                break;
        }
        $content = htmlspecialchars_decode($content);

        file_put_contents(base_path($file), $content);
    }
}