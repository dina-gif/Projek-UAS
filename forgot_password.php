<?php
require_once __DIR__ . '/config.php';

$errors = [];
$message = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Masukkan email yang valid.';
    } else {
        $stmt = $pdo->prepare('SELECT id, nama FROM users WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();
        if ($user) {
            $token = bin2hex(random_bytes(16));
            $expires = date('Y-m-d H:i:s', time() + 3600); // 1 jam
            $stmt = $pdo->prepare('INSERT INTO password_resets (email, token, expires_at) VALUES (:email, :token, :expires_at)');
            $stmt->execute(['email' => $email, 'token' => $token, 'expires_at' => $expires]);

            // Build reset link
            $resetLink = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/reset_password.php?token=' . $token;

            // Try to send email using configured mailer (send_mail helper in config.php)
            $subject = 'Instruksi Reset Password';
            $body = '<p>Halo ' . htmlspecialchars($user['nama']) . ',</p>' .
              '<p>Untuk mereset password Anda, klik tautan berikut (berlaku 1 jam):</p>' .
              '<p><a href="' . htmlspecialchars($resetLink) . '">Reset Password</a></p>' .
              '<p>Jika Anda tidak meminta reset, abaikan email ini.</p>';

            $sent = false;
            if (function_exists('send_mail')) {
              $sent = send_mail($email, $subject, $body, true);
            }

            if ($sent) {
              $message = 'Jika email terdaftar, Anda akan menerima instruksi reset via email.';
            } else {
              // Fallback: show link (useful for local/dev)
              $message = 'Link reset kata sandi (demo): <a href="' . htmlspecialchars($resetLink) . '">' . htmlspecialchars($resetLink) . '</a>';
            }
        } else {
            // Jangan ungkapkan apakah email terdaftar — tampilkan pesan umum
            $message = 'Jika email terdaftar, Anda akan menerima instruksi reset melalui email.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Lupa Password</title>
  <link rel="stylesheet" href="assets/css/style.css" />
</head>
<body class="page-auth">
  <main class="auth-shell">
    <div class="auth-card">
      <h1>Lupa Password</h1>
      <p>Masukkan email Anda, kami akan mengirimkan instruksi untuk mereset password.</p>
      <?php if (!empty($errors)): ?>
        <div class="alert alert-error"><ul><?php foreach ($errors as $e) echo '<li>' . htmlspecialchars($e) . '</li>'; ?></ul></div>
      <?php endif; ?>
      <?php if ($message): ?>
        <div class="alert alert-success"><?php echo $message; ?></div>
      <?php endif; ?>
      <form method="post" class="auth-form">
        <label>Email
          <input type="email" name="email" required />
        </label>
        <button class="button button-primary" type="submit">Kirim Link Reset</button>
      </form>
      <p class="form-note"><a href="login.php">Kembali ke login</a></p>
    </div>
  </main>
</body>
</html>