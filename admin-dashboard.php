<?php
require_once __DIR__ . '/config.php';
require_role('Admin');

$totalPasien = $pdo->query('SELECT COUNT(*) FROM pasien')->fetchColumn();
$totalTerapis = $pdo->query('SELECT COUNT(*) FROM terapis')->fetchColumn();
$totalReservasi = $pdo->query('SELECT COUNT(*) FROM reservasi')->fetchColumn();
$totalPendapatan = $pdo->query('SELECT COALESCE(SUM(jumlah), 0) FROM pembayaran WHERE status = "Lunas"')->fetchColumn();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard Admin - Sistem Informasi Fisioterapi</title>
    <link rel="stylesheet" href="assets/css/style.css" />
</head>

<body>
    <header class="top-nav top-nav-dark">
        <div class="container nav-inner">
            <a class="brand" href="index.php">Fisioterapi</a>
            <nav class="nav-menu">
                <a href="#dashboard">Dashboard</a>
                <a href="#data">Master Data</a>
                <a href="#laporan">Laporan</a>
                <a href="site-links.php">Semua Halaman</a>
            </nav>
            <div class="nav-actions">
                <a class="button button-secondary" href="logout.php">Logout</a>
            </div>
        </div>
    </header>

    <main class="section section-dark">
        <div class="container">
            <div class="section-header section-header-light">
                <span class="eyebrow eyebrow-light">Admin Dashboard</span>
                <h1>Ringkasan Operasional</h1>
                <p>Kelola pasien, terapis, reservasi, pembayaran, dan laporan dalam satu panel terpadu.</p>
            </div>
            <div class="dashboard-grid">
                <article class="dashboard-card">
                    <p>Total Pasien</p>
                    <strong><?php echo number_format($totalPasien, 0, ',', '.'); ?></strong>
                </article>
                <article class="dashboard-card">
                    <p>Total Terapis</p>
                    <strong><?php echo number_format($totalTerapis, 0, ',', '.'); ?></strong>
                </article>
                <article class="dashboard-card">
                    <p>Reservasi</p>
                    <strong><?php echo number_format($totalReservasi, 0, ',', '.'); ?></strong>
                </article>
                <article class="dashboard-card">
                    <p>Pendapatan</p>
                    <strong>Rp <?php echo number_format($totalPendapatan, 0, ',', '.'); ?></strong>
                </article>
            </div>
        </div>
    </main>
</body>

</html>