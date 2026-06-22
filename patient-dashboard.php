<?php
require_once __DIR__ . '/config.php';
require_role('Pasien');

$user = current_user();
$pasien = null;
$reservasiList = [];
$paymentHistory = [];

$stmt = $pdo->prepare('SELECT * FROM pasien WHERE user_id = :user_id LIMIT 1');
$stmt->execute(['user_id' => $user['id']]);
$pasien = $stmt->fetch();

if ($pasien) {
    $stmt = $pdo->prepare(
        'SELECT r.*, t.nama AS terapis_nama, l.nama_layanan, j.tanggal, j.jam_mulai, j.jam_selesai
         FROM reservasi r
         JOIN terapis t ON r.terapis_id = t.id_terapis
         JOIN layanan l ON r.layanan_id = l.id_layanan
         JOIN jadwal j ON r.jadwal_id = j.id_jadwal
         WHERE r.pasien_id = :pasien_id
         ORDER BY j.tanggal DESC, j.jam_mulai DESC'
    );
    $stmt->execute(['pasien_id' => $pasien['id_pasien']]);
    $reservasiList = $stmt->fetchAll();

    $stmt = $pdo->prepare(
        'SELECT p.*, r.status AS reservasi_status, l.nama_layanan
         FROM pembayaran p
         JOIN reservasi r ON p.reservasi_id = r.id_reservasi
         JOIN layanan l ON r.layanan_id = l.id_layanan
         WHERE r.pasien_id = :pasien_id
         ORDER BY p.tanggal DESC'
    );
    $stmt->execute(['pasien_id' => $pasien['id_pasien']]);
    $paymentHistory = $stmt->fetchAll();
}

$upcomingCount = 0;
$completedCount = 0;
$pendingPaymentCount = 0;
foreach ($reservasiList as $reservasi) {
    if ($reservasi['status'] === 'Dikonfirmasi' || $reservasi['status'] === 'Menunggu') {
        $upcomingCount++;
    }
    if ($reservasi['status'] === 'Selesai') {
        $completedCount++;
    }
}
foreach ($paymentHistory as $payment) {
    if ($payment['status'] === 'Pending') {
        $pendingPaymentCount++;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard Pasien - Sistem Informasi Fisioterapi</title>
  <link rel="stylesheet" href="assets/css/style.css" />
</head>
<body>
  <header class="top-nav">
    <div class="container nav-inner">
      <a class="brand" href="index.php">Fisioterapi</a>
      <nav class="nav-menu">
        <a href="patient-dashboard.php">Dashboard</a>
        <a href="payment.php">Pembayaran</a>
        <a href="site-links.php">Semua Halaman</a>
        <a href="logout.php">Logout</a>
      </nav>
      <div class="nav-actions">
        <span>Halo, <?php echo htmlspecialchars($user['nama']); ?></span>
      </div>
    </div>
  </header>

  <main class="section">
    <div class="container">
      <div class="section-header">
        <span class="eyebrow">Dashboard Pasien</span>
        <h1>Selamat datang, <?php echo htmlspecialchars($user['nama']); ?></h1>
        <p>Ini adalah ringkasan reservasi terapi Anda, riwayat pembayaran, dan informasi profil.</p>
      </div>

      <div class="dashboard-grid">
        <article class="dashboard-card">
          <p>Reservasi Mendatang</p>
          <strong><?php echo $upcomingCount; ?></strong>
        </article>
        <article class="dashboard-card">
          <p>Sesi Selesai</p>
          <strong><?php echo $completedCount; ?></strong>
        </article>
        <article class="dashboard-card">
          <p>Pembayaran Tertunda</p>
          <strong><?php echo $pendingPaymentCount; ?></strong>
        </article>
      </div>

      <div class="admin-grid">
        <section class="admin-panel">
          <h2>Profil Pasien</h2>
          <?php if ($pasien): ?>
            <p><strong>Nama:</strong> <?php echo htmlspecialchars($pasien['nama']); ?></p>
            <p><strong>Jenis Kelamin:</strong> <?php echo htmlspecialchars($pasien['jenis_kelamin']); ?></p>
            <p><strong>Tanggal Lahir:</strong> <?php echo htmlspecialchars($pasien['tanggal_lahir']); ?></p>
            <p><strong>Telepon:</strong> <?php echo htmlspecialchars($pasien['telepon']); ?></p>
            <p><strong>Alamat:</strong> <?php echo nl2br(htmlspecialchars($pasien['alamat'])); ?></p>
          <?php else: ?>
            <p>Data profil pasien tidak ditemukan.</p>
          <?php endif; ?>
        </section>

        <section class="admin-panel admin-table-panel">
          <h2>Reservasi Saya</h2>
          <div class="table-scroll">
            <table class="admin-table">
              <thead>
                <tr>
                  <th>Terapis</th>
                  <th>Layanan</th>
                  <th>Jadwal</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($reservasiList)): ?>
                  <tr><td colspan="4">Belum ada reservasi.</td></tr>
                <?php else: ?>
                  <?php foreach ($reservasiList as $reservasi): ?>
                    <tr>
                      <td><?php echo htmlspecialchars($reservasi['terapis_nama']); ?></td>
                      <td><?php echo htmlspecialchars($reservasi['nama_layanan']); ?></td>
                      <td><?php echo htmlspecialchars($reservasi['tanggal'] . ' — ' . $reservasi['jam_mulai'] . ' sampai ' . $reservasi['jam_selesai']); ?></td>
                      <td><?php echo htmlspecialchars($reservasi['status']); ?></td>
                    </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </section>
      </div>

      <section class="section-alt">
        <div class="section-header">
          <span class="eyebrow">Riwayat Pembayaran</span>
        </div>
        <div class="table-scroll">
          <table class="admin-table">
            <thead>
              <tr>
                <th>Order ID</th>
                <th>Layanan</th>
                <th>Jumlah</th>
                <th>Status</th>
                <th>Tanggal</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($paymentHistory)): ?>
                <tr><td colspan="5">Belum ada riwayat pembayaran.</td></tr>
              <?php else: ?>
                <?php foreach ($paymentHistory as $payment): ?>
                  <tr>
                    <td><?php echo htmlspecialchars($payment['order_id']); ?></td>
                    <td><?php echo htmlspecialchars($payment['nama_layanan']); ?></td>
                    <td>Rp <?php echo number_format($payment['jumlah'], 0, ',', '.'); ?></td>
                    <td><?php echo htmlspecialchars($payment['status']); ?></td>
                    <td><?php echo htmlspecialchars($payment['tanggal']); ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </section>
    </div>
  </main>
</body>
</html>
