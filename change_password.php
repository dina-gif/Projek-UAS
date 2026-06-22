<?php
require_once __DIR__ . '/config.php';
require_login();

$user = current_user();
$errors = [];
$success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current = $_POST['current_password'] ?? '';
    $password = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';

    if ($password === '' || strlen($password) < 6) {
        $errors[] = 'Password baru minimal 6 karakter.';
    }
    if ($password !== $password2) {
        $errors[] = 'Password dan konfirmasi tidak sama.';
    }

    // verify current password
    $stmt = $pdo->prepare('SELECT password FROM users WHERE id = :id LIMIT 1');
    $stmt->execute(['id' => $user['id']]);
    $row = $stmt->fetch();
    if (!$row || !password_verify($current, $row['password'])) {
        $errors[] = 'Password saat ini salah.';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare('UPDATE users SET password = :password WHERE id = :id');
        $stmt->execute(['password' => password_hash($password, PASSWORD_DEFAULT), 'id' => $user['id']]);
        $success = 'Password berhasil diubah.';
    }
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Ubah Password</title>
  <link rel="stylesheet" href="assets/css/style.css" />
</head>
<body>
  <header class="top-nav">
    <div class="container nav-inner">
      <a class="brand" href="index.php">Fisioterapi</a>
      <nav class="nav-menu">
        <a href="patient-dashboard.php">Dashboard</a>
        <a href="logout.php">Logout</a>
      </nav>
    </div>
  </header>
  <main class="section">
    <div class="container">
      <div class="section-header"><h1>Ubah Password</h1></div>
      <?php if (!empty($errors)): ?>
        <div class="alert alert-error"><ul><?php foreach ($errors as $e) echo '<li>' . htmlspecialchars($e) . '</li>'; ?></ul></div>
      <?php endif; ?>
      <?php if ($success): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
      <?php endif; ?>
      <form method="post" class="form-grid">
        <label>Password Saat Ini
          <input type="password" name="current_password" required />
        </label>
        <label>Password Baru
          <input type="password" name="password" required />
        </label>
        <label>Konfirmasi Password Baru
          <input type="password" name="password2" required />
        </label>
        <div class="form-actions">
          <button class="button button-primary" type="submit">Simpan</button>
        </div>
      </form>
    </div>
  </main>
</body>
</html>