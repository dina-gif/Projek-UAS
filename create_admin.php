<?php
require_once __DIR__ . '/config.php';

$errors = [];
$success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? 'Admin');
    $email = trim($_POST['email'] ?? 'admin@example.com');
    $password = $_POST['password'] ?? 'admin123';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email tidak valid.';
    }
    if (strlen($password) < 6) {
        $errors[] = 'Password minimal 6 karakter.';
    }

    if (empty($errors)) {
        // check exists
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);
        if ($stmt->fetch()) {
            $errors[] = 'Email sudah terdaftar.';
        } else {
            $stmt = $pdo->prepare('INSERT INTO users (nama, email, password, role) VALUES (:nama, :email, :password, :role)');
            $stmt->execute(['nama' => $nama, 'email' => $email, 'password' => password_hash($password, PASSWORD_DEFAULT), 'role' => 'Admin']);
            $success = 'Akun admin berhasil dibuat. Silakan login.';
        }
    }
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Buat Admin</title>
  <link rel="stylesheet" href="assets/css/style.css" />
</head>
<body class="page-auth">
  <main class="auth-shell">
    <div class="auth-card">
      <h1>Buat Admin</h1>
      <?php if (!empty($errors)): ?>
        <div class="alert alert-error"><ul><?php foreach ($errors as $e) echo '<li>' . htmlspecialchars($e) . '</li>'; ?></ul></div>
      <?php endif; ?>
      <?php if ($success): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
      <?php endif; ?>
      <form method="post" class="auth-form">
        <label>Nama
          <input type="text" name="nama" value="Admin" />
        </label>
        <label>Email
          <input type="email" name="email" value="admin@example.com" />
        </label>
        <label>Password
          <input type="password" name="password" value="admin123" />
        </label>
        <button class="button button-primary" type="submit">Buat Admin</button>
      </form>
      <p class="form-note"><a href="login.php">Kembali ke login</a></p>
    </div>
  </main>
</body>
</html>