<?php

declare(strict_types=1);

require_once __DIR__ . '/config.php';

$pdo = get_db();

$adminName = 'Admin';
$adminEmail = 'admin@example.com';
$adminPass = 'admin123';
$techName = 'Tech One';
$techEmail = 'tech@example.com';
$techPass = 'tech123';

$pdo->beginTransaction();
try {
    $pdo->exec("INSERT IGNORE INTO users (id, name, email, role, password_hash) VALUES (1, 'placeholder', 'placeholder@example.com', 'tech', 'x')");
    $pdo->exec("DELETE FROM users WHERE email IN ('placeholder@example.com')");

    $stmt = $pdo->prepare('INSERT INTO users (name, email, role, password_hash) VALUES (?, ?, ?, ?)');
    $stmt->execute([$adminName, $adminEmail, 'admin', password_hash($adminPass, PASSWORD_DEFAULT)]);
    $stmt->execute([$techName, $techEmail, 'tech', password_hash($techPass, PASSWORD_DEFAULT)]);

    $pdo->commit();
    echo "Seed complete.\n";
    echo "Admin: {$adminEmail} / {$adminPass}\n";
    echo "Tech: {$techEmail} / {$techPass}\n";
} catch (Throwable $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo 'Seed failed: ' . $e->getMessage();
}