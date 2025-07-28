<?php
require_once 'config/admin.php';

$error = '';
$success = '';

// Proses login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    if (empty($username) || empty($password)) {
        $error = 'Username dan password harus diisi';
    } else {
        if (loginAdmin($username, $password)) {
            header('Location: admin/dashboard.php');
            exit;
        } else {
            $error = 'Username atau password salah';
        }
    }
}

// Redirect jika sudah login
if (isAdminLoggedIn()) {
    header('Location: admin/dashboard.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Sistem Beasiswa</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .login-container {
            max-width: 400px;
            margin: 5rem auto;
            padding: 2rem;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .login-header h2 {
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }
        .login-header p {
            color: #666;
        }
        .back-link {
            text-align: center;
            margin-top: 2rem;
        }
        .back-link a {
            color: #3498db;
            text-decoration: none;
        }
        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-container">
            <div class="login-header">
                <h2>🔐 Login Admin</h2>
                <p>Sistem Pendaftaran Beasiswa</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success">
                    <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required 
                           value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>"
                           placeholder="Masukkan username admin">
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required
                           placeholder="Masukkan password admin">
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-success" style="width: 100%;">
                        Login
                    </button>
                </div>
            </form>

            <div class="alert alert-info" style="margin-top: 2rem;">
                <h4>Demo Login:</h4>
                <p><strong>Username:</strong> admin</p>
                <p><strong>Password:</strong> admin123</p>
            </div>

            <div class="back-link">
                <a href="index.php">← Kembali ke Halaman Utama</a>
            </div>
        </div>
    </div>
</body>
</html>