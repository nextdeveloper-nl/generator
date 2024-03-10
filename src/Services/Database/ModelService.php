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
        $fillable = self::generateFillable($columns);
        $documentation = self::generateIdeDocumentation($columns);
        $tabAmount = 2;

        $hasTimestamps = false;

        foreach ($columns as $column) {
            if(config('database.default') == 'mysql') {
                if ($column->Field == 'updated_at') {
                    $hasTimestamps = true;
                    break;
                }
            } else if(config('database.default') == 'pgsql') {
                if ($column->column_name == 'updated_at') {
                    $hasTimestamps = true;
                    break;
                }
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
            'hasTimestamps' => $hasTimestamps,
            'fillable' => $fillable,
            'documentation' => $documentation
        ])->render();

        return $render;
    }

    public static function generateIdeDocumentation($columns) {
        $documentation = [];

        foreach ($columns as $column) {
            if(config('database.default') == 'mysql') {
                $columnType = self::cleanColumnType($column->Type);
                $columnType = self::changeColumnTypeToPhpObject($columnType);
                $documentation[] = '@property ' . $columnType . ' $' . $column->Field;
            } else if(config('database.default') == 'pgsql') {
                $columnType = self::cleanColumnType($column->data_type);
                $columnType = self::changeColumnTypeToPhpObject($columnType);
                $documentation[] = '@property ' . $columnType . ' $' . $column->column_name;
            }
        }

        return $documentation;
    }

    public static function changeColumnTypeToPhpObject($columnType) {
        switch ($columnType) {
            case 'text':
            case 'char':
            case 'mediumtext':
            case 'longtext':
            case 'varchar':
                return 'string';
            case 'integer':
            case 'bigint':
            case 'mediumint':
            case 'smallint':
            case 'float':
                return 'integer';
            case 'tinyint':
            case 'boolean':
                return 'boolean';
            case 'ARRAY':
                return 'array';
            case 'timestamp':
            case 'datetime':
            case 'immutable_date':
            case 'immutable_datetime':
            case 'date':
            case 'timestamp with time zone':
                return '\Carbon\Carbon';
            case 'uuid':
                return 'string';
        }
    }

    public static function generateFillable($columns) {
        $fillable = [];

        foreach ($columns as $column) {
            if(config('database.default') == 'mysql') {
                if ($column->Field != 'id' && $column->Field != 'uuid' && $column->Field != 'created_at' && $column->Field != 'updated_at' && $column->Field != 'deleted_at') {
                    $fillable[] = $column->Field;
                }
            } else if(config('database.default') == 'pgsql') {
                if ($column->column_name != 'id' && $column->column_name != 'uuid' && $column->column_name != 'created_at' && $column->column_name != 'updated_at' && $column->column_name != 'deleted_at') {
                    $fillable[] = $column->column_name;
                }
            }
        }

        return $fillable;
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

    public static function generateCastsArrayMySQL($columns)
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

    public static function generateCastsArrayPostgreSQL($columns)
    {
        $casts = [];

        foreach ($columns as $column) {
            $columnType = self::cleanColumnType($column->data_type);
            switch ($columnType) {
                case 'boolean':
                case 'tinyint':
                    $casts[$column->column_name] = 'boolean';
                    break;
                case 'decimal':
                case 'float':
                case 'double':
                case 'real':
                    $casts[$column->column_name] = 'double';
                    break;
                case 'int':
                case 'integer':
                case 'bigint':
                case 'mediumint':
                case 'smallint':
                    $casts[$column->column_name] = 'integer';
                    break;
                case 'date':
                case 'datetime':
                case 'timestamp':
                case 'timestamp with time zone':
                case 'immutable_date':
                case 'immutable_datetime':
                    $casts[$column->column_name] = 'datetime';
                    break;
                case 'text':
                case 'mediumtext':
                case 'longtext':
                case 'varchar':
                case 'char':
                    $casts[$column->column_name] = 'string';
                    break;
                case 'json':
                    $casts[$column->column_name] = 'array';
                    break;
                case 'ARRAY':
                    switch ($column->udt_name) {
                        case '_integer':
                            $casts[$column->column_name] = '\Tpetry\PostgresqlEnhanced\Eloquent\Casts\IntegerArrayCast::class';
                            break;
                        case '_int2':
                        case '_int4':
                        case '_int8':
                            $casts[$column->column_name] = 'array:integer';
                            break;
                        case '_float4':
                        case '_float8':
                            $casts[$column->column_name] = 'array:double';
                            break;
                        case '_bool':
                            $casts[$column->column_name] = 'array:boolean';
                            break;
                        case '_varchar':
                        case '_text':
                            $casts[$column->column_name] = '\NextDeveloper\Commons\Database\Casts\TextArray::class';
                            break;
                    }
            }
        }

        return $casts;
    }

    public static function generateCastsArray($columns)
    {
        if(config('database.default') == 'mysql') {
            return self::generateCastsArrayMySQL($columns);
        } else if(config('database.default') == 'pgsql') {
            return self::generateCastsArrayPostgreSQL($columns);
        }
    }

    public static function generateDatesArrayMySQL($columns)
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

    public static function generateDatesArrayPostgreSQL($columns)
    {
        $dates = [];

        foreach ($columns as $column) {
            $columnType = self::cleanColumnType($column->data_type);
            switch ($columnType) {
                case 'date':
                case 'datetime':
                case 'timestamp':
                case 'timestamp with time zone':
                case 'immutable_date':
                case 'immutable_datetime':
                    $dates[] = $column->column_name;
                    break;
            }
        }

        return $dates;
    }

    public static function generateDatesArray($columns)
    {
        if(config('database.default') == 'mysql') {
            return self::generateDatesArrayMySQL($columns);
        } else if(config('database.default') == 'pgsql') {
            return self::generateDatesArrayPostgreSQL($columns);
        }
    }

    public static function generateFullTextFieldsArray($model)
    {
        $fullTextFields = [];

//        $expression = DB::raw("SHOW INDEX FROM " . $model . " WHERE Index_type = 'FULLTEXT'");
//        $query = $expression->getValue( DB::connection()->getQueryGrammar() );
//
//        $indexes = DB::select($query);
//        foreach ($indexes as $index) {
//            $fullTextFields[] = $index->Column_name;
//        }
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

    public static function getIdFieldsMySQL($namespace, $module, $model): array
    {
        $columns = self::getColumns($model);

        $idFields = [];

        foreach ($columns as $column) {
            if($column->Field == 'object_id') continue;

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
                    Str::camel($column->Field),
                    Str::contains($column->Comment, '[!model]') ? 1 : 0
                ];
            }
        }

        return $idFields;
    }

    public static function getIdFieldsPostgreSQL($namespace, $module, $model): array
    {
        $columns = self::getColumns($model);

        $idFields = [];

        foreach ($columns as $column) {
            $aliasColumn = null;

            if($column->column_name == 'object_id') continue;

            //  Checking if we have an alias. Because this may be an another column.
            if(AbstractService::columnHasComment($model,  $column->column_name, '[alias:')) {
                $aliasColumn = AbstractService::getAliasModel($model, $column->column_name);
            }

            $foreignModel = Str::remove('_id', $column->column_name);

            if($aliasColumn) {
                $foreignModel = $aliasColumn;
                $foreignModel = Str::remove('_id', $foreignModel);
            }

            if (Str::endsWith($column->column_name, '_id')) {
                $configModules = config('generator.modules');

                $classModule = '';
                foreach ($configModules as $configModule) {
                    if($aliasColumn) {
                        if (Str::startsWith($aliasColumn, $configModule['prefix'] . '_')) {
                            $classModule = $configModule['name'];
                            break;
                        }
                    } else {
                        if (Str::startsWith($column->column_name, $configModule['prefix'] . '_')) {
                            $classModule = $configModule['name'];
                            break;
                        }
                    }
                }

                $module = strtolower(Str::singular($module));
                $foreignModel = ucfirst(Str::camel(Str::singular($foreignModel)));
                $modelWithoutModule = self::getModelName($foreignModel, $classModule);

                $idFields[] = [
                    '\\' . $namespace . '\\' . $classModule . '\\Database\\Models\\' . $modelWithoutModule,
                    $column->column_name,
                    Str::camel($column->column_name),
                    self::columnHasComment($model, $column, '[!model]') ? 1 : 0
                ];
            }
        }

        return $idFields;
    }

    public static function getIdFields($namespace, $module, $model): array
    {
        if(config('database.default') == 'mysql') {
            return self::getIdFieldsMySQL($namespace, $module, $model);
        } else if(config('database.default') == 'pgsql') {
            return self::getIdFieldsPostgreSQL($namespace, $module, $model);
        }

        return [];
    }

    public static function columnHasComment($model, $column, $comment) {
        if(config('database.default') == 'mysql') {
            return Str::contains($column->Comment, $comment);
        } else if(config('database.default') == 'pgsql') {
            $columnComment = self::getCommentsOfPostgreSQL($model, $column->column_name);
            foreach ($columnComment as $value) {
                if(Str::contains($value, $comment))
                    return true;
            }
        }

        return false;
    }

    public static function getCommentsOfPostgreSQL($model, $column)
    {
        $postgreComments = DB::select("select
            c.table_schema,
            c.table_name,
            c.column_name,
            pgd.description
        from pg_catalog.pg_statio_all_tables as st
                 inner join pg_catalog.pg_description pgd on (
            pgd.objoid = st.relid
            )
                 inner join information_schema.columns c on (
            pgd.objsubid   = c.ordinal_position and
            c.table_schema = st.schemaname and
            c.table_name   = st.relname
            )
        where c.table_schema = 'public'
          AND c.table_name   = '" . $model . "'
          AND c.column_name = '" . $column . "';");

        $comments = [];

        foreach ($postgreComments as $comment) {
            $comments[$comment->column_name] = $comment->description;
        }

        return $comments;
    }

    public static function getCommentsOfMySQL($model)
    {
        $columns = self::getColumns($model);

        $comments = [];

        foreach ($columns as $column) {
            $comments[$column->Field] = $column->Comment;
        }

        return $comments;
    }
}
