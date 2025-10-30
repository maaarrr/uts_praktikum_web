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
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin Gudang</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container-fluid">
    <a class="navbar-brand" href="#">Admin Gudang</a>
    <div class="collapse navbar-collapse">
    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="products_list.php">Produk</a></li>
        <li class="nav-item"><a class="nav-link" href="profile.php">Profil</a></li>
        <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
    </ul>
    </div>
    </div>
</nav>
<div class="container">
    <h2>Selamat datang, <?php echo htmlspecialchars($user['name']); ?>!</h2>
    <p>Email: <?php echo htmlspecialchars($user['email']); ?></p>
    <hr>
    <div class="row">
        <div class="col-md-6">
            <h4>Menu Produk</h4>
            <a href="products_list.php" class="btn btn-primary">Kelola Produk</a>
        </div>
        <div class="col-md-6">
            <h4>Profil Anda</h4>
            <a href="profile.php" class="btn btn-secondary">Lihat/Ubah Profil</a>
        </div>
    </div>
</div>
</body>
</html>
