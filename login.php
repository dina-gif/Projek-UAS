<?php
require_once __DIR__ . '/config.php';

if (is_logged_in()) {
    $user = current_user();
    if ($user['role'] === 'Admin') {
        header('Location: admin-dashboard.php');
    } elseif ($user['role'] === 'Terapis') {
        header('Location: therapist-dashboard.php');
    } else {
        header('Location: patient-dashboard.php');
    }
    exit;
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $errors[] = 'Email dan password harus diisi.';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            unset($user['password']);
            $_SESSION['user'] = $user;

            if ($user['role'] === 'Admin') {
                header('Location: admin-dashboard.php');
            } elseif ($user['role'] === 'Terapis') {
                header('Location: therapist-dashboard.php');
            } else {
                header('Location: patient-dashboard.php');
            }
            exit;
        }

        $errors[] = 'Email atau password tidak sesuai.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login - Sistem Informasi Fisioterapi</title>
  <link rel="stylesheet" href="assets/css/style.css" />
</head>
<body class="page-auth">
  <main class="auth-shell">
    <div class="auth-card">
      <a class="brand brand-center" href="index.php">Fisioterapi</a>
      <h1>Masuk ke Akun Anda</h1>
      <p>Login untuk mengelola reservasi, pasien, jadwal, dan hasil terapi.</p>
      <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
          <ul>
            <?php foreach ($errors as $error): ?>
              <li><?php echo htmlspecialchars($error); ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>
      <form action="login.php" method="post" class="auth-form">
        <label>
          Email
          <input type="email" name="email" placeholder="nama@domain.com" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required />
        </label>
        <label>
          Password
          <input type="password" name="password" placeholder="••••••••" required />
        </label>
        <button class="button button-primary" type="submit">Login</button>
      </form>
      <p class="form-note">Belum punya akun? <a href="register.php">Daftar sekarang</a></p>
      <p class="form-note"><a href="site-links.php">Lihat semua halaman</a></p>
    </div>
  </main>
</body>
</html>
