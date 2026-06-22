<?php
session_start();

// Load Composer autoload and Dotenv if available
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
    if (class_exists(\Dotenv\Dotenv::class)) {
        $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__);
        $dotenv->safeLoad();
    }
}

// Environment-backed configuration with sensible defaults
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('DB_NAME') ?: 'fisioterapi');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');

try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    http_response_code(500);
    echo 'Koneksi database gagal: ' . htmlspecialchars($e->getMessage());
    exit;
}

define('MIDTRANS_SERVER_KEY', getenv('MIDTRANS_SERVER_KEY') ?: 'YOUR_SERVER_KEY_HERE');
define('MIDTRANS_CLIENT_KEY', getenv('MIDTRANS_CLIENT_KEY') ?: 'YOUR_CLIENT_KEY_HERE');
define('MIDTRANS_IS_PRODUCTION', filter_var(getenv('MIDTRANS_IS_PRODUCTION') ?: '0', FILTER_VALIDATE_BOOLEAN));

// SMTP / Email configuration (optional)
define('SMTP_HOST', getenv('SMTP_HOST') ?: 'smtp.example.com');
define('SMTP_PORT', intval(getenv('SMTP_PORT') ?: 587));
define('SMTP_USER', getenv('SMTP_USER') ?: 'user@example.com');
define('SMTP_PASS', getenv('SMTP_PASS') ?: 'supersecret');
define('SMTP_SECURE', getenv('SMTP_SECURE') ?: 'tls'); // tls or ssl or empty
define('FROM_EMAIL', getenv('FROM_EMAIL') ?: 'no-reply@example.com');
define('FROM_NAME', getenv('FROM_NAME') ?: 'Fisioterapi');

define('MIDTRANS_BASE_URL', MIDTRANS_IS_PRODUCTION ? 'https://api.midtrans.com' : 'https://api.sandbox.midtrans.com');

function midtrans_is_sandbox(): bool
{
    return !MIDTRANS_IS_PRODUCTION;
}

function midtrans_api_url(string $path): string
{
    return MIDTRANS_BASE_URL . $path;
}

function midtrans_snap_script_url(): string
{
    return midtrans_is_sandbox()
        ? 'https://app.sandbox.midtrans.com/snap/snap.js'
        : 'https://app.midtrans.com/snap/snap.js';
}

function generate_order_id(): string
{
    return 'FISIO-' . strtoupper(bin2hex(random_bytes(4))) . '-' . time();
}

function midtrans_request(string $path, array $data): array
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, midtrans_api_url($path));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json',
    ]);
    curl_setopt($ch, CURLOPT_USERPWD, MIDTRANS_SERVER_KEY . ':');
    $response = curl_exec($ch);
    $error = curl_error($ch);
    $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($response === false) {
        return ['error' => $error];
    }

    $result = json_decode($response, true);
    if (!is_array($result)) {
        return ['error' => 'Midtrans tidak merespons dengan JSON yang valid. HTTP ' . $httpStatus];
    }

    return $result;
}

function is_logged_in(): bool
{
    return isset($_SESSION['user']);
}

/**
 * Send email using PHPMailer if available, fallback to mail().
 */
function send_mail(string $to, string $subject, string $body, bool $isHtml = true): bool
{
    // Try composer autoload
    if (file_exists(__DIR__ . '/vendor/autoload.php')) {
        require_once __DIR__ . '/vendor/autoload.php';
    }

    if (class_exists('\PHPMailer\PHPMailer\PHPMailer')) {
        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = SMTP_HOST;
            $mail->SMTPAuth = true;
            $mail->Username = SMTP_USER;
            $mail->Password = SMTP_PASS;
            $mail->SMTPSecure = SMTP_SECURE ?: '';
            $mail->Port = SMTP_PORT;

            $mail->setFrom(FROM_EMAIL, FROM_NAME);
            $mail->addAddress($to);
            $mail->isHTML($isHtml);
            $mail->Subject = $subject;
            $mail->Body = $body;
            $mail->AltBody = strip_tags($body);
            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log('PHPMailer error: ' . $e->getMessage());
            // fallback to mail()
        }
    }

    // Fallback
    $headers = 'From: ' . FROM_NAME . ' <' . FROM_EMAIL . '>\r\n';
    if ($isHtml) {
        $headers .= 'MIME-Version: 1.0\r\nContent-type: text/html; charset=utf-8\r\n';
    }
    return mail($to, $subject, $body, $headers);
}

function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function require_login(): void
{
    if (!is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}

function require_role(string $role): void
{
    require_login();
    if (!isset($_SESSION['user']['role']) || $_SESSION['user']['role'] !== $role) {
        http_response_code(403);
        echo 'Akses ditolak.';
        exit;
    }
}

function flash(string $key, ?string $message = null): ?string
{
    if ($message !== null) {
        $_SESSION['flash'][$key] = $message;
        return null;
    }

    if (isset($_SESSION['flash'][$key])) {
        $value = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $value;
    }

    return null;
}
