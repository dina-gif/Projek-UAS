<?php
require_once __DIR__ . '/config.php';
require_role('Admin');

$errors = [];
$success = null;
$editPembayaran = null;

$reservasiAvailable = $pdo->query(
    'SELECT r.id_reservasi, p.nama AS pasien_nama, t.nama AS terapis_nama, l.nama_layanan, l.harga, j.tanggal, j.jam_mulai
     FROM reservasi r
     JOIN pasien p ON r.pasien_id = p.id_pasien
     JOIN terapis t ON r.terapis_id = t.id_terapis
     JOIN layanan l ON r.layanan_id = l.id_layanan
     JOIN jadwal j ON r.jadwal_id = j.id_jadwal
     ORDER BY j.tanggal DESC, j.jam_mulai ASC'
)->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        $reservasi_id = intval($_POST['reservasi_id'] ?? 0);
        $order_id = trim($_POST['order_id'] ?? '') ?: generate_order_id();
        $metode = trim($_POST['metode'] ?? '');
        $jumlah = floatval($_POST['jumlah'] ?? 0);
        $status = $_POST['status'] ?? 'Pending';

        if ($reservasi_id <= 0 || $metode === '' || $jumlah <= 0) {
            $errors[] = 'Reservasi, metode, dan jumlah pembayaran wajib diisi.';
        }

        if (empty($errors)) {
            try {
                $stmt = $pdo->prepare('INSERT INTO pembayaran (reservasi_id, order_id, metode, jumlah, status) VALUES (:reservasi_id, :order_id, :metode, :jumlah, :status)');
                $stmt->execute([
                    'reservasi_id' => $reservasi_id,
                    'order_id' => $order_id,
                    'metode' => $metode,
                    'jumlah' => $jumlah,
                    'status' => $status,
                ]);
                $success = 'Pembayaran berhasil ditambahkan.';
            } catch (PDOException $e) {
                $errors[] = 'Tidak dapat menambahkan pembayaran: ' . $e->getMessage();
            }
        }
    }

    if ($action === 'update') {
        $id_pembayaran = intval($_POST['id_pembayaran'] ?? 0);
        $order_id = trim($_POST['order_id'] ?? '');
        $metode = trim($_POST['metode'] ?? '');
        $jumlah = floatval($_POST['jumlah'] ?? 0);
        $status = $_POST['status'] ?? 'Pending';

        if ($id_pembayaran <= 0 || $order_id === '' || $metode === '' || $jumlah <= 0) {
            $errors[] = 'Data pembayaran tidak lengkap.';
        }

        if (empty($errors)) {
            try {
                $stmt = $pdo->prepare('UPDATE pembayaran SET order_id = :order_id, metode = :metode, jumlah = :jumlah, status = :status WHERE id_pembayaran = :id_pembayaran');
                $stmt->execute([
                    'order_id' => $order_id,
                    'metode' => $metode,
                    'jumlah' => $jumlah,
                    'status' => $status,
                    'id_pembayaran' => $id_pembayaran,
                ]);
                $success = 'Data pembayaran berhasil diperbarui.';
            } catch (PDOException $e) {
                $errors[] = 'Tidak dapat memperbarui pembayaran: ' . $e->getMessage();
            }
        }
    }

    if ($action === 'delete') {
        $id_pembayaran = intval($_POST['id_pembayaran'] ?? 0);
        if ($id_pembayaran > 0) {
            try {
                $stmt = $pdo->prepare('DELETE FROM pembayaran WHERE id_pembayaran = :id_pembayaran');
                $stmt->execute(['id_pembayaran' => $id_pembayaran]);
                $success = 'Pembayaran berhasil dihapus.';
            } catch (PDOException $e) {
                $errors[] = 'Tidak dapat menghapus pembayaran: ' . $e->getMessage();
            }
        }
    }
}

if (isset($_GET['edit'])) {
    $editId = intval($_GET['edit']);
    if ($editId > 0) {
        $stmt = $pdo->prepare('SELECT * FROM pembayaran WHERE id_pembayaran = :id_pembayaran');
        $stmt->execute(['id_pembayaran' => $editId]);
        $editPembayaran = $stmt->fetch();
        if (!$editPembayaran) {
            $errors[] = 'Pembayaran tidak ditemukan.';
        }
    }
}

