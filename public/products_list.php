<?php
require_once '../includes/db.php';

// Tampilkan daftar produk dari database
$stmt = $pdo->query('SELECT * FROM products');
$products = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Produk</title>
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
    <h2>Daftar Produk</h2>
    <a href="product_create.php" class="btn btn-success mb-3">Tambah Produk</a>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama</th>
                <th>Deskripsi</th>
                <th>Harga</th>
                <th>Stok</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $p): ?>
            <tr>
                <td><?php echo $p['id']; ?></td>
                <td><?php echo htmlspecialchars($p['name']); ?></td>
                <td><?php echo htmlspecialchars($p['description']); ?></td>
                <td><?php echo number_format($p['price'],2); ?></td>
                <td><?php echo $p['stock']; ?></td>
                <td>
                    <a href="product_edit.php?id=<?php echo $p['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                    <a href="product_delete.php?id=<?php echo $p['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus produk?')">Hapus</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>