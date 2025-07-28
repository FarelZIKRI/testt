<?php
require_once 'includes/functions.php';

$page = isset($_GET['page']) ? $_GET['page'] : 'home';
$allowed_pages = ['home', 'daftar', 'hasil'];

if (!in_array($page, $allowed_pages)) {
    $page = 'home';
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Pendaftaran Beasiswa</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>Sistem Pendaftaran Beasiswa</h1>
            <p>Universitas Contoh - Portal Beasiswa Online</p>
        </div>
    </header>

    <nav>
        <div class="container">
            <ul>
                <li><a href="?page=home" class="<?= $page == 'home' ? 'active' : '' ?>">Beranda</a></li>
                <li><a href="?page=daftar" class="<?= $page == 'daftar' ? 'active' : '' ?>">Daftar Beasiswa</a></li>
                <li><a href="?page=hasil" class="<?= $page == 'hasil' ? 'active' : '' ?>">Hasil Pendaftaran</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <main>
            <?php
            switch($page) {
                case 'home':
                    include 'pages/home.php';
                    break;
                case 'daftar':
                    include 'pages/daftar.php';
                    break;
                case 'hasil':
                    include 'pages/hasil.php';
                    break;
                default:
                    include 'pages/home.php';
            }
            ?>
        </main>
    </div>

    <footer style="text-align: center; padding: 2rem 0; margin-top: 2rem; background-color: #2c3e50; color: white;">
        <div class="container">
            <p>&copy; 2024 Sistem Pendaftaran Beasiswa - Universitas Contoh</p>
        </div>
    </footer>

    <script src="assets/js/script.js"></script>
</body>
</html>