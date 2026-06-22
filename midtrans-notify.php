<?php
// Server-to-server notification handler for Midtrans
require_once __DIR__ . '/config.php';

$body = file_get_contents('php://input');
$payload = json_decode($body, true);
if (!$payload) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON']);
    exit;
}

// Required fields
$order_id = $payload['order_id'] ?? null;
$transaction_status = $payload['transaction_status'] ?? ($payload['transaction_status'] ?? null);
$status_code = $payload['status_code'] ?? null;
$gross_amount = $payload['gross_amount'] ?? ($payload['gross_amount'] ?? null);
$signature = $payload['signature_key'] ?? null;

if (!$order_id || !$signature) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing order_id or signature_key']);
    exit;
}

// Validate signature (Midtrans v2 style): sha512(order_id + status_code + gross_amount + server_key)
$serverKey = MIDTRANS_SERVER_KEY;
$expected = hash('sha512', ($order_id . ($status_code ?? '') . ($gross_amount ?? '') . $serverKey));
if (!hash_equals($expected, $signature)) {
    // Log mismatch
    file_put_contents(__DIR__ . '/storage/midtrans.log', date('c') . " INVALID_SIGNATURE order_id={$order_id} payload=" . $body . "\n", FILE_APPEND);
    http_response_code(403);
    echo json_encode(['error' => 'Invalid signature']);
    exit;
}

// Map statuses
$mapStatus = [
    'capture' => 'Lunas',
    'settlement' => 'Lunas',
    'pending' => 'Pending',
    'deny' => 'Gagal',
    'cancel' => 'Dibatalkan',
    'expire' => 'Dibatalkan',
    'success' => 'Lunas',
];

$updateStatus = $mapStatus[$transaction_status] ?? ($mapStatus[$payload['transaction_status'] ?? ''] ?? null);

// Logging notification
@mkdir(__DIR__ . '/storage', 0777, true);
file_put_contents(__DIR__ . '/storage/midtrans.log', date('c') . " NOTIFICATION order_id={$order_id} status={$transaction_status} status_code={$status_code} gross_amount={$gross_amount}\n", FILE_APPEND);

if ($updateStatus) {
    // Check idempotency: don't overwrite if already set to same final state
    $stmt = $pdo->prepare('SELECT status FROM pembayaran WHERE order_id = :order_id LIMIT 1');
    $stmt->execute(['order_id' => $order_id]);
    $row = $stmt->fetch();
    $current = $row['status'] ?? null;
    if ($current === $updateStatus) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'no-change']);
        exit;
    }

    $stmt = $pdo->prepare('UPDATE pembayaran SET status = :status, raw_notification = :raw WHERE order_id = :order_id');
    $stmt->execute(['status' => $updateStatus, 'raw' => $body, 'order_id' => $order_id]);
}

header('Content-Type: application/json');
echo json_encode(['status' => 'ok']);
