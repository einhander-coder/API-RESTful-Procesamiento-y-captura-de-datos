<?php

declare(strict_types=1);

require_once __DIR__ . '/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = (string)($_POST['password'] ?? '');

    if ($email === '' || $password === '') {
        $error = 'Email and password are required.';
    } else {
        $pdo = get_db();
        $stmt = $pdo->prepare('SELECT id, name, email, role, password_hash FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user'] = [
                'id' => (int)$user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'role' => $user['role'],
            ];
            header('Location: /repair_shop/index.php');
            exit;
        } else {
            $error = 'Invalid credentials.';
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Repair Shop Login</title>
  <link rel="stylesheet" href="/repair_shop/styles.css">
</head>
<body>
  <div class="container">
    <form method="post" class="card narrow">
      <h2>Technician/Admin Login</h2>
      <?php if ($error): ?>
        <div class="alert"><?= h($error) ?></div>
      <?php endif; ?>
      <label>Email
        <input type="email" name="email" required>
      </label>
      <label>Password
        <input type="password" name="password" required>
      </label>
      <button type="submit">Login</button>
    </form>
  </div>
</body>
</html>