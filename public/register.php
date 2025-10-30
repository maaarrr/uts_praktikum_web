<?php
session_start();
require_once '../includes/db.php';
require_once '../vendor/autoload.php';
require_once '../includes/functions.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validasi
    if (empty($name) || empty($email) || empty($password)) {
        $error = 'Semua field harus diisi!';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Email tidak valid!';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter!';
    } elseif ($password !== $confirm_password) {
        $error = 'Konfirmasi password tidak cocok!';
    } else {
        // Cek email sudah terdaftar
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = 'Email sudah terdaftar!';
        } else {
            // Hash password dan buat token aktivasi
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $activation_token = bin2hex(random_bytes(32));
            
            // Insert user
            $stmt = $pdo->prepare('INSERT INTO users (name, email, password, activation_token, status) VALUES (?, ?, ?, ?, ?)');
            $stmt->execute([$name, $email, $hashed_password, $activation_token, 'PENDING']);
            
            // Kirim email aktivasi
            $activation_link = BASE_URL . 'activate.php?email=' . urlencode($email) . '&token=' . $activation_token;
            $subject = 'Aktivasi Akun Management System';
            $body = "
                <h2>Halo, $name!</h2>
                <p>Terima kasih telah mendaftar. Silakan klik tautan di bawah ini untuk mengaktifkan akun Anda:</p>
                <p><a href='$activation_link'>$activation_link</a></p>
                <p>Jika Anda tidak mendaftar, abaikan email ini.</p>
            ";
            
            if (sendEmail($email, $subject, $body)) {
                $success = 'Registrasi berhasil! Silakan cek email Anda untuk aktivasi akun.';
            } else {
                $error = 'Registrasi berhasil, tetapi gagal mengirim email aktivasi. Hubungi administrator.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Registrasi - Admin Gudang</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container">
    <div class="row justify-content-center mt-5">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-body">
                    <h3 class="text-center mb-4">Registrasi Admin Gudang</h3>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <?php if ($success): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>
                    
                    <form method="post">
                        <div class="mb-3">
                            <label>Nama Lengkap</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Konfirmasi Password</label>
                            <input type="password" name="confirm_password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Daftar</button>
                    </form>
                    
                    <div class="text-center mt-3">
                        <p>Sudah punya akun? <a href="login.php">Login di sini</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>