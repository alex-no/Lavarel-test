<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class AddLanguageColumnCommand extends Command
{
    const TYPE_MAP = [
        'varchar' => 'string',
        'char' => 'string',
        'text' => 'text',
        'longtext' => 'text',
        'mediumtext' => 'text',
        'tinytext' => 'text',
        'int' => 'integer',
        'bigint' => 'bigInteger',
        'smallint' => 'smallInteger',
        'tinyint' => 'tinyInteger',
        'boolean' => 'boolean',
        'datetime' => 'dateTime',
        'timestamp' => 'timestamp',
        'date' => 'date',
        'float' => 'float',
        'double' => 'double',
        'decimal' => 'decimal',
    ];

    protected $signature = 'db:add-language-column \
                            {--suffix= : New language suffix to add} \
                            {--base-langs= : Comma-separated list of base language suffixes}';

    protected $description = 'Add new localized column to all tables that contain base language columns.';

    public function handle(): int
    {
        $suffix = trim($this->option('suffix'));
        $baseLangs = array_filter(array_map('trim', explode(',', $this->option('base-langs'))));

        if (!$suffix || empty($baseLangs)) {
            $this->error('Both --suffix and --base-langs options are required.');
            return static::FAILURE;
        }

        $this->info("Looking for tables with columns matching base languages: " . implode(', ', $baseLangs));

        // Fetch list of table names without Doctrine
        $tables = DB::select("SHOW TABLES");
        $tableKey = 'Tables_in_' . DB::getDatabaseName();
        $tables = array_map(fn($row) => $row->$tableKey, $tables);

        foreach ($tables as $table) {
            $columns = Schema::getColumnListing($table);

            // Group columns by base name
            $groups = [];
            foreach ($columns as $column) {
                foreach ($baseLangs as $lang) {
                    if (Str::endsWith($column, "_{$lang}")) {
                        $base = Str::beforeLast($column, "_{$lang}");
                        $groups[$base][] = $column;
                        break;
                    }
                }
            }

            foreach ($groups as $base => $groupColumns) {
                $newColumn = "{$base}_{$suffix}";

                if (in_array($newColumn, $columns)) {
                    $this->line("[{$table}] Skipping: '{$newColumn}' already exists.");
                    continue;
                }

                // Determine type from last column in group
                $lastColumn = end($groupColumns);
                $baseType = DB::getSchemaBuilder()->getColumnType($table, $lastColumn);

                $blueprintMethod = self::TYPE_MAP[$baseType] ?? 'string';
                Schema::table($table, function ($tableBlueprint) use ($newColumn, $blueprintMethod, $lastColumn) {
                    $tableBlueprint->{$blueprintMethod}($newColumn)->nullable()->after($lastColumn);
                });

                $this->info("[{$table}] Added column '{$newColumn}' of type '{$blueprintMethod}' after '{$lastColumn}'.");
            }
        }

        return static::SUCCESS;
    }
}
