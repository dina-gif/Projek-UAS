<?php
require_once __DIR__ . '/config.php';
require_role('Admin');

$errors = [];
$success = null;
$editPasien = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        $nama = trim($_POST['nama'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $jenis_kelamin = $_POST['jenis_kelamin'] ?? '';
        $tanggal_lahir = $_POST['tanggal_lahir'] ?? null;
        $alamat = trim($_POST['alamat'] ?? '');
        $telepon = trim($_POST['telepon'] ?? '');

        if ($nama === '' || $email === '' || $password === '' || $jenis_kelamin === '') {
            $errors[] = 'Nama, email, password, dan jenis kelamin wajib diisi.';
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email tidak valid.';
        }

        if (strlen($password) < 6) {
            $errors[] = 'Password minimal 6 karakter.';
        }

        if (empty($errors)) {
            $stmt = $pdo->prepare('SELECT id FROM users WHERE email = :email');
            $stmt->execute(['email' => $email]);
            if ($stmt->fetch()) {
                $errors[] = 'Email sudah terdaftar.';
            }
        }

        if (empty($errors)) {
            $pdo->beginTransaction();
            try {
                $stmt = $pdo->prepare('INSERT INTO users (nama, email, password, role) VALUES (:nama, :email, :password, :role)');
                $stmt->execute([
                    'nama' => $nama,
                    'email' => $email,
                    'password' => password_hash($password, PASSWORD_DEFAULT),
                    'role' => 'Pasien',
                ]);
                $userId = $pdo->lastInsertId();

                $stmt = $pdo->prepare('INSERT INTO pasien (user_id, nama, jenis_kelamin, tanggal_lahir, alamat, telepon) VALUES (:user_id, :nama, :jenis_kelamin, :tanggal_lahir, :alamat, :telepon)');
                $stmt->execute([
                    'user_id' => $userId,
                    'nama' => $nama,
                    'jenis_kelamin' => $jenis_kelamin,
                    'tanggal_lahir' => $tanggal_lahir ?: null,
                    'alamat' => $alamat,
                    'telepon' => $telepon,
                ]);
                $pdo->commit();
                $success = 'Pasien berhasil ditambahkan.';
            } catch (PDOException $e) {
                $pdo->rollBack();
                $errors[] = 'Tidak dapat menambahkan pasien: ' . $e->getMessage();
            }
        }
    }

    if ($action === 'update') {
        $id_pasien = intval($_POST['id_pasien'] ?? 0);
        $nama = trim($_POST['nama'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $jenis_kelamin = $_POST['jenis_kelamin'] ?? '';
        $tanggal_lahir = $_POST['tanggal_lahir'] ?? null;
        $alamat = trim($_POST['alamat'] ?? '');
        $telepon = trim($_POST['telepon'] ?? '');

        if ($id_pasien <= 0) {
            $errors[] = 'ID pasien tidak valid.';
        }
        if ($nama === '' || $email === '' || $jenis_kelamin === '') {
            $errors[] = 'Nama, email, dan jenis kelamin wajib diisi.';
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email tidak valid.';
        }

        if (empty($errors)) {
            $stmt = $pdo->prepare('SELECT p.user_id FROM pasien p WHERE p.id_pasien = :id_pasien');
            $stmt->execute(['id_pasien' => $id_pasien]);
            $row = $stmt->fetch();
            if (!$row) {
                $errors[] = 'Pasien tidak ditemukan.';
            }
        }

        if (empty($errors)) {
            $userId = $row['user_id'];
            $stmt = $pdo->prepare('SELECT id FROM users WHERE email = :email AND id != :id');
            $stmt->execute(['email' => $email, 'id' => $userId]);
            if ($stmt->fetch()) {
                $errors[] = 'Email sudah dipakai pengguna lain.';
            }
        }

        if (empty($errors)) {
            try {
                $stmt = $pdo->prepare('UPDATE users SET nama = :nama, email = :email' . ($password !== '' ? ', password = :password' : '') . ' WHERE id = :id');
                $params = ['nama' => $nama, 'email' => $email, 'id' => $userId];
                if ($password !== '') {
                    $params['password'] = password_hash($password, PASSWORD_DEFAULT);
                }
                $stmt->execute($params);

                $stmt = $pdo->prepare('UPDATE pasien SET nama = :nama, jenis_kelamin = :jenis_kelamin, tanggal_lahir = :tanggal_lahir, alamat = :alamat, telepon = :telepon WHERE id_pasien = :id_pasien');
                $stmt->execute([
                    'nama' => $nama,
                    'jenis_kelamin' => $jenis_kelamin,
                    'tanggal_lahir' => $tanggal_lahir ?: null,
                    'alamat' => $alamat,
                    'telepon' => $telepon,
                    'id_pasien' => $id_pasien,
                ]);
                $success = 'Data pasien berhasil diperbarui.';
            } catch (PDOException $e) {
                $errors[] = 'Tidak dapat memperbarui pasien: ' . $e->getMessage();
            }
        }
    }

    if ($action === 'delete') {
        $id_pasien = intval($_POST['id_pasien'] ?? 0);
        if ($id_pasien > 0) {
            try {
                $stmt = $pdo->prepare('SELECT user_id FROM pasien WHERE id_pasien = :id_pasien');
                $stmt->execute(['id_pasien' => $id_pasien]);
                $row = $stmt->fetch();
                if ($row) {
                    $stmt = $pdo->prepare('DELETE FROM users WHERE id = :id');
                    $stmt->execute(['id' => $row['user_id']]);
                    $success = 'Pasien berhasil dihapus.';
                } else {
                    $errors[] = 'Pasien tidak ditemukan.';
                }
            } catch (PDOException $e) {
                $errors[] = 'Tidak dapat menghapus pasien: ' . $e->getMessage();
            }
        }
    }
}

if (isset($_GET['edit'])) {
    $editId = intval($_GET['edit']);
    if ($editId > 0) {
        $stmt = $pdo->prepare('SELECT p.*, u.email FROM pasien p JOIN users u ON p.user_id = u.id WHERE p.id_pasien = :id_pasien');
        $stmt->execute(['id_pasien' => $editId]);
        $editPasien = $stmt->fetch();
        if (!$editPasien) {
            $errors[] = 'Pasien tidak ditemukan.';
        }
    }
}

$pasienList = $pdo->query('SELECT p.*, u.email FROM pasien p JOIN users u ON p.user_id = u.id ORDER BY p.nama ASC')->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin - Pasien</title>
  <link rel="stylesheet" href="assets/css/style.css" />
</head>
<body>
  <header class="top-nav top-nav-dark">
    <div class="container nav-inner">
      <a class="brand" href="admin-dashboard.php">Fisioterapi</a>
      <nav class="nav-menu">
        <a href="admin-dashboard.php">Dashboard</a>
        <a href="admin-pasien.php">Pasien</a>
        <a href="admin-terapis.php">Terapis</a>
        <a href="admin-layanan.php">Layanan</a>
        <a href="admin-jadwal.php">Jadwal</a>
        <a href="admin-reservasi.php">Reservasi</a>
      </nav>
      <div class="nav-actions">
        <a class="button button-secondary" href="logout.php">Logout</a>
      </div>
    </div>
  </header>

  <main class="section">
    <div class="container">
      <div class="section-header">
        <span class="eyebrow">Master Data</span>
        <h1>Kelola Pasien</h1>
        <p>Tambah, ubah, atau hapus data pasien secara langsung dari panel admin.</p>
      </div>

      <?php if ($success): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
      <?php endif; ?>
      <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
          <ul>
            <?php foreach ($errors as $error): ?>
              <li><?php echo htmlspecialchars($error); ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <div class="admin-grid">
        <section class="admin-panel">
          <h2><?php echo $editPasien ? 'Ubah Pasien' : 'Tambah Pasien'; ?></h2>
          <form method="post" class="form-grid">
            <input type="hidden" name="action" value="<?php echo $editPasien ? 'update' : 'create'; ?>" />
            <?php if ($editPasien): ?>
              <input type="hidden" name="id_pasien" value="<?php echo htmlspecialchars($editPasien['id_pasien']); ?>" />
            <?php endif; ?>
            <label>
              Nama Lengkap
              <input type="text" name="nama" value="<?php echo htmlspecialchars($editPasien['nama'] ?? ''); ?>" required />
            </label>
            <label>
              Email
              <input type="email" name="email" value="<?php echo htmlspecialchars($editPasien['email'] ?? ''); ?>" required />
            </label>
            <label>
              Password <?php echo $editPasien ? '(Kosongkan jika tidak diubah)' : '' ; ?>
              <input type="password" name="password" <?php echo $editPasien ? '' : 'required'; ?> />
            </label>
            <label>
              Jenis Kelamin
              <select name="jenis_kelamin" required>
                <option value="">Pilih...</option>
                <option value="Laki-laki" <?php echo (isset($editPasien['jenis_kelamin']) && $editPasien['jenis_kelamin'] === 'Laki-laki') ? 'selected' : ''; ?>>Laki-laki</option>
                <option value="Perempuan" <?php echo (isset($editPasien['jenis_kelamin']) && $editPasien['jenis_kelamin'] === 'Perempuan') ? 'selected' : ''; ?>>Perempuan</option>
              </select>
            </label>
            <label>
              Tanggal Lahir
              <input type="date" name="tanggal_lahir" value="<?php echo htmlspecialchars($editPasien['tanggal_lahir'] ?? ''); ?>" />
            </label>
            <label>
              Telepon
              <input type="text" name="telepon" value="<?php echo htmlspecialchars($editPasien['telepon'] ?? ''); ?>" />
            </label>
            <label class="full-width">
              Alamat
              <textarea name="alamat"><?php echo htmlspecialchars($editPasien['alamat'] ?? ''); ?></textarea>
            </label>
            <button class="button button-primary" type="submit"><?php echo $editPasien ? 'Perbarui Pasien' : 'Tambah Pasien'; ?></button>
            <?php if ($editPasien): ?>
              <a class="button button-secondary" href="admin-pasien.php">Batal</a>
            <?php endif; ?>
          </form>
        </section>

        <section class="admin-panel admin-table-panel">
          <h2>Daftar Pasien</h2>
          <div class="table-scroll">
            <table class="admin-table">
              <thead>
                <tr>
                  <th>Nama</th>
                  <th>Email</th>
                  <th>Jenis Kelamin</th>
                  <th>Tanggal Lahir</th>
                  <th>Telepon</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($pasienList as $pasien): ?>
                  <tr>
                    <td><?php echo htmlspecialchars($pasien['nama']); ?></td>
                    <td><?php echo htmlspecialchars($pasien['email']); ?></td>
                    <td><?php echo htmlspecialchars($pasien['jenis_kelamin']); ?></td>
                    <td><?php echo htmlspecialchars($pasien['tanggal_lahir']); ?></td>
                    <td><?php echo htmlspecialchars($pasien['telepon']); ?></td>
                    <td>
                      <a class="button button-secondary button-small" href="admin-pasien.php?edit=<?php echo htmlspecialchars($pasien['id_pasien']); ?>">Ubah</a>
                      <form method="post" action="admin-pasien.php" class="inline-form" onsubmit="return confirm('Hapus pasien ini?');">
                        <input type="hidden" name="action" value="delete" />
                        <input type="hidden" name="id_pasien" value="<?php echo htmlspecialchars($pasien['id_pasien']); ?>" />
                        <button class="button button-outline" type="submit">Hapus</button>
                      </form>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </section>
      </div>
    </div>
  </main>
</body>
</html>
