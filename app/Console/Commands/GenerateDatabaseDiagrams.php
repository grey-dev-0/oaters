<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GenerateDatabaseDiagrams extends Command
{
    protected $signature = 'db:generate-diagrams {--output=docs/database} {--database= : The database to generate diagrams from}';
    protected $description = 'Generate Mermaid ERD diagrams from the current database schema';

    public function handle()
    {
        $outputDir = $this->option('output');
        $database = $this->option('database');
        
        $this->ensureDirectoryExists($outputDir);

        $tables = $this->extractDatabaseSchema($database);
        $diagrams = $this->groupTablesByModule($tables);

        foreach ($diagrams as $module => $moduleData) {
            $this->generateModuleDiagram($module, $moduleData, $outputDir);
        }

        $this->generateCentralDiagram($tables, $outputDir);
        $this->info('Database diagrams generated successfully!');
    }

    private function extractDatabaseSchema(?string $database = null): array
    {
        $tables = [];
        $connection = DB::connection();
        
        if ($database) {
            $connection->statement("USE `{$database}`");
            $databaseName = $database;
        } else {
            $databaseName = $connection->getDatabaseName();
        }
        
        $allTables = DB::select("SHOW TABLES");
        $tableKey = "Tables_in_{$databaseName}";
        
        foreach ($allTables as $tableObj) {
            $tableName = $tableObj->$tableKey;
            
            if ($tableName === 'migrations') {
                continue;
            }
            
            $columns = $this->getTableColumns($tableName);
            $relationships = $this->getTableRelationships($tableName);
            
            $tables[$tableName] = [
                'name' => $tableName,
                'columns' => $columns,
                'relationships' => $relationships,
            ];
        }
        
        return $tables;
    }
    
    private function getTableColumns(string $tableName): array
    {
        $columns = [];
        
        $columnInfo = DB::select("SHOW COLUMNS FROM `{$tableName}`");
        
        foreach ($columnInfo as $column) {
            $columnName = $column->Field;
            $columnType = $this->parseColumnType($column->Type);
            $columns[$columnName] = $columnType;
        }
        
        return $columns;
    }
    
    private function parseColumnType(string $fullType): string
    {
        if (preg_match('/^([a-z]+)/i', $fullType, $matches)) {
            return strtolower($matches[1]);
        }
        
        return 'string';
    }
    
    private function getTableRelationships(string $tableName): array
    {
        $relationships = [];
        $connection = DB::connection();
        
        $result = DB::select("SELECT DATABASE() as db");
        $databaseName = $result[0]->db;
        
        $foreignKeys = DB::select("
            SELECT 
                COLUMN_NAME,
                REFERENCED_TABLE_NAME,
                REFERENCED_COLUMN_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = ? 
                AND TABLE_NAME = ?
                AND REFERENCED_TABLE_NAME IS NOT NULL
        ", [$databaseName, $tableName]);
        
        foreach ($foreignKeys as $fk) {
            $relationships[] = [
                'column' => $fk->COLUMN_NAME,
                'references' => $fk->REFERENCED_COLUMN_NAME,
                'table' => $fk->REFERENCED_TABLE_NAME,
            ];
        }
        
        return $relationships;
    }

    private function groupTablesByModule(array $tables): array
    {
        $grouped = [];
        
        $prefixMap = [
            's_' => 'Sapphire',
            'a_' => 'Amethyst',
            'r_' => 'Ruby',
            'e_' => 'Emerald',
            'o_' => 'Onyx',
            'la_' => 'Article',
            'lc_' => 'Common',
            'le_' => 'Commerce',
        ];

        foreach ($tables as $table) {
            $tableName = $table['name'];
            $module = 'Core';
            
            foreach ($prefixMap as $prefix => $moduleName) {
                if (strpos($tableName, $prefix) === 0) {
                    $module = $moduleName;
                    break;
                }
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
            'Article' => 'Lava Article',
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
                    $mermaid .= "    " . $rel['table'] . " ||--o{ {$tableName} : \"\"\n";
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
            'bigint' => 'bigint',
            'integer' => 'int',
            'int' => 'int',
            'smallint' => 'smallint',
            'tinyint' => 'tinyint',
            'varchar' => 'string',
            'string' => 'string',
            'char' => 'char',
            'text' => 'text',
            'mediumtext' => 'text',
            'longtext' => 'text',
            'float' => 'float',
            'double' => 'double',
            'decimal' => 'decimal',
            'boolean' => 'boolean',
            'tinyint(1)' => 'boolean',
            'enum' => 'string',
            'date' => 'date',
            'datetime' => 'datetime',
            'timestamp' => 'datetime',
            'time' => 'time',
            'json' => 'json',
            'uuid' => 'uuid',
            'increments' => 'int',
            'bigIncrements' => 'bigint',
            'unsignedInteger' => 'int',
            'unsignedBigInteger' => 'bigint',
            'bigInteger' => 'bigint',
            'smallInteger' => 'smallint',
            'unsignedSmallInteger' => 'smallint',
            'unsignedTinyInteger' => 'tinyint',
            'tinyInteger' => 'tinyint',
            'mediumText' => 'text',
            'longText' => 'text',
            'dateTime' => 'datetime',
            'jsonb' => 'json',
            'ulid' => 'string',
        ];

        return $typeMap[strtolower($laravelType)] ?? 'string';
    }
}
