{
  "project": {
    "name": "Sistem Informasi Fisioterapi",
    "technology": {
      "backend": "PHP Native",
      "database": "MySQL",
      "frontend": "HTML5, CSS3, JavaScript, Bootstrap 5",
      "payment_gateway": "Midtrans",
      "server": "Apache (XAMPP/CPanel)"
    }
  },
  "modules": {
    "authentication": {
      "features": [
        "Login",
        "Register Pasien",
        "Forgot Password",
        "Logout",
        "Role Management"
      ],
      "roles": [
        "Admin",
        "Terapis",
        "Pasien"
      ]
    },
    "admin": {
      "dashboard": [
        "Total Pasien",
        "Total Terapis",
        "Total Reservasi",
        "Total Pendapatan",
        "Grafik Kunjungan",
        "Grafik Pembayaran"
      ],
      "master_data": [
        "Data Pasien",
        "Data Terapis",
        "Data Layanan Fisioterapi",
        "Data Jadwal",
        "Data Artikel Kesehatan"
      ],
      "transaction": [
        "Kelola Reservasi",
        "Verifikasi Pembayaran",
        "Riwayat Pembayaran"
      ],
      "reports": [
        "Laporan Pasien",
        "Laporan Reservasi",
        "Laporan Pendapatan",
        "Export PDF",
        "Export Excel"
      ]
    },
    "therapist": {
      "dashboard": [
        "Jadwal Hari Ini",
        "Jumlah Pasien",
        "Riwayat Terapi"
      ],
      "features": [
        "Kelola Jadwal",
        "Melihat Data Pasien",
        "Input Hasil Pemeriksaan",
        "Input Catatan Terapi",
        "Upload Program Latihan"
      ]
    },
    "patient": {
      "dashboard": [
        "Profil Pasien",
        "Jadwal Terapi",
        "Riwayat Terapi",
        "Riwayat Pembayaran"
      ],
      "features": [
        "Booking Jadwal",
        "Pilih Terapis",
        "Pilih Layanan",
        "Pembayaran Online",
        "Download Hasil Terapi",
        "Melihat Artikel Kesehatan"
      ]
    },
    "payment_gateway": {
      "provider": "Midtrans",
      "features": [
        "QRIS",
        "Transfer Bank",
        "E-Wallet",
        "Virtual Account",
        "Payment Notification",
        "Payment Status"
      ]
    }
  },
  "database": {
    "tables": [
      {
        "name": "users",
        "fields": [
          "id",
          "nama",
          "email",
          "password",
          "role",
          "created_at"
        ]
      },
      {
        "name": "pasien",
        "fields": [
          "id_pasien",
          "user_id",
          "nama",
          "jenis_kelamin",
          "tanggal_lahir",
          "alamat",
          "telepon"
        ]
      },
      {
        "name": "terapis",
        "fields": [
          "id_terapis",
          "nama",
          "spesialis",
          "telepon",
          "foto"
        ]
      },
      {
        "name": "layanan",
        "fields": [
          "id_layanan",
          "nama_layanan",
          "deskripsi",
          "harga",
          "durasi"
        ]
      },
      {
        "name": "jadwal",
        "fields": [
          "id_jadwal",
          "terapis_id",
          "tanggal",
          "jam_mulai",
          "jam_selesai",
          "status"
        ]
      },
      {
        "name": "reservasi",
        "fields": [
          "id_reservasi",
          "pasien_id",
          "terapis_id",
          "layanan_id",
          "jadwal_id",
          "keluhan",
          "status"
        ]
      },
      {
        "name": "hasil_terapi",
        "fields": [
          "id_hasil",
          "reservasi_id",
          "diagnosa",
          "catatan",
          "rekomendasi"
        ]
      },
      {
        "name": "pembayaran",
        "fields": [
          "id_pembayaran",
          "reservasi_id",
          "order_id",
          "metode",
          "jumlah",
          "status",
          "tanggal"
        ]
      },
      {
        "name": "artikel",
        "fields": [
          "id_artikel",
          "judul",
          "isi",
          "gambar",
          "tanggal"
        ]
      }
    ]
  },
  "pages": {
    "public": [
      "Home",
      "Tentang Kami",
      "Layanan",
      "Daftar Terapis",
      "Artikel",
      "Kontak",
      "Login",
      "Register"
    ],
    "admin": [
      "Dashboard",
      "Kelola Pasien",
      "Kelola Terapis",
      "Kelola Layanan",
      "Kelola Jadwal",
      "Kelola Reservasi",
      "Kelola Pembayaran",
      "Laporan",
      "Pengaturan"
    ],
    "therapist": [
      "Dashboard",
      "Jadwal Saya",
      "Data Pasien",
      "Hasil Terapi",
      "Profil"
    ],
    "patient": [
      "Dashboard",
      "Booking Terapi",
      "Riwayat Reservasi",
      "Riwayat Pembayaran",
      "Hasil Terapi",
      "Profil"
    ]
  },
  "ui_design": {
    "theme": "Mengikuti desain.md",
    "colors": {
      "primary": "#2563EB",
      "secondary": "#10B981",
      "background": "#F8FAFC"
    },
    "components": [
      "Navbar Responsive",
      "Sidebar Dashboard",
      "Card Statistik",
      "Data Tables",
      "Form Booking",
      "Calendar Jadwal",
      "Payment Modal",
      "Chart Dashboard"
    ]
  },
  "development_phases": [
    {
      "phase": 1,
      "name": "Analisis Kebutuhan",
      "duration": "3 Hari"
    },
    {
      "phase": 2,
      "name": "Desain Database dan ERD",
      "duration": "2 Hari"
    },
    {
      "phase": 3,
      "name": "Pembuatan Authentication",
      "duration": "3 Hari"
    },
    {
      "phase": 4,
      "name": "Pembuatan Dashboard Admin",
      "duration": "5 Hari"
    },
    {
      "phase": 5,
      "name": "Pembuatan Dashboard Terapis",
      "duration": "4 Hari"
    },
    {
      "phase": 6,
      "name": "Pembuatan Dashboard Pasien",
      "duration": "5 Hari"
    },
    {
      "phase": 7,
      "name": "Integrasi Payment Gateway",
      "duration": "3 Hari"
    },
    {
      "phase": 8,
      "name": "Testing dan Bug Fixing",
      "duration": "5 Hari"
    },
    {
      "phase": 9,
      "name": "Deployment",
      "duration": "2 Hari"
    }
  ]
}