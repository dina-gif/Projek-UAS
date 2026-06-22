<?php
require_once __DIR__ . '/config.php';
require_role('Terapis');

$user = current_user();
$stmt = $pdo->prepare('SELECT * FROM terapis WHERE nama = :nama LIMIT 1');
$stmt->execute(['nama' => $user['nama']]);
$terapis = $stmt->fetch();

$errors = [];
$success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $spesialis = trim($_POST['spesialis'] ?? '');
    $telepon = trim($_POST['telepon'] ?? '');
    $foto = trim($_POST['foto'] ?? '');

    if ($nama === '' || $spesialis === '') {
        $errors[] = 'Nama dan spesialis wajib diisi.';
    }

    if (empty($errors)) {
        try {
            if ($terapis) {
                $stmt = $pdo->prepare('UPDATE terapis SET nama = :nama, spesialis = :spesialis, telepon = :telepon, foto = :foto WHERE id_terapis = :id_terapis');
                $stmt->execute([
                    'nama' => $nama,
                    'spesialis' => $spesialis,
                    'telepon' => $telepon,
                    'foto' => $foto ?: null,
                    'id_terapis' => $terapis['id_terapis'],
                ]);
            } else {
                $stmt = $pdo->prepare('INSERT INTO terapis (nama, spesialis, telepon, foto) VALUES (:nama, :spesialis, :telepon, :foto)');
                $stmt->execute([
                    'nama' => $nama,
                    'spesialis' => $spesialis,
                    'telepon' => $telepon,
                    'foto' => $foto ?: null,
                ]);
            }
            $success = 'Profil terapis berhasil diperbarui.';
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
  <title>Profil Terapis</title>
  <link rel="stylesheet" href="assets/css/style.css" />
</head>
<body>
  <header class="top-nav">
    <div class="container nav-inner">
      <a class="brand" href="therapist-dashboard.php">Fisioterapi</a>
      <nav class="nav-menu">
        <a href="therapist-dashboard.php">Dashboard</a>
        <a href="logout.php">Logout</a>
      </nav>
      <div class="nav-actions">Halo, <?php echo htmlspecialchars($user['nama']); ?></div>
    </div>
  </header>

  <main class="section">
    <div class="container">
      <div class="section-header">
        <h1>Profil Terapis</h1>
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
              <input type="text" name="nama" value="<?php echo htmlspecialchars($terapis['nama'] ?? $user['nama']); ?>" required />
            </label>
            <label>Spesialis
              <input type="text" name="spesialis" value="<?php echo htmlspecialchars($terapis['spesialis'] ?? ''); ?>" required />
            </label>
            <label>Telepon
              <input type="text" name="telepon" value="<?php echo htmlspecialchars($terapis['telepon'] ?? ''); ?>" />
            </label>
            <label>Foto (URL)
              <input type="url" name="foto" value="<?php echo htmlspecialchars($terapis['foto'] ?? ''); ?>" />
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