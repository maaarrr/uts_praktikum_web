<?php
session_start();
require_once '../includes/db.php';
// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
// Ambil data user
$stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
// Proses ubah profil
$success = '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    // Validasi email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Email tidak valid!';
    } else {
        // Cek email sudah dipakai user lain
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? AND id != ?');
        $stmt->execute([$email, $user['id']]);
        if ($stmt->fetch()) {
            $error = 'Email sudah digunakan pengguna lain!';
        } else {
            // Update profil
            $stmt = $pdo->prepare('UPDATE users SET name = ?, email = ? WHERE id = ?');
            $stmt->execute([$name, $email, $user['id']]);
            $success = 'Profil berhasil diubah.';
            $user['name'] = $name;
            $user['email'] = $email;
        }
    }
}
// Proses ubah password
if (isset($_POST['change_password'])) {
    $old = $_POST['old_password'];
    $new = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];
    if (!password_verify($old, $user['password'])) {
        $error = 'Password lama salah!';
    } elseif (strlen($new) < 6) {
        $error = 'Password baru minimal 6 karakter!';
    } elseif ($new !== $confirm) {
        $error = 'Konfirmasi password tidak cocok!';
    } else {
        $hash = password_hash($new, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('UPDATE users SET password = ? WHERE id = ?');
        $stmt->execute([$hash, $user['id']]);
        $success = 'Password berhasil diubah.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Profil Pengguna</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container-fluid">
    <a class="navbar-brand" href="dashboard.php">Admin Gudang</a>
    <div class="collapse navbar-collapse">
    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="products_list.php">Produk</a></li>
        <li class="nav-item"><a class="nav-link active" href="profile.php">Profil</a></li>
        <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
    </ul>
    </div>
    </div>
</nav>
<div class="container">
    <h2>Profil Pengguna</h2>
    <?php if ($success) echo '<div class="alert alert-success">'.$success.'</div>'; ?>
    <?php if ($error) echo '<div class="alert alert-danger">'.$error.'</div>'; ?>
    <form method="post" class="mb-4">
        <div class="mb-3">
            <label>Nama</label>
            <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($user['name']); ?>" required>
        </div>
        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Simpan Profil</button>
    </form>
    <hr>
    <h4>Ubah Password</h4>
    <form method="post">
        <input type="hidden" name="change_password" value="1">
        <div class="mb-3">
            <label>Password Lama</label>
            <input type="password" name="old_password" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Password Baru</label>
            <input type="password" name="new_password" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Konfirmasi Password Baru</label>
            <input type="password" name="confirm_password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-warning">Ubah Password</button>
    </form>
</div>
</body>
</html>
