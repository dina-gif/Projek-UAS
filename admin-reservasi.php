<?php
require_once __DIR__ . '/config.php';
require_role('Admin');

$errors = [];
$success = null;
$editReservasi = null;

$pasienList = $pdo->query('SELECT id_pasien, nama FROM pasien ORDER BY nama ASC')->fetchAll();
$terapisList = $pdo->query('SELECT id_terapis, nama FROM terapis ORDER BY nama ASC')->fetchAll();
$layananList = $pdo->query('SELECT id_layanan, nama_layanan FROM layanan ORDER BY nama_layanan ASC')->fetchAll();
$jadwalList = $pdo->query('SELECT j.id_jadwal, t.nama AS terapis_nama, j.tanggal, j.jam_mulai FROM jadwal j JOIN terapis t ON j.terapis_id = t.id_terapis WHERE j.status = "Tersedia" ORDER BY j.tanggal ASC, j.jam_mulai ASC')->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        $pasien_id = intval($_POST['pasien_id'] ?? 0);
        $terapis_id = intval($_POST['terapis_id'] ?? 0);
        $layanan_id = intval($_POST['layanan_id'] ?? 0);
        $jadwal_id = intval($_POST['jadwal_id'] ?? 0);
        $keluhan = trim($_POST['keluhan'] ?? '');
        $status = $_POST['status'] ?? 'Menunggu';

        if ($pasien_id <= 0 || $terapis_id <= 0 || $layanan_id <= 0 || $jadwal_id <= 0) {
            $errors[] = 'Semua pilihan wajib diisi.';
        }

        if (empty($errors)) {
            try {
                $stmt = $pdo->prepare('INSERT INTO reservasi (pasien_id, terapis_id, layanan_id, jadwal_id, keluhan, status) VALUES (:pasien_id, :terapis_id, :layanan_id, :jadwal_id, :keluhan, :status)');
                $stmt->execute([
                    'pasien_id' => $pasien_id,
                    'terapis_id' => $terapis_id,
                    'layanan_id' => $layanan_id,
                    'jadwal_id' => $jadwal_id,
                    'keluhan' => $keluhan,
                    'status' => $status,
                ]);
                $stmt = $pdo->prepare('UPDATE jadwal SET status = "Terisi" WHERE id_jadwal = :jadwal_id');
                $stmt->execute(['jadwal_id' => $jadwal_id]);
                $success = 'Reservasi berhasil dibuat.';
            } catch (PDOException $e) {
                $errors[] = 'Tidak dapat membuat reservasi: ' . $e->getMessage();
            }
        }
    }

    if ($action === 'update') {
        $id_reservasi = intval($_POST['id_reservasi'] ?? 0);
        $status = $_POST['status'] ?? 'Menunggu';

        if ($id_reservasi <= 0) {
            $errors[] = 'ID reservasi tidak valid.';
        }

        if (empty($errors)) {
            try {
                $stmt = $pdo->prepare('UPDATE reservasi SET status = :status WHERE id_reservasi = :id_reservasi');
                $stmt->execute([
                    'status' => $status,
                    'id_reservasi' => $id_reservasi,
                ]);
                $success = 'Status reservasi diperbarui.';
            } catch (PDOException $e) {
                $errors[] = 'Tidak dapat memperbarui reservasi: ' . $e->getMessage();
            }
        }
    }

    if ($action === 'delete') {
        $id_reservasi = intval($_POST['id_reservasi'] ?? 0);
        if ($id_reservasi > 0) {
            try {
                $stmt = $pdo->prepare('SELECT jadwal_id FROM reservasi WHERE id_reservasi = :id_reservasi');
                $stmt->execute(['id_reservasi' => $id_reservasi]);
                $row = $stmt->fetch();
                if ($row) {
                    $stmt = $pdo->prepare('DELETE FROM reservasi WHERE id_reservasi = :id_reservasi');
                    $stmt->execute(['id_reservasi' => $id_reservasi]);
                    $stmt = $pdo->prepare('UPDATE jadwal SET status = "Tersedia" WHERE id_jadwal = :jadwal_id');
                    $stmt->execute(['jadwal_id' => $row['jadwal_id']]);
                    $success = 'Reservasi berhasil dihapus.';
                } else {
                    $errors[] = 'Reservasi tidak ditemukan.';
                }
            } catch (PDOException $e) {
                $errors[] = 'Tidak dapat menghapus reservasi: ' . $e->getMessage();
            }
        }
    }
}

if (isset($_GET['edit'])) {
    $editId = intval($_GET['edit']);
    if ($editId > 0) {
        $stmt = $pdo->prepare('SELECT * FROM reservasi WHERE id_reservasi = :id_reservasi');
        $stmt->execute(['id_reservasi' => $editId]);
        $editReservasi = $stmt->fetch();
        if (!$editReservasi) {
            $errors[] = 'Reservasi tidak ditemukan.';
        }
    }
}

