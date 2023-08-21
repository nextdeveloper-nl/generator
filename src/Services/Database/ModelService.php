<?php

namespace NextDeveloper\Generator\Services\Database;

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

        $render = view('Generator::templates/database/model', [
            'namespace' => $namespace,
            'module' => $module,
            'lcModule' => strtolower($module),
            'has_created' => self::hasColumn('created_at', $model),
            'has_updated' => self::hasColumn('updated_at', $model),
            'has_deleted' => self::hasColumn('deleted_at', $model),
            'model' => ucfirst(Str::camel(Str::singular($model))),
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
        $columns = self::getColumns($model);

        $render = view('Generator::templates/database/belongs_to_model', [
            'namespace' => $namespace,
            'module' => $module,
            'model' => Str::camel(Str::singular($model)),
            'columns' => $columns
        ])->render();

        return $render;
    }

    /**
     * @throws TemplateNotFoundException
     */
    public static function generateHasManyContent($namespace, $module, $model)
    {
        $columns = self::getColumns($model);

        $render = view('Generator::templates/database/has_many_model', [
            'namespace' => $namespace,
            'module' => $module,
            'model' => Str::camel($model),
            'columns' => $columns
        ])->render();

        return $render;
    }

    public static function generateFile($rootPath, $namespace, $module, $model, $forceOverwrite): bool
    {
        $content = self::generate($namespace, $module, $model);

        self::writeToFile($forceOverwrite, $rootPath . '/src/Database/Models/' . ucfirst(Str::camel(Str::singular($model))) . '.php', $content);

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

        $currentModelRootpath = $rootPath . '/src/Database/Models/' . ucfirst(Str::camel(Str::singular($model))) . '.php';

        foreach ($foreignKeys as $foreignKey) {
            $foreignModelRootPath = $rootPath . '/src/Database/Models/' . ucfirst(Str::camel(Str::singular($foreignKey->REFERENCED_TABLE_NAME))) . '.php';

            if (file_exists(base_path($foreignModelRootPath))) {
                $currentModelContent = self::generateBelongsToContent($namespace, $module, $foreignKey->REFERENCED_TABLE_NAME);

                if (!self::isMethodExists($foreignModelRootPath, $currentModelContent)) {
                    self::appendToFile($currentModelRootpath, $currentModelContent, $forceOverwrite);
                }

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

                $idFields[] = [
                    '\\' . $namespace . '\\' . $classModule . '\\Database\\Models\\' . Str::singular(Str::ucfirst(Str::camel($foreignModel))),
                    $column->Field,
                    Str::camel($column->Field)
                ];
            }
        }

        return $idFields;
    }

}