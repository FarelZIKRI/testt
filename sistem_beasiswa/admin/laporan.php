<?php
require_once '../includes/admin_functions.php';
requireAdminLogin();

// Proses export CSV
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    $status_filter = isset($_GET['status']) ? $_GET['status'] : null;
    $beasiswa_filter = isset($_GET['beasiswa']) ? $_GET['beasiswa'] : null;
    
    $data = getPendaftaranWithFilter($status_filter, $beasiswa_filter);
    $filename = 'laporan_beasiswa_' . date('Y-m-d_H-i-s') . '.csv';
    
    exportToCSV($data, $filename);
    exit;
}

$stats = getDashboardStats();
$beasiswa_list = getAllBeasiswa();

// Filter untuk laporan
$status_filter = isset($_GET['status']) ? $_GET['status'] : null;
$beasiswa_filter = isset($_GET['beasiswa']) ? $_GET['beasiswa'] : null;
$pendaftaran_list = getPendaftaranWithFilter($status_filter, $beasiswa_filter);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan - Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <header class="admin-header">
        <div class="container">
            <h1>📊 Laporan & Statistik</h1>
            <p>Analisis dan export data pendaftaran beasiswa</p>
        </div>
    </header>

    <nav class="admin-nav">
        <div class="container">
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="pendaftaran.php">Kelola Pendaftaran</a></li>
                <li><a href="beasiswa.php">Kelola Beasiswa</a></li>
                <li><a href="laporan.php" class="active">Laporan</a></li>
                <li><a href="../index.php" target="_blank">Lihat Website</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <!-- Ringkasan Statistik -->
        <div class="card">
            <h3>📈 Ringkasan Statistik</h3>
            <div class="stats-summary">
                <div class="stat-card">
                    <div class="stat-number"><?= $stats['total_pendaftaran'] ?></div>
                    <div>Total Pendaftaran</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?= $stats['belum_verifikasi'] ?></div>
                    <div>Belum Diverifikasi</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?= $stats['diverifikasi'] ?></div>
                    <div>Diverifikasi</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?= $stats['ditolak'] ?></div>
                    <div>Ditolak</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?= $stats['hari_ini'] ?></div>
                    <div>Hari Ini</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?= $stats['minggu_ini'] ?></div>
                    <div>Minggu Ini</div>
                </div>
            </div>
        </div>

        <!-- Export Data -->
        <div class="card">
            <h3>📤 Export Data</h3>
            
            <!-- Filter untuk Export -->
            <form method="GET" style="margin-bottom: 1.5rem;">
                <div style="display: grid; grid-template-columns: 1fr 1fr auto; gap: 1rem; align-items: end;">
                    <div class="form-group">
                        <label for="status">Filter Status</label>
                        <select id="status" name="status">
                            <option value="">Semua Status</option>
                            <option value="belum di verifikasi" <?= $status_filter === 'belum di verifikasi' ? 'selected' : '' ?>>Belum Diverifikasi</option>
                            <option value="diverifikasi" <?= $status_filter === 'diverifikasi' ? 'selected' : '' ?>>Diverifikasi</option>
                            <option value="ditolak" <?= $status_filter === 'ditolak' ? 'selected' : '' ?>>Ditolak</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="beasiswa">Filter Beasiswa</label>
                        <select id="beasiswa" name="beasiswa">
                            <option value="">Semua Beasiswa</option>
                            <?php foreach ($beasiswa_list as $beasiswa): ?>
                                <option value="<?= $beasiswa['id'] ?>" <?= $beasiswa_filter == $beasiswa['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($beasiswa['nama_beasiswa']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn">Filter</button>
                </div>
            </form>

            <div class="export-buttons">
                <a href="?export=csv<?= $status_filter ? '&status=' . urlencode($status_filter) : '' ?><?= $beasiswa_filter ? '&beasiswa=' . urlencode($beasiswa_filter) : '' ?>" 
                   class="btn" style="background: #27ae60;">
                    📊 Export ke CSV (<?= count($pendaftaran_list) ?> data)
                </a>
                <a href="pendaftaran.php" class="btn">
                    👁️ Lihat Data di Tabel
                </a>
            </div>

            <div class="alert alert-info">
                <strong>Info Export:</strong> File CSV akan berisi data sesuai filter yang dipilih. 
                Format: Nama, Email, No HP, Semester, IPK, Jenis Beasiswa, Status, Tanggal Daftar.
            </div>
        </div>

        <!-- Statistik per Jenis Beasiswa -->
        <div class="chart-container">
            <h3>📊 Statistik per Jenis Beasiswa</h3>
            <?php if (!empty($stats['per_beasiswa'])): ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Jenis Beasiswa</th>
                            <th>Total Pendaftar</th>
                            <th>Persentase</th>
                            <th>Diverifikasi</th>
                            <th>Ditolak</th>
                            <th>Belum Diverifikasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($stats['per_beasiswa'] as $beasiswa): ?>
                            <?php 
                            $percentage = $stats['total_pendaftaran'] > 0 ? 
                                round(($beasiswa['total'] / $stats['total_pendaftaran']) * 100, 1) : 0;
                            
                            // Hitung detail status per beasiswa
                            $detail_stats = getPendaftaranWithFilter(null, $beasiswa['nama_beasiswa']);
                            $verified = count(array_filter($detail_stats, function($p) { return $p['status_ajuan'] === 'diverifikasi'; }));
                            $rejected = count(array_filter($detail_stats, function($p) { return $p['status_ajuan'] === 'ditolak'; }));
                            $pending = count(array_filter($detail_stats, function($p) { return $p['status_ajuan'] === 'belum di verifikasi'; }));
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($beasiswa['nama_beasiswa']) ?></td>
                                <td><?= $beasiswa['total'] ?></td>
                                <td><?= $percentage ?>%</td>
                                <td><span class="status-badge status-verified"><?= $verified ?></span></td>
                                <td><span class="status-badge status-rejected"><?= $rejected ?></span></td>
                                <td><span class="status-badge status-pending"><?= $pending ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Belum ada data pendaftaran.</p>
            <?php endif; ?>
        </div>

        <!-- Analisis Trend -->
        <div class="card">
            <h3>📈 Analisis Trend</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
                <div>
                    <h4>Status Pendaftaran</h4>
                    <ul>
                        <li>Tingkat Persetujuan: 
                            <?= $stats['total_pendaftaran'] > 0 ? round(($stats['diverifikasi'] / $stats['total_pendaftaran']) * 100, 1) : 0 ?>%
                        </li>
                        <li>Tingkat Penolakan: 
                            <?= $stats['total_pendaftaran'] > 0 ? round(($stats['ditolak'] / $stats['total_pendaftaran']) * 100, 1) : 0 ?>%
                        </li>
                        <li>Pending Review: 
                            <?= $stats['total_pendaftaran'] > 0 ? round(($stats['belum_verifikasi'] / $stats['total_pendaftaran']) * 100, 1) : 0 ?>%
                        </li>
                    </ul>
                </div>
                
                <div>
                    <h4>Aktivitas Pendaftaran</h4>
                    <ul>
                        <li>Pendaftaran Hari Ini: <?= $stats['hari_ini'] ?> pendaftar</li>
                        <li>Pendaftaran Minggu Ini: <?= $stats['minggu_ini'] ?> pendaftar</li>
                        <li>Rata-rata per Hari: 
                            <?= $stats['minggu_ini'] > 0 ? round($stats['minggu_ini'] / 7, 1) : 0 ?> pendaftar
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Rekomendasi -->
        <div class="card" style="background-color: #f8f9fa;">
            <h3>💡 Rekomendasi</h3>
            <ul>
                <?php if ($stats['belum_verifikasi'] > 0): ?>
                    <li><strong>Review Pending:</strong> Ada <?= $stats['belum_verifikasi'] ?> pendaftaran yang perlu diverifikasi.</li>
                <?php endif; ?>
                
                <?php if ($stats['total_pendaftaran'] > 0 && ($stats['ditolak'] / $stats['total_pendaftaran']) > 0.3): ?>
                    <li><strong>Tingkat Penolakan Tinggi:</strong> Pertimbangkan untuk meninjau kembali syarat beasiswa.</li>
                <?php endif; ?>
                
                <?php if ($stats['hari_ini'] > 5): ?>
                    <li><strong>Aktivitas Tinggi:</strong> Hari ini ada banyak pendaftaran baru, pastikan proses review berjalan lancar.</li>
                <?php endif; ?>
                
                <li><strong>Backup Data:</strong> Lakukan export data secara berkala untuk backup.</li>
            </ul>
        </div>
    </div>
</body>
</html>