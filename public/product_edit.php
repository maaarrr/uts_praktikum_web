<?php
session_start();
require_once '../includes/db.php';
// Cek login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
// Ambil data produk
if (!isset($_GET['id'])) {
    header('Location: products_list.php');
    exit;
}
$id = intval($_GET['id']);
$stmt = $pdo->prepare('SELECT * FROM products WHERE id = ?');
$stmt->execute([$id]);
$product = $stmt->fetch();
if (!$product) {
    header('Location: products_list.php');
    exit;
}
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $desc = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);
    if ($name == '' || $price < 0 || $stock < 0) {
        $error = 'Data produk tidak valid!';
    } else {
        $stmt = $pdo->prepare('UPDATE products SET name = ?, description = ?, price = ?, stock = ?, updated_at = NOW() WHERE id = ?');
        $stmt->execute([$name, $desc, $price, $stock, $id]);
        header('Location: products_list.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Produk</title>
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
    <h2>Edit Produk</h2>
    <?php if ($error) echo '<div class="alert alert-danger">'.$error.'</div>'; ?>
    <form method="post">
        <div class="mb-3">
            <label>Nama Produk</label>
            <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($product['name']); ?>" required>
        </div>
        <div class="mb-3">
            <label>Deskripsi</label>
            <textarea name="description" class="form-control"><?php echo htmlspecialchars($product['description']); ?></textarea>
        </div>
        <div class="mb-3">
            <label>Harga</label>
            <input type="number" name="price" class="form-control" step="0.01" value="<?php echo $product['price']; ?>" required>
        </div>
        <div class="mb-3">
            <label>Stok</label>
            <input type="number" name="stock" class="form-control" value="<?php echo $product['stock']; ?>" required>
        </div>
        <button type="submit" class="btn btn-warning">Simpan Perubahan</button>
        <a href="products_list.php" class="btn btn-secondary">Kembali</a>
    </form>
</div>
</body>
</html>
