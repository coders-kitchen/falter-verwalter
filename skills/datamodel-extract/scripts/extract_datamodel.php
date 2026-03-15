#!/usr/bin/env php
<?php

declare(strict_types=1);

final class DataModelExtractor
{
    private string $root;
    private string $modelsDir;
    private string $migrationsDir;
    private string $outputDir;

    /** @var array<string, array<string, mixed>> */
    private array $tables = [];

    /** @var array<string, array<string, mixed>> */
    private array $models = [];

    public function __construct(string $root, ?string $outputDir = null)
    {
        $this->root = rtrim($root, DIRECTORY_SEPARATOR);
        $this->modelsDir = $this->root . '/app/Models';
        $this->migrationsDir = $this->root . '/database/migrations';
        $this->outputDir = $outputDir
            ? $this->normalizePath($outputDir)
            : $this->root . '/docs/context/data-model';
    }

    public function run(): void
    {
        $this->parseMigrations();
        $this->parseModels();
        $this->attachModelMetadataToTables();
        $this->writeOutputs();

        fwrite(STDOUT, "Generated data model context in {$this->outputDir}\n");
    }

    private function parseMigrations(): void
    {
        $files = glob($this->migrationsDir . '/*.php');
        sort($files);

        foreach ($files as $file) {
            $content = file_get_contents($file);
            if ($content === false) {
                continue;
            }

            $upBody = $this->extractMethodBody($content, 'up');
            if ($upBody === null) {
                continue;
            }

            $this->applyDropIfExists($upBody);
            $this->applySchemaBlocks($upBody, basename($file));
        }
    }

    private function parseModels(): void
    {
        $files = glob($this->modelsDir . '/*.php');
        sort($files);

        foreach ($files as $file) {
            $content = file_get_contents($file);
            if ($content === false) {
                continue;
            }

            if (!preg_match('/class\s+(\w+)/', $content, $classMatch)) {
                continue;
            }

            $className = $classMatch[1];
            $table = $this->parseModelTable($content, $className);

            $this->models[$className] = [
                'class' => $className,
                'table' => $table,
                'file' => $this->relativePath($file),
                'fillable' => $this->parsePhpArrayProperty($content, 'fillable'),
                'casts' => $this->parsePhpArrayProperty($content, 'casts', true),
                'relations' => $this->parseRelations($content),
            ];
        }
    }

    private function attachModelMetadataToTables(): void
    {
        foreach ($this->models as $model) {
            $tableName = $model['table'];

            if (!isset($this->tables[$tableName])) {
                $this->tables[$tableName] = [
                    'name' => $tableName,
                    'columns' => [],
                    'indexes' => [],
                    'model' => null,
                    'sources' => [],
                ];
            }

            $this->tables[$tableName]['model'] = $model['class'];
        }

        ksort($this->tables);
        ksort($this->models);
    }

