<?php
require_once __DIR__ . '/config.php';
require_role('Terapis');

$user = current_user();

$terapisName = $user['nama'];

$scheduleList = $pdo->prepare(
    'SELECT j.*, t.nama AS terapis_nama, COUNT(r.id_reservasi) AS total_reservasi
     FROM jadwal j
     LEFT JOIN terapis t ON j.terapis_id = t.id_terapis
     LEFT JOIN reservasi r ON r.jadwal_id = j.id_jadwal
     WHERE t.nama = :nama
     GROUP BY j.id_jadwal
     ORDER BY j.tanggal ASC, j.jam_mulai ASC'
);
$scheduleList->execute(['nama' => $terapisName]);
$scheduleList = $scheduleList->fetchAll();

$patientCount = $pdo->prepare(
    'SELECT COUNT(DISTINCT r.pasien_id) FROM reservasi r JOIN terapis t ON r.terapis_id = t.id_terapis WHERE t.nama = :nama'
);
$patientCount->execute(['nama' => $terapisName]);
$patientCount = $patientCount->fetchColumn();

$upcomingCount = 0;
$completedCount = 0;
foreach ($scheduleList as $schedule) {
    if ($schedule['status'] === 'Tersedia') {
        $upcomingCount++;
    }
    if ($schedule['status'] === 'Terisi') {
        $completedCount++;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard Terapis - Sistem Informasi Fisioterapi</title>
  <link rel="stylesheet" href="assets/css/style.css" />
</head>
<body>
  <header class="top-nav">
    <div class="container nav-inner">
      <a class="brand" href="index.php">Fisioterapi</a>
      <nav class="nav-menu">
        <a href="therapist-dashboard.php">Dashboard</a>
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
        <span class="eyebrow">Dashboard Terapis</span>
        <h1>Halo, <?php echo htmlspecialchars($user['nama']); ?></h1>
        <p>Kelola jadwal, pasien, dan reservasi terapi Anda dari panel ini.</p>
      </div>

      <div class="dashboard-grid">
        <article class="dashboard-card">
          <p>Slot Tersedia</p>
          <strong><?php echo $upcomingCount; ?></strong>
        </article>
        <article class="dashboard-card">
          <p>Slot Terisi</p>
          <strong><?php echo $completedCount; ?></strong>
        </article>
        <article class="dashboard-card">
          <p>Pasien Unik</p>
          <strong><?php echo number_format($patientCount, 0, ',', '.'); ?></strong>
        </article>
      </div>

      <section class="admin-panel">
        <h2>Jadwal Saya</h2>
        <div class="table-scroll">
          <table class="admin-table">
            <thead>
              <tr>
                <th>Tanggal</th>
                <th>Jam</th>
                <th>Status</th>
                <th>Reservasi</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($scheduleList)): ?>
                <tr><td colspan="4">Tidak ada jadwal untuk akun ini.</td></tr>
              <?php else: ?>
                <?php foreach ($scheduleList as $schedule): ?>
                  <tr>
                    <td><?php echo htmlspecialchars($schedule['tanggal']); ?></td>
                    <td><?php echo htmlspecialchars($schedule['jam_mulai'] . ' - ' . $schedule['jam_selesai']); ?></td>
                    <td><?php echo htmlspecialchars($schedule['status']); ?></td>
                    <td><?php echo htmlspecialchars($schedule['total_reservasi']); ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </section>

      <section class="section-alt admin-panel">
        <h2>Informasi Akun</h2>
        <p><strong>Nama:</strong> <?php echo htmlspecialchars($user['nama']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
        <p><strong>Peran:</strong> Terapis</p>
      </section>
    </div>
  </main>
</body>
</html>
