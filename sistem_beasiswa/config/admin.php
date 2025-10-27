<?php
// Konfigurasi Admin
define('ADMIN_USERNAME', 'admin');
define('ADMIN_PASSWORD', 'admin123'); // Dalam implementasi real, gunakan hash password

// Fungsi untuk memulai session
function startSession() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

// Fungsi untuk login admin
function loginAdmin($username, $password) {
    if ($username === ADMIN_USERNAME && $password === ADMIN_PASSWORD) {
        startSession();
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $username;
        $_SESSION['login_time'] = time();
        return true;
    }
    return false;
}

// Fungsi untuk mengecek apakah admin sudah login
function isAdminLoggedIn() {
    startSession();
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

// Fungsi untuk logout admin
function logoutAdmin() {
    startSession();
    unset($_SESSION['admin_logged_in']);
    unset($_SESSION['admin_username']);
    unset($_SESSION['login_time']);
    session_destroy();
}

// Fungsi untuk redirect jika belum login
function requireAdminLogin() {
    if (!isAdminLoggedIn()) {
        header('Location: admin_login.php');
        exit;
    }
}

// Fungsi untuk mendapatkan nama admin
function getAdminUsername() {
    startSession();
    return isset($_SESSION['admin_username']) ? $_SESSION['admin_username'] : '';
}

// Fungsi untuk mendapatkan waktu login
function getLoginTime() {
    startSession();
    return isset($_SESSION['login_time']) ? $_SESSION['login_time'] : 0;
}
?>