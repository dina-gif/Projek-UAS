<?php
require_once __DIR__ . '/config.php';
require_role('Pasien');

$user = current_user();

$stmt = $pdo->prepare('SELECT * FROM pasien WHERE user_id = :user_id LIMIT 1');
$stmt->execute(['user_id' => $user['id']]);
$pasien = $stmt->fetch();

$pendingPayments = [];
$snapToken = null;
$paymentError = null;
$paymentMessage = null;

if ($pasien) {
    $stmt = $pdo->prepare(
        'SELECT pay.*, r.id_reservasi, l.nama_layanan, j.tanggal, j.jam_mulai
         FROM pembayaran pay
         JOIN reservasi r ON pay.reservasi_id = r.id_reservasi
         JOIN layanan l ON r.layanan_id = l.id_layanan
         JOIN jadwal j ON r.jadwal_id = j.id_jadwal
         WHERE r.pasien_id = :pasien_id AND pay.status != "Lunas"
         ORDER BY pay.tanggal DESC'
    );
    $stmt->execute(['pasien_id' => $pasien['id_pasien']]);
    $pendingPayments = $stmt->fetchAll();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payment_id = intval($_POST['payment_id'] ?? 0);

    if ($payment_id <= 0) {
        $paymentError = 'Pembayaran tidak valid.';
    } else {
        $stmt = $pdo->prepare('SELECT pay.*, r.id_reservasi, r.keluhan, l.nama_layanan, l.harga, j.tanggal, j.jam_mulai, t.nama AS terapis_nama FROM pembayaran pay JOIN reservasi r ON pay.reservasi_id = r.id_reservasi JOIN layanan l ON r.layanan_id = l.id_layanan JOIN jadwal j ON r.jadwal_id = j.id_jadwal JOIN terapis t ON r.terapis_id = t.id_terapis WHERE pay.id_pembayaran = :id_pembayaran LIMIT 1');
        $stmt->execute(['id_pembayaran' => $payment_id]);
        $payment = $stmt->fetch();

        if (!$payment) {
            $paymentError = 'Pembayaran tidak ditemukan.';
        } else {
            $body = [
                'transaction_details' => [
                    'order_id' => $payment['order_id'],
                    'gross_amount' => $payment['jumlah'],
                ],
                'customer_details' => [
                    'first_name' => $user['nama'],
                    'email' => $user['email'],
                ],
                'item_details' => [
                    [
                        'id' => $payment['id_pembayaran'],
                        'price' => $payment['jumlah'],
                        'quantity' => 1,
                        'name' => $payment['nama_layanan'],
                    ],
                ],
                'enabled_payments' => ['gopay', 'shopeepay', 'permata_va', 'bca_va', 'bni_va', 'bri_va', 'qris'],
            ];

            $result = midtrans_request('/snap/v1/transactions', $body);
            if (isset($result['token'])) {
                $snapToken = $result['token'];
            } else {
                $paymentError = $result['error'] ?? 'Gagal memproses transaksi Midtrans.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Pembayaran - Sistem Informasi Fisioterapi</title>
  <link rel="stylesheet" href="assets/css/style.css" />
  <?php if ($snapToken): ?>
    <script src="<?php echo midtrans_snap_script_url(); ?>" data-client-key="<?php echo MIDTRANS_CLIENT_KEY; ?>"></script>
  <?php endif; ?>
</head>
<body>
  <header class="top-nav">
    <div class="container nav-inner">
      <a class="brand" href="index.php">Fisioterapi</a>
      <nav class="nav-menu">
        <a href="patient-dashboard.php">Dashboard</a>
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
        <span class="eyebrow">Pembayaran</span>
        <h1>Bayar Reservasi Anda</h1>
        <p>Pilih salah satu transaksi tertunda dan lanjutkan ke Midtrans untuk pembayaran online.</p>
      </div>

      <?php if ($paymentError): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($paymentError); ?></div>
      <?php endif; ?>
      <?php if ($paymentMessage): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($paymentMessage); ?></div>
      <?php endif; ?>

      <?php if (empty($pendingPayments)): ?>
        <div class="admin-panel">
          <p>Tidak ada pembayaran tertunda untuk akun Anda.</p>
        </div>
      <?php else: ?>
        <div class="admin-panel admin-table-panel">
          <div class="table-scroll">
            <table class="admin-table">
              <thead>
                <tr>
                  <th>Order ID</th>
                  <th>Layanan</th>
                  <th>Jadwal</th>
                  <th>Jumlah</th>
                  <th>Status</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($pendingPayments as $payment): ?>
                  <tr>
                    <td><?php echo htmlspecialchars($payment['order_id']); ?></td>
                    <td><?php echo htmlspecialchars($payment['nama_layanan']); ?></td>
                    <td><?php echo htmlspecialchars($payment['tanggal'] . ' — ' . $payment['jam_mulai']); ?></td>
                    <td>Rp <?php echo number_format($payment['jumlah'], 0, ',', '.'); ?></td>
                    <td><?php echo htmlspecialchars($payment['status']); ?></td>
                    <td>
                      <form method="post" action="payment.php" class="inline-form">
                        <input type="hidden" name="payment_id" value="<?php echo htmlspecialchars($payment['id_pembayaran']); ?>" />
                        <button class="button button-primary button-small" type="submit">Bayar</button>
                      </form>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </main>

  <?php if ($snapToken): ?>
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        snap.pay(<?php echo json_encode($snapToken); ?>, {
          onSuccess: function(result) {
            window.location.href = 'payment-callback.php?status=success&order_id=' + encodeURIComponent(result.order_id);
          },
          onPending: function(result) {
            window.location.href = 'payment-callback.php?status=pending&order_id=' + encodeURIComponent(result.order_id);
          },
          onError: function(result) {
            window.location.href = 'payment-callback.php?status=error&order_id=' + encodeURIComponent(result.order_id);
          },
          onClose: function() {
            alert('Pembayaran dibatalkan. Silakan coba lagi.');
          }
        });
      });
    </script>
  <?php endif; ?>
</body>
</html>
