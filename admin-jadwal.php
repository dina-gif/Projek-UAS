<?php
require_once __DIR__ . '/config.php';
require_role('Admin');

$errors = [];
$success = null;
$editJadwal = null;

$terapisList = $pdo->query('SELECT id_terapis, nama FROM terapis ORDER BY nama ASC')->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        $terapis_id = intval($_POST['terapis_id'] ?? 0);
        $tanggal = $_POST['tanggal'] ?? '';
        $jam_mulai = $_POST['jam_mulai'] ?? '';
        $jam_selesai = $_POST['jam_selesai'] ?? '';
        $status = $_POST['status'] ?? 'Tersedia';

        if ($terapis_id <= 0 || $tanggal === '' || $jam_mulai === '' || $jam_selesai === '') {
            $errors[] = 'Terapis, tanggal, jam mulai, dan jam selesai wajib diisi.';
        }

        if (empty($errors)) {
            try {
                $stmt = $pdo->prepare('INSERT INTO jadwal (terapis_id, tanggal, jam_mulai, jam_selesai, status) VALUES (:terapis_id, :tanggal, :jam_mulai, :jam_selesai, :status)');
                $stmt->execute([
                    'terapis_id' => $terapis_id,
                    'tanggal' => $tanggal,
                    'jam_mulai' => $jam_mulai,
                    'jam_selesai' => $jam_selesai,
                    'status' => $status,
                ]);
                $success = 'Jadwal berhasil ditambahkan.';
            } catch (PDOException $e) {
                $errors[] = 'Tidak dapat menambahkan jadwal: ' . $e->getMessage();
            }
        }
    }

    if ($action === 'update') {
        $id_jadwal = intval($_POST['id_jadwal'] ?? 0);
        $terapis_id = intval($_POST['terapis_id'] ?? 0);
        $tanggal = $_POST['tanggal'] ?? '';
        $jam_mulai = $_POST['jam_mulai'] ?? '';
        $jam_selesai = $_POST['jam_selesai'] ?? '';
        $status = $_POST['status'] ?? 'Tersedia';

        if ($id_jadwal <= 0 || $terapis_id <= 0 || $tanggal === '' || $jam_mulai === '' || $jam_selesai === '') {
            $errors[] = 'Semua bidang wajib diisi untuk pembaruan jadwal.';
        }

        if (empty($errors)) {
            try {
                $stmt = $pdo->prepare('UPDATE jadwal SET terapis_id = :terapis_id, tanggal = :tanggal, jam_mulai = :jam_mulai, jam_selesai = :jam_selesai, status = :status WHERE id_jadwal = :id_jadwal');
                $stmt->execute([
                    'terapis_id' => $terapis_id,
                    'tanggal' => $tanggal,
                    'jam_mulai' => $jam_mulai,
                    'jam_selesai' => $jam_selesai,
                    'status' => $status,
                    'id_jadwal' => $id_jadwal,
                ]);
                $success = 'Jadwal berhasil diperbarui.';
            } catch (PDOException $e) {
                $errors[] = 'Tidak dapat memperbarui jadwal: ' . $e->getMessage();
            }
        }
    }

    if ($action === 'delete') {
        $id_jadwal = intval($_POST['id_jadwal'] ?? 0);
        if ($id_jadwal > 0) {
            try {
                $stmt = $pdo->prepare('DELETE FROM jadwal WHERE id_jadwal = :id_jadwal');
                $stmt->execute(['id_jadwal' => $id_jadwal]);
                $success = 'Jadwal berhasil dihapus.';
            } catch (PDOException $e) {
                $errors[] = 'Tidak dapat menghapus jadwal: ' . $e->getMessage();
            }
        }
    }
}

if (isset($_GET['edit'])) {
    $editId = intval($_GET['edit']);
    if ($editId > 0) {
        $stmt = $pdo->prepare('SELECT * FROM jadwal WHERE id_jadwal = :id_jadwal');
        $stmt->execute(['id_jadwal' => $editId]);
        $editJadwal = $stmt->fetch();
        if (!$editJadwal) {
            $errors[] = 'Jadwal tidak ditemukan.';
        }
    }
}

