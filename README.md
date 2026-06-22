# Sistem Informasi Fisioterapi

Ini adalah prototype sistem manajemen klinik fisioterapi built with PHP Native dan MySQL.

Setup cepat (XAMPP):

1. Tempatkan proyek di `C:\xampp\htdocs\fisioterapi`.
2. Import `database.sql` ke MySQL (gunakan phpMyAdmin atau mysql client):

```sql
SOURCE database.sql;
```

3. Pastikan `config.php` berisi kredensial database yang benar.
4. Atur `MIDTRANS_SERVER_KEY` dan `MIDTRANS_CLIENT_KEY` di `config.php`.
5. (Optional) Untuk pengiriman email otomatis, instal PHPMailer melalui Composer:

```bash
composer require phpmailer/phpmailer
```

Tambahkan file `vendor/autoload.php` (Composer) akan di-include otomatis oleh aplikasi jika ada.
5. Jalankan XAMPP Apache dan MySQL, buka `http://localhost/fisioterapi/`.

Fitur yang sudah tersedia:
- Autentikasi (Admin, Terapis, Pasien)
- CRUD master data: pasien, terapis, layanan, jadwal
- Reservasi dan manajemen reservasi
- Halaman dashboard untuk Admin/Terapis/Pasien
- Pembayaran via Midtrans (integrasi Snap + webhook)
- Lupa password (token) dan reset password
- Profil pasien dan terapis

Hal yang perlu dikonfigurasi sebelum produksi:
- SMTP untuk pengiriman email (forgot password)
- Ganti `MIDTRANS_*` keys dengan kunci produksi
- Amankan `midtrans-notify.php` endpoint (validasi signature)

Database: jika Anda telah mengimpor `database.sql` versi lama, pastikan tabel `pembayaran` memiliki kolom `transaction_id` dan `raw_notification`.

Contoh SQL untuk menambahkan kolom yang diperlukan:

```sql
ALTER TABLE pembayaran ADD COLUMN transaction_id VARCHAR(255) DEFAULT NULL;
ALTER TABLE pembayaran ADD COLUMN raw_notification LONGTEXT DEFAULT NULL;
```

Jika ingin saya lanjutkan implementasi lebih detail (email SMTP, validasi webhook signature, unit tests), beri tahu fitur prioritas Anda.
