<?php
require_once 'config/database.php';

// Fungsi untuk mendapatkan IPK (simulasi otomatis)
function getIPK() {
    // Simulasi IPK otomatis random antara 2.6 - 4.0
    $min = 2.6;
    $max = 4.0;
    $ipk = $min + mt_rand() / mt_getrandmax() * ($max - $min);
    return round($ipk, 2); // Bulatkan ke 2 desimal
}

// Fungsi untuk validasi email
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Fungsi untuk validasi nomor HP
function validatePhone($phone) {
    return preg_match('/^[0-9]+$/', $phone);
}

// Fungsi untuk mendapatkan semua jenis beasiswa
function getAllBeasiswa() {
    $pdo = getConnection();
    $stmt = $pdo->query("SELECT * FROM jenis_beasiswa ORDER BY nama_beasiswa");
    return $stmt->fetchAll();
}

// Fungsi untuk mendapatkan beasiswa berdasarkan ID
function getBeasiswaById($id) {
    $pdo = getConnection();
    $stmt = $pdo->prepare("SELECT * FROM jenis_beasiswa WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

// Fungsi untuk menyimpan pendaftaran
function savePendaftaran($data) {
    $pdo = getConnection();
    $sql = "INSERT INTO pendaftaran_beasiswa (nama, email, no_hp, semester, ipk, jenis_beasiswa_id, berkas_syarat, status_ajuan) 
            VALUES (?, ?, ?, ?, ?, ?, ?, 'belum di verifikasi')";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([
        $data['nama'],
        $data['email'],
        $data['no_hp'],
        $data['semester'],
        $data['ipk'],
        $data['jenis_beasiswa_id'],
        $data['berkas_syarat']
    ]);
}

// Fungsi untuk mendapatkan semua pendaftaran
function getAllPendaftaran() {
    $pdo = getConnection();
    $sql = "SELECT p.*, j.nama_beasiswa 
            FROM pendaftaran_beasiswa p 
            JOIN jenis_beasiswa j ON p.jenis_beasiswa_id = j.id 
            ORDER BY p.created_at DESC";
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll();
}

// Fungsi untuk upload file
function uploadFile($file) {
    $target_dir = "uploads/";
    
    // Buat folder uploads jika belum ada
    if (!is_dir($target_dir)) {
        if (!mkdir($target_dir, 0755, true)) {
            return false; // Gagal membuat folder
        }
    }
    
    $file_extension = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    $new_filename = uniqid() . '.' . $file_extension;
    $target_file = $target_dir . $new_filename;
    
    // Cek ekstensi file
    $allowed_extensions = ['pdf', 'jpg', 'jpeg', 'png', 'zip'];
    if (!in_array($file_extension, $allowed_extensions)) {
        return false;
    }
    
    // Cek ukuran file (max 5MB)
    if ($file["size"] > 5000000) {
        return false;
    }
    
    // Pastikan folder dapat ditulis
    if (!is_writable($target_dir)) {
        return false;
    }
    
    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return $new_filename;
    }
    return false;
}

// Fungsi untuk format tanggal Indonesia
function formatTanggal($date) {
    $bulan = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    $timestamp = strtotime($date);
    $hari = date('d', $timestamp);
    $bulan_idx = date('n', $timestamp);
    $tahun = date('Y', $timestamp);
    
    return $hari . ' ' . $bulan[$bulan_idx] . ' ' . $tahun;
}
?>