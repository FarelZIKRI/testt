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
    <style>
        .admin-header {
            background: linear-gradient(135deg, #2c3e50, #3498db);
            color: white;
            padding: 1rem 0;
            margin-bottom: 2rem;
        }
        .admin-nav {
            background-color: #34495e;
            padding: 1rem 0;
            margin-bottom: 2rem;
        }
        .admin-nav ul {
            list-style: none;
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            margin: 0;
            padding: 0;
        }
        .admin-nav ul li {
            margin: 0 1rem;
        }
        .admin-nav ul li a {
            color: white;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            transition: background-color 0.3s;
        }
        .admin-nav ul li a:hover,
        .admin-nav ul li a.active {
            background-color: #2c3e50;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-align: center;
            border-left: 4px solid #3498db;
        }
        .stat-card.pending {
            border-left-color: #f39c12;
        }
        .stat-card.approved {
            border-left-color: #27ae60;
        }
        .stat-card.rejected {
            border-left-color: #e74c3c;
        }
        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }
        .stat-label {
            color: #666;
            font-size: 0.9rem;
        }
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .action-btn {
            display: block;
            text-align: center;
            padding: 1rem;
            background: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            transition: background-color 0.3s;
        }
        .action-btn:hover {
            background: #2980b9;
        }
        .action-btn.manage {
            background: #27ae60;
        }
        .action-btn.manage:hover {
            background: #229954;
        }
        .action-btn.export {
            background: #8e44ad;
        }
        .action-btn.export:hover {
            background: #7d3c98;
        }
    </style>
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