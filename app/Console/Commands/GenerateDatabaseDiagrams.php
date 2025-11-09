<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

class GenerateDatabaseDiagrams extends Command
{
    protected $signature = 'db:generate-diagrams {--output=docs/database}';
    protected $description = 'Generate Mermaid ERD diagrams from database migrations';

    public function handle()
    {
        $outputDir = $this->option('output');
        $this->ensureDirectoryExists($outputDir);

        $migrations = $this->collectMigrations();
        $tables = $this->parseMigrations($migrations);
        $diagrams = $this->groupTablesByModule($tables);

        foreach ($diagrams as $module => $moduleData) {
            $this->generateModuleDiagram($module, $moduleData, $outputDir);
        }

        $this->generateCentralDiagram($tables, $outputDir);
        $this->info('Database diagrams generated successfully!');
    }

    private function collectMigrations(): array
    {
        $migrations = [];
        $basePath = base_path('Modules');

        if (is_dir($basePath)) {
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($basePath),
                \RecursiveIteratorIterator::CHILD_FIRST
            );

            foreach ($iterator as $file) {
                if ($file->getExtension() === 'php' && strpos($file->getPathname(), 'migrations') !== false) {
                    $pathParts = explode('/', str_replace($basePath . '/', '', $file->getPathname()));
                    $moduleName = $pathParts[0] ?? 'Unknown';
                    $migrations[$moduleName][] = $file->getPathname();
                }
            }
        }

        // Add core migrations
        $corePath = database_path('migrations');
        if (is_dir($corePath)) {
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($corePath),
                \RecursiveIteratorIterator::CHILD_FIRST
            );