    private function writeOutputs(): void
    {
        if (!is_dir($this->outputDir)) {
            mkdir($this->outputDir, 0777, true);
        }

        $generatedAt = date('c');
        $schemaPayload = [
            'generated_at' => $generatedAt,
            'source' => [
                'models_dir' => $this->relativePath($this->modelsDir),
                'migrations_dir' => $this->relativePath($this->migrationsDir),
            ],
            'tables' => array_values($this->tables),
            'models' => array_values($this->models),
        ];

        file_put_contents(
            $this->outputDir . '/schema.json',
            json_encode($schemaPayload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL
        );

        file_put_contents($this->outputDir . '/schema.md', $this->renderSchemaMarkdown($generatedAt));
        file_put_contents($this->outputDir . '/relations.md', $this->renderRelationsMarkdown($generatedAt));
    }

    private function renderSchemaMarkdown(string $generatedAt): string
    {
        $lines = [
            '# Data Model Schema',
            '',
            '- Generated at: ' . $generatedAt,
            '- Source: `app/Models` + `database/migrations`',
            '- Note: Static extraction from source files. Treat as a context snapshot.',
            '',
        ];

        foreach ($this->tables as $table) {
            $lines[] = '## `' . $table['name'] . '`';
            $lines[] = '';
            if (!empty($table['model'])) {
                $lines[] = '- Model: `' . $table['model'] . '`';
            }
            if (!empty($table['sources'])) {
                $lines[] = '- Migrations: ' . implode(', ', array_map(
                    static fn (string $source): string => '`' . $source . '`',
                    $table['sources']
                ));
            }
            $lines[] = '';
            $lines[] = '| Column | Type | Nullable | Default | References |';
            $lines[] = '| --- | --- | --- | --- | --- |';

            if (empty($table['columns'])) {
                $lines[] = '| _(none detected)_ |  |  |  |  |';
            } else {
                foreach ($table['columns'] as $column) {
                    $references = $column['references'] ?? '';
                    $default = $column['default'] ?? '';
                    $lines[] = sprintf(
                        '| `%s` | `%s` | %s | `%s` | `%s` |',
                        $column['name'],
                        $column['type'],
                        $column['nullable'] ? 'yes' : 'no',
                        $default,
                        $references
                    );
                }
            }

            if (!empty($table['indexes'])) {
                $lines[] = '';
                $lines[] = 'Indexes:';
                foreach ($table['indexes'] as $index) {
                    $lines[] = '- `' . $index['type'] . '` on `' . implode('`, `', $index['columns']) . '`';
                }
            }

            $lines[] = '';
        }

        return implode(PHP_EOL, $lines) . PHP_EOL;
    }

    private function renderRelationsMarkdown(string $generatedAt): string
    {
        $lines = [
            '# Data Model Relations',
            '',
            '- Generated at: ' . $generatedAt,
            '- Source: Eloquent relation methods in `app/Models`',
            '',
        ];

        foreach ($this->models as $model) {
            $lines[] = '## `' . $model['class'] . '`';
            $lines[] = '';
            $lines[] = '- Table: `' . $model['table'] . '`';
            $lines[] = '- File: `' . $model['file'] . '`';

            if (!empty($model['fillable'])) {
                $lines[] = '- Fillable: `' . implode('`, `', $model['fillable']) . '`';
            }

            if (!empty($model['casts'])) {
                $castPairs = [];
                foreach ($model['casts'] as $field => $cast) {
                    $castPairs[] = $field . ':' . $cast;
                }
                $lines[] = '- Casts: `' . implode('`, `', $castPairs) . '`';
            }

            $lines[] = '';
            $lines[] = '| Method | Relation | Target | Details |';
            $lines[] = '| --- | --- | --- | --- |';

            if (empty($model['relations'])) {
                $lines[] = '| _(none detected)_ |  |  |  |';
            } else {
                foreach ($model['relations'] as $relation) {
                    $details = [];

                    if (!empty($relation['table'])) {
                        $details[] = 'table=' . $relation['table'];
                    }
                    if (!empty($relation['foreign_key'])) {
                        $details[] = 'fk=' . $relation['foreign_key'];
                    }
                    if (!empty($relation['related_key'])) {
                        $details[] = 'related=' . $relation['related_key'];
                    }
                    if (!empty($relation['pivot'])) {
                        $details[] = 'pivot=' . implode(',', $relation['pivot']);
                    }

                    $lines[] = sprintf(
                        '| `%s` | `%s` | `%s` | %s |',
                        $relation['method'],
                        $relation['type'],
                        $relation['target'],
                        $details === [] ? '' : '`' . implode('; ', $details) . '`'
                    );
                }
            }

            $lines[] = '';
        }

        return implode(PHP_EOL, $lines) . PHP_EOL;
    }

    private function applyDropIfExists(string $upBody): void
    {
        if (!preg_match_all("/Schema::dropIfExists\\('([^']+)'\\)/", $upBody, $matches, PREG_SET_ORDER)) {
            return;
        }

        foreach ($matches as $match) {
            unset($this->tables[$match[1]]);
        }
    }

    private function applySchemaBlocks(string $upBody, string $sourceFile): void
    {
        if (!preg_match_all(
            '~Schema::(create|table)\(\s*\'([^\']+)\'\s*,\s*function\s*\(Blueprint \$table\)\s*\{(.*?)\n\s*\}\);~s',
            $upBody,
            $matches,
            PREG_SET_ORDER
        )) {
            return;
        }

        foreach ($matches as $match) {
            $mode = $match[1];
            $tableName = $match[2];
            $body = $match[3];

            if ($mode === 'create' || !isset($this->tables[$tableName])) {
                $this->tables[$tableName] = [
                    'name' => $tableName,
                    'columns' => [],
                    'indexes' => [],
                    'model' => null,
                    'sources' => [],
                ];
            }

            if (!in_array($sourceFile, $this->tables[$tableName]['sources'], true)) {
                $this->tables[$tableName]['sources'][] = $sourceFile;
            }

            $statements = $this->extractTableStatements($body);
            foreach ($statements as $statement) {
                $this->applyTableStatement($tableName, $statement);
            }
        }
    }

    /**
     * @return list<string>
     */
    private function extractTableStatements(string $body): array
    {
        preg_match_all('/\\$table->.*?;/s', $body, $matches);

        return array_map(
            static fn (string $statement): string => trim(preg_replace('/\s+/', ' ', $statement) ?? $statement),
            $matches[0]
        );
    }

    private function applyTableStatement(string $tableName, string $statement): void
    {
        $statement = rtrim($statement, ';');

        if (preg_match('/^\$table->timestamps\(\)$/', $statement)) {
            $this->setColumn($tableName, [
                'name' => 'created_at',
                'type' => 'timestamp',
                'nullable' => true,
                'default' => null,
                'references' => null,
            ]);
            $this->setColumn($tableName, [
                'name' => 'updated_at',
                'type' => 'timestamp',
                'nullable' => true,
                'default' => null,
                'references' => null,
            ]);
            return;
        }

        if (preg_match('/^\$table->rememberToken\(\)$/', $statement)) {
            $this->setColumn($tableName, [
                'name' => 'remember_token',
                'type' => 'string',
                'nullable' => true,
                'default' => null,
                'references' => null,
            ]);
            return;
        }

        if (preg_match('/^\$table->id\(\)(.*)$/', $statement, $match)) {
            $this->setColumn($tableName, [
                'name' => 'id',
                'type' => 'bigint',
                'nullable' => false,
                'default' => null,
                'references' => null,
            ]);
            return;
        }

        if (preg_match('/^\$table->dropConstrainedForeignId\(\'([^\']+)\'\)$/', $statement, $match)) {
            unset($this->tables[$tableName]['columns'][$match[1]]);
            return;
        }

        if (preg_match('/^\$table->dropColumn\((.+)\)$/', $statement, $match)) {
            foreach ($this->parseColumnArgumentList($match[1]) as $column) {
                unset($this->tables[$tableName]['columns'][$column]);
            }
            return;
        }

        if ($this->applyIndexStatement($tableName, $statement)) {
            return;
        }

        if (preg_match('/^\$table->([A-Za-z_]+)\((.+?)\)(.*)$/', $statement, $match)) {
            $method = $match[1];
            $args = $match[2];
            $chain = $match[3];

            $column = $this->parseColumnDefinition($tableName, $method, $args, $chain);
            if ($column !== null) {
                $this->setColumn($tableName, $column);
            }
        }
    }

    private function applyIndexStatement(string $tableName, string $statement): bool
    {
        if (!preg_match('/^\$table->(index|unique)\((.+)\)$/', $statement, $match)) {
            return false;
        }

        $columns = $this->parseColumnArgumentList($match[2]);
        if ($columns === []) {
            return true;
        }

        $this->addIndex($tableName, $match[1], $columns);
        return true;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function parseColumnDefinition(string $tableName, string $method, string $args, string $chain): ?array
    {
        $columnName = $this->extractFirstQuoted($args);
        if ($columnName === null) {
            return null;
        }

        $nullable = str_contains($chain, '->nullable()');
        $default = $this->extractDefault($chain);
        $references = null;
        $type = $this->normalizeColumnType($method);

        if ($method === 'foreignId') {
            $references = $this->extractConstrainedTable($columnName, $chain) . '.id';
            $type = 'foreignId';
        } elseif ($method === 'enum') {
            $enumValues = $this->extractEnumValues($args);
            $type = 'enum(' . implode(', ', $enumValues) . ')';
        } elseif ($method === 'string' && preg_match('/,\s*(\d+)/', $args, $lengthMatch)) {
            $type = 'string(' . $lengthMatch[1] . ')';
        }

        if (str_contains($chain, '->unique()')) {
            $this->addIndex($tableName, 'unique', [$columnName]);
        }

        return [
            'name' => $columnName,
            'type' => $type,
            'nullable' => $nullable,
            'default' => $default,
            'references' => $references,
        ];
    }

    private function setColumn(string $tableName, array $column): void
    {
        $this->tables[$tableName]['columns'][$column['name']] = $column;
        ksort($this->tables[$tableName]['columns']);
    }

    /**
     * @param list<string> $columns
     */
    private function addIndex(string $tableName, string $type, array $columns): void
    {
        $signature = $type . ':' . implode(',', $columns);
        foreach ($this->tables[$tableName]['indexes'] as $existing) {
            if ($existing['signature'] === $signature) {
                return;
            }
        }

        $this->tables[$tableName]['indexes'][] = [
            'type' => $type,
            'columns' => $columns,
            'signature' => $signature,
        ];
    }

    private function normalizeColumnType(string $method): string
    {
        return match ($method) {
            'bigIncrements' => 'bigint',
            'foreignId' => 'foreignId',
            'unsignedTinyInteger' => 'unsignedTinyInteger',
            default => $method,
        };
    }

    /**
     * @return list<string>
     */
    private function parseColumnArgumentList(string $raw): array
    {
        $raw = trim($raw);

        if (preg_match('/^\[(.*?)\]/s', $raw, $match)) {
            preg_match_all("/'([^']+)'/", $match[1], $valueMatches);
            return $valueMatches[1];
        }

        if (preg_match("/'([^']+)'/", $raw, $match)) {
            return [$match[1]];
        }

        return [];
    }

    /**
     * @return list<string>
     */
    private function extractEnumValues(string $args): array
    {
        if (!preg_match('/\[(.*)\]/s', $args, $match)) {
            return [];
        }

        preg_match_all("/'([^']+)'/", $match[1], $valueMatches);
        return $valueMatches[1];
    }

    private function extractFirstQuoted(string $value): ?string
    {
        return preg_match("/'([^']+)'/", $value, $match) ? $match[1] : null;
    }

    private function extractConstrainedTable(string $columnName, string $chain): string
    {
        if (preg_match("/->constrained\\('([^']+)'\\)/", $chain, $match)) {
            return $match[1];
        }

        $base = preg_replace('/_id$/', '', $columnName) ?? $columnName;
        if (str_ends_with($base, 'y')) {
            return substr($base, 0, -1) . 'ies';
        }

        return str_ends_with($base, 's') ? $base : $base . 's';
    }

    private function extractDefault(string $chain): ?string
    {
        if (!preg_match('/->default\(([^)]+)\)/', $chain, $match)) {
            return null;
        }

        return trim(str_replace(["'", '"'], '', $match[1]));
    }

    private function parseModelTable(string $content, string $className): string
    {
        if (preg_match('/protected \$table = \'([^\']+)\'/', $content, $match)) {
            return $match[1];
        }

        return $this->pluralize($this->toSnakeCase($className));
    }

    /**
     * @return list<string>|array<string, string>
     */
    private function parsePhpArrayProperty(string $content, string $property, bool $keyValue = false): array
    {
        if (!preg_match("/protected \\$$property = \\[(.*?)\\];/s", $content, $match)) {
            return [];
        }

        $body = $match[1];
        if ($keyValue) {
            preg_match_all("/'([^']+)'\\s*=>\\s*'([^']+)'/", $body, $pairs, PREG_SET_ORDER);
            $result = [];
            foreach ($pairs as $pair) {
                $result[$pair[1]] = $pair[2];
            }
            return $result;
        }

        preg_match_all("/'([^']+)'/", $body, $values);
        return $values[1];
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function parseRelations(string $content): array
    {
        preg_match_all(
            '/public function (\w+)\(\):\s*([A-Za-z\\\\]+)\s*\{(.*?)\n\s*\}/s',
            $content,
            $matches,
            PREG_SET_ORDER
        );

        $relations = [];

        foreach ($matches as $match) {
            $method = $match[1];
            $returnType = $this->classBasename($match[2]);
            $body = preg_replace('/\s+/', ' ', trim($match[3])) ?? trim($match[3]);

            $relation = match ($returnType) {
                'BelongsTo' => $this->parseBelongsTo($method, $body),
                'HasMany' => $this->parseHasMany($method, $body),
                'HasOne' => $this->parseHasOne($method, $body),
                'BelongsToMany' => $this->parseBelongsToMany($method, $body),
                default => null,
            };

            if ($relation !== null) {
                $relations[] = $relation;
            }
        }

        return $relations;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function parseBelongsTo(string $method, string $body): ?array
    {
        if (!preg_match('/belongsTo\((\w+)::class(?:,\s*\'([^\']+)\')?/', $body, $match)) {
            return null;
        }

        return [
            'method' => $method,
            'type' => 'belongsTo',
            'target' => $match[1],
            'table' => null,
            'foreign_key' => $match[2] ?? null,
            'related_key' => null,
            'pivot' => [],
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    private function parseHasMany(string $method, string $body): ?array
    {
        if (!preg_match('/hasMany\((\w+)::class(?:,\s*\'([^\']+)\')?/', $body, $match)) {
            return null;
        }

        return [
            'method' => $method,
            'type' => 'hasMany',
            'target' => $match[1],
            'table' => null,
            'foreign_key' => $match[2] ?? null,
            'related_key' => null,
            'pivot' => [],
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    private function parseHasOne(string $method, string $body): ?array
    {
        if (!preg_match('/hasOne\((\w+)::class(?:,\s*\'([^\']+)\')?/', $body, $match)) {
            return null;
        }

        return [
            'method' => $method,
            'type' => 'hasOne',
            'target' => $match[1],
            'table' => null,
            'foreign_key' => $match[2] ?? null,
            'related_key' => null,
            'pivot' => [],
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    private function parseBelongsToMany(string $method, string $body): ?array
    {
        if (!preg_match(
            '/belongsToMany\((\w+)::class(?:,\s*\'([^\']+)\')?(?:,\s*\'([^\']+)\')?(?:,\s*\'([^\']+)\')?/',
            $body,
            $match
        )) {
            return null;
        }

        $pivotFields = [];
        if (preg_match('/->withPivot\(([^)]+)\)/', $body, $pivotMatch)) {
            preg_match_all("/'([^']+)'/", $pivotMatch[1], $pivotFieldsMatch);
            $pivotFields = $pivotFieldsMatch[1];
        }

        return [
            'method' => $method,
            'type' => 'belongsToMany',
            'target' => $match[1],
            'table' => $match[2] ?? null,
            'foreign_key' => $match[3] ?? null,
            'related_key' => $match[4] ?? null,
            'pivot' => $pivotFields,
        ];
    }

    private function extractMethodBody(string $content, string $method): ?string
    {
        $needle = 'function ' . $method . '(';
        $start = strpos($content, $needle);
        if ($start === false) {
            return null;
        }

        $braceStart = strpos($content, '{', $start);
        if ($braceStart === false) {
            return null;
        }

        $depth = 1;
        $length = strlen($content);
        $inSingle = false;
        $inDouble = false;
        $inLineComment = false;
        $inBlockComment = false;

        for ($i = $braceStart + 1; $i < $length; $i++) {
            $char = $content[$i];
            $next = $content[$i + 1] ?? '';

            if ($inLineComment) {
                if ($char === "\n") {
                    $inLineComment = false;
                }
                continue;
            }

            if ($inBlockComment) {
                if ($char === '*' && $next === '/') {
                    $inBlockComment = false;
                    $i++;
                }
                continue;
            }

            if (!$inSingle && !$inDouble) {
                if ($char === '/' && $next === '/') {
                    $inLineComment = true;
                    $i++;
                    continue;
                }

                if ($char === '/' && $next === '*') {
                    $inBlockComment = true;
                    $i++;
                    continue;
                }
            }

            if ($char === "'" && !$inDouble) {
                $inSingle = !$inSingle;
                continue;
            }

            if ($char === '"' && !$inSingle) {
                $inDouble = !$inDouble;
                continue;
            }

            if ($inSingle || $inDouble) {
                if ($char === '\\') {
                    $i++;
                }
                continue;
            }

            if ($char === '{') {
                $depth++;
                continue;
            }

            if ($char === '}') {
                $depth--;
                if ($depth === 0) {
                    return substr($content, $braceStart + 1, $i - $braceStart - 1);
                }
            }
        }

        return null;
    }

    private function normalizePath(string $path): string
    {
        if (str_starts_with($path, '/')) {
            return rtrim($path, DIRECTORY_SEPARATOR);
        }

        return $this->root . '/' . trim($path, DIRECTORY_SEPARATOR);
    }

    private function relativePath(string $path): string
    {
        return ltrim(str_replace($this->root, '', $path), '/');
    }

    private function toSnakeCase(string $value): string
    {
        $snake = strtolower((string) preg_replace('/(?<!^)[A-Z]/', '_$0', $value));
        return $snake;
    }

    private function pluralize(string $value): string
    {
        if (str_ends_with($value, 'y')) {
            return substr($value, 0, -1) . 'ies';
        }

        if (str_ends_with($value, 's')) {
            return $value;
        }

        return $value . 's';
    }

    private function classBasename(string $fqcn): string
    {
        $parts = explode('\\', $fqcn);
        return end($parts) ?: $fqcn;
    }
}

$root = realpath(__DIR__ . '/../../..');
if ($root === false) {
    fwrite(STDERR, "Could not resolve repository root.\n");
    exit(1);
}

$outputDir = null;
foreach (array_slice($argv, 1) as $arg) {
    if (str_starts_with($arg, '--output-dir=')) {
        $outputDir = substr($arg, strlen('--output-dir='));
    }
}

$extractor = new DataModelExtractor($root, $outputDir);
$extractor->run();
