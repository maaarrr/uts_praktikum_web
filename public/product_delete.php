<?php
session_start();
require_once '../includes/db.php';
// Cek login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
if (!isset($_GET['id'])) {
    header('Location: products_list.php');
    exit;
}
$id = intval($_GET['id']);
// Hapus produk
$stmt = $pdo->prepare('DELETE FROM products WHERE id = ?');
$stmt->execute([$id]);
header('Location: products_list.php');
exit;
