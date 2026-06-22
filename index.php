<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Sistem Informasi Fisioterapi</title>
  <link rel="stylesheet" href="assets/css/style.css" />
</head>
<body>
  <header class="top-nav">
    <div class="container nav-inner">
      <a class="brand" href="index.php">Fisioterapi</a>
      <nav class="nav-menu">
        <a href="#home">Home</a>
        <a href="#layanan">Layanan</a>
        <a href="#terapis">Daftar Terapis</a>
        <a href="#artikel">Artikel</a>
        <a href="#kontak">Kontak</a>
      </nav>
      <div class="nav-actions">
        <a class="button button-secondary" href="login.php">Login</a>
        <a class="button button-primary" href="register.php">Register</a>
      </div>
    </div>
  </header>

  <main>
    <section class="hero" id="home">
      <div class="container hero-grid">
        <div class="hero-copy">
          <span class="eyebrow">Sistem Fisioterapi Modern</span>
          <h1>Kelola pasien, jadwal, dan pembayaran dengan antarmuka yang bersih dan profesional.</h1>
          <p>Solusi native PHP untuk klinik fisio yang membutuhkan manajemen pasien, reservasi online, hasil terapi, dan laporan lengkap.</p>
          <div class="hero-actions">
            <a class="button button-primary" href="register.php">Daftar Pasien</a>
            <a class="button button-secondary" href="site-links.php">Lihat Semua Halaman</a>
          </div>
        </div>
        <div class="hero-card">
          <div class="stat-card">
            <div>
              <p class="stat-label">Total Pasien</p>
              <h2>1.280+</h2>
            </div>
            <div class="stat-badge">Aktif</div>
          </div>
          <div class="hero-stats">
            <div class="hero-stat">
              <p>Reservasi Hari Ini</p>
              <strong>42</strong>
            </div>
            <div class="hero-stat">
              <p>Pendapatan</p>
              <strong>Rp 38,7jt</strong>
            </div>
            <div class="hero-stat">
              <p>Terapis</p>
              <strong>12</strong>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="section section-alt" id="layanan">
      <div class="container">
        <div class="section-header">
          <span class="eyebrow">Layanan</span>
          <h2>Pelayanan Fisioterapi yang Terintegrasi</h2>
          <p>Menu layanan dirancang untuk membuat setiap sesi terapi dapat dikelola mulai dari pemesanan hingga hasil dan rekomendasi latihan.</p>
        </div>
        <div class="card-grid">
          <article class="feature-card">
            <h3>Terapi Rehabilitasi</h3>
            <p>Program khusus untuk pemulihan cedera otot, sendi, dan pasca-operasi dengan catatan medis terpusat.</p>
          </article>
          <article class="feature-card">
            <h3>Terapi Nyeri Kronis</h3>
            <p>Pendekatan personal untuk mengurangi nyeri punggung, leher, dan sendi dengan jadwal terapi yang mudah di-booking.</p>
          </article>
          <article class="feature-card">
            <h3>Program Latihan</h3>
            <p>Upload rencana latihan dan rekomendasi yang bisa diunduh oleh pasien setelah sesi terapi selesai.</p>
          </article>
        </div>
      </div>
    </section>

    <section class="section" id="terapis">
      <div class="container">
        <div class="section-header">
          <span class="eyebrow">Terapis</span>
          <h2>Daftar Terapis Profesional</h2>
          <p>Kelola data terapis lengkap dengan spesialisasi, jadwal, dan riwayat pasien.</p>
        </div>
        <div class="profile-grid">
          <article class="profile-card">
            <div class="profile-avatar">AR</div>
            <h3>Ani Rachma</h3>
            <p>Spesialis Terapi Muskuloskeletal</p>
          </article>
          <article class="profile-card">
            <div class="profile-avatar">MR</div>
            <h3>Mira Rahma</h3>
            <p>Spesialis Terapi Nyeri Kronis</p>
          </article>
          <article class="profile-card">
            <div class="profile-avatar">DP</div>
            <h3>Dewi Putri</h3>
            <p>Spesialis Rehabilitasi Olahraga</p>
          </article>
        </div>
      </div>
    </section>

    <section class="section section-alt" id="artikel">
      <div class="container">
        <div class="section-header">
          <span class="eyebrow">Artikel</span>
          <h2>Wawasan Kesehatan dan Terapi</h2>
          <p>Publikasi artikel untuk membantu pasien memahami kondisi dan rencana terapi mereka lebih baik.</p>
        </div>
        <div class="card-grid">
          <article class="article-card">
            <h3>5 Cara Mempercepat Pemulihan Pasca Operasi</h3>
            <p>Panduan praktis untuk latihan ringan dan tindak lanjut terapi agar hasil pemulihan optimal.</p>
          </article>
          <article class="article-card">
            <h3>Manfaat Terapi Fisioterapi untuk Nyeri Punggung</h3>
            <p>Pelajari teknik terapi dan rekomendasi aktivitas harian yang mendukung pengurangan nyeri.</p>
          </article>
          <article class="article-card">
            <h3>Cara Memilih Jadwal Terapi yang Efektif</h3>
            <p>Tips memadukan terapi dengan rutinitas harian agar konsistensi dan hasil lebih baik.</p>
          </article>
        </div>
      </div>
    </section>

    <section class="section" id="kontak">
      <div class="container contact-grid">
        <div>
          <span class="eyebrow">Kontak</span>
          <h2>Siap Bantu Klinik Fisioterapi Anda</h2>
          <p>Hubungi kami untuk demo sistem atau mulai integrasi manajemen pasien dan pembayaran online.</p>
          <p class="contact-info"><strong>Email:</strong> hello@fisioterapi.id<br /><strong>Telepon:</strong> 0812-3456-7890</p>
        </div>
        <a class="button button-primary" href="register.php">Mulai Sekarang</a>
      </div>
    </section>
  </main>

  <footer class="footer">
    <div class="container footer-inner">
      <p>&copy; 2026 Sistem Informasi Fisioterapi. Semua hak dilindungi.</p>
      <nav>
        <a href="#home">Home</a>
        <a href="#layanan">Layanan</a>
        <a href="#kontak">Kontak</a>
      </nav>
    </div>
  </footer>
</body>
</html>
