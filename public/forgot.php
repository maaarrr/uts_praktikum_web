<?php
session_start();
require_once '../includes/db.php';
require_once '../vendor/autoload.php';
require_once '../includes/functions.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    
    if (empty($email)) {
        $error = 'Email harus diisi!';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Email tidak valid!';
    } else {
        // Cek email terdaftar
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user) {
            // Buat token reset
            $reset_token = bin2hex(random_bytes(32));
            $reset_expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            // Simpan token
            $stmt = $pdo->prepare('UPDATE users SET reset_token = ?, reset_expires = ? WHERE email = ?');
            $stmt->execute([$reset_token, $reset_expires, $email]);
            
            // Kirim email
            $reset_link = BASE_URL . 'reset.php?email=' . urlencode($email) . '&token=' . $reset_token;
            $subject = 'Reset Password Management System';
            $body = "
                <h2>Halo, {$user['name']}!</h2>
                <p>Anda menerima email ini karena ada permintaan reset password untuk akun Anda.</p>
                <p>Klik tautan di bawah ini untuk mereset password (berlaku 1 jam):</p>
                <p><a href='$reset_link'>$reset_link</a></p>
                <p>Jika Anda tidak meminta reset password, abaikan email ini.</p>
            ";
            
            if (sendEmail($email, $subject, $body)) {
                $success = 'Tautan reset password telah dikirim ke email Anda.';
            } else {
                $error = 'Gagal mengirim email. Hubungi administrator.';
            }
        } else {
            $error = 'Email tidak terdaftar!';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Lupa Password</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container">
    <div class="row justify-content-center mt-5">
        <div class="col-md-5">
            <div class="card shadow">
                <div class="card-body">
                    <h3 class="text-center mb-4">Lupa Password</h3>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <?php if ($success): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>
                    
                    <p class="text-muted">Masukkan email Anda untuk menerima tautan reset password.</p>
                    
                    <form method="post">
                        <div class="mb-3">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Kirim Tautan Reset</button>
                    </form>
                    
                    <div class="text-center mt-3">
                        <a href="login.php">Kembali ke Login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>