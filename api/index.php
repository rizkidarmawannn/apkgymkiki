<?php

// Ensure required writable folders exist in /tmp on Vercel serverless environment
$directories = [
    '/tmp/storage',
    '/tmp/storage/app',
    '/tmp/storage/framework',
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/logs',
    '/tmp/bootstrap',
    '/tmp/bootstrap/cache',
];

foreach ($directories as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
}

// Copy sqlite database to /tmp if it does not exist yet
$srcDb = __DIR__ . '/../database/database.sqlite';
$tmpDb = '/tmp/database.sqlite';

if (!file_exists($tmpDb)) {
    if (file_exists($srcDb)) {
        @copy($srcDb, $tmpDb);
    } else {
        @touch($tmpDb);
    }
}

// Set environment variables for SQLite
putenv('DB_DATABASE=' . $tmpDb);
$_ENV['DB_DATABASE'] = $tmpDb;
$_SERVER['DB_DATABASE'] = $tmpDb;

require __DIR__ . '/../public/index.php';