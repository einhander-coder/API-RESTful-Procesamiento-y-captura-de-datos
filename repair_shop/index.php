<?php

declare(strict_types=1);

require_once __DIR__ . '/auth.php';
require_login();

$pdo = get_db();
$user = current_user();

// Stats
if (is_admin()) {
    $totalRequests = (int)$pdo->query('SELECT COUNT(*) FROM repair_requests')->fetchColumn();
    $pendingRequests = (int)$pdo->query("SELECT COUNT(*) FROM repair_requests WHERE status = 'pending'")->fetchColumn();
} else {
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM repair_requests WHERE technician_id = ?');
    $stmt->execute([$user['id']]);
    $totalRequests = (int)$stmt->fetchColumn();

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM repair_requests WHERE technician_id = ? AND status = 'pending'");
    $stmt->execute([$user['id']]);
    $pendingRequests = (int)$stmt->fetchColumn();
}

// List
if (is_admin()) {
    $listStmt = $pdo->query(
        'SELECT rr.id, rr.status, rr.problem_report, rr.result_status, rr.observations, rr.created_at, ' .
        'u.name AS technician_name, c.name AS customer_name, d.model AS device_model ' .
        'FROM repair_requests rr ' .
        'JOIN users u ON rr.technician_id = u.id ' .
        'JOIN devices d ON rr.device_id = d.id ' .
        'JOIN customers c ON d.customer_id = c.id ' .
        'ORDER BY rr.created_at DESC LIMIT 200'
    );
} else {
    $listStmt = $pdo->prepare(
        'SELECT rr.id, rr.status, rr.problem_report, rr.result_status, rr.observations, rr.created_at, ' .
        'u.name AS technician_name, c.name AS customer_name, d.model AS device_model ' .
        'FROM repair_requests rr ' .
        'JOIN users u ON rr.technician_id = u.id ' .
        'JOIN devices d ON rr.device_id = d.id ' .
        'JOIN customers c ON d.customer_id = c.id ' .
        'WHERE rr.technician_id = ? ' .
        'ORDER BY rr.created_at DESC LIMIT 200'
    );
    $listStmt->execute([$user['id']]);
}
$requests = $isStmt = isset($listStmt) && $listStmt instanceof PDOStatement ? $listStmt->fetchAll() : [];
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Repair Shop Admin Panel</title>
  <link rel="stylesheet" href="/repair_shop/styles.css">
</head>
<body>
  <header class="topbar">
    <div>
      <strong>Repair Admin</strong>
    </div>
    <nav>
      <span>Signed in as: <?= h($user['name']) ?> (<?= h($user['role']) ?>)</span>
      <a href="/repair_shop/request_new.php">New Request</a>
      <a href="/repair_shop/logout.php">Logout</a>
    </nav>
  </header>
  <main class="container">
    <div class="grid">
      <div class="stat">
        <div class="stat-label">Total Requests</div>
        <div class="stat-value"><?= $totalRequests ?></div>
      </div>
      <div class="stat">
        <div class="stat-label">Pending Requests</div>
        <div class="stat-value"><?= $pendingRequests ?></div>
      </div>
    </div>

    <h2>Recent Requests</h2>
    <div class="table-responsive">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Technician</th>
            <th>Owner</th>
            <th>Device Model</th>
            <th>Status</th>
            <th>Problem</th>
            <th>Result</th>
            <th>Observations</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($requests as $row): ?>
          <tr>
            <td>#<?= (int)$row['id'] ?></td>
            <td><?= h($row['technician_name']) ?></td>
            <td><?= h($row['customer_name']) ?></td>
            <td><?= h($row['device_model']) ?></td>
            <td><span class="badge <?= 'status-' . h($row['status']) ?>"><?= h($row['status']) ?></span></td>
            <td class="clip"><?= h($row['problem_report']) ?></td>
            <td class="clip"><?= h($row['result_status']) ?></td>
            <td class="clip"><?= h($row['observations']) ?></td>
            <td>
              <a href="/repair_shop/request_edit.php?id=<?= (int)$row['id'] ?>">Edit</a>
              <a href="/repair_shop/print_pdf.php?id=<?= (int)$row['id'] ?>" target="_blank">PDF</a>
              <a href="/repair_shop/print_pos.php?id=<?= (int)$row['id'] ?>" target="_blank">POS</a>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </main>
</body>
</html>