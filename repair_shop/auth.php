<?php

declare(strict_types=1);

require_once __DIR__ . '/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function require_login(?string $role = null): void {
    if (empty($_SESSION['user'])) {
        header('Location: /repair_shop/login.php');
        exit;
    }
    if ($role !== null && ($_SESSION['user']['role'] ?? null) !== $role) {
        http_response_code(403);
        echo 'Forbidden';
        exit;
    }
}

function current_user(): ?array {
    return $_SESSION['user'] ?? null;
}

function is_admin(): bool {
    return (current_user()['role'] ?? null) === 'admin';
}

function is_technician(): bool {
    return (current_user()['role'] ?? null) === 'tech';
}