$paymentList = $pdo->query(
    'SELECT pay.*, r.id_reservasi, p.nama AS pasien_nama, t.nama AS terapis_nama, l.nama_layanan, j.tanggal, j.jam_mulai
     FROM pembayaran pay
     JOIN reservasi r ON pay.reservasi_id = r.id_reservasi
     JOIN pasien p ON r.pasien_id = p.id_pasien
     JOIN terapis t ON r.terapis_id = t.id_terapis
     JOIN layanan l ON r.layanan_id = l.id_layanan
     JOIN jadwal j ON r.jadwal_id = j.id_jadwal
     ORDER BY pay.tanggal DESC'
)->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin - Pembayaran</title>
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
        <a href="admin-pembayaran.php">Pembayaran</a>
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
        <h1>Kelola Pembayaran</h1>
        <p>Buat dan pantau pembayaran reservasi, termasuk status transaksi, metode, dan order ID.</p>
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
          <h2><?php echo $editPembayaran ? 'Ubah Pembayaran' : 'Tambah Pembayaran'; ?></h2>
          <form method="post" class="form-grid">
            <input type="hidden" name="action" value="<?php echo $editPembayaran ? 'update' : 'create'; ?>" />
            <?php if ($editPembayaran): ?>
              <input type="hidden" name="id_pembayaran" value="<?php echo htmlspecialchars($editPembayaran['id_pembayaran']); ?>" />
            <?php endif; ?>

            <label>
              Reservasi
              <select name="reservasi_id" required>
                <option value="">Pilih reservasi...</option>
                <?php foreach ($reservasiAvailable as $reservasi): ?>
                  <option value="<?php echo htmlspecialchars($reservasi['id_reservasi']); ?>" <?php echo (isset($editPembayaran['reservasi_id']) && $editPembayaran['reservasi_id'] == $reservasi['id_reservasi']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($reservasi['pasien_nama'] . ' / ' . $reservasi['nama_layanan'] . ' / ' . $reservasi['tanggal'] . ' ' . $reservasi['jam_mulai']); ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </label>
            <label>
              Order ID
              <input type="text" name="order_id" value="<?php echo htmlspecialchars($editPembayaran['order_id'] ?? ''); ?>" placeholder="Order ID" required />
            </label>
            <label>
              Metode Pembayaran
              <select name="metode" required>
                <?php foreach (['QRIS', 'Transfer Bank', 'E-Wallet', 'Virtual Account'] as $metodeOption): ?>
                  <option value="<?php echo $metodeOption; ?>" <?php echo (isset($editPembayaran['metode']) && $editPembayaran['metode'] === $metodeOption) ? 'selected' : ''; ?>><?php echo $metodeOption; ?></option>
                <?php endforeach; ?>
              </select>
            </label>
            <label>
              Jumlah (Rp)
              <input type="number" min="0" step="0.01" name="jumlah" value="<?php echo htmlspecialchars($editPembayaran['jumlah'] ?? '0'); ?>" required />
            </label>
            <label>
              Status
              <select name="status" required>
                <?php foreach (['Pending', 'Lunas', 'Gagal', 'Dibatalkan'] as $statusOption): ?>
                  <option value="<?php echo $statusOption; ?>" <?php echo (isset($editPembayaran['status']) && $editPembayaran['status'] === $statusOption) ? 'selected' : ''; ?>><?php echo $statusOption; ?></option>
                <?php endforeach; ?>
              </select>
            </label>
            <div class="form-actions">
              <button class="button button-primary" type="submit"><?php echo $editPembayaran ? 'Perbarui Pembayaran' : 'Tambah Pembayaran'; ?></button>
              <?php if ($editPembayaran): ?>
                <a class="button button-secondary" href="admin-pembayaran.php">Batal</a>
              <?php endif; ?>
            </div>
          </form>
        </section>

        <section class="admin-panel admin-table-panel">
          <h2>Daftar Pembayaran</h2>
          <div class="table-scroll">
            <table class="admin-table">
              <thead>
                <tr>
                  <th>Order ID</th>
                  <th>Pasien</th>
                  <th>Layanan</th>
                  <th>Metode</th>
                  <th>Jumlah</th>
                  <th>Status</th>
                  <th>Tanggal</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($paymentList as $payment): ?>
                  <tr>
                    <td><?php echo htmlspecialchars($payment['order_id']); ?></td>
                    <td><?php echo htmlspecialchars($payment['pasien_nama']); ?></td>
                    <td><?php echo htmlspecialchars($payment['nama_layanan']); ?></td>
                    <td><?php echo htmlspecialchars($payment['metode']); ?></td>
                    <td>Rp <?php echo number_format($payment['jumlah'], 0, ',', '.'); ?></td>
                    <td><?php echo htmlspecialchars($payment['status']); ?></td>
                    <td><?php echo htmlspecialchars($payment['tanggal']); ?></td>
                    <td>
                      <a class="button button-secondary button-small" href="admin-pembayaran.php?edit=<?php echo htmlspecialchars($payment['id_pembayaran']); ?>">Ubah</a>
                      <form method="post" action="admin-pembayaran.php" class="inline-form" onsubmit="return confirm('Hapus pembayaran ini?');">
                        <input type="hidden" name="action" value="delete" />
                        <input type="hidden" name="id_pembayaran" value="<?php echo htmlspecialchars($payment['id_pembayaran']); ?>" />
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
