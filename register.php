<?php
require_once __DIR__ . '/config.php';

if (is_logged_in()) {
    header('Location: index.php');
    exit;
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($nama === '' || $email === '' || $password === '') {
        $errors[] = 'Semua bidang harus diisi.';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Format email tidak valid.';
    }

    if (strlen($password) < 6) {
        $errors[] = 'Password minimal 6 karakter.';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = :email');
        $stmt->execute(['email' => $email]);

        if ($stmt->fetch()) {
            $errors[] = 'Email sudah terdaftar.';
        }
    }

    if (empty($errors)) {
        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare('INSERT INTO users (nama, email, password, role) VALUES (:nama, :email, :password, :role)');
            $stmt->execute([
                'nama' => $nama,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'role' => 'Pasien',
            ]);
            $userId = $pdo->lastInsertId();

            $stmt = $pdo->prepare('INSERT INTO pasien (user_id, nama) VALUES (:user_id, :nama)');
            $stmt->execute([
                'user_id' => $userId,
                'nama' => $nama,
            ]);

            $pdo->commit();
            $_SESSION['user'] = [
                'id' => $userId,
                'nama' => $nama,
                'email' => $email,
                'role' => 'Pasien',
            ];
            header('Location: index.php');
            exit;
        } catch (PDOException $e) {
            $pdo->rollBack();
            $errors[] = 'Pendaftaran gagal. Silakan coba lagi.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Register - Sistem Informasi Fisioterapi</title>
  <link rel="stylesheet" href="assets/css/style.css" />
</head>
<body class="page-auth">
  <main class="auth-shell">
    <div class="auth-card">
      <a class="brand brand-center" href="index.php">Fisioterapi</a>
      <h1>Daftar Pasien Baru</h1>
      <p>Buat akun untuk mulai booking terapi dan melihat riwayat layanan.</p>
      <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
          <ul>
            <?php foreach ($errors as $error): ?>
              <li><?php echo htmlspecialchars($error); ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>
      <form action="register.php" method="post" class="auth-form">
        <label>
          Nama Lengkap
          <input type="text" name="nama" placeholder="Nama lengkap" value="<?php echo htmlspecialchars($_POST['nama'] ?? ''); ?>" required />
        </label>
        <label>
          Email
          <input type="email" name="email" placeholder="nama@domain.com" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required />
        </label>
        <label>
          Password
          <input type="password" name="password" placeholder="••••••••" required />
        </label>
        <button class="button button-primary" type="submit">Daftar</button>
      </form>
      <p class="form-note">Sudah punya akun? <a href="login.php">Login di sini</a></p>
    </div>
  </main>
</body>
</html>
