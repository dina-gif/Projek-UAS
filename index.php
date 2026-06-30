<?php
// index.php
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>FisioTerapi</title>

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />

  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
    }

    body {
      background: #f8f9fc;
      color: #333;
    }

    a {
      text-decoration: none;
    }

    .navbar {
      background: white;
      padding: 18px 0;
      box-shadow: 0 3px 10px rgba(0, 0, 0, .05);
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      z-index: 9999;
    }

    .logo {
      font-size: 22px;
      font-weight: 700;
      color: #2563eb;
    }

    .logo i {
      margin-right: 8px;
    }

    .nav-link {
      color: #333 !important;
      font-weight: 500;
      margin: 0 10px;
    }

    .nav-link.active {
      color: #2563eb !important;
    }

    .btn-login {

      background: #2563eb;
      color: white;
      border-radius: 8px;
      padding: 10px 28px;
      font-weight: 600;

    }

    .btn-login:hover {
      background: #1d4ed8;
      color: white;
    }

    .hero {

      padding: 80px 0;

    }

    .hero h1 {

      font-size: 55px;
      font-weight: 700;
      line-height: 1.2;
      margin-bottom: 20px;

    }

    .hero p {

      color: #666;
      line-height: 30px;
      margin-bottom: 35px;

    }

    .btn-book {

      background: #2563eb;
      color: white;
      padding: 12px 30px;
      border-radius: 10px;
      margin-right: 10px;
      font-weight: 600;

    }

    .btn-service {

      border: 1px solid #ccc;
      color: #333;
      padding: 12px 30px;
      border-radius: 10px;

    }

    .hero img {

      width: 100%;

    }

    .feature-card {

      background: white;
      border-radius: 15px;
      padding: 25px;
      box-shadow: 0 5px 20px rgba(0, 0, 0, .05);
      transition: .3s;

    }

    .feature-card:hover {

      transform: translateY(-5px);

    }

    .feature-card i {

      font-size: 35px;
      color: #2563eb;
      margin-bottom: 15px;

    }

    .feature-card h5 {

      font-weight: 600;

    }

    .feature-card p {

      font-size: 14px;
      color: #666;

    }

    .section-title {

      text-align: center;
      margin-top: 80px;
      margin-bottom: 50px;

    }

    .section-title h2 {

      font-weight: 700;

    }

    .section-title p {

      color: #666;

    }

    .service-card {

      background: white;
      border-radius: 25px;
      border: 1px solid #ddd;
      padding: 35px 25px;
      text-align: center;
      transition: .3s;

    }

    .service-card:hover {

      border-color: #2563eb;
      transform: translateY(-6px);

    }

    .service-card i {

      font-size: 45px;
      color: #2563eb;
      margin-bottom: 20px;

    }

    .service-card h5 {

      font-weight: 600;

    }

    .service-card p {

      color: #666;
      font-size: 14px;

    }

    /* Terapis */

    .therapist-section {
      padding: 90px 0;
    }

    .therapist-img img {
      width: 100%;
      border-radius: 20px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, .1);
    }

    .therapist-content h2 {
      font-weight: 700;
      margin-bottom: 20px;
    }

    .therapist-content h5 {
      color: #2563eb;
      font-weight: 600;
      margin-bottom: 20px;
    }

    .therapist-content p {
      color: #666;
      line-height: 30px;
      text-align: justify;
    }

    .therapist-info {
      margin-top: 25px;
    }

    .therapist-info li {
      list-style: none;
      margin-bottom: 12px;
      font-size: 16px;
    }

    .therapist-info i {
      color: #2563eb;
      margin-right: 10px;
    }

    .btn-profile {
      margin-top: 25px;
      background: #2563eb;
      color: #fff;
      padding: 12px 28px;
      border-radius: 8px;
      font-weight: 600;
    }

    .btn-profile:hover {
      background: #1d4ed8;
      color: white;
    }

    /*================ CONTACT =================*/

    .contact-section {
      padding: 80px 0;
      background: #fff;
    }

    .contact-title {
      font-size: 34px;
      font-weight: 700;
      margin-bottom: 10px;
    }

    .contact-subtitle {
      color: #666;
      margin-bottom: 40px;
    }

    .contact-card {
      background: #fff;
      border-radius: 15px;
      padding: 20px;
      margin-bottom: 20px;
      box-shadow: 0 5px 20px rgba(0, 0, 0, .06);
      display: flex;
      align-items: flex-start;
      transition: .3s;
    }

    .contact-card:hover {
      transform: translateY(-5px);
    }

    .contact-icon {
      width: 55px;
      height: 55px;
      background: #EEF4FF;
      border-radius: 50%;
      display: flex;
      justify-content: center;
      align-items: center;
      color: #2563EB;
      font-size: 22px;
      margin-right: 18px;
    }

    .contact-card h5 {
      font-size: 18px;
      font-weight: 600;
    }

    .contact-card p {
      color: #666;
      margin-bottom: 0;
    }

    .map-box {

      background: white;
      border-radius: 18px;
      box-shadow: 0 5px 20px rgba(0, 0, 0, .06);
      overflow: hidden;

    }

    .map-box iframe {
      width: 100%;
      height: 430px;
      border: none;
    }

    .btn-map {
      width: 100%;
      border-radius: 0;
      padding: 15px;
      font-weight: 600;
    }

    .form-box {

      background: #fff;
      border-radius: 18px;
      padding: 30px;
      box-shadow: 0 5px 20px rgba(0, 0, 0, .06);

    }

    .form-box h3 {

      font-weight: 700;
      margin-bottom: 10px;

    }

    .form-box p {

      color: #666;
      margin-bottom: 25px;

    }

    .form-control,
    .form-select {

      border-radius: 10px;
      padding: 13px;

    }

    textarea {

      resize: none;

    }

    .btn-send {

      width: 100%;
      padding: 14px;
      border-radius: 10px;
      background: #2563EB;
      color: white;
      font-weight: 600;

    }

    .btn-send:hover {

      background: #1D4ED8;
      color: white;

    }

    /*==============================
        ABOUT HERO
==============================*/

    .about-section {
      padding: 80px 0;
      background: #fff;
    }

    .about-title {

      font-size: 52px;
      font-weight: 700;
      color: #1F2937;
      margin-bottom: 20px;

    }

    .about-desc {

      color: #666;
      font-size: 18px;
      line-height: 35px;
      margin-bottom: 35px;

    }

    .about-img img {

      width: 100%;
      border-radius: 20px;

    }

    .about-stat {

      background: #fff;
      border-radius: 15px;
      padding: 22px;
      box-shadow: 0 5px 20px rgba(0, 0, 0, .08);
      display: flex;
      align-items: center;
      transition: .3s;

    }

    .about-stat:hover {

      transform: translateY(-5px);

    }

    .about-icon {

      width: 60px;
      height: 60px;
      background: #2563EB;
      color: #fff;
      border-radius: 50%;
      display: flex;
      justify-content: center;
      align-items: center;
      font-size: 24px;
      margin-right: 18px;

    }

    .about-stat h3 {

      font-size: 34px;
      font-weight: 700;
      margin-bottom: 0;

    }

    .about-stat p {

      margin: 0;
      color: #666;

    }

    /* ===========================
   CTA / BOOKING
=========================== */

    .cta-section {
      margin-top: 70px;
      margin-bottom: 70px;
    }

    .cta-box {

      background: linear-gradient(90deg, #EEF4FF, #F8FBFF);
      border-radius: 18px;
      padding: 35px 40px;

    }

    .cta-icon {

      width: 70px;
      height: 70px;
      background: #DCEBFF;
      color: #2563EB;
      border-radius: 15px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 35px;

    }

    .cta-title {

      font-size: 32px;
      font-weight: 700;

    }

    .cta-text {

      color: #666;
      margin-top: 5px;

    }

    .btn-booking {

      background: #2563EB;
      color: white;
      padding: 14px 35px;
      border-radius: 10px;
      font-weight: 600;

    }

    .btn-booking:hover {

      background: #1D4ED8;
      color: white;

    }

    /* ===========================
        FOOTER
=========================== */

    .footer {

      background: white;
      padding: 50px 0 20px;

    }

    .footer-logo {

      font-size: 30px;
      font-weight: 700;
      color: #2563EB;

    }

    .footer-desc {

      margin-top: 15px;
      color: #666;
      line-height: 28px;

    }

    .footer h5 {

      font-weight: 700;
      margin-bottom: 20px;

    }

    .footer ul {

      list-style: none;
      padding: 0;

    }

    .footer ul li {

      margin-bottom: 10px;

    }

    .footer ul li a {

      color: #555;
      text-decoration: none;

    }

    .footer ul li a:hover {

      color: #2563EB;

    }

    .footer-contact i {

      color: #2563EB;
      width: 25px;

    }

    .footer-contact p {

      color: #555;
      margin-bottom: 10px;

    }

    .social a {

      display: inline-block;
      width: 38px;
      height: 38px;
      line-height: 38px;
      text-align: center;
      background: #EEF4FF;
      color: #2563EB;
      border-radius: 50%;
      margin-right: 8px;
      transition: .3s;

    }

    .social a:hover {

      background: #2563EB;
      color: white;

    }

    .footer-bottom {

      margin-top: 40px;
      padding-top: 20px;
      border-top: 1px solid #ddd;
      text-align: center;
      color: #777;

    }

    .form-note {

      color: #777;
      font-size: 14px;
      margin-top: 15px;

    }
  </style>

</head>

<body>

  <nav class="navbar navbar-expand-lg">

    <div class="container">

      <a class="navbar-brand logo" href="#">
        <i class="fa-solid fa-heart-pulse"></i>
        FisioTerapi
      </a>

      <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#menu">

        <span class="navbar-toggler-icon"></span>

      </button>

      <div class="collapse navbar-collapse" id="menu">

        <ul class="navbar-nav mx-auto">

          <li class="nav-item">
            <a class="nav-link active" href="#beranda">Beranda</a>
          </li>

          <li class="nav-item">
            <a class="nav-link" href="#layanan">Layanan</a>
          </li>

          <li class="nav-item">
            <a class="nav-link" href="#terapis">Terapis</a>
          </li>

          <li class="nav-item">
            <a class="nav-link" href="#tentang-kami">Tentang Kami</a>
          </li>

          <li class="nav-item">
            <a class="nav-link" href="#kontak">Kontak</a>
          </li>

        </ul>

        <a href="login.php" class="btn btn-login">
          Login
        </a>

      </div>

    </div>

  </nav>

  <!-- HERO SECTION -->
  <section class="hero" id="beranda">

    <div class="container">

      <div class="row align-items-center">

        <div class="col-lg-6">

          <h1>
            Solusi Terbaik Untuk
            <br>
            Kesehatan Gerak Anda
          </h1>

          <p>
            FisioCare hadir untuk membantu Anda memulihkan
            mobilitas, mengurangi nyeri, dan meningkatkan
            kualitas hidup melalui layanan fisioterapi profesional.
          </p>

          <a href="#" class="btn btn-book">
            Booking Sekarang
          </a>

          <a href="#" class="btn btn-service">
            Lihat Layanan
          </a>

        </div>

        <div class="col-lg-6 text-center">

          <img src="assets/images/hero.png"
            class="img-fluid"
            alt="Hero">

        </div>

      </div>

    </div>

  </section>

  <!-- KEUNGGULAN -->

  <section>

    <div class="container">

      <div class="row g-4">

        <div class="col-md-4">

          <div class="feature-card text-center">

            <i class="fa-solid fa-user-doctor"></i>

            <h5>Terapis Profesional</h5>

            <p>
              Ditangani oleh fisioterapis
              berpengalaman dan bersertifikat.
            </p>

          </div>

        </div>

        <div class="col-md-4">

          <div class="feature-card text-center">

            <i class="fa-solid fa-notes-medical"></i>

            <h5>Layanan Lengkap</h5>

            <p>
              Berbagai layanan terapi
              untuk kebutuhan Anda.
            </p>

          </div>

        </div>

        <div class="col-md-4">

          <div class="feature-card text-center">

            <i class="fa-solid fa-calendar-days"></i>

            <h5>Jadwal Fleksibel</h5>

            <p>
              Atur jadwal terapi sesuai
              waktu yang Anda inginkan.
            </p>

          </div>

        </div>

      </div>

    </div>

  </section>


  <!-- LAYANAN -->

  <section id="layanan">

    <div class="container">

      <div class="section-title">

        <h2>Layanan Kami</h2>

        <p>
          Berbagai jenis layanan fisioterapi
          untuk membantu pemulihan kesehatan Anda.
        </p>

      </div>

      <div class="row g-4">

        <div class="col-lg-3">

          <div class="service-card">

            <i class="fa-solid fa-person-walking"></i>

            <h5>Terapi Nyeri</h5>

            <p>
              Mengatasi nyeri punggung,
              sendi dan leher.
            </p>

          </div>

        </div>

        <div class="col-lg-3">

          <div class="service-card">

            <i class="fa-solid fa-bone"></i>

            <h5>Rehabilitasi Cedera</h5>

            <p>
              Pemulihan pasca cedera
              olahraga maupun kecelakaan.
            </p>

          </div>

        </div>

        <div class="col-lg-3">

          <div class="service-card">

            <i class="fa-solid fa-brain"></i>

            <h5>Terapi Neurologi</h5>

            <p>
              Terapi stroke,
              parkinson,
              dan gangguan saraf.
            </p>

          </div>

        </div>

        <div class="col-lg-3">

          <div class="service-card">

            <i class="fa-solid fa-child-reaching"></i>

            <h5>Terapi Anak</h5>

            <p>
              Terapi tumbuh kembang
              dan gangguan motorik anak.
            </p>

          </div>

        </div>

      </div>

    </div>

  </section>

  <!-- TERAPIS -->

  <section class="therapist-section" id="terapis">

    <div class="container">

      <div class="row align-items-center">

        <div class="col-lg-5">

          <div class="therapist-img">

            <img src="gambar/1.jpg" alt="Terapis">

          </div>

        </div>

        <div class="col-lg-7">

          <div class="therapist-content">

            <h2>Terapis Profesional</h2>

            <h5>Nurul Safira</h5>

            <p>
              Nurul Safira merupakan fisioterapis profesional yang memiliki pengalaman dalam menangani berbagai kasus cedera olahraga, nyeri otot, gangguan sendi, serta rehabilitasi pascaoperasi. Dengan pendekatan terapi yang tepat dan berfokus pada kebutuhan setiap pasien, beliau berkomitmen membantu meningkatkan kualitas hidup melalui pelayanan fisioterapi yang aman, nyaman, dan profesional.
            </p>

            <ul class="therapist-info">

              <li>
                <i class="fa-solid fa-graduation-cap"></i>
                Lulusan S1 Fisioterapi
              </li>

              <li>
                <i class="fa-solid fa-award"></i>
                Berpengalaman
              </li>

              <li>
                <i class="fa-solid fa-heart-pulse"></i>
                Spesialis Rehabilitasi Cedera & Terapi Nyeri
              </li>

              <li>
                <i class="fa-solid fa-user-check"></i>
                Telah menangani lebih dari 2.000 pasien
              </li>

            </ul>

            <a href="#" class="btn btn-profile">
              Lihat Profil
            </a>

          </div>

        </div>

      </div>

    </div>

  </section>

  <!-- ==========================
      TENTANG KAMI
=========================== -->

  <section class="about-section" id="tentang-kami">

    <div class="container">

      <div class="row align-items-center">

        <div class="col-lg-6">

          <h1 class="about-title">
            Tentang FisioCare
          </h1>

          <p class="about-desc">

            FisioCare hadir untuk memberikan layanan fisioterapi
            yang profesional, aman, dan terpercaya.
            Kami berkomitmen membantu setiap pasien
            mencapai kualitas hidup yang lebih baik melalui
            penanganan yang tepat dan personal.

          </p>

          <div class="row g-3">

            <div class="col-md-4">

              <div class="about-stat">

                <div class="about-icon">

                  <i class="fa-solid fa-users"></i>

                </div>

                <div>

                  <h3>500+</h3>

                  <p>Pasien Terbantu</p>

                </div>

              </div>

            </div>

            <div class="col-md-4">

              <div class="about-stat">

                <div class="about-icon">

                  <i class="fa-solid fa-calendar-check"></i>

                </div>

                <div>

                  <h3>1000+</h3>

                  <p>Sesi Terapi</p>

                </div>

              </div>

            </div>

            <div class="col-md-4">

              <div class="about-stat">

                <div class="about-icon">

                  <i class="fa-solid fa-user-doctor"></i>

                </div>

                <div>

                  <h3>6+</h3>

                  <p>Tahun Pengalaman</p>

                </div>

              </div>

            </div>

          </div>

        </div>

        <div class="col-lg-6">

          <div class="about-img">

            <img src="assets/images/about.png" alt="Tentang Kami">

          </div>

        </div>

      </div>

    </div>

  </section>

  <!-- KONTAK -->
  <section class="contact-section" id="kontak">
    <div class="container">
      <div class="row">

        <!-- KIRI -->
        <div class="col-lg-7">
          <h2 class="contact-title">
            Informasi Kontak
          </h2>
          <p class="contact-subtitle">
            Hubungi kami apabila memiliki pertanyaan
            atau ingin melakukan reservasi terapi.
          </p>
          <div class="row">
            <div class="col-md-5">
              <div class="contact-card">
                <div class="contact-icon">
                  <i class="fa-solid fa-location-dot"></i>
                </div>
                <div>
                  <h5>Alamat Klinik</h5>
                  <p>
                    Jl. Sehat No.123
                    Jakarta Selatan
                    DKI Jakarta 12345
                  </p>
                </div>
              </div>
              <div class="contact-card">
                <div class="contact-icon">
                  <i class="fa-solid fa-phone"></i>
                </div>
                <div>
                  <h5>Telepon</h5>
                  <p>
                    (021)12345678
                  </p>
                </div>
              </div>
              <div class="contact-card">
                <div class="contact-icon">
                  <i class="fa-solid fa-envelope"></i>
                </div>
                <div>
                  <h5>Email</h5>
                  <p>
                    info@fisiocare.id
                  </p>
                </div>
              </div>
              <div class="contact-card">
                <div class="contact-icon">
                  <i class="fa-regular fa-clock"></i>
                </div>
                <div>
                  <h5>Jam Operasional</h5>
                  <p>Senin - Jumat : 08.00 - 17.00 WIB</p>
                  <p>Sabtu : 08.00 - 12.00 WIB</p>
                  <p>Minggu : Tutup</p>
                </div>
              </div>
            </div>
            <div class="col-md-7">
              <div class="map-box">
                <iframe
                  src="https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d246.6498463279501!2d118.76266965000616!3d-8.460729236529037!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e0!3m2!1sid!2sid!4v1782784064006!5m2!1sid!2sid" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="strict-origin-when-cross-origin">
                </iframe>
                <a href="https://maps.google.com"
                  target="_blank"
                  class="btn btn-outline-primary btn-map">
                  <i class="fa-solid fa-map-location-dot"></i>
                  Lihat di Google Maps
                </a>
              </div>
            </div>
          </div>
        </div>

        <!-- KANAN -->
        <div class="col-lg-5">
          <div class="form-box">
            <h3>Kirim Pesan</h3>
            <p>
              Isi formulir di bawah ini dan kami akan segera menghubungi Anda.
            </p>
            <form>
              <div class="row">
                <div class="col-md-6 mb-3">
                  <input
                    type="text"
                    class="form-control"
                    placeholder="Nama Lengkap">
                </div>
                <div class="col-md-6 mb-3">
                  <input
                    type="text"
                    class="form-control"
                    placeholder="Nomor WhatsApp">
                </div>
              </div>
              <div class="mb-3">
                <input
                  type="email"
                  class="form-control"
                  placeholder="Email">
              </div>
              <div class="mb-3">
                <select class="form-select">
                  <option>Pilih Topik</option>
                  <option>Konsultasi</option>
                  <option>Reservasi</option>
                  <option>Informasi Layanan</option>
                  <option>Lainnya</option>
                </select>
              </div>
              <div class="mb-3">
                <textarea
                  class="form-control"
                  rows="6"
                  placeholder="Pesan Anda"></textarea>
              </div>
              <button class="btn btn-send">
                <i class="fa-regular fa-paper-plane"></i>
                Kirim Pesan
              </button>
              <div class="form-note">
                <i class="fa-solid fa-lock"></i>
                Data Anda aman dan tidak akan dibagikan kepada pihak lain.
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="cta-section">
    <div class="container">
      <div class="cta-box">
        <div class="row align-items-center">
          <div class="col-lg-8">
            <div class="d-flex">
              <div class="cta-icon">
                <i class="fa-solid fa-calendar-check"></i>
              </div>
              <div class="ms-4">
                <h2 class="cta-title">
                  Siap Memulai Terapi?
                </h2>
                <p class="cta-text">
                  Booking jadwal konsultasi sekarang dan dapatkan
                  penanganan terbaik dari terapis kami.
                </p>
              </div>
            </div>
          </div>
          <div class="col-lg-4 text-end">
            <a href="booking.php"
              class="btn btn-booking">
              Booking Sekarang
            </a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <footer class="footer">

    <div class="container">

      <div class="row">

        <div class="col-lg-4">

          <h3 class="footer-logo">

            <i class="fa-solid fa-heart-pulse"></i>

            FisioCare

          </h3>

          <p class="footer-desc">

            FisioCare adalah platform layanan fisioterapi
            profesional yang membantu pemulihan kesehatan
            gerak dan meningkatkan kualitas hidup.

          </p>

        </div>

        <div class="col-lg-2">

          <h5>Menu</h5>

          <ul>

            <li><a href="#">Beranda</a></li>

            <li><a href="#">Layanan</a></li>

            <li><a href="#">Terapis</a></li>

            <li><a href="#">Artikel</a></li>

            <li><a href="#">Tentang Kami</a></li>

            <li><a href="#">Kontak</a></li>

          </ul>

        </div>

        <div class="col-lg-2">

          <h5>Layanan</h5>

          <ul>

            <li><a href="#">Terapi Nyeri</a></li>

            <li><a href="#">Rehabilitasi Cedera</a></li>

            <li><a href="#">Terapi Neurologi</a></li>

            <li><a href="#">Terapi Anak</a></li>

            <li><a href="#">Postural Correction</a></li>

          </ul>

        </div>

        <div class="col-lg-2 footer-contact">

          <h5>Kontak</h5>

          <p><i class="fa-solid fa-location-dot"></i>Jl. Sehat No.123</p>

          <p><i class="fa-solid fa-phone"></i>(021)12345678</p>

          <p><i class="fa-solid fa-envelope"></i>info@fisiocare.id</p>

          <div class="social mt-3">

            <a href="#"><i class="fab fa-facebook-f"></i></a>

            <a href="#"><i class="fab fa-instagram"></i></a>

            <a href="#"><i class="fab fa-whatsapp"></i></a>

          </div>

        </div>

        <div class="col-lg-2 footer-contact">

          <h5>Jam Operasional</h5>

          <p>Senin - Jumat</p>

          <p>08.00 - 17.00 WIB</p>

          <br>

          <p>Sabtu</p>

          <p>08.00 - 12.00 WIB</p>

          <br>

          <p>Minggu & Hari Libur</p>

          <p>Tutup</p>

        </div>

      </div>

      <div class="footer-bottom">

        © 2026 FisioCare. All rights reserved.

      </div>

    </div>

  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>