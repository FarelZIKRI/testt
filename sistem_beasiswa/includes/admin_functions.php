<?php
require_once 'functions.php';
require_once '../config/admin.php';

// Fungsi untuk update status pendaftaran
function updateStatusPendaftaran($id, $status) {
    $pdo = getConnection();
    $allowed_status = ['belum di verifikasi', 'diverifikasi', 'ditolak'];
    
    if (!in_array($status, $allowed_status)) {
        return false;
    }
    
    $sql = "UPDATE pendaftaran_beasiswa SET status_ajuan = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$status, $id]);
}

// Fungsi untuk mendapatkan statistik dashboard
function getDashboardStats() {
    $pdo = getConnection();
    
    $stats = [];
    
    // Total pendaftaran
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM pendaftaran_beasiswa");
    $stats['total_pendaftaran'] = $stmt->fetch()['total'];
    
    // Pendaftaran belum diverifikasi
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM pendaftaran_beasiswa WHERE status_ajuan = 'belum di verifikasi'");
    $stats['belum_verifikasi'] = $stmt->fetch()['total'];
    
    // Pendaftaran diverifikasi
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM pendaftaran_beasiswa WHERE status_ajuan = 'diverifikasi'");
    $stats['diverifikasi'] = $stmt->fetch()['total'];
    
    // Pendaftaran ditolak
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM pendaftaran_beasiswa WHERE status_ajuan = 'ditolak'");
    $stats['ditolak'] = $stmt->fetch()['total'];
    
    // Statistik per jenis beasiswa
    $stmt = $pdo->query("
        SELECT j.nama_beasiswa, COUNT(p.id) as total 
        FROM jenis_beasiswa j 
        LEFT JOIN pendaftaran_beasiswa p ON j.id = p.jenis_beasiswa_id 
        GROUP BY j.id, j.nama_beasiswa
    ");
    $stats['per_beasiswa'] = $stmt->fetchAll();
    
    // Pendaftaran hari ini
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM pendaftaran_beasiswa WHERE DATE(created_at) = CURDATE()");
    $stats['hari_ini'] = $stmt->fetch()['total'];
    
    // Pendaftaran minggu ini
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM pendaftaran_beasiswa WHERE WEEK(created_at) = WEEK(NOW()) AND YEAR(created_at) = YEAR(NOW())");
    $stats['minggu_ini'] = $stmt->fetch()['total'];
    
    return $stats;
}

// Fungsi untuk mendapatkan pendaftaran dengan filter
function getPendaftaranWithFilter($status = null, $jenis_beasiswa = null, $limit = null) {
    $pdo = getConnection();
    
    $sql = "SELECT p.*, j.nama_beasiswa 
            FROM pendaftaran_beasiswa p 
            JOIN jenis_beasiswa j ON p.jenis_beasiswa_id = j.id";
    
    $conditions = [];
    $params = [];
    
    if ($status) {
        $conditions[] = "p.status_ajuan = ?";
        $params[] = $status;
    }
    
    if ($jenis_beasiswa) {
        $conditions[] = "p.jenis_beasiswa_id = ?";
        $params[] = $jenis_beasiswa;
    }
    
    if (!empty($conditions)) {
        $sql .= " WHERE " . implode(" AND ", $conditions);
    }
    
    $sql .= " ORDER BY p.created_at DESC";
    
    if ($limit) {
        $sql .= " LIMIT " . intval($limit);
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

// Fungsi untuk mendapatkan detail pendaftaran
function getPendaftaranById($id) {
    $pdo = getConnection();
    $sql = "SELECT p.*, j.nama_beasiswa, j.syarat_ipk 
            FROM pendaftaran_beasiswa p 
            JOIN jenis_beasiswa j ON p.jenis_beasiswa_id = j.id 
            WHERE p.id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    return $stmt->fetch();
}

// Fungsi untuk menghapus pendaftaran
function deletePendaftaran($id) {
    $pdo = getConnection();
    
    // Ambil data file untuk dihapus
    $pendaftaran = getPendaftaranById($id);
    if ($pendaftaran && $pendaftaran['berkas_syarat']) {
        $file_path = "../uploads/" . $pendaftaran['berkas_syarat'];
        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }
    
    // Hapus dari database
    $sql = "DELETE FROM pendaftaran_beasiswa WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$id]);
}

// Fungsi untuk menambah jenis beasiswa baru
function addJenisBeasiswa($nama, $syarat_ipk, $deskripsi) {
    $pdo = getConnection();
    $sql = "INSERT INTO jenis_beasiswa (nama_beasiswa, syarat_ipk, deskripsi) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$nama, $syarat_ipk, $deskripsi]);
}

// Fungsi untuk update jenis beasiswa
function updateJenisBeasiswa($id, $nama, $syarat_ipk, $deskripsi) {
    $pdo = getConnection();
    $sql = "UPDATE jenis_beasiswa SET nama_beasiswa = ?, syarat_ipk = ?, deskripsi = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$nama, $syarat_ipk, $deskripsi, $id]);
}

// Fungsi untuk hapus jenis beasiswa
function deleteJenisBeasiswa($id) {
    $pdo = getConnection();
    
    // Cek apakah ada pendaftaran yang menggunakan jenis beasiswa ini
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM pendaftaran_beasiswa WHERE jenis_beasiswa_id = ?");
    $stmt->execute([$id]);
    $count = $stmt->fetch()['total'];
    
    if ($count > 0) {
        return false; // Tidak bisa hapus karena masih ada pendaftaran
    }
    
    $sql = "DELETE FROM jenis_beasiswa WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$id]);
}

// Fungsi untuk export data ke CSV
function exportToCSV($data, $filename) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    
    $output = fopen('php://output', 'w');
    
    // Header CSV
    fputcsv($output, [
        'No', 'Nama', 'Email', 'No HP', 'Semester', 'IPK', 
        'Jenis Beasiswa', 'Status', 'Tanggal Daftar'
    ]);
    
    // Data
    foreach ($data as $index => $row) {
        fputcsv($output, [
            $index + 1,
            $row['nama'],
            $row['email'],
            $row['no_hp'],
            $row['semester'],
            $row['ipk'],
            $row['nama_beasiswa'],
            $row['status_ajuan'],
            date('d/m/Y H:i', strtotime($row['created_at']))
        ]);
    }
    
    fclose($output);
    exit;
}
?>