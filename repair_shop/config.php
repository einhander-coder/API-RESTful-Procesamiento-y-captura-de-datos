<?php

declare(strict_types=1);

// Database configuration and connection helper

function get_env(string $key, ?string $default = null): ?string {
    $value = getenv($key);
    if ($value === false) {
        return $default;
    }
    return $value;
}

function get_db(): PDO {
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $dbHost = get_env('DB_HOST', '127.0.0.1');
    $dbPort = get_env('DB_PORT', '3306');
    $dbName = get_env('DB_NAME', 'repair_shop');
    $dbUser = get_env('DB_USER', 'root');
    $dbPass = get_env('DB_PASS', '');

    $dsn = "mysql:host={$dbHost};port={$dbPort};dbname={$dbName};charset=utf8mb4";

    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    $pdo = new PDO($dsn, $dbUser, $dbPass, $options);
    return $pdo;
}

function h(?string $value): string {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}