$stmt = $pdo->query('SELECT r.*, p.nama AS pasien_nama, t.nama AS terapis_nama, l.nama_layanan, j.tanggal, j.jam_mulai FROM reservasi r JOIN pasien p ON r.pasien_id = p.id_pasien JOIN terapis t ON r.terapis_id = t.id_terapis JOIN layanan l ON r.layanan_id = l.id_layanan JOIN jadwal j ON r.jadwal_id = j.id_jadwal ORDER BY r.created_at DESC');
$reservasiList = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin - Reservasi</title>
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
        <span class="eyebrow">Transaksi</span>
        <h1>Kelola Reservasi</h1>
        <p>Buat reservasi baru, ubah status, atau batalkan jadwal terapi.</p>
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
          <h2>Tambah Reservasi</h2>
          <form method="post" class="form-grid">
            <input type="hidden" name="action" value="create" />
            <label>
              Pasien
              <select name="pasien_id" required>
                <option value="">Pilih pasien...</option>
                <?php foreach ($pasienList as $pasien): ?>
                  <option value="<?php echo htmlspecialchars($pasien['id_pasien']); ?>"><?php echo htmlspecialchars($pasien['nama']); ?></option>
                <?php endforeach; ?>
              </select>
            </label>
            <label>
              Terapis
              <select name="terapis_id" required>
                <option value="">Pilih terapis...</option>
                <?php foreach ($terapisList as $terapis): ?>
                  <option value="<?php echo htmlspecialchars($terapis['id_terapis']); ?>"><?php echo htmlspecialchars($terapis['nama']); ?></option>
                <?php endforeach; ?>
              </select>
            </label>
            <label>
              Layanan
              <select name="layanan_id" required>
                <option value="">Pilih layanan...</option>
                <?php foreach ($layananList as $layanan): ?>
                  <option value="<?php echo htmlspecialchars($layanan['id_layanan']); ?>"><?php echo htmlspecialchars($layanan['nama_layanan']); ?></option>
                <?php endforeach; ?>
              </select>
            </label>
            <label>
              Jadwal
              <select name="jadwal_id" required>
                <option value="">Pilih jadwal tersedia...</option>
                <?php foreach ($jadwalList as $jadwal): ?>
                  <option value="<?php echo htmlspecialchars($jadwal['id_jadwal']); ?>"><?php echo htmlspecialchars($jadwal['terapis_nama'] . ' — ' . $jadwal['tanggal'] . ' ' . $jadwal['jam_mulai']); ?></option>
                <?php endforeach; ?>
              </select>
            </label>
            <label class="full-width">
              Keluhan
              <textarea name="keluhan"></textarea>
            </label>
            <label>
              Status
              <select name="status" required>
                <?php foreach (['Menunggu', 'Dikonfirmasi', 'Selesai', 'Batal'] as $statusOption): ?>
                  <option value="<?php echo $statusOption; ?>"><?php echo $statusOption; ?></option>
                <?php endforeach; ?>
              </select>
            </label>
            <button class="button button-primary" type="submit">Simpan Reservasi</button>
          </form>
        </section>

        <section class="admin-panel admin-table-panel">
          <h2>Daftar Reservasi</h2>
          <div class="table-scroll">
            <table class="admin-table">
              <thead>
                <tr>
                  <th>Pasien</th>
                  <th>Terapis</th>
                  <th>Layanan</th>
                  <th>Jadwal</th>
                  <th>Status</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($reservasiList as $reservasi): ?>
                  <tr>
                    <td><?php echo htmlspecialchars($reservasi['pasien_nama']); ?></td>
                    <td><?php echo htmlspecialchars($reservasi['terapis_nama']); ?></td>
                    <td><?php echo htmlspecialchars($reservasi['nama_layanan']); ?></td>
                    <td><?php echo htmlspecialchars($reservasi['tanggal'] . ' ' . $reservasi['jam_mulai']); ?></td>
                    <td><?php echo htmlspecialchars($reservasi['status']); ?></td>
                    <td>
                      <form method="post" action="admin-reservasi.php" class="inline-form">
                        <input type="hidden" name="action" value="update" />
                        <input type="hidden" name="id_reservasi" value="<?php echo htmlspecialchars($reservasi['id_reservasi']); ?>" />
                        <select name="status" class="status-select">
                          <?php foreach (['Menunggu', 'Dikonfirmasi', 'Selesai', 'Batal'] as $statusOption): ?>
                            <option value="<?php echo $statusOption; ?>" <?php echo ($reservasi['status'] === $statusOption) ? 'selected' : ''; ?>><?php echo $statusOption; ?></option>
                          <?php endforeach; ?>
                        </select>
                        <button class="button button-secondary button-small" type="submit">Update</button>
                      </form>
                      <form method="post" action="admin-reservasi.php" class="inline-form" onsubmit="return confirm('Batalkan reservasi ini?');">
                        <input type="hidden" name="action" value="delete" />
                        <input type="hidden" name="id_reservasi" value="<?php echo htmlspecialchars($reservasi['id_reservasi']); ?>" />
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
