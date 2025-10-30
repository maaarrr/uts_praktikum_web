<?php
session_start();
require_once '../includes/db.php';
// Cek login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
// Proses tambah produk
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $desc = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);
    if ($name == '' || $price < 0 || $stock < 0) {
        $error = 'Data produk tidak valid!';
    } else {
        $stmt = $pdo->prepare('INSERT INTO products (name, description, price, stock, created_by) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$name, $desc, $price, $stock, $_SESSION['user_id']]);
        header('Location: products_list.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Produk</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container-fluid">
    <a class="navbar-brand" href="dashboard.php">Admin Gudang</a>
    <div class="collapse navbar-collapse">
    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
        <li class="nav-item"><a class="nav-link active" href="products_list.php">Produk</a></li>
        <li class="nav-item"><a class="nav-link" href="profile.php">Profil</a></li>
        <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
        </ul>
    </div>
    </div>
</nav>
<div class="container">
    <h2>Tambah Produk</h2>
    <?php if ($error) echo '<div class="alert alert-danger">'.$error.'</div>'; ?>
    <form method="post">
        <div class="mb-3">
            <label>Nama Produk</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Deskripsi</label>
            <textarea name="description" class="form-control"></textarea>
        </div>
        <div class="mb-3">
            <label>Harga</label>
            <input type="number" name="price" class="form-control" step="0.01" required>
        </div>
        <div class="mb-3">
            <label>Stok</label>
            <input type="number" name="stock" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-success">Simpan</button>
        <a href="products_list.php" class="btn btn-secondary">Kembali</a>
    </form>
</div>
</body>
</html>
