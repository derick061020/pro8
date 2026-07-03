<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TenantMigrationsMakeIdempotentCommand extends Command
{
    protected $signature = 'tenant:migrations-make-idempotent
        {--dry-run : Mostrar cambios sin escribir archivos}
        {--file= : Procesar solo este archivo (ruta absoluta o nombre)}
        {--limit= : Procesar solo los primeros N archivos (para pruebas)}';

    protected $description = 'Patcha en masa todas las migraciones de database/migrations/tenant/ para que sean idempotentes (fallback por archivo)';

    private array $stats = [
        'files_total' => 0,
        'files_modified' => 0,
        'files_skipped_already_patched' => 0,
        'schema_create_wrapped' => 0,
        'schema_table_wrapped' => 0,
        'columns_wrapped_with_hascolumn' => 0,
        'drops_wrapped_with_hascolumn' => 0,
        'insert_to_insertorignore' => 0,
        'use_db_added' => 0,
    ];

    private const COLUMN_TYPES = [
        'string', 'char', 'text', 'mediumText', 'longText', 'tinyText',
        'integer', 'bigInteger', 'mediumInteger', 'smallInteger', 'tinyInteger',
        'unsignedInteger', 'unsignedBigInteger', 'unsignedMediumInteger',
        'unsignedSmallInteger', 'unsignedTinyInteger',
        'boolean', 'decimal', 'double', 'float',
        'date', 'dateTime', 'dateTimeTz', 'time', 'timeTz', 'timestamp', 'timestampTz', 'year',
        'json', 'jsonb', 'binary', 'uuid', 'ulid', 'ipAddress', 'macAddress',
        'enum', 'set', 'foreignId', 'foreignUuid', 'foreignUlid',
        'rememberToken', 'softDeletes', 'softDeletesTz',
    ];

    public function handle()
    {
        $dryRun = (bool) $this->option('dry-run');
        $fileOption = $this->option('file');
        $limit = $this->option('limit') ? (int) $this->option('limit') : null;

        $files = $this->collectFiles($fileOption, $limit);
        $this->info(sprintf('Procesando %d archivos...', count($files)));

        foreach ($files as $file) {
            $this->stats['files_total']++;
            $this->processFile($file, $dryRun);
        }

        $this->line('');
        $this->info('=== Resumen ===');
        foreach ($this->stats as $key => $value) {
            $this->line(sprintf('  %s: %d', $key, $value));
        }

        if ($dryRun) {
            $this->comment('Ejecución en --dry-run. Ningún archivo fue modificado.');
        }

        return 0;
    }

    private function collectFiles(?string $fileOption, ?int $limit): array
    {
        $base = database_path('migrations/tenant');

        if ($fileOption) {
            $path = file_exists($fileOption) ? $fileOption : $base . '/' . ltrim($fileOption, '/');
            if (! file_exists($path)) {
                $this->error("Archivo no encontrado: {$fileOption}");
                exit(1);
            }
            return [$path];
        }

        $files = glob($base . '/*.php') ?: [];
        sort($files);

        return $limit ? array_slice($files, 0, $limit) : $files;
    }

    private function processFile(string $path, bool $dryRun): void
    {
        $original = file_get_contents($path);
        $content = $original;

        $alreadyPatched = str_contains($content, 'Schema::hasTable')
            && str_contains($content, 'tenant:migrations-make-idempotent:patched');

        if ($alreadyPatched) {
            $this->stats['files_skipped_already_patched']++;
            return;
        }

        $content = $this->wrapSchemaCreate($content);
        $content = $this->wrapSchemaTable($content);
        $content = $this->convertInsertToInsertOrIgnore($content);
        $content = $this->ensureDbImport($content);

        if ($content === $original) {
            return;
        }

        $content = $this->addPatchedMarker($content);

        $this->stats['files_modified']++;
        $this->line('  [PATCH] ' . basename($path));

        if (! $dryRun) {
            file_put_contents($path, $content);
        }
    }

    private function wrapSchemaCreate(string $content): string
    {
        return $this->applyBraceWrap(
            $content,
            'Schema::create',
            function (string $tableName, string $indent) {
                $this->stats['schema_create_wrapped']++;
                return [
                    "if (! Schema::hasTable('{$tableName}')) {\n{$indent}    ",
                    "\n{$indent}}",
                ];
            }
        );
    }

    private function wrapSchemaTable(string $content): string
    {
        $content = $this->addHasColumnChecks($content);

        return $this->applyBraceWrap(
            $content,
            'Schema::table',
            function (string $tableName, string $indent) {
                $this->stats['schema_table_wrapped']++;
                $prefix = "try {\n{$indent}    ";
                $suffix = "\n{$indent}} catch (\\Illuminate\\Database\\QueryException \$e) {\n"
                    . "{$indent}    if (! preg_match('/Duplicate (column|key|entry)|already exists/i', \$e->getMessage())) {\n"
                    . "{$indent}        throw \$e;\n"
                    . "{$indent}    }\n"
                    . "{$indent}}";
                return [$prefix, $suffix];
            }
        );
    }

    private function addHasColumnChecks(string $content): string
    {
        $pattern = '/Schema::table\(\s*[\'"](\w+)[\'"]\s*,\s*function\s*\(/';
        $offset = 0;

        while (preg_match($pattern, $content, $matches, PREG_OFFSET_CAPTURE, $offset)) {
            $callStart = $matches[0][1];
            $tableName = $matches[1][0];

            $bodyStart = strpos($content, '{', $callStart + strlen($matches[0][0]));
            if ($bodyStart === false) {
                $offset = $callStart + strlen($matches[0][0]);
                continue;
            }
            $bodyStart++;

            $bodyEnd = $this->findMatchingBrace($content, $bodyStart - 1);
            if ($bodyEnd === false) {
                $offset = $callStart + strlen($matches[0][0]);
                continue;
            }

            $body = substr($content, $bodyStart, $bodyEnd - $bodyStart);
            $newBody = $this->wrapColumnLines($body, $tableName);

            if ($newBody !== $body) {
                $content = substr($content, 0, $bodyStart) . $newBody . substr($content, $bodyEnd);
                $offset = $bodyStart + strlen($newBody);
            } else {
                $offset = $bodyEnd;
            }
        }

        return $content;
    }

    private function wrapColumnLines(string $body, string $tableName): string
    {
        $columnTypes = implode('|', array_map('preg_quote', self::COLUMN_TYPES));
        $lines = explode("\n", $body);
        $out = [];
        $i = 0;
        $total = count($lines);

        while ($i < $total) {
            $line = $lines[$i];

            if (preg_match('/^(\s*)\$table->(' . $columnTypes . ')\(\s*[\'"](\w+)[\'"]/', $line, $m)) {
                $indent = $m[1];
                $col = $m[3];
                [$stmtLines, $consumed] = $this->collectStatement($lines, $i);

                $out[] = $indent . "if (! Schema::hasColumn('{$tableName}', '{$col}')) {";
                foreach ($stmtLines as $idx => $stmtLine) {
                    $out[] = $idx === 0
                        ? $indent . "    " . ltrim($stmtLine)
                        : $indent . "    " . $stmtLine;
                }
                $out[] = $indent . "}";
                $this->stats['columns_wrapped_with_hascolumn']++;
                $i += $consumed;
                continue;
            }

            if (preg_match('/^(\s*)\$table->dropColumn\(\s*\[?\s*[\'"](\w+)[\'"]/', $line, $m)) {
                $indent = $m[1];
                $col = $m[2];
                [$stmtLines, $consumed] = $this->collectStatement($lines, $i);

                $out[] = $indent . "if (Schema::hasColumn('{$tableName}', '{$col}')) {";
                foreach ($stmtLines as $idx => $stmtLine) {
                    $out[] = $idx === 0
                        ? $indent . "    " . ltrim($stmtLine)
                        : $indent . "    " . $stmtLine;
                }
                $out[] = $indent . "}";
                $this->stats['drops_wrapped_with_hascolumn']++;
                $i += $consumed;
                continue;
            }

            $out[] = $line;
            $i++;
        }

        return implode("\n", $out);
    }

    private function collectStatement(array $lines, int $start): array
    {
        $collected = [];
        $count = count($lines);
        $i = $start;

        while ($i < $count) {
            $line = $lines[$i];
            $collected[] = $line;

            $stripped = preg_replace('/[\'"][^\'"]*[\'"]/', '', $line);
            $stripped = preg_replace('#//.*$#', '', $stripped);
            if (str_contains($stripped, ';')) {
                break;
            }
            $i++;
        }

        return [$collected, count($collected)];
    }

    private function findMatchingBrace(string $content, int $openBracePos): int|false
    {
        $len = strlen($content);
        $depth = 0;
        $inString = false;
        $stringChar = '';

        for ($i = $openBracePos; $i < $len; $i++) {
            $ch = $content[$i];
            $prev = $i > 0 ? $content[$i - 1] : '';

            if ($inString) {
                if ($ch === $stringChar && $prev !== '\\') {
                    $inString = false;
                }
                continue;
            }
            if ($ch === '"' || $ch === "'") {
                $inString = true;
                $stringChar = $ch;
                continue;
            }
            if ($ch === '{') {
                $depth++;
            } elseif ($ch === '}') {
                $depth--;
                if ($depth === 0) {
                    return $i;
                }
            }
        }
        return false;
    }

    private function applyBraceWrap(string $content, string $callPrefix, callable $wrapBuilder): string
    {
        $pattern = '/' . preg_quote($callPrefix, '/') . '\(\s*[\'"](\w+)[\'"]\s*,\s*function\s*\(/';
        $offset = 0;

        while (preg_match($pattern, $content, $matches, PREG_OFFSET_CAPTURE, $offset)) {
            $callStart = $matches[0][1];
            $tableName = $matches[1][0];

            $lineStart = strrpos(substr($content, 0, $callStart), "\n");
            $lineStart = $lineStart === false ? 0 : $lineStart + 1;
            $before = substr($content, $lineStart, $callStart - $lineStart);

            if (preg_match('/^\s*(if\s*\(|try\s*\{|}\s*else)/', $before)) {
                $offset = $callStart + strlen($matches[0][0]);
                continue;
            }

            $end = $this->findCallEnd($content, $callStart);
            if ($end === false) {
                $offset = $callStart + strlen($matches[0][0]);
                continue;
            }

            $indent = $before;

            [$prefix, $suffix] = $wrapBuilder($tableName, $indent);
            $callBody = substr($content, $callStart, $end - $callStart);

            $wrapped = $prefix . $callBody . $suffix;

            $content = substr($content, 0, $callStart) . $wrapped . substr($content, $end);
            $offset = $callStart + strlen($wrapped);
        }

        return $content;
    }

    private function findCallEnd(string $content, int $start): int|false
    {
        $len = strlen($content);
        $i = $start;
        $parenDepth = 0;
        $braceDepth = 0;
        $inString = false;
        $stringChar = '';
        $started = false;

        while ($i < $len) {
            $ch = $content[$i];
            $prev = $i > 0 ? $content[$i - 1] : '';

            if ($inString) {
                if ($ch === $stringChar && $prev !== '\\') {
                    $inString = false;
                }
                $i++;
                continue;
            }

            if ($ch === '"' || $ch === "'") {
                $inString = true;
                $stringChar = $ch;
                $i++;
                continue;
            }

            if ($ch === '(') {
                $parenDepth++;
                $started = true;
            } elseif ($ch === ')') {
                $parenDepth--;
                if ($started && $parenDepth === 0) {
                    if ($i + 1 < $len && $content[$i + 1] === ';') {
                        return $i + 2;
                    }
                    return $i + 1;
                }
            } elseif ($ch === '{') {
                $braceDepth++;
            } elseif ($ch === '}') {
                $braceDepth--;
            }

            $i++;
        }

        return false;
    }

    private function convertInsertToInsertOrIgnore(string $content): string
    {
        $count = 0;
        $new = preg_replace(
            '/(DB::table\(\s*[\'"]\w+[\'"]\s*\)(?:\s*->\s*\w+\([^)]*\))*\s*)->\s*insert\s*\(/',
            '$1->insertOrIgnore(',
            $content,
            -1,
            $count
        );
        $this->stats['insert_to_insertorignore'] += $count;
        return $new ?? $content;
    }

    private function ensureDbImport(string $content): string
    {
        if (! preg_match('/\bDB::/', $content)) {
            return $content;
        }
        if (preg_match('/use\s+Illuminate\\\\Support\\\\Facades\\\\DB\s*;/', $content)) {
            return $content;
        }
        if (preg_match('/(use\s+Illuminate\\\\[^;]+;\s*\n)/', $content, $m, PREG_OFFSET_CAPTURE)) {
            $insertAt = $m[0][1];
            $content = substr($content, 0, $insertAt)
                . "use Illuminate\\Support\\Facades\\DB;\n"
                . substr($content, $insertAt);
            $this->stats['use_db_added']++;
        }
        return $content;
    }

    private function addPatchedMarker(string $content): string
    {
        $marker = "// tenant:migrations-make-idempotent:patched\n";
        if (str_contains($content, $marker)) {
            return $content;
        }
        return preg_replace('/^<\?php\s*\n/', "<?php\n{$marker}", $content, 1);
    }
}
