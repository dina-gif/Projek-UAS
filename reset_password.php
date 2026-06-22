<?php
require_once __DIR__ . '/config.php';

$token = $_GET['token'] ?? $_POST['token'] ?? null;
$errors = [];
$message = null;

if (!$token) {
    $errors[] = 'Token reset tidak diberikan.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // handled below
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'] ?? '';
    $password = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';

    if ($password === '' || strlen($password) < 6) {
        $errors[] = 'Password minimal 6 karakter.';
    }
    if ($password !== $password2) {
        $errors[] = 'Password dan konfirmasi tidak sama.';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare('SELECT * FROM password_resets WHERE token = :token LIMIT 1');
        $stmt->execute(['token' => $token]);
        $row = $stmt->fetch();
        if (!$row) {
            $errors[] = 'Token tidak valid.';
        } elseif (strtotime($row['expires_at']) < time()) {
            $errors[] = 'Token telah kadaluarsa.';
        } else {
            $email = $row['email'];
            $stmt = $pdo->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch();
            if (!$user) {
                $errors[] = 'Pengguna tidak ditemukan.';
            } else {
                $stmt = $pdo->prepare('UPDATE users SET password = :password WHERE id = :id');
                $stmt->execute(['password' => password_hash($password, PASSWORD_DEFAULT), 'id' => $user['id']]);
                $stmt = $pdo->prepare('DELETE FROM password_resets WHERE token = :token');
                $stmt->execute(['token' => $token]);
                $message = 'Password berhasil diperbarui. Silakan login.';
            }
        }
    }
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Reset Password</title>
  <link rel="stylesheet" href="assets/css/style.css" />
</head>
<body class="page-auth">
  <main class="auth-shell">
    <div class="auth-card">
      <h1>Reset Password</h1>
      <?php if (!empty($errors)): ?>
        <div class="alert alert-error"><ul><?php foreach ($errors as $e) echo '<li>' . htmlspecialchars($e) . '</li>'; ?></ul></div>
      <?php endif; ?>
      <?php if ($message): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
        <p><a class="button button-primary" href="login.php">Login</a></p>
      <?php else: ?>
        <form method="post" class="auth-form">
          <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>" />
          <label>Password Baru
            <input type="password" name="password" required />
          </label>
          <label>Konfirmasi Password
            <input type="password" name="password2" required />
          </label>
          <button class="button button-primary" type="submit">Atur Ulang Password</button>
        </form>
      <?php endif; ?>
      <p class="form-note"><a href="login.php">Kembali ke login</a></p>
    </div>
  </main>
</body>
</html>