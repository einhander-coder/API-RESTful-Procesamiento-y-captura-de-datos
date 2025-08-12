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

// Load request ensuring permission
if (is_admin()) {
    $stmt = $pdo->prepare(
        'SELECT rr.*, u.name AS technician_name, c.name AS customer_name, d.model AS device_model ' .
        'FROM repair_requests rr ' .
        'JOIN users u ON rr.technician_id = u.id ' .
        'JOIN devices d ON rr.device_id = d.id ' .
        'JOIN customers c ON d.customer_id = c.id ' .
        'WHERE rr.id = ? LIMIT 1'
    );
    $stmt->execute([$id]);
} else {
    $stmt = $pdo->prepare(
        'SELECT rr.*, u.name AS technician_name, c.name AS customer_name, d.model AS device_model ' .
        'FROM repair_requests rr ' .
        'JOIN users u ON rr.technician_id = u.id ' .
        'JOIN devices d ON rr.device_id = d.id ' .
        'JOIN customers c ON d.customer_id = c.id ' .
        'WHERE rr.id = ? AND rr.technician_id = ? LIMIT 1'
    );
    $stmt->execute([$id, $user['id']]);
}
$request = $stmt->fetch();
if (!$request) {
    http_response_code(404);
    echo 'Not found';
    exit;
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = $_POST['status'] ?? $request['status'];
    $resultStatus = trim($_POST['result_status'] ?? $request['result_status']);
    $observations = trim($_POST['observations'] ?? $request['observations']);

    $allowedStatuses = ['pending', 'in_progress', 'completed', 'cancelled'];
    if (!in_array($status, $allowedStatuses, true)) {
        $errors[] = 'Invalid status';
    }

    if (!$errors) {
        $stmt = $pdo->prepare('UPDATE repair_requests SET status = ?, result_status = ?, observations = ?, updated_at = NOW() WHERE id = ?');
        $stmt->execute([$status, $resultStatus, $observations, $id]);
        header('Location: /repair_shop/index.php');
        exit;
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Edit Request #<?= (int)$request['id'] ?></title>
  <link rel="stylesheet" href="/repair_shop/styles.css">
</head>
<body>
  <header class="topbar">
    <div><strong>Edit Request #<?= (int)$request['id'] ?></strong></div>
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

    <div class="card">
      <div class="grid">
        <div><strong>Technician:</strong> <?= h($request['technician_name']) ?></div>
        <div><strong>Owner:</strong> <?= h($request['customer_name']) ?></div>
        <div><strong>Device:</strong> <?= h($request['device_model']) ?></div>
      </div>
    </div>

    <form method="post" class="card">
      <label>Status
        <select name="status">
          <?php foreach (['pending','in_progress','completed','cancelled'] as $s): ?>
            <option value="<?= $s ?>" <?= $request['status'] === $s ? 'selected' : '' ?>><?= ucfirst(str_replace('_',' ', $s)) ?></option>
          <?php endforeach; ?>
        </select>
      </label>

      <label>Result Status
        <input type="text" name="result_status" value="<?= h($request['result_status']) ?>">
      </label>

      <label>Observations
        <textarea name="observations" rows="5"><?= h($request['observations']) ?></textarea>
      </label>

      <button type="submit">Save Changes</button>
    </form>
  </main>
</body>
</html>