<?php

declare(strict_types=1);

/**
 * Apply every SQL migration in `migrations/` against the hub database.
 *
 * Migrations are sorted lexicographically and executed top-to-bottom.
 * Each `.sql` file is split on semicolons; empty statements are
 * skipped. Errors are reported but do not abort the run — they're
 * usually idempotent re-applies (e.g. "table already exists").
 *
 * @package Phlex\Hub
 * @since 0.1.0
 */

use Phlex\Hub\Common\Database\ConnectionPool;

require_once __DIR__ . '/../vendor/autoload.php';

$configPath = __DIR__ . '/../config/database.php';
ConnectionPool::init($configPath);

$db = ConnectionPool::getConnection('mysql');

$migrationsDir = __DIR__ . '/../migrations';
$files = glob($migrationsDir . '/*.sql') ?: [];
sort($files);

if ($files === []) {
    echo "No SQL migrations found in {$migrationsDir}. (Real migrations land in B.6.)\n";
    exit(0);
}

foreach ($files as $file) {
    $sql = file_get_contents($file);
    if ($sql === false) {
        echo "Failed to read migration: " . basename($file) . "\n";
        continue;
    }
    echo "Running migration: " . basename($file) . "\n";

    $statements = array_filter(array_map('trim', explode(';', $sql)));
    foreach ($statements as $statement) {
        if ($statement === '' || str_starts_with($statement, '--')) {
            continue;
        }
        try {
            $db->query($statement);
        } catch (\Throwable $e) {
            echo "  Warning: " . $e->getMessage() . "\n";
        }
    }
}

echo "Migrations complete.\n";
