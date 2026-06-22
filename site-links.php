<?php
require_once __DIR__ . '/config.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Daftar Link Halaman - Sistem Informasi Fisioterapi</title>
  <link rel="stylesheet" href="assets/css/style.css" />
</head>
<body class="page-auth">
  <main class="auth-shell">
    <div class="auth-card">
      <a class="brand brand-center" href="index.php">Fisioterapi</a>
      <h1>Daftar Link Halaman</h1>
      <p>Gunakan tautan ini untuk membuka setiap halaman yang ada di sistem.</p>
      <ul class="link-list">
        <li><a href="index.php">Beranda</a></li>
        <li><a href="login.php">Login</a></li>
        <li><a href="register.php">Register</a></li>
        <li><a href="forgot_password.php">Lupa Password</a></li>
        <li><a href="reset_password.php?token=TEST">Reset Password (butuh token valid)</a></li>
        <li><a href="create_admin.php">Buat Admin</a></li>
        <li><a href="patient-dashboard.php">Dashboard Pasien</a></li>
        <li><a href="patient-profile.php">Profil Pasien</a></li>
        <li><a href="therapist-dashboard.php">Dashboard Terapis</a></li>
        <li><a href="therapist-profile.php">Profil Terapis</a></li>
        <li><a href="change_password.php">Ubah Password</a></li>
        <li><a href="payment.php">Pembayaran</a></li>
        <li><a href="admin-dashboard.php">Dashboard Admin</a></li>
        <li><a href="admin-pasien.php">Admin - Pasien</a></li>
        <li><a href="admin-terapis.php">Admin - Terapis</a></li>
        <li><a href="admin-layanan.php">Admin - Layanan</a></li>
        <li><a href="admin-jadwal.php">Admin - Jadwal</a></li>
        <li><a href="admin-reservasi.php">Admin - Reservasi</a></li>
        <li><a href="admin-pembayaran.php">Admin - Pembayaran</a></li>
      </ul>
      <p class="form-note">Beberapa tautan akan mengarahkan ke login jika Anda belum masuk sebagai peran yang sesuai.</p>
    </div>
  </main>
</body>
</html>