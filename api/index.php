<?php

putenv('VERCEL=1');
$_ENV['VERCEL'] = '1';
$_SERVER['VERCEL'] = '1';

// Ensure required writable folders exist in /tmp on Vercel serverless environment
$directories = [
    '/tmp/storage',
    '/tmp/storage/app',
    '/tmp/storage/framework',
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/logs',
    '/tmp/bootstrap',
    '/tmp/bootstrap/cache',
];

foreach ($directories as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0777, true);
    }
}

// Check database connection mode
$dbUrl = getenv('DB_URL') ?: ($_ENV['DB_URL'] ?? null);
$extDb = getenv('DB_CONNECTION') ?: ($_ENV['DB_CONNECTION'] ?? null);

if (!$dbUrl && (!$extDb || $extDb === 'sqlite')) {
    // Default to SQLite in /tmp
    $srcDb = __DIR__ . '/../database/database.sqlite';
    $tmpDb = '/tmp/database.sqlite';

    if (!file_exists($tmpDb) || filesize($tmpDb) === 0) {
        if (file_exists($srcDb) && filesize($srcDb) > 0) {
            @copy($srcDb, $tmpDb);
        } else {
            @touch($tmpDb);
        }
    }
    @chmod($tmpDb, 0666);

    putenv('DB_CONNECTION=sqlite');
    $_ENV['DB_CONNECTION'] = 'sqlite';
    $_SERVER['DB_CONNECTION'] = 'sqlite';

    putenv('DB_DATABASE=' . $tmpDb);
    $_ENV['DB_DATABASE'] = $tmpDb;
    $_SERVER['DB_DATABASE'] = $tmpDb;
}

require __DIR__ . '/../public/index.php';