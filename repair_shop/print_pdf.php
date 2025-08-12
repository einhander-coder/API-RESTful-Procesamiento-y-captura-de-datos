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

// Fetch record
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

ob_start();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <style>
    body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; }
    .title { font-size: 18px; font-weight: bold; margin-bottom: 10px; }
    .row { display: flex; gap: 16px; margin: 6px 0; }
    .label { color: #555; width: 160px; }
    .value { flex: 1; }
    .box { border: 1px solid #ccc; padding: 10px; margin-top: 10px; }
  </style>
</head>
<body>
  <div class="title">Repair Request #<?= (int)$rec['id'] ?></div>
  <div class="row"><div class="label">Technician</div><div class="value"><?= h($rec['technician_name']) ?></div></div>
  <div class="row"><div class="label">Owner</div><div class="value"><?= h($rec['customer_name']) ?> (<?= h($rec['customer_phone']) ?>, <?= h($rec['customer_email']) ?>)</div></div>
  <div class="row"><div class="label">Device</div><div class="value">Model: <?= h($rec['device_model']) ?>, Serial: <?= h($rec['device_serial']) ?></div></div>
  <div class="row"><div class="label">Status</div><div class="value"><?= h($rec['status']) ?></div></div>
  <div class="box">
    <div class="label">Problem Reporting</div>
    <div><?= nl2br(h($rec['problem_report'])) ?></div>
  </div>
  <div class="box">
    <div class="label">Result Status</div>
    <div><?= nl2br(h($rec['result_status'])) ?></div>
  </div>
  <div class="box">
    <div class="label">Observations</div>
    <div><?= nl2br(h($rec['observations'])) ?></div>
  </div>
  <div class="row"><div class="label">Created</div><div class="value"><?= h($rec['created_at']) ?></div></div>
  <div class="row"><div class="label">Updated</div><div class="value"><?= h($rec['updated_at']) ?></div></div>
</body>
</html>
<?php
$html = ob_get_clean();

$dompdfAvailable = false;
try {
    $autoload = __DIR__ . '/vendor/autoload.php';
    if (file_exists($autoload)) {
        require_once $autoload;
        if (class_exists('Dompdf\\Dompdf')) {
            $dompdfAvailable = true;
        }
    }
} catch (Throwable $e) {
    $dompdfAvailable = false;
}

if ($dompdfAvailable) {
    $dompdf = new Dompdf\Dompdf([ 'isRemoteEnabled' => true ]);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    $dompdf->stream('repair-request-' . (int)$rec['id'] . '.pdf', ['Attachment' => true]);
    exit;
}

// Fallback: serve HTML and prompt user to use browser Print to PDF
header('Content-Type: text/html; charset=utf-8');
echo $html;