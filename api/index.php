<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

define('LARAVEL_START', microtime(true));

// 1. Pastikan folder writable di /tmp tersedia untuk Vercel Serverless
$tmpDirs = [
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/app/public',
    '/tmp/bootstrap/cache',
];

foreach ($tmpDirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
}

// 2. Setup SQLite database di /tmp jika menggunakan driver sqlite
$dbConn = getenv('DB_CONNECTION') ?: ($_ENV['DB_CONNECTION'] ?? 'sqlite');
$isNewSqlite = false;

if ($dbConn === 'sqlite') {
    $tmpDb = '/tmp/database.sqlite';
    putenv("DB_CONNECTION=sqlite");
    putenv("DB_DATABASE={$tmpDb}");
    $_ENV['DB_CONNECTION'] = 'sqlite';
    $_ENV['DB_DATABASE'] = $tmpDb;

    if (!file_exists($tmpDb)) {
        $sourceDb = __DIR__ . '/../database/database.sqlite';
        if (file_exists($sourceDb) && filesize($sourceDb) > 0) {
            @copy($sourceDb, $tmpDb);
        } else {
            @touch($tmpDb);
            $isNewSqlite = true;
        }
    }
}

// 3. Register autoloader & load Laravel app
require __DIR__ . '/../vendor/autoload.php';

/** @var Application $app */
$app = require_once __DIR__ . '/../bootstrap/app.php';

// 4. Jalankan migrasi & seeder jika database baru dibuat di /tmp
if ($isNewSqlite) {
    try {
        Artisan::call('migrate', ['--force' => true]);
        Artisan::call('db:seed', ['--force' => true]);
    } catch (\Throwable $e) {
        // Abaikan jika migrasi sudah ada atau gagal non-kritis
    }
}

// 5. Handle HTTP Request
$app->handleRequest(Request::capture());
