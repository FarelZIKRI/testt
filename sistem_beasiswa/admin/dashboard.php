<?php
require_once '../includes/admin_functions.php';
requireAdminLogin();

$stats = getDashboardStats();
$recent_pendaftaran = getPendaftaranWithFilter(null, null, 5);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Sistem Beasiswa</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <header class="admin-header">
        <div class="container">
            <h1>🎓 Dashboard Admin</h1>
            <p>Selamat datang, <?= getAdminUsername() ?> | Login: <?= date('d/m/Y H:i', getLoginTime()) ?></p>
        </div>
    </header>

    <nav class="admin-nav">
        <div class="container">
            <ul>
                <li><a href="dashboard.php" class="active">Dashboard</a></li>
                <li><a href="pendaftaran.php">Kelola Pendaftaran</a></li>
                <li><a href="beasiswa.php">Kelola Beasiswa</a></li>
                <li><a href="laporan.php">Laporan</a></li>
                <li><a href="../index.php" target="_blank">Lihat Website</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <!-- Statistik Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?= $stats['total_pendaftaran'] ?></div>
                <div class="stat-label">Total Pendaftaran</div>
            </div>
            <div class="stat-card pending">
                <div class="stat-number"><?= $stats['belum_verifikasi'] ?></div>
                <div class="stat-label">Belum Diverifikasi</div>
            </div>
            <div class="stat-card approved">
                <div class="stat-number"><?= $stats['diverifikasi'] ?></div>
                <div class="stat-label">Diverifikasi</div>
            </div>
            <div class="stat-card rejected">
                <div class="stat-number"><?= $stats['ditolak'] ?></div>
                <div class="stat-label">Ditolak</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $stats['hari_ini'] ?></div>
                <div class="stat-label">Pendaftaran Hari Ini</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $stats['minggu_ini'] ?></div>
                <div class="stat-label">Pendaftaran Minggu Ini</div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card">
            <h3>Quick Actions</h3>
            <div class="quick-actions">
                <a href="pendaftaran.php?status=belum di verifikasi" class="action-btn">
                    📋 Review Pendaftaran Baru
                </a>
                <a href="beasiswa.php" class="action-btn manage">
                    🎯 Kelola Jenis Beasiswa
                </a>
                <a href="laporan.php" class="action-btn export">
                    📊 Export Laporan
                </a>
                <a href="pendaftaran.php" class="action-btn">
                    👥 Semua Pendaftaran
                </a>
            </div>
        </div>

        <!-- Statistik per Jenis Beasiswa -->
        <div class="card">
            <h3>Statistik per Jenis Beasiswa</h3>
            <?php if (!empty($stats['per_beasiswa'])): ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Jenis Beasiswa</th>
                            <th>Jumlah Pendaftar</th>
                            <th>Persentase</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($stats['per_beasiswa'] as $beasiswa): ?>
                            <?php 
                            $percentage = $stats['total_pendaftaran'] > 0 ? 
                                round(($beasiswa['total'] / $stats['total_pendaftaran']) * 100, 1) : 0;
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($beasiswa['nama_beasiswa']) ?></td>
                                <td><?= $beasiswa['total'] ?></td>
                                <td><?= $percentage ?>%</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Belum ada data pendaftaran.</p>
            <?php endif; ?>
        </div>

        <!-- Pendaftaran Terbaru -->
        <div class="card">
            <h3>Pendaftaran Terbaru</h3>
            <?php if (!empty($recent_pendaftaran)): ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Jenis Beasiswa</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_pendaftaran as $pendaftaran): ?>
                            <tr>
                                <td><?= htmlspecialchars($pendaftaran['nama']) ?></td>
                                <td><?= htmlspecialchars($pendaftaran['email']) ?></td>
                                <td><?= htmlspecialchars($pendaftaran['nama_beasiswa']) ?></td>
                                <td>
                                    <?php
                                    $status_class = '';
                                    switch($pendaftaran['status_ajuan']) {
                                        case 'belum di verifikasi':
                                            $status_class = 'status-pending';
                                            break;
                                        case 'diverifikasi':
                                            $status_class = 'status-verified';
                                            break;
                                        case 'ditolak':
                                            $status_class = 'status-rejected';
                                            break;
                                    }
                                    ?>
                                    <span class="status-badge <?= $status_class ?>">
                                        <?= ucfirst($pendaftaran['status_ajuan']) ?>
                                    </span>
                                </td>
                                <td><?= formatTanggal($pendaftaran['created_at']) ?></td>
                                <td>
                                    <a href="detail_pendaftaran.php?id=<?= $pendaftaran['id'] ?>" 
                                       class="btn" style="padding: 0.25rem 0.5rem; font-size: 0.875rem;">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div style="text-align: center; margin-top: 1rem;">
                    <a href="pendaftaran.php" class="btn">Lihat Semua Pendaftaran</a>
                </div>
            <?php else: ?>
                <p>Belum ada pendaftaran.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>