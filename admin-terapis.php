<?php
require_once __DIR__ . '/config.php';
require_role('Admin');

$errors = [];
$success = null;
$editTerapis = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        $nama = trim($_POST['nama'] ?? '');
        $spesialis = trim($_POST['spesialis'] ?? '');
        $telepon = trim($_POST['telepon'] ?? '');
        $foto = trim($_POST['foto'] ?? '');

        if ($nama === '' || $spesialis === '') {
            $errors[] = 'Nama dan spesialis wajib diisi.';
        }

        if (empty($errors)) {
            try {
                $stmt = $pdo->prepare('INSERT INTO terapis (nama, spesialis, telepon, foto) VALUES (:nama, :spesialis, :telepon, :foto)');
                $stmt->execute([
                    'nama' => $nama,
                    'spesialis' => $spesialis,
                    'telepon' => $telepon,
                    'foto' => $foto ?: null,
                ]);
                $success = 'Terapis berhasil ditambahkan.';
            } catch (PDOException $e) {
                $errors[] = 'Tidak dapat menambahkan terapis: ' . $e->getMessage();
            }
        }
    }

    if ($action === 'update') {
        $id_terapis = intval($_POST['id_terapis'] ?? 0);
        $nama = trim($_POST['nama'] ?? '');
        $spesialis = trim($_POST['spesialis'] ?? '');
        $telepon = trim($_POST['telepon'] ?? '');
        $foto = trim($_POST['foto'] ?? '');

        if ($id_terapis <= 0) {
            $errors[] = 'ID terapis tidak valid.';
        }

        if ($nama === '' || $spesialis === '') {
            $errors[] = 'Nama dan spesialis wajib diisi.';
        }

        if (empty($errors)) {
            try {
                $stmt = $pdo->prepare('UPDATE terapis SET nama = :nama, spesialis = :spesialis, telepon = :telepon, foto = :foto WHERE id_terapis = :id_terapis');
                $stmt->execute([
                    'nama' => $nama,
                    'spesialis' => $spesialis,
                    'telepon' => $telepon,
                    'foto' => $foto ?: null,
                    'id_terapis' => $id_terapis,
                ]);
                $success = 'Data terapis berhasil diperbarui.';
            } catch (PDOException $e) {
                $errors[] = 'Tidak dapat memperbarui terapis: ' . $e->getMessage();
            }
        }
    }

    if ($action === 'delete') {
        $id_terapis = intval($_POST['id_terapis'] ?? 0);
        if ($id_terapis > 0) {
            try {
                $stmt = $pdo->prepare('DELETE FROM terapis WHERE id_terapis = :id_terapis');
                $stmt->execute(['id_terapis' => $id_terapis]);
                $success = 'Terapis berhasil dihapus.';
            } catch (PDOException $e) {
                $errors[] = 'Tidak dapat menghapus terapis: ' . $e->getMessage();
            }
        }
    }
}

if (isset($_GET['edit'])) {
    $editId = intval($_GET['edit']);
    if ($editId > 0) {
        $stmt = $pdo->prepare('SELECT * FROM terapis WHERE id_terapis = :id_terapis');
        $stmt->execute(['id_terapis' => $editId]);
        $editTerapis = $stmt->fetch();
        if (!$editTerapis) {
            $errors[] = 'Terapis tidak ditemukan.';
        }
    }
}

$terapisList = $pdo->query('SELECT * FROM terapis ORDER BY nama ASC')->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin - Terapis</title>
  <link rel="stylesheet" href="assets/css/style.css" />
</head>
<body>
  <header class="top-nav top-nav-dark">
    <div class="container nav-inner">
      <a class="brand" href="admin-dashboard.php">Fisioterapi</a>
      <nav class="nav-menu">
        <a href="admin-dashboard.php">Dashboard</a>
        <a href="admin-pasien.php">Pasien</a>
        <a href="admin-terapis.php">Terapis</a>
        <a href="admin-layanan.php">Layanan</a>
        <a href="admin-jadwal.php">Jadwal</a>
        <a href="admin-reservasi.php">Reservasi</a>
      </nav>
      <div class="nav-actions">
        <a class="button button-secondary" href="logout.php">Logout</a>
      </div>
    </div>
  </header>

  <main class="section">
    <div class="container">
      <div class="section-header">
        <span class="eyebrow">Master Data</span>
        <h1>Kelola Terapis</h1>
        <p>Kelola data terapis, spesialisasi, kontak, dan foto profil yang ditampilkan.</p>
      </div>

      <?php if ($success): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
      <?php endif; ?>
      <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
          <ul>
            <?php foreach ($errors as $error): ?>
              <li><?php echo htmlspecialchars($error); ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <div class="admin-grid">
        <section class="admin-panel">
          <h2><?php echo $editTerapis ? 'Ubah Terapis' : 'Tambah Terapis'; ?></h2>
          <form method="post" class="form-grid">
            <input type="hidden" name="action" value="<?php echo $editTerapis ? 'update' : 'create'; ?>" />
            <?php if ($editTerapis): ?>
              <input type="hidden" name="id_terapis" value="<?php echo htmlspecialchars($editTerapis['id_terapis']); ?>" />
            <?php endif; ?>
            <label>
              Nama Terapis
              <input type="text" name="nama" value="<?php echo htmlspecialchars($editTerapis['nama'] ?? ''); ?>" required />
            </label>
            <label>
              Spesialisasi
              <input type="text" name="spesialis" value="<?php echo htmlspecialchars($editTerapis['spesialis'] ?? ''); ?>" required />
            </label>
            <label>
              Telepon
              <input type="text" name="telepon" value="<?php echo htmlspecialchars($editTerapis['telepon'] ?? ''); ?>" />
            </label>
            <label class="full-width">
              Foto (URL)
              <input type="url" name="foto" value="<?php echo htmlspecialchars($editTerapis['foto'] ?? ''); ?>" />
            </label>
            <div class="form-actions">
              <button class="button button-primary" type="submit"><?php echo $editTerapis ? 'Perbarui Terapis' : 'Tambah Terapis'; ?></button>
              <?php if ($editTerapis): ?>
                <a class="button button-secondary" href="admin-terapis.php">Batal</a>
              <?php endif; ?>
            </div>
          </form>
        </section>

        <section class="admin-panel admin-table-panel">
          <h2>Daftar Terapis</h2>
          <div class="table-scroll">
            <table class="admin-table">
              <thead>
                <tr>
                  <th>Nama</th>
                  <th>Spesialis</th>
                  <th>Telepon</th>
                  <th>Foto</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($terapisList as $terapis): ?>
                  <tr>
                    <td><?php echo htmlspecialchars($terapis['nama']); ?></td>
                    <td><?php echo htmlspecialchars($terapis['spesialis']); ?></td>
                    <td><?php echo htmlspecialchars($terapis['telepon']); ?></td>
                    <td><?php echo htmlspecialchars($terapis['foto']); ?></td>
                    <td>
                      <a class="button button-secondary button-small" href="admin-terapis.php?edit=<?php echo htmlspecialchars($terapis['id_terapis']); ?>">Ubah</a>
                      <form method="post" action="admin-terapis.php" class="inline-form" onsubmit="return confirm('Hapus terapis ini?');">
                        <input type="hidden" name="action" value="delete" />
                        <input type="hidden" name="id_terapis" value="<?php echo htmlspecialchars($terapis['id_terapis']); ?>" />
                        <button class="button button-outline" type="submit">Hapus</button>
                      </form>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </section>
      </div>
    </div>
  </main>
</body>
</html>
