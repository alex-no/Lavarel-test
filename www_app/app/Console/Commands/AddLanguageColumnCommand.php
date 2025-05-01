<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class AddLanguageColumnCommand extends Command
{
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
        $tables = DB::connection()->getDoctrineSchemaManager()->listTableNames();

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
                $columnType = DB::getSchemaBuilder()->getColumnType($table, $lastColumn);

                // Default to string if unknown
                if (!$columnType) {
                    $columnType = 'string';
                }

                Schema::table($table, function ($tableBlueprint) use ($newColumn, $columnType) {
                    $tableBlueprint->{$columnType}($newColumn)->nullable();
                });

                $this->info("[{$table}] Added column '{$newColumn}' of type '{$columnType}'");
            }
        }

        return static::SUCCESS;
    }
}
