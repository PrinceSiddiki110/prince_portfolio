<?php
session_start();
require __DIR__ . '/db.php'; // db.php provides $mysqli

// If already logged in, go to admin panel
if (!empty($_SESSION['admin_id'])) {
  header('Location: admin.php');
  exit;
}

$err = '';

// Handle POST (login)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $token = $_POST['csrf_token'] ?? '';
  if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
    $err = 'Invalid request';
  } else {
    $username = trim((string)($_POST['username'] ?? ''));
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
      $err = 'Invalid username or password';
    } else {
      try {
        $stmt = $mysqli->prepare('SELECT id, password FROM admins WHERE username = ? LIMIT 1');
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $stmt->bind_result($admin_id, $admin_password);
        $admin = null;
        if ($stmt->fetch()) {
          $admin = ['id' => $admin_id, 'password' => $admin_password];
        }
        $stmt->close();

        if ($admin && $password == $admin['password']) {
          session_regenerate_id(true);
          $_SESSION['admin_id'] = $admin['id'];
          // rotate CSRF token after successful login
          unset($_SESSION['csrf_token']);
          header('Location: admin.php');
          exit;
        } else {
          $err = 'Invalid username or password';
        }
      } catch (Exception $e) {
        error_log('Admin login error: ' . $e->getMessage());
        $err = 'An error occurred. Try again later.';
      }
    }
  }
}

// Generate CSRF token for the form (fresh on each GET / failed POST)
$csrf_token = bin2hex(random_bytes(32));
$_SESSION['csrf_token'] = $csrf_token;
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Admin Login</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <style>
    body{font-family:system-ui,Segoe UI,Roboto,Helvetica,Arial;margin:2rem}
    form{max-width:360px}
    label{display:block;margin:0.5rem 0}
    input{width:100%;padding:0.5rem}
    .err{color:#b00020}
  </style>
</head>
<body>
  <h2>Admin Login</h2>
  <?php if ($err): ?>
    <p class="err"><?= htmlspecialchars($err) ?></p>
  <?php endif; ?>
  <form method="post" autocomplete="off" novalidate>
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
    <label>
      Username
      <input name="username" required maxlength="64" autofocus>
    </label>
    <label>
      Password
      <input name="password" type="password" required>
    </label>
    <button type="submit">Login</button>
  </form>
  <p><a href="index.php">Back to site</a></p>
</body>
</html>
