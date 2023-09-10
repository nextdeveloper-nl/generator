<?php

namespace NextDeveloper\Generator\Services\Database;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use NextDeveloper\Generator\Services\AbstractService;
use NextDeveloper\Generator\Exceptions\TemplateNotFoundException;

class ModelService extends AbstractService
{

    /**
     * @throws TemplateNotFoundException
     */
    public static function generate($namespace, $module, $model)
    {
        $columns = self::getColumns($model);
        $casts = self::generateCastsArray($columns);
        $dates = self::generateDatesArray($columns);
        $fullTextFields = self::generateFullTextFieldsArray($model);
        $tabAmount = 2;

        $hasTimestamps = false;

        foreach ($columns as $column) {
            if ($column->Field == 'created_at') {
                $hasTimestamps = true;
                break;
            }
        }

        $modelWithoutModule = self::getModelName($model, $module);

        $render = view('Generator::templates/database/model', [
            'namespace' => $namespace,
            'module' => $module,
            'lcModule' => strtolower($module),
            'has_created' => self::hasColumn('created_at', $model),
            'has_updated' => self::hasColumn('updated_at', $model),
            'has_deleted' => self::hasColumn('deleted_at', $model),
            'model' => $modelWithoutModule,
            'table' => $model,
            'casts' => self::objectArrayToString($casts, $tabAmount),
            'dates' => self::arrayToString($dates),
            'fullTextFields' => self::arrayToString($fullTextFields),
            'perPage' => config('generator.pagination.perPage'),
            'hasTimestamps' => $hasTimestamps
        ])->render();

        return $render;
    }

    /**
     * @throws TemplateNotFoundException
     */
    public static function generateBelongsToContent($namespace, $module, $model)
    {
        $modelWithoutModule = self::getModelName($model, $module);

        $modules = config('generator.modules');

        $currentModule = '';

        foreach ($modules as $module) {
            if(Str::startsWith($model, $module['prefix'] . '_')) {
                $currentModule = $module;
                break;
            }
        }

        $modelWithoutModule = self::getModelName($model, $currentModule['name']);

        $render = view('Generator::templates/database/belongs_to_model', [
            'namespace' => $namespace,
            'module' => $module,
            'model' => $modelWithoutModule,
            'class' =>  '\\' . $currentModule['namespace'] . '\\' . $currentModule['name'] . '\\Database\\Models\\' . $modelWithoutModule,
        ])->render();

        return $render;
    }

    /**
     * @throws TemplateNotFoundException
     */
    public static function generateHasManyContent($namespace, $module, $model)
    {
        $columns = self::getColumns($model);

        $modules = config('generator.modules');

        $currentModule = '';

        foreach ($modules as $module) {
            if(Str::startsWith($model, $module['prefix'] . '_')) {
                $currentModule = $module;
                break;
            }
        }

        $modelWithoutModule = self::getModelName($model, $currentModule['name']);

        $render = view('Generator::templates/database/has_many_model', [
            'namespace' => $namespace,
            'module' => $module,
            'model' => $modelWithoutModule,
            'class' =>  '\\' . $currentModule['namespace'] . '\\' . $currentModule['name'] . '\\Database\\Models\\' . $modelWithoutModule,
        ])->render();

        return $render;
    }

    public static function generateFile($rootPath, $namespace, $module, $model, $forceOverwrite): bool
    {
        $content = self::generate($namespace, $module, $model);

        $modelWithoutModule = self::getModelName($model, $module);

        self::writeToFile($forceOverwrite, $rootPath . '/src/Database/Models/' . $modelWithoutModule . '.php', $content);

        return true;
    }

    public static function generateCastsArray($columns)
    {
        $casts = [];

        foreach ($columns as $column) {
            $columnType = self::cleanColumnType($column->Type);
            switch ($columnType) {
                case 'boolean':
                case 'tinyint':
                    $casts[$column->Field] = 'boolean';
                    break;
                case 'decimal':
                case 'float':
                case 'double':
                case 'real':
                    $casts[$column->Field] = 'double';
                    break;
                case 'int':
                case 'integer':
                case 'bigint':
                case 'mediumint':
                case 'smallint':
                    $casts[$column->Field] = 'integer';
                    break;
                case 'date':
                case 'datetime':
                case 'timestamp':
                case 'immutable_date':
                case 'immutable_datetime':
                    $casts[$column->Field] = 'datetime';
                    break;
                case 'text':
                case 'mediumtext':
                case 'longtext':
                case 'varchar':
                case 'char':
                    $casts[$column->Field] = 'string';
                    break;
            }

        }

        return $casts;
    }

