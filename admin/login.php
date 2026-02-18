<?php
session_start();

// If already logged in, redirect to dashboard
if (isset($_SESSION['admin_logged_in'])) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once '../config/database.php';
    $conn = getConnection();
    
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];
    
    $result = $conn->query("SELECT * FROM admin_users WHERE username = '$username'");
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        // Simple password check (in production, use password_hash/password_verify)
        if ($password === $user['password']) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_name'] = $user['nama_lengkap'];
            header('Location: index.php');
            exit;
        } else {
            $error = 'Password salah!';
        }
    } else {
        $error = 'Username tidak ditemukan!';
    }
    
    closeConnection($conn);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - SIHANPANGAN851</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body class="login-page">
    <div class="login-container">
        <div class="login-box">
            <div class="login-header">
                <span class="logo-icon">🌾</span>
                <h1>SIHANPANGAN851</h1>
                <p>Admin Login</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>
            
            <form method="POST" class="login-form">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" required autofocus>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Login</button>
            </form>
            
            <div class="login-footer">
                <a href="../index.php">← Kembali ke Beranda</a>
            </div>
        </div>
    </div>
</body>
</html>
