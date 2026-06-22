<?php
require_once __DIR__ . '/config.php';
require_role('Admin');

$errors = [];
$success = null;
$editLayanan = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        $nama_layanan = trim($_POST['nama_layanan'] ?? '');
        $deskripsi = trim($_POST['deskripsi'] ?? '');
        $harga = trim($_POST['harga'] ?? '0');
        $durasi = trim($_POST['durasi'] ?? '');

        if ($nama_layanan === '' || $deskripsi === '') {
            $errors[] = 'Nama layanan dan deskripsi wajib diisi.';
        }

        if (!is_numeric($harga) || floatval($harga) < 0) {
            $errors[] = 'Harga harus angka positif.';
        }

        if (empty($errors)) {
            try {
                $stmt = $pdo->prepare('INSERT INTO layanan (nama_layanan, deskripsi, harga, durasi) VALUES (:nama_layanan, :deskripsi, :harga, :durasi)');
                $stmt->execute([
                    'nama_layanan' => $nama_layanan,
                    'deskripsi' => $deskripsi,
                    'harga' => floatval($harga),
                    'durasi' => $durasi,
                ]);
                $success = 'Layanan berhasil ditambahkan.';
            } catch (PDOException $e) {
                $errors[] = 'Tidak dapat menambahkan layanan: ' . $e->getMessage();
            }
        }
    }

    if ($action === 'update') {
        $id_layanan = intval($_POST['id_layanan'] ?? 0);
        $nama_layanan = trim($_POST['nama_layanan'] ?? '');
        $deskripsi = trim($_POST['deskripsi'] ?? '');
        $harga = trim($_POST['harga'] ?? '0');
        $durasi = trim($_POST['durasi'] ?? '');

        if ($id_layanan <= 0) {
            $errors[] = 'ID layanan tidak valid.';
        }

        if ($nama_layanan === '' || $deskripsi === '') {
            $errors[] = 'Nama layanan dan deskripsi wajib diisi.';
        }

        if (!is_numeric($harga) || floatval($harga) < 0) {
            $errors[] = 'Harga harus angka positif.';
        }

        if (empty($errors)) {
            try {
                $stmt = $pdo->prepare('UPDATE layanan SET nama_layanan = :nama_layanan, deskripsi = :deskripsi, harga = :harga, durasi = :durasi WHERE id_layanan = :id_layanan');
                $stmt->execute([
                    'nama_layanan' => $nama_layanan,
                    'deskripsi' => $deskripsi,
                    'harga' => floatval($harga),
                    'durasi' => $durasi,
                    'id_layanan' => $id_layanan,
                ]);
                $success = 'Layanan berhasil diperbarui.';
            } catch (PDOException $e) {
                $errors[] = 'Tidak dapat memperbarui layanan: ' . $e->getMessage();
            }
        }
    }

    if ($action === 'delete') {
        $id_layanan = intval($_POST['id_layanan'] ?? 0);
        if ($id_layanan > 0) {
            try {
                $stmt = $pdo->prepare('DELETE FROM layanan WHERE id_layanan = :id_layanan');
                $stmt->execute(['id_layanan' => $id_layanan]);
                $success = 'Layanan berhasil dihapus.';
            } catch (PDOException $e) {
                $errors[] = 'Tidak dapat menghapus layanan: ' . $e->getMessage();
            }
        }
    }
}

if (isset($_GET['edit'])) {
    $editId = intval($_GET['edit']);
    if ($editId > 0) {
        $stmt = $pdo->prepare('SELECT * FROM layanan WHERE id_layanan = :id_layanan');
        $stmt->execute(['id_layanan' => $editId]);
        $editLayanan = $stmt->fetch();
        if (!$editLayanan) {
            $errors[] = 'Layanan tidak ditemukan.';
        }
    }
}

$layananList = $pdo->query('SELECT * FROM layanan ORDER BY nama_layanan ASC')->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin - Layanan</title>
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
        <h1>Kelola Layanan</h1>
        <p>Tambah, edit, atau hapus layanan fisio yang dapat dibooking oleh pasien.</p>
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
          <h2><?php echo $editLayanan ? 'Ubah Layanan' : 'Tambah Layanan'; ?></h2>
          <form method="post" class="form-grid">
            <input type="hidden" name="action" value="<?php echo $editLayanan ? 'update' : 'create'; ?>" />
            <?php if ($editLayanan): ?>
              <input type="hidden" name="id_layanan" value="<?php echo htmlspecialchars($editLayanan['id_layanan']); ?>" />
            <?php endif; ?>
            <label>
              Nama Layanan
              <input type="text" name="nama_layanan" value="<?php echo htmlspecialchars($editLayanan['nama_layanan'] ?? ''); ?>" required />
            </label>
            <label class="full-width">
              Deskripsi
              <textarea name="deskripsi" required><?php echo htmlspecialchars($editLayanan['deskripsi'] ?? ''); ?></textarea>
            </label>
            <label>
              Harga (Rp)
              <input type="number" min="0" step="0.01" name="harga" value="<?php echo htmlspecialchars($editLayanan['harga'] ?? '0'); ?>" required />
            </label>
            <label>
              Durasi
              <input type="text" name="durasi" value="<?php echo htmlspecialchars($editLayanan['durasi'] ?? ''); ?>" placeholder="Contoh: 60 menit" />
            </label>
            <div class="form-actions">
              <button class="button button-primary" type="submit"><?php echo $editLayanan ? 'Perbarui Layanan' : 'Tambah Layanan'; ?></button>
              <?php if ($editLayanan): ?>
                <a class="button button-secondary" href="admin-layanan.php">Batal</a>
              <?php endif; ?>
            </div>
          </form>
        </section>

        <section class="admin-panel admin-table-panel">
          <h2>Daftar Layanan</h2>
          <div class="table-scroll">
            <table class="admin-table">
              <thead>
                <tr>
                  <th>Nama Layanan</th>
                  <th>Deskripsi</th>
                  <th>Harga</th>
                  <th>Durasi</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($layananList as $layanan): ?>
                  <tr>
                    <td><?php echo htmlspecialchars($layanan['nama_layanan']); ?></td>
                    <td><?php echo htmlspecialchars($layanan['deskripsi']); ?></td>
                    <td>Rp <?php echo number_format($layanan['harga'], 0, ',', '.'); ?></td>
                    <td><?php echo htmlspecialchars($layanan['durasi']); ?></td>
                    <td>
                      <a class="button button-secondary button-small" href="admin-layanan.php?edit=<?php echo htmlspecialchars($layanan['id_layanan']); ?>">Ubah</a>
                      <form method="post" action="admin-layanan.php" class="inline-form" onsubmit="return confirm('Hapus layanan ini?');">
                        <input type="hidden" name="action" value="delete" />
                        <input type="hidden" name="id_layanan" value="<?php echo htmlspecialchars($layanan['id_layanan']); ?>" />
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
