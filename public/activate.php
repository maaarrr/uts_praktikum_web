<?php
require_once '../includes/db.php';

$message = '';
$success = false;

if (isset($_GET['email']) && isset($_GET['token'])) {
    $email = $_GET['email'];
    $token = $_GET['token'];
    
    // Cek token dan email
    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ? AND activation_token = ? AND status = ?');
    $stmt->execute([$email, $token, 'PENDING']);
    $user = $stmt->fetch();
    
    if ($user) {
        // Aktifkan user
        $stmt = $pdo->prepare('UPDATE users SET status = ?, activation_token = NULL WHERE email = ?');
        $stmt->execute(['ACTIVE', $email]);
        
        $message = 'Akun Anda berhasil diaktifkan! Silakan login.';
        $success = true;
    } else {
        $message = 'Token aktivasi tidak valid atau akun sudah diaktifkan.';
    }
} else {
    $message = 'Permintaan tidak valid.';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Aktivasi Akun</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container">
    <div class="row justify-content-center mt-5">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-body text-center">
                    <h3 class="mb-4">Aktivasi Akun</h3>
                    
                    <div class="alert alert-<?php echo $success ? 'success' : 'danger'; ?>">
                        <?php echo $message; ?>
                    </div>
                    
                    <?php if ($success): ?>
                        <a href="login.php" class="btn btn-primary">Login Sekarang</a>
                    <?php else: ?>
                        <a href="register.php" class="btn btn-secondary">Kembali ke Registrasi</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>