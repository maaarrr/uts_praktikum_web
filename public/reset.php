<?php
session_start();
require_once '../includes/db.php';

$error = '';
$success = '';
$valid_token = false;
$email = '';

if (isset($_GET['email']) && isset($_GET['token'])) {
    $email = $_GET['email'];
    $token = $_GET['token'];
    
    // Cek token valid dan belum expired
    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ? AND reset_token = ? AND reset_expires > NOW()');
    $stmt->execute([$email, $token]);
    $user = $stmt->fetch();
    
    if ($user) {
        $valid_token = true;
    } else {
        $error = 'Token reset tidak valid atau sudah kadaluarsa.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $valid_token) {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    if (strlen($new_password) < 6) {
        $error = 'Password minimal 6 karakter!';
    } elseif ($new_password !== $confirm_password) {
        $error = 'Konfirmasi password tidak cocok!';
    } else {
        // Update password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('UPDATE users SET password = ?, reset_token = NULL, reset_expires = NULL WHERE email = ?');
        $stmt->execute([$hashed_password, $email]);
        
        $success = 'Password berhasil direset! Silakan login dengan password baru Anda.';
        $valid_token = false;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container">
    <div class="row justify-content-center mt-5">
        <div class="col-md-5">
            <div class="card shadow">
                <div class="card-body">
                    <h3 class="text-center mb-4">Reset Password</h3>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <?php if ($success): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                        <div class="text-center">
                            <a href="login.php" class="btn btn-primary">Login Sekarang</a>
                        </div>
                    <?php elseif ($valid_token): ?>
                        <form method="post">
                            <div class="mb-3">
                                <label>Password Baru</label>
                                <input type="password" name="new_password" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label>Konfirmasi Password Baru</label>
                                <input type="password" name="confirm_password" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Reset Password</button>
                        </form>
                    <?php else: ?>
                        <div class="text-center">
                            <a href="forgot.php" class="btn btn-secondary">Kembali</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>