$stmt = $pdo->query('SELECT j.*, t.nama AS terapis_nama FROM jadwal j JOIN terapis t ON j.terapis_id = t.id_terapis ORDER BY tanggal DESC, jam_mulai ASC');
$jadwalList = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin - Jadwal</title>
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
        <h1>Kelola Jadwal</h1>
        <p>Tambahkan jadwal terapis, edit slot waktu, dan tandai status ketersediaan.</p>
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
          <h2><?php echo $editJadwal ? 'Ubah Jadwal' : 'Tambah Jadwal'; ?></h2>
          <form method="post" class="form-grid">
            <input type="hidden" name="action" value="<?php echo $editJadwal ? 'update' : 'create'; ?>" />
            <?php if ($editJadwal): ?>
              <input type="hidden" name="id_jadwal" value="<?php echo htmlspecialchars($editJadwal['id_jadwal']); ?>" />
            <?php endif; ?>
            <label>
              Terapis
              <select name="terapis_id" required>
                <option value="">Pilih terapis...</option>
                <?php foreach ($terapisList as $terapis): ?>
                  <option value="<?php echo htmlspecialchars($terapis['id_terapis']); ?>" <?php echo (isset($editJadwal['terapis_id']) && $editJadwal['terapis_id'] == $terapis['id_terapis']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($terapis['nama']); ?></option>
                <?php endforeach; ?>
              </select>
            </label>
            <label>
              Tanggal
              <input type="date" name="tanggal" value="<?php echo htmlspecialchars($editJadwal['tanggal'] ?? ''); ?>" required />
            </label>
            <label>
              Jam Mulai
              <input type="time" name="jam_mulai" value="<?php echo htmlspecialchars($editJadwal['jam_mulai'] ?? ''); ?>" required />
            </label>
            <label>
              Jam Selesai
              <input type="time" name="jam_selesai" value="<?php echo htmlspecialchars($editJadwal['jam_selesai'] ?? ''); ?>" required />
            </label>
            <label>
              Status
              <select name="status" required>
                <?php foreach (['Tersedia', 'Terisi', 'Tidak Tersedia'] as $statusOption): ?>
                  <option value="<?php echo $statusOption; ?>" <?php echo (isset($editJadwal['status']) && $editJadwal['status'] === $statusOption) ? 'selected' : ''; ?>><?php echo $statusOption; ?></option>
                <?php endforeach; ?>
              </select>
            </label>
            <div class="form-actions">
              <button class="button button-primary" type="submit"><?php echo $editJadwal ? 'Perbarui Jadwal' : 'Tambah Jadwal'; ?></button>
              <?php if ($editJadwal): ?>
                <a class="button button-secondary" href="admin-jadwal.php">Batal</a>
              <?php endif; ?>
            </div>
          </form>
        </section>

        <section class="admin-panel admin-table-panel">
          <h2>Daftar Jadwal</h2>
          <div class="table-scroll">
            <table class="admin-table">
              <thead>
                <tr>
                  <th>Terapis</th>
                  <th>Tanggal</th>
                  <th>Mulai</th>
                  <th>Selesai</th>
                  <th>Status</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($jadwalList as $jadwal): ?>
                  <tr>
                    <td><?php echo htmlspecialchars($jadwal['terapis_nama']); ?></td>
                    <td><?php echo htmlspecialchars($jadwal['tanggal']); ?></td>
                    <td><?php echo htmlspecialchars($jadwal['jam_mulai']); ?></td>
                    <td><?php echo htmlspecialchars($jadwal['jam_selesai']); ?></td>
                    <td><?php echo htmlspecialchars($jadwal['status']); ?></td>
                    <td>
                      <a class="button button-secondary button-small" href="admin-jadwal.php?edit=<?php echo htmlspecialchars($jadwal['id_jadwal']); ?>">Ubah</a>
                      <form method="post" action="admin-jadwal.php" class="inline-form" onsubmit="return confirm('Hapus jadwal ini?');">
                        <input type="hidden" name="action" value="delete" />
                        <input type="hidden" name="id_jadwal" value="<?php echo htmlspecialchars($jadwal['id_jadwal']); ?>" />
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
