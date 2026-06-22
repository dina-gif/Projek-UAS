<?php
require_once __DIR__ . '/config.php';
require_role('Pasien');

$user = current_user();
$stmt = $pdo->prepare('SELECT * FROM pasien WHERE user_id = :user_id LIMIT 1');
$stmt->execute(['user_id' => $user['id']]);
$pasien = $stmt->fetch();

$errors = [];
$success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $jenis_kelamin = $_POST['jenis_kelamin'] ?? '';
    $tanggal_lahir = $_POST['tanggal_lahir'] ?? null;
    $alamat = trim($_POST['alamat'] ?? '');
    $telepon = trim($_POST['telepon'] ?? '');

    if ($nama === '' || $jenis_kelamin === '') {
        $errors[] = 'Nama dan jenis kelamin wajib diisi.';
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare('UPDATE pasien SET nama = :nama, jenis_kelamin = :jenis_kelamin, tanggal_lahir = :tanggal_lahir, alamat = :alamat, telepon = :telepon WHERE user_id = :user_id');
            $stmt->execute([
                'nama' => $nama,
                'jenis_kelamin' => $jenis_kelamin,
                'tanggal_lahir' => $tanggal_lahir ?: null,
                'alamat' => $alamat,
                'telepon' => $telepon,
                'user_id' => $user['id'],
            ]);
            $success = 'Profil berhasil diperbarui.';
        } catch (PDOException $e) {
            $errors[] = 'Tidak dapat memperbarui profil: ' . $e->getMessage();
        }
    }
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Profil Pasien</title>
  <link rel="stylesheet" href="assets/css/style.css" />
</head>
<body>
  <header class="top-nav">
    <div class="container nav-inner">
      <a class="brand" href="patient-dashboard.php">Fisioterapi</a>
      <nav class="nav-menu">
        <a href="patient-dashboard.php">Dashboard</a>
        <a href="payment.php">Pembayaran</a>
        <a href="logout.php">Logout</a>
      </nav>
      <div class="nav-actions">Halo, <?php echo htmlspecialchars($user['nama']); ?></div>
    </div>
  </header>

  <main class="section">
    <div class="container">
      <div class="section-header">
        <h1>Profil Pasien</h1>
      </div>

      <?php if ($success): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
      <?php endif; ?>
      <?php if (!empty($errors)): ?>
        <div class="alert alert-error"><ul><?php foreach ($errors as $e) echo '<li>' . htmlspecialchars($e) . '</li>'; ?></ul></div>
      <?php endif; ?>

      <div class="admin-grid">
        <section class="admin-panel">
          <form method="post" class="form-grid">
            <label>Nama Lengkap
              <input type="text" name="nama" value="<?php echo htmlspecialchars($pasien['nama'] ?? $user['nama']); ?>" required />
            </label>
            <label>Jenis Kelamin
              <select name="jenis_kelamin" required>
                <option value="">Pilih...</option>
                <option value="Laki-laki" <?php echo (isset($pasien['jenis_kelamin']) && $pasien['jenis_kelamin'] === 'Laki-laki') ? 'selected' : ''; ?>>Laki-laki</option>
                <option value="Perempuan" <?php echo (isset($pasien['jenis_kelamin']) && $pasien['jenis_kelamin'] === 'Perempuan') ? 'selected' : ''; ?>>Perempuan</option>
              </select>
            </label>
            <label>Tanggal Lahir
              <input type="date" name="tanggal_lahir" value="<?php echo htmlspecialchars($pasien['tanggal_lahir'] ?? ''); ?>" />
            </label>
            <label>Telepon
              <input type="text" name="telepon" value="<?php echo htmlspecialchars($pasien['telepon'] ?? ''); ?>" />
            </label>
            <label class="full-width">Alamat
              <textarea name="alamat"><?php echo htmlspecialchars($pasien['alamat'] ?? ''); ?></textarea>
            </label>
            <div class="form-actions">
              <button class="button button-primary" type="submit">Simpan</button>
            </div>
          </form>
        </section>

        <section class="admin-panel admin-table-panel">
          <h2>Akun</h2>
          <p><strong>Nama akun:</strong> <?php echo htmlspecialchars($user['nama']); ?></p>
          <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
        </section>
      </div>
    </div>
  </main>
</body>
</html>