    public static function generateDatesArray($columns)
    {
        $dates = [];

        foreach ($columns as $column) {
            $columnType = self::cleanColumnType($column->Type);
            switch ($columnType) {
                case 'date':
                case 'datetime':
                case 'timestamp':
                case 'immutable_date':
                case 'immutable_datetime':
                    $dates[] = $column->Field;
                    break;
            }
        }

        return $dates;
    }

    public static function generateFullTextFieldsArray($model)
    {
        $fullTextFields = [];

        $indexes = DB::select(DB::raw("SHOW INDEX FROM " . $model . " WHERE Index_type = 'FULLTEXT'"));
        foreach ($indexes as $index) {
            $fullTextFields[] = $index->Column_name;
        }
        return $fullTextFields;
    }

    public static function foreignKeys($table)
    {
        $query = "
            SELECT
                `COLUMN_NAME`,
                `REFERENCED_TABLE_NAME`,
                `REFERENCED_COLUMN_NAME`
            FROM
                `information_schema`.`KEY_COLUMN_USAGE`
            WHERE
                `TABLE_SCHEMA` = DATABASE() AND
                `TABLE_NAME` = ? AND
                `REFERENCED_TABLE_NAME` IS NOT NULL
        ";
        return DB::select($query, [$table]);
    }

    public static function generateOneToManyRelations($rootPath, $namespace, $module, $model, $forceOverwrite)
    {
        $foreignKeys = self::foreignKeys($model);

        $modelFile = self::getModelName($model, $module);

        $currentModelRootpath = $rootPath . '/src/Database/Models/' . $modelFile . '.php';

        $configModules = config('generator.modules');

        foreach ($foreignKeys as $foreignKey) {
            $classModule = '';
            foreach ($configModules as $configModule) {
                if (Str::startsWith($foreignKey->COLUMN_NAME, $configModule['prefix'] . '_')) {
                    $classModule = $configModule;
                    break;
                }
            }

            if(!$classModule) {
                Log::error('Found an ID field but cannot find which module is that. Which is: ' .
                    $foreignKey->REFERENCED_TABLE_NAME . '. In table: ' . $model . '. Dont forget to add module name' .
                    ' in front of the column.');

                throw new \Exception('Found an ID field but cannot find which module is that. Please look ' .
                    'at the logs.');
            }

            $foreignModel = self::getModelName($foreignKey->REFERENCED_TABLE_NAME, $classModule['name']);
            $foreignModelRootPath = $rootPath . '/../' . $classModule['name'] . '/src/Database/Models/' . $foreignModel . '.php';

            if(file_exists(base_path($currentModelRootpath))) {
                $currentModelContent = self::generateBelongsToContent($namespace, $module, $foreignKey->REFERENCED_TABLE_NAME);

                if (!self::isMethodExists($foreignModelRootPath, $currentModelContent)) {
                    self::appendToFile($currentModelRootpath, $currentModelContent, $forceOverwrite);
                }
            }

            if (file_exists(base_path($foreignModelRootPath))) {
                $foreignModelContent = self::generateHasManyContent($namespace, $module, $model);

                if (!self::isMethodExists($foreignModelRootPath, $foreignModelContent)) {
                    self::appendToFile($foreignModelRootPath, $foreignModelContent, $forceOverwrite);
                }
            }
        }
    }

    public static function getIdFields($namespace, $module, $model): array
    {
        $columns = self::getColumns($model);

        $idFields = [];

        foreach ($columns as $column) {
            $foreignModel = Str::remove('_id', $column->Field);

            if (Str::endsWith($column->Field, '_id')) {
                $configModules = config('generator.modules');

                $classModule = '';
                foreach ($configModules as $configModule) {
                    if (Str::startsWith($column->Field, $configModule['prefix'] . '_')) {
                        $classModule = $configModule['name'];
                        break;
                    }
                }

                $module = strtolower(Str::singular($module));
                $foreignModel = ucfirst(Str::camel(Str::singular($foreignModel)));
                $modelWithoutModule = self::getModelName($foreignModel, $classModule);

                $idFields[] = [
                    '\\' . $namespace . '\\' . $classModule . '\\Database\\Models\\' . $modelWithoutModule,
                    $column->Field,
                    Str::camel($column->Field)
                ];
            }
        }

        return $idFields;
    }

}