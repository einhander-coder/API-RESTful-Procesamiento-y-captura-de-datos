<?php

declare(strict_types=1);

require_once __DIR__ . '/auth.php';
require_login();

$pdo = get_db();
$user = current_user();

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    http_response_code(400);
    echo 'Bad request';
    exit;
}

if (is_admin()) {
    $stmt = $pdo->prepare(
        'SELECT rr.*, u.name AS technician_name, c.name AS customer_name, c.phone AS customer_phone, c.email AS customer_email, d.model AS device_model, d.serial AS device_serial ' .
        'FROM repair_requests rr ' .
        'JOIN users u ON rr.technician_id = u.id ' .
        'JOIN devices d ON rr.device_id = d.id ' .
        'JOIN customers c ON d.customer_id = c.id ' .
        'WHERE rr.id = ? LIMIT 1'
    );
    $stmt->execute([$id]);
} else {
    $stmt = $pdo->prepare(
        'SELECT rr.*, u.name AS technician_name, c.name AS customer_name, c.phone AS customer_phone, c.email AS customer_email, d.model AS device_model, d.serial AS device_serial ' .
        'FROM repair_requests rr ' .
        'JOIN users u ON rr.technician_id = u.id ' .
        'JOIN devices d ON rr.device_id = d.id ' .
        'JOIN customers c ON d.customer_id = c.id ' .
        'WHERE rr.id = ? AND rr.technician_id = ? LIMIT 1'
    );
    $stmt->execute([$id, $user['id']]);
}
$rec = $stmt->fetch();
if (!$rec) {
    http_response_code(404);
    echo 'Not found';
    exit;
}

function wrap_text(string $text, int $width = 40): string {
    $lines = [];
    foreach (preg_split('/\r?\n/', $text) as $line) {
        $lines = array_merge($lines, explode("\n", wordwrap($line, $width, "\n", true)));
    }
    return implode("\n", $lines);
}

function line(string $left, string $right = '', int $width = 40): string {
    $left = mb_strimwidth($left, 0, $width, '', 'UTF-8');
    $right = mb_strimwidth($right, 0, $width, '', 'UTF-8');
    $space = max(1, $width - mb_strlen($left) - mb_strlen($right));
    return $left . str_repeat(' ', $space) . $right;
}

$w = 40;
$out = [];
$out[] = str_pad('REPAIR REQUEST #' . (int)$rec['id'], $w, ' ', STR_PAD_BOTH);
$out[] = str_repeat('-', $w);
$out[] = 'Tech: ' . $rec['technician_name'];
$out[] = 'Owner: ' . $rec['customer_name'];
$out[] = 'Phone: ' . ($rec['customer_phone'] ?? '');
$out[] = 'Email: ' . ($rec['customer_email'] ?? '');
$out[] = 'Device: ' . $rec['device_model'];
$out[] = 'Serial: ' . ($rec['device_serial'] ?? '');
$out[] = line('Status:', (string)$rec['status'], $w);
$out[] = str_repeat('-', $w);
$out[] = 'Problem:';
$out[] = wrap_text((string)$rec['problem_report'], $w);
$out[] = str_repeat('-', $w);
$out[] = 'Result:';
$out[] = wrap_text((string)$rec['result_status'], $w);
$out[] = str_repeat('-', $w);
$out[] = 'Observations:';
$out[] = wrap_text((string)$rec['observations'], $w);
$out[] = str_repeat('-', $w);
$out[] = line('Created:', (string)$rec['created_at'], $w);
$out[] = line('Updated:', (string)$rec['updated_at'], $w);
$out[] = str_repeat('=', $w);
$out[] = str_pad('Thank you!', $w, ' ', STR_PAD_BOTH);
$out[] = str_repeat('=', $w);

$text = implode("\n", $out) . "\n";

header('Content-Type: text/plain; charset=utf-8');
header('Content-Disposition: attachment; filename="repair-request-' . (int)$rec['id'] . '.txt"');
header('Content-Length: ' . strlen($text));
echo $text;