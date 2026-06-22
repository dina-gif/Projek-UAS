<?php
require_once __DIR__ . '/config.php';

$payload = null;
$status = $_GET['status'] ?? null;
$order_id = $_GET['order_id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $body = file_get_contents('php://input');
    $payload = json_decode($body, true);
    $order_id = $payload['order_id'] ?? null;
    $transaction_status = $payload['transaction_status'] ?? null;
} else {
    $transaction_status = $status;
}

if ($order_id) {
    $mapStatus = [
        'capture' => 'Lunas',
        'settlement' => 'Lunas',
        'pending' => 'Pending',
        'deny' => 'Gagal',
        'cancel' => 'Dibatalkan',
        'expire' => 'Dibatalkan',
    ];

    if (isset($mapStatus[$transaction_status])) {
        $updateStatus = $mapStatus[$transaction_status];
        $stmt = $pdo->prepare('UPDATE pembayaran SET status = :status WHERE order_id = :order_id');
        $stmt->execute(['status' => $updateStatus, 'order_id' => $order_id]);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'ok']);
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Status Pembayaran</title>
  <link rel="stylesheet" href="assets/css/style.css" />
</head>
<body class="page-auth">
  <main class="auth-shell">
    <div class="auth-card">
      <h1>Status Pembayaran</h1>
      <p>
        <?php if ($transaction_status === 'success' || $transaction_status === 'settlement' || $transaction_status === 'capture'): ?>
          Pembayaran berhasil. Order ID: <?php echo htmlspecialchars($order_id); ?>.
        <?php elseif ($transaction_status === 'pending'): ?>
          Pembayaran sedang menunggu konfirmasi. Order ID: <?php echo htmlspecialchars($order_id); ?>.
        <?php elseif ($transaction_status === 'error' || $transaction_status === 'deny' || $transaction_status === 'cancel' || $transaction_status === 'expire'): ?>
          Pembayaran gagal atau dibatalkan. Order ID: <?php echo htmlspecialchars($order_id); ?>.
        <?php else: ?>
          Status pembayaran tidak diketahui.
        <?php endif; ?>
      </p>
      <a class="button button-primary" href="patient-dashboard.php">Kembali ke Dashboard</a>
    </div>
  </main>
</body>
</html>
