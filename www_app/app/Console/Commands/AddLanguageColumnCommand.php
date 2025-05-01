<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

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

    const REPORT_HEADER = ['table', 'new_column', 'type', 'after_column', 'status'];

    protected $signature = 'db:add-language-column \
                            {--suffix= : New language suffix to add} \
                            {--base-langs= : Comma-separated list of base language suffixes} \
                            {--report= : Optional path to CSV report file}';

    protected $description = 'Add new localized column to all tables that contain base language columns.';

    public function handle(): int
    {
        $suffix = trim($this->option('suffix'));
        $baseLangs = array_filter(array_map('trim', explode(',', $this->option('base-langs'))));
        $reportPath = $this->option('report');

        if (!$suffix || empty($baseLangs)) {
            $this->error('Both --suffix and --base-langs options are required.');
            return static::FAILURE;
        }

        $this->info("Looking for tables with columns matching base languages: " . implode(', ', $baseLangs));

        $tables = DB::select("SHOW TABLES");
        $tableKey = 'Tables_in_' . DB::getDatabaseName();
        $tables = array_map(fn($row) => $row->$tableKey, $tables);

        $reportRows = [self::REPORT_HEADER];

        foreach ($tables as $table) {
            $columns = Schema::getColumnListing($table);

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
                    $reportRows[] = [$table, $newColumn, '', '', 'skipped (already exists)'];
                    continue;
                }

                // Determine type from last column in group
                $lastColumn = end($groupColumns);
                $columnDetails = DB::selectOne("SHOW COLUMNS FROM `{$table}` WHERE Field = ?", [$lastColumn]);

                $baseType = DB::getSchemaBuilder()->getColumnType($table, $lastColumn);
                $blueprintMethod = self::TYPE_MAP[$baseType] ?? 'string';

                // Extract length if present (e.g., from varchar(255), decimal(10,2), etc.)
                $lengthParams = null;
                if (preg_match('/^([a-z]+)\\(([^)]+)\\)/i', $columnDetails->Type, $matches)) {
                    $lengthParams = explode(',', $matches[2]);
                }

                Schema::table($table, function ($tableBlueprint) use ($newColumn, $blueprintMethod, $lastColumn, $lengthParams) {
                    $tableBlueprint->{$blueprintMethod}($newColumn, ...($lengthParams ?? []))->nullable()->after($lastColumn);
                });

                $lengthDesc = $lengthParams ? implode(',', $lengthParams) : '';
                $this->info("[{$table}] Added column '{$newColumn}' of type '{$blueprintMethod}({$lengthDesc})' after '{$lastColumn}'.");
                $reportRows[] = [$table, $newColumn, $blueprintMethod . ($lengthDesc ? "({$lengthDesc})" : ''), $lastColumn, 'added'];
            }
        }

        if ($reportPath) {
            $csvContent = collect($reportRows)->map(fn($row) => implode(',', array_map(fn($v) => '"' . str_replace('"', '""', $v) . '"', $row)))->implode("\n");
            file_put_contents(base_path($reportPath), $csvContent);
            $this->info("Report saved to: {$reportPath}");
        }

        return static::SUCCESS;
    }
}