            foreach ($iterator as $file) {
                if ($file->getExtension() === 'php') {
                    $migrations['Core'][] = $file->getPathname();
                }
            }
        }

        return $migrations;
    }

    private function parseMigrations(array $migrations): array
    {
        $tables = [];

        foreach ($migrations as $module => $files) {
            foreach ($files as $file) {
                $content = file_get_contents($file);
                $parsedTables = $this->extractTablesFromMigration($content);

                foreach ($parsedTables as $table) {
                    $table['module'] = $module;
                    $tables[$table['name']] = $table;
                }
            }
        }

        return $tables;
    }

    private function extractTablesFromMigration(string $content): array
    {
        $tables = [];

        if (preg_match_all('/Schema::create\([\'"]([^\'"]+)[\'"]\s*,\s*function\s*\(\s*Blueprint\s+\$\w+\s*\)\s*\{(.*?)\}\s*\);/s', $content, $matches)) {
            for ($i = 0; $i < count($matches[1]); $i++) {
                $tableName = $matches[1][$i];
                $tableBody = $matches[2][$i];

                $table = [
                    'name' => $tableName,
                    'columns' => $this->extractColumns($tableBody),
                    'relationships' => $this->extractRelationships($tableBody),
                ];

                $tables[] = $table;
            }
        }

        if (strpos($content, 'permission.table_names') !== false) {
            $sapphireTables = [
                ['name' => 's_permissions', 'columns' => ['id' => 'bigIncrements', 'name' => 'string', 'guard_name' => 'string'], 'relationships' => []],
                ['name' => 's_roles', 'columns' => ['id' => 'bigIncrements', 'name' => 'string', 'guard_name' => 'string'], 'relationships' => []],
                ['name' => 's_role_locales', 'columns' => ['id' => 'bigIncrements', 'role_id' => 'unsignedBigInteger', 'title' => 'string', 'locale' => 'string'], 'relationships' => [['column' => 'role_id', 'references' => 'id', 'table' => 's_roles']]],
                ['name' => 's_model_has_permissions', 'columns' => ['permission_id' => 'unsignedBigInteger', 'model_id' => 'unsignedBigInteger', 'model_type' => 'string'], 'relationships' => [['column' => 'permission_id', 'references' => 'id', 'table' => 's_permissions']]],
                ['name' => 's_model_has_roles', 'columns' => ['role_id' => 'unsignedBigInteger', 'model_id' => 'unsignedBigInteger', 'model_type' => 'string'], 'relationships' => [['column' => 'role_id', 'references' => 'id', 'table' => 's_roles']]],
                ['name' => 's_role_has_permissions', 'columns' => ['permission_id' => 'unsignedBigInteger', 'role_id' => 'unsignedBigInteger'], 'relationships' => [['column' => 'permission_id', 'references' => 'id', 'table' => 's_permissions'], ['column' => 'role_id', 'references' => 'id', 'table' => 's_roles']]],
            ];
            $tables = array_merge($tables, $sapphireTables);
        }

        return $tables;
    }

    private function extractColumns(string $tableBody): array
    {
        $columns = [];

        if (preg_match_all('/\$table->([\w]+)\([\'"]?([^\'"(),\[\]]+)[\'"]?[^;]*\);/m', $tableBody, $matches)) {
            for ($i = 0; $i < count($matches[1]); $i++) {
                $type = $matches[1][$i];
                $name = trim($matches[2][$i]);

                if (empty($name) || !preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $name)) {
                    continue;
                }

                if (!in_array($type, ['foreign', 'primary', 'timestamps'])) {
                    $columns[$name] = $type;
                }
            }
        }

        return $columns;
    }

    private function extractRelationships(string $tableBody): array
    {
        $relationships = [];

        if (preg_match_all('/->foreign\([\'"](\w+)[\'"]\)->references\([\'"](\w+)[\'"]\)->on\([\'"](\w+)[\'"]\)/m', $tableBody, $matches)) {
            for ($i = 0; $i < count($matches[1]); $i++) {
                $relationships[] = [
                    'column' => $matches[1][$i],
                    'references' => $matches[2][$i],
                    'table' => $matches[3][$i],
                ];
            }
        }

        return $relationships;
    }

    private function groupTablesByModule(array $tables): array
    {
        $grouped = [];

        foreach ($tables as $table) {
            $module = $table['module'];

            if ($module === 'Core' && strpos($table['name'], 's_') === 0) {
                $module = 'Sapphire';
            }

            if ($module === 'Core' && strpos($table['name'], 'le_') === 0) {
                $module = 'Lava Commerce';
            }

            if (!isset($grouped[$module])) {
                $grouped[$module] = [];
            }
            $grouped[$module][$table['name']] = $table;
        }

        return $grouped;
    }

    private function generateModuleDiagram(string $module, array $moduleData, string $outputDir): void
    {
        $mermaid = $this->buildMermaidDiagram($moduleData);

        $displayNames = [
            'Common' => 'Lava Common',
            'Commerce' => 'Lava Commerce',
        ];
        $displayName = $displayNames[$module] ?? $module;
        
        $filename = "{$outputDir}/" . strtolower(str_replace(' ', '-', $module)) . '-diagram.md';

        $content = "# {$displayName} Database Schema\n\n";
        $content .= "```mermaid\n{$mermaid}\n```\n";

        file_put_contents($filename, $content);
        $this->line("Generated: {$filename}");
    }

    private function generateCentralDiagram(array $allTables, string $outputDir): void
    {
        $mermaid = $this->buildMermaidDiagram($allTables);
        $filename = "{$outputDir}/database-diagram.md";

        $content = "# Complete Database Schema\n\n";
        $content .= "```mermaid\n{$mermaid}\n```\n";

        file_put_contents($filename, $content);
        $this->line("Generated: {$filename}");
    }

    private function buildMermaidDiagram(array $tables): string
    {
        $mermaid = "erDiagram\n";
        $relationships = [];
        $processedRelationships = [];

        foreach ($tables as $tableName => $table) {
            $mermaid .= "    {$tableName} {\n";
            foreach ($table['columns'] as $columnName => $columnType) {
                $mermaidType = $this->mapLaravelTypeToMermaid($columnType);
                $mermaid .= "        {$mermaidType} {$columnName}\n";
            }
            $mermaid .= "    }\n";
        }

        foreach ($tables as $tableName => $table) {
            foreach ($table['relationships'] as $rel) {
                $relKey = "{$tableName}|" . $rel['table'];
                if (!in_array($relKey, $processedRelationships)) {
                    $mermaid .= "    {$tableName} ||--o{ " . $rel['table'] . " : \"\"\n";
                    $processedRelationships[] = $relKey;
                }
            }
        }

        return $mermaid;
    }

    private function ensureDirectoryExists(string $dir): void
    {
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }

    private function mapLaravelTypeToMermaid(string $laravelType): string
    {
        $typeMap = [
            'increments' => 'int',
            'bigIncrements' => 'bigint',
            'unsignedInteger' => 'int',
            'unsignedBigInteger' => 'bigint',
            'integer' => 'int',
            'bigInteger' => 'bigint',
            'smallInteger' => 'smallint',
            'unsignedSmallInteger' => 'smallint',
            'unsignedTinyInteger' => 'tinyint',
            'tinyInteger' => 'tinyint',
            'string' => 'string',
            'char' => 'char',
            'text' => 'text',
            'mediumText' => 'text',
            'longText' => 'text',
            'float' => 'float',
            'double' => 'double',
            'decimal' => 'decimal',
            'boolean' => 'boolean',
            'enum' => 'string',
            'date' => 'date',
            'dateTime' => 'datetime',
            'timestamp' => 'datetime',
            'time' => 'time',
            'json' => 'json',
            'jsonb' => 'json',
            'uuid' => 'uuid',
            'ulid' => 'string',
        ];

        return $typeMap[$laravelType] ?? 'string';
    }
}
