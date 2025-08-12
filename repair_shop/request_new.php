<?php

declare(strict_types=1);

require_once __DIR__ . '/auth.php';
require_login();

$pdo = get_db();
$user = current_user();

$errors = [];
$successId = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customerName = trim($_POST['customer_name'] ?? '');
    $customerPhone = trim($_POST['customer_phone'] ?? '');
    $customerEmail = trim($_POST['customer_email'] ?? '');
    $deviceModel = trim($_POST['device_model'] ?? '');
    $deviceSerial = trim($_POST['device_serial'] ?? '');
    $problemReport = trim($_POST['problem_report'] ?? '');
    $technicianId = is_admin() ? (int)($_POST['technician_id'] ?? 0) : (int)$user['id'];

    if ($customerName === '') $errors[] = 'Customer name is required';
    if ($deviceModel === '') $errors[] = 'Device model is required';
    if ($problemReport === '') $errors[] = 'Problem report is required';
    if ($technicianId <= 0) $errors[] = 'Technician is required';

    if (!$errors) {
        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare('INSERT INTO customers (name, phone, email) VALUES (?, ?, ?)');
            $stmt->execute([$customerName, $customerPhone, $customerEmail]);
            $customerId = (int)$pdo->lastInsertId();

            $stmt = $pdo->prepare('INSERT INTO devices (customer_id, model, serial) VALUES (?, ?, ?)');
            $stmt->execute([$customerId, $deviceModel, $deviceSerial]);
            $deviceId = (int)$pdo->lastInsertId();

            $stmt = $pdo->prepare("INSERT INTO repair_requests (device_id, technician_id, status, problem_report, result_status, observations, created_at, updated_at) VALUES (?, ?, 'pending', ?, '', '', NOW(), NOW())");
            $stmt->execute([$deviceId, $technicianId, $problemReport]);
            $successId = (int)$pdo->lastInsertId();
            $pdo->commit();
            header('Location: /repair_shop/index.php');
            exit;
        } catch (Throwable $e) {
            $pdo->rollBack();
            $errors[] = 'Failed to create request: ' . $e->getMessage();
        }
    }
}

$techOptions = [];
if (is_admin()) {
    $techOptions = $pdo->query("SELECT id, name FROM users WHERE role = 'tech' ORDER BY name")->fetchAll();
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>New Repair Request</title>
  <link rel="stylesheet" href="/repair_shop/styles.css">
</head>
<body>
  <header class="topbar">
    <div><strong>New Request</strong></div>
    <nav>
      <a href="/repair_shop/index.php">Back</a>
      <a href="/repair_shop/logout.php">Logout</a>
    </nav>
  </header>
  <main class="container">
    <?php if ($errors): ?>
      <div class="alert">
        <?php foreach ($errors as $err): ?>
          <div><?= h($err) ?></div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <form method="post" class="card">
      <?php if (is_admin()): ?>
      <label>Technician
        <select name="technician_id" required>
          <option value="">Select technician</option>
          <?php foreach ($techOptions as $t): ?>
            <option value="<?= (int)$t['id'] ?>"><?= h($t['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </label>
      <?php endif; ?>

      <h3>Owner Information</h3>
      <label>Owner Name
        <input type="text" name="customer_name" required>
      </label>
      <div class="grid">
        <label>Phone
          <input type="text" name="customer_phone">
        </label>
        <label>Email
          <input type="email" name="customer_email">
        </label>
      </div>

      <h3>Device Information</h3>
      <div class="grid">
        <label>Device Model
          <input type="text" name="device_model" required>
        </label>
        <label>Serial Number
          <input type="text" name="device_serial">
        </label>
      </div>

      <h3>Problem Reporting</h3>
      <label>
        <textarea name="problem_report" rows="5" required></textarea>
      </label>

      <button type="submit">Create Request</button>
    </form>
  </main>
</body>